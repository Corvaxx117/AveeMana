<?php

namespace Controllers;

use Exception;

class OrdersController extends BaseController
{
    // AFFICHAGE DES DONNES PERSONNELLES ET DE TOUTES LES COMMANDES DU SITE (ADMIN)
    public function displayAccountAdmin($errors)
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
        $template = "userArea.phtml";
        include_once 'views/layout.phtml';
    }
    // AFFICHAGE DES DONNES PERSONNELLES ET DU TABLEAU DES COMMANDES (USER)
    public function displayAccountUser($errors)
    {
        $token = $this->randomString->getRandomString(35);
        $_SESSION['shield'] = $token;
        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connection
        if (!isset($_SESSION['user']['id']) && empty($_SESSION['user']['id'])) {

            header('location:index.php?route=login');
            exit;
        }
        // Récupérer les commandes de l'utilisateur
        $combinedData = $this->getCombinedOrderData();

        // Passer les données à la vue
        $template = "userArea.phtml";
        include_once 'views/layout.phtml';
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
        }
        // Générer un nouveau jeton
        $token = $this->randomString->getRandomString(35);
        $_SESSION['shield'] = $token;

        // Charger la vue avec les données des commandes
        $template = "userArea.phtml";
        include_once 'views/layout.phtml';
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
