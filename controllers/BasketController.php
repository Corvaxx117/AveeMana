<?php

namespace Controllers;

class BasketController extends BaseController
{
    // AJOUTE (OU SUPPRIME) UN ARTICLE AU PANIER
    public function addToBasket(int $id, string $action)
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
    public function readBasket(array $errors = [])
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
    public function removeFromBasket(int $id)
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
}
