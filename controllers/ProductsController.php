<?php

namespace Controllers;

class ProductsController extends BaseController
{
    // AFFICHAGE DE LA BOUTIQUE
    public function displayAllProducts()
    {
        // token
        $token = $this->randomString->getRandomString(30);
        $_SESSION['shield'] = $token;

        // Récupéreration de tous les produits de la bdd
        $products =  $this->productsModel->getAllProducts();
        $template = "products.phtml";
        include_once 'views/layout.phtml';
    }
    // SOUMISSION DU FORMULAIRE, VERIFICATION ET AJOUT DE PRODUIT DANS LA BOUTIQUE
    public function submitFormAddProduct()
    {
        $errors = [];
        $valids = [];
        // die();
        if (
            array_key_exists('name', $_POST) &&
            array_key_exists('description', $_POST) &&
            array_key_exists('price', $_POST) &&
            array_key_exists('quantity_in_stock', $_POST) &&
            array_key_exists('picture_path', $_FILES)
        ) {
            // VERIFICATION DU NOM 
            if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 150)
                $errors[] = "Le nom du produit doit contenir entre 3 et 150 caractères";

            // VERIFICATION DE LA TAILLE DE LA DESCRIPTION
            if (strlen($_POST['description']) > 400)
                $errors[] = "La description du produit doit contenir moins de 400 caractères";

            // VERIFICATION DU PRIX
            if (!preg_match("/^[0-9]+$/", $_POST['price']))
                $errors[] = "Veuillez rentrer un prix valide";

            // VERIFICATION DE LA QUANTITE EN STOCK
            if (!preg_match("/^[0-9]+$/", $_POST['quantity_in_stock']))
                $errors[] = "Veuillez rentrer une quantité valide";

            // SI PAS D'ERREUR 
            if (count($errors) == 0) {
                $newNameFile = $this->uploadsModel->uploadingfiles($_FILES['picture_path'], 'image', $errors);
                if (count($errors) == 0) {
                    $data = [
                        trim(ucfirst($_POST['name'])),
                        trim(ucfirst($_POST['description'])),
                        trim($_POST['price']),
                        trim($_POST['quantity_in_stock']),
                        trim($newNameFile)

                    ];
                    $resultNewProduct =  $this->productsModel->addNewProduct($data);
                    $valids[] = 'Votre demande d\'ajout de produit a bien été enregistrée.';
                } else {
                    // token
                    $token = $this->randomString->getRandomString(30);
                    $_SESSION['shield'] = $token;
                    
                    $products =  $this->productsModel->getAllProducts();
                    $template = 'products.phtml';
                    include_once 'views/layout.phtml';
                }
            }
        }
        // token
        $token = $this->randomString->getRandomString(30);
        $_SESSION['shield'] = $token;
                    
        $products =  $this->productsModel->getAllProducts();
        $template = 'products.phtml';
        include_once 'views/layout.phtml';
    }
    // MASQUER OU REAFFICHER UN PRODUIT DE LA BOUTIQUE
    public function hideOrShowProduct($id, $action)
    {
        $status = 1;
        if ($action == 'hide') $status = 0;
        $statusProduct =  $this->productsModel->changeStatusProduct($id, $status);

        header('location:index.php?route=shop');
        exit;
    }

}
