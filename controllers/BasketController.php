<?php

namespace Controllers;

class BasketController extends BaseController
{
    // AJOUTE (OU SUPPRIME) UN ARTICLE AU PANIER
    public function addToBasket($id, $action)
    {
        // Si le cookie existe on le charge, sinon, tableau vide
        $array = [];
        $found = false;

        if (isset($_COOKIE['basket'])) {
            // on lit le Cookie et on le décode : on obtient un tableau associatif
            $array = json_decode($_COOKIE['basket'], true);
        }
        // on cherche si le produit est déjà dans le panier
        foreach ($array as &$product) {
            if ($product['id'] == intval($id)) {
                if ($action == 'add') {
                    $product['qty']++;
                } elseif ($action == 'remove') {
                    if ($product['qty'] > 0)
                        $product['qty']--;
                }
                $found = true;
                break; // on sort de la boucle dès qu'on a trouvé le produit
            }
        }
        // Si le produit n'a pas été trouvé dans le panier, on l'ajoute avec une quantité de 1
        if (!$found && $action == 'add') {
            $array[] = ['id' => intval($id), 'qty' => 1];
        }
        // On encode le nouveau tableau
        $arrayjson = json_encode($array);
        // On réécrit le cookie
        setcookie('basket', $arrayjson, time() + 86400); // durée du cookie = 24H
        // On redirige l'utilisateur vers la page du panier
        header('location:index.php?route=basketProducts');
        exit;
    }
    // AFFICHAGE DU PANIER ET CALCUL TOTAL QUANTITE / PRIX
    public function readBasket()
    {
        $myBasket = [];
        if (isset($_COOKIE['basket'])) {
            $myBasket = json_decode($_COOKIE['basket'], true);
        }
        $products = [];
        $totalPrice = 0;
        foreach ($myBasket as $key => $elem) {
            $products[] = $this->productsModel->getOneProductById($elem['id']);
            $products[$key]['qty'] = $elem['qty'];
        }
        foreach ($products as $product) {
            $totalPrice = $totalPrice + $product['qty'] * $product['price'];
        }
        $totalItems = 0;
        foreach ($myBasket as $elem) {
            $totalItems += $elem['qty'];
        }

        $template = "basket.phtml";
        include_once 'views/layout.phtml';
    }
    // VIDE L'INTEGRALITE DU PANIER 
    public function deleteAllBasket()
    {
        setcookie('basket', '', time() + 1); // Le panier sera vidé apres 1 seconde 
        header('Location: index.php?route=basketProducts');
        exit;
    }
    // SUPPRIME UN ITEM DU PANIER 
    public function removeFromBasket($id)
    {
        $array = [];
        if (isset($_COOKIE['basket'])) {
            $array = json_decode($_COOKIE['basket'], true);
        }

        foreach ($array as $key => $product) {
            if ($product['id'] == intval($id)) {
                unset($array[$key]); // on supprime l'élément du tableau
                break;
            }
        }

        if (empty($array)) {
            $this->deleteAllBasket();
        } else {
            // On encode le nouveau tableau
            $arrayjson = json_encode(array_values($array)); // on réindexe le tableau pour éviter des clés manquantes
            // On réécrit le cookie
            setcookie('basket', $arrayjson, time() + 86400); // durée du cookie = 24H
            header('Location: index.php?route=basketProducts');
            exit;
        }
    }
    // VERIFICATION DU PANIER ET FINALISATION DE LA COMMANDE
    public function validateOrder()
    {
        $valids = [];
        $errors = [];
        $myBasket = [];
        $totalPrice = 0;

        // Récupère le contenu du cookie
        if (isset($_COOKIE['basket'])) {
            $myBasket = json_decode($_COOKIE['basket'], true);
        }

        $error = false;

        // Boucler sur le tableau obtenu, pour chaque id, vérifier si la qty désirée est dispo dans la bdd
        foreach ($myBasket as $elem) {
            $product = $this->productsModel->getOneProductById($elem['id']);
            if (isset($product['quantity_in_stock']) && $elem['qty'] > $product['quantity_in_stock']) {
                $error = false;
            }
        }

        if (!$error) {

            // Si l'intégralité du panier peut être satisfaite, alors INSERT INTO dans la table `orders` et `orderDetails`
            // Tableau $data avec les deux valeurs nécessaires pour la table orders : user_id et prix total du panier 
            $data = [
                $_SESSION['user']['id'],
                0 // Prix total initialisé à 0
            ];

            // Récupération de l'Id du dernier produit ajouté dans la table orders
            $lastOrderId = $this->ordersModel->addOrder($data);

            // Boucler sur le cookie, à chaque élément du tableau, faire un INSERT INTO dans la bdd
            foreach ($myBasket as $product) {
                $productId = $product['id'];
                $productQty = $product['qty'];

                // Récupérer le prix du produit depuis la base de données
                $productData = $this->productsModel->getOneProductById($productId);
                if (!$productData || !isset($productData['price'])) {
                    // Gérer le cas où le produit ou le prix n'est pas disponible
                    $error = true;
                    break;
                }
                // Pour y ajouter $lastOrderId, idproduct et qty
                $allDatas = [
                    $lastOrderId,
                    $productId,
                    $productQty
                ];

                // INSERT INTO orderDetails
                $details = $this->ordersModel->addOrderDetails($allDatas);

                // Appeler une méthode Update qui retirera la qty choisie par le client de la qty en stock
                $newStock = $productData['quantity_in_stock'] - $productQty;
                $updateQty = $this->productsModel->updateProductQuantity($newStock, $productId);

                // Calculer le prix total de la commande
                $totalPrice = $totalPrice + $productQty * $productData['price'];
            }

            // Mettre à jour le prix total dans la commande principale
            $this->ordersModel->updateOrderTotalPrice($lastOrderId, $totalPrice);
            $token = $this->randomString->getRandomString(35);
            $_SESSION['shield'] = $token;

            // On vide le panier
            setcookie('basket', '', time() + 1);

            // Récupérer les données des commandes de cet utilisateur
            $orders = $this->ordersModel->getUserOrdersJoinDetails($_SESSION['user']['id']);

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
            $valids[] = 'Votre commande à bien été enregistrée, merci ! <3';
            // Charger la vue avec les données des commandes
            $template = "userArea.phtml";
            include_once 'views/layout.phtml';

            return $combinedData;
        } else {
            // Sinon, si au moins un article n'est pas en qty suffisante, message d'erreur et ne pas valider le panier
            $errors[] = 'Désolé, la quantité disponible pour certains articles est insuffisante.';
            header('Location: index.php?route=basketProducts');
            exit;
        }
    }
}
