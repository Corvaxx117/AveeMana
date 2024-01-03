<?php

namespace Controllers;

use Exception;
use Stripe\Terminal\Location;

class OrdersController extends BaseController
{
    // AFFICHAGE DES DONNES PERSONNELLES ET DE TOUTES LES COMMANDES DU SITE (ADMIN)
    public function displayAccount(array $valids, array $errors)
    {
        // Générer un nouveau jeton
        $token = $this->randomString->getRandomString(35);
        $_SESSION['shield'] = $token;
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connection
        if (!isset($_SESSION['user']['id']) && empty($_SESSION['user']['id'])) {
            header('location:index.php?route=login');
            exit;
        }
        // Charger la vue avec les données des commandes
        $combinedData = $this->getCombinedOrderData();

        $this->loadUserAreaView($combinedData, $valids, $errors);
    }
    // UN ADMIN PEUT SUPPRIMER UNE COMMANDE DU TABLEAU
    public function deleteOrder()
    {
        $errors = [];
        $valids = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST['orderId'])) {
                $orderId = $_POST['orderId'];

                try {
                    // Appelez la méthode deleteOrderWithCascade pour supprimer la commande en cascade
                    $this->ordersModel->deleteOrderWithCascade($orderId);
                    $valids[] = "La commande a été supprimée avec succès.";
                } catch (Exception $e) {
                    $errors[] = "Une erreur est survenue lors de la suppression de la commande : " . $e->getMessage();
                }
                // Afficher toutes les commandes
                $orders = $this->ordersModel->getAllOrdersJoinDetails();
                $combinedData = $this->getCombinedOrderData();
            }
        } else {
            header('location:index.php?route=userAccount');
            exit;
        }
        // Générer un nouveau jeton
        $token = $this->randomString->getRandomString(35);
        $_SESSION['shield'] = $token;

        // Charger la vue avec les données des commandes
        $this->loadUserAreaView($combinedData, $valids, $errors);
    }
    // VERIFICATION DE LA VALIDITE DU PANIER ET LA DISPONIBILITE DES PRODUITS
    public function isBasketValid()
    {
        // Vérifier si la méthode a été appelée via POST et si la taille et le panier existent
        $allowedSizes = ['S', 'M', 'L', 'XL'];

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_COOKIE['basket'])) {

            // Récupérer le contenu du cookie
            $myBasket = json_decode($_COOKIE['basket'], true);

            // Initialiser les variables
            $totalPrice = 0;
            $products = [];
            $errors = [];
            if (isset($_POST['size']) && in_array($_POST['size'], $allowedSizes)) {
                // Vérifier si la taille est valide
                $selectedSize = $_POST['size'];
                // Enregistrer la taille sélectionnée dans la session
                $_SESSION['size'] = $selectedSize;
            }
            // Parcourir le panier
            foreach ($myBasket as $elem) {
                $product = $this->productsModel->getOneProductById($elem['id']);

                // Vérifier la quantité en stock
                if (!isset($product['quantity_in_stock']) || $elem['qty'] <= $product['quantity_in_stock']) {
                    $product['qty'] = $elem['qty'];
                    $products[] = $product;

                    // Calculer le total du prix du panier
                    $totalPrice += $product['qty'] * $product['price'];
                } else {
                    // Gérer le cas où le panier n'est pas valide
                    $errors[] = 'Désolé, la quantité disponible pour l\'article : ' . $product['name'] . ' est insuffisante pour valider la commande.';
                }
            }
            // Vérifier s'il y a des erreurs avant d'afficher la page de paiement
            if (empty($errors)) {
                // Récupérer le nombre total d'articles dans le panier
                $totalItems = array_sum(array_column($myBasket, 'qty'));

                // Afficher la page de paiement
                $template = "payment.phtml";
                include_once 'views/layout.phtml';
            } else {
                // Gérer le cas où des erreurs sont présentes
                $this->redirectToBasketWithErrors($errors);
            }
        } else {
            $errors[] = 'Une erreur s\'est produite';
            $this->redirectToBasketWithErrors($errors);
        }
    }
    // REDIRECTION VERS LE PANIER SI LA COMMANDE NE PEUT ETRE SATISFAITE
    function redirectToBasketWithErrors(array $errors = [])
    {
        $_SESSION['errors'] = $errors;
        header('Location: index.php?route=basketProducts');
        exit;
    }
    // METHODE DE VALIDATION D'UNE COMMANDE
    public function validateOrder()
    {
        $errors = [];
        $valids = [];
        $myBasket = [];

        // Récupérer le contenu du cookie
        if (isset($_COOKIE['basket'])) {
            $myBasket = json_decode($_COOKIE['basket'], true);
        }

        if ($myBasket !== []) {
            // Initialiser les données de la commande principale
            $orderData = [
                $_SESSION['user']['id'],
                0 // Prix total initialisé à 0
            ];
            // Ajouter la commande principale dans la base de données
            $lastOrderId = $this->ordersModel->addOrder($orderData);

            // Traiter les détails de la commande
            $this->processOrderDetails($myBasket, $lastOrderId);

            // Nettoyer le panier
            setcookie('basket', '', time() + 1);

            // Récupérer les données des commandes de cet utilisateur
            $combinedData = $this->getUserOrderDetails($_SESSION['user']['id']);

            $valids[] = 'Votre commande a bien été enregistrée, merci ! <3';

            $this->loadUserAreaView($combinedData, $valids, $errors);
        } else {
            header('location:index.php?route=userAccount');
            exit;
        }
    }
    // METHODE POUR REGROUPER LES ARTICLES DE LA MEME COMMANDE
    private function groupOrderItems(array $combinedData)
    {
        $groupedData = [];

        foreach ($combinedData as $combined) {
            $order = $combined['order'];
            $productId = $combined['product']['id'];

            if (!isset($groupedData[$order['id']])) {
                // Si la commande n'est pas encore présente dans $groupedData, l'ajouter avec le premier article
                $groupedData[$order['id']] = [
                    'order' => $order,
                    'products' => [$combined['product']]
                ];
            } else {
                // Si la commande est déjà présente, ajouter l'article au tableau des produits
                $groupedData[$order['id']]['products'][] = $combined['product'];
            }
        }

        return $groupedData;
    }
    // CHARGER LA VUE 'MON COMPTE' EN AFFICHANT LE RECAPITULATIF DES COMMANDES
    private function loadUserAreaView(array $combinedData, array $valids, array $errors)
    {

        // Regrouper les articles de la même commande
        $groupedData = $this->groupOrderItems($combinedData);

        // Préparer les variables pour la vue
        $orderViewData = [];

        foreach ($groupedData as $orderData) {
            $orderViewData[] = [
                'order' => $orderData['order'],
                'products' => $orderData['products'],
            ];
        }
        $template = "userArea.phtml";
        include_once 'views/layout.phtml';
    }
    // METHODE POUR TRAITER LES DETAILS DE LA COMMANDE
    private function processOrderDetails(array $myBasket, int $lastOrderId)
    {
        $totalPrice = 0;

        // Parcourir le panier
        foreach ($myBasket as $product) {
            $productId = $product['id'];
            $productQty = $product['qty'];

            // Récupérer le produit depuis la base de données
            $productData = $this->productsModel->getOneProductById($productId);

            // Récupérer la taille sélectionnée
            $selectedSize = $_SESSION['size'];

            // Ajouter les détails de la commande dans la base de données
            $orderDetailsData = [
                $lastOrderId,
                $productId,
                $productQty,
                $selectedSize
            ];
            $this->ordersModel->addOrderDetails($orderDetailsData);

            // Mettre à jour la quantité en stock du produit
            $newStock = $productData['quantity_in_stock'] - $productQty;
            $this->productsModel->updateProductQuantity($newStock, $productId);

            // Calculer le prix total de la commande
            $totalPrice += $productQty * $productData['price'];
        }

        // Mettre à jour le prix total dans la commande principale
        $this->ordersModel->updateOrderTotalPrice($lastOrderId, $totalPrice);
    }
    // METHODE POUR RECUPERER LES DETAILS DE LA COMMANDE D4UN UTILISATEUR
    private function getUserOrderDetails(int $userId)
    {
        $orders = $this->ordersModel->getUserOrdersJoinDetails($userId);

        $combinedData = [];
        // Instancier le modèle des produits
        $productsModel = new \Models\Products();

        // Utiliser la boucle sur $orders car c'est l'ensemble complet des commandes
        foreach ($orders as $order) {
            // Récupérer les données du produit pour chaque commande
            $product = $productsModel->getOneProductById($order['productId']);

            // Ajouter les données combinées à $combinedData
            $combinedData[] = [
                'order' => $order,
                'product' => $product
            ];
        }

        return $combinedData;
    }
    // REGROUPE DES INFORMATIONS SUR LA COMMANDE ET SUR LES PRODUITS QUI LA COMPOSENT
    public function getCombinedOrderData()
    {
        // Si l'utilisateur est Admin, on affiche toutes les commmandes
        if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2) {
            // Récupérer les données des commandes
            $orders = $this->ordersModel->getAllOrdersJoinDetails();
            // Sinon on affiche uniquement les commande de cet utilisateur
        } else {
            // Récupérer les données des commandes de cet utilisateur
            $orders = $this->ordersModel->getUserOrdersJoinDetails($_SESSION['user']['id']);
        }
        $combinedData = [];
        // Instancier le modèle des produits
        $productsModel = new \Models\Products();

        // Utiliser la boucle sur $orders car c'est l'ensemble complet des commandes
        foreach ($orders as $order) {
            // Récupérer les données du produit pour chaque commande
            $product = $productsModel->getOneProductById($order['productId']);

            // Ajouter les données combinées à $combinedData
            $combinedData[] = [
                'order' => $order,
                'product' => $product,
                // ... (autres données nécessaires)
            ];
        }

        return $combinedData;
    }
}
