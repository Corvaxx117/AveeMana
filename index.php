<?php
require "vendor/autoload.php";
session_start();

spl_autoload_register(function ($class) {                            // $class = new Controllers\HomeController
    require_once lcfirst(str_replace('\\', '/', $class)) . '.php';   // require_once controllers/HomeController.php
});

// index.php
if (array_key_exists("route", $_GET)) :
    switch ($_GET['route']) {

            /*******************************************************************************************
            -------------------------------------------------- PAGE HOME 
         *******************************************************************************************/

            // AFFICHAGE PAGE D'ACCUEIL
        case 'home':
            $controller = new Controllers\HomeController;
            $controller->displayHome();
            break;

            // SOUMISSION DU FORMULAIRE DE CREATION D'EVENEMENTS
        case 'submitFormAddEvent':
            $controller = new Controllers\HomeController;
            $controller->submitFormAddEvent();
            break;
            // SUPPRESSION D'UN EVENEMENT (ADMIN)
        case 'deleteEvent':
            $controller = new Controllers\HomeController;
            $controller->deleteEvent();
            break;

            /*******************************************************************************************
            -------------------------------------------------- PAGE SHOP
             *******************************************************************************************/

            // AFFICHAGE BOUTIQUE
        case 'shop':
            $controller = new Controllers\ProductsController;
            $controller->displayAllProducts();
            break;

            // AFFICHER / MASQUER UN ARTICLE (ADMIN)
        case 'changeStatusProduct':
            $controller = new Controllers\ProductsController;
            $controller->hideOrShowProduct($_GET['id'], $_GET['action']);
            break;

            // SOUMISSION DU FORMULAIRE DE CREATION DE PRODUIT (ADMIN)
        case 'submitFormAddProduct':
            $controller = new Controllers\ProductsController;
            $controller->submitFormAddProduct();
            break;

            /*******************************************************************************************
            -------------------------------------------------- PAGE PANIER D'ARTICLES - (USER)
             *******************************************************************************************/

            // LECTURE DU PANIER
        case 'basketProducts':
            $controller = new Controllers\BasketController;
            $controller->readBasket();
            break;

            // AJOUTER PRODUIT DANS PANIER 
        case 'addToBasket':
            $controller = new Controllers\BasketController;
            $controller->addToBasket($_GET['id'], $_GET['action']);
            break;

            // SUPPRIMER UN ARTICLE DU PANIER
        case 'removeFromBasket':
            $controller = new Controllers\BasketController;
            $controller->removeFromBasket($_GET['id']);
            break;

            // SUPPRIMER L'INTEGRALITE DU PANIER
        case 'deleteAllBasket':
            $controller = new Controllers\BasketController;
            $controller->deleteAllBasket();
            break;

            // PROCEDER A LA COMMANDE
        case 'proceedOrder':
            $controller = new Controllers\BasketController;
            $controller->validateOrder();

            /*******************************************************************************************
            -------------------------------------------------- PAGE CREATION DE COMPTE
             *******************************************************************************************/

            // AFFICHAGE DU FORMULAIRE DE CREATION DE COMPTE
        case 'addUsers':
            $controller = new Controllers\CustomersController;
            $controller->displayFormAddUsers();
            break;

            // SOUMISSION DU FORMULAIRE DE CREATION DE COMPTE
        case 'submitFormAddUser':
            $controller = new Controllers\CustomersController;
            $controller->submitFormAddUser();
            break;

            /*******************************************************************************************
            -------------------------------------------------- PAGE GALERIE (ADMIN)
             *******************************************************************************************/

            // AFFICHAGE DU FORMULAIRE D'AJOUT DE MEDIA 
        case 'gallery':
            $controller = new Controllers\MediaController;
            $controller->displayAllMedias();
            break;

            // SOUMISSION DU FORMULAIRE D'AJOUT DE MEDIA
        case 'submitFormAddMedia':
            $controller = new Controllers\MediaController;
            $controller->submitFormAddMedia();
            break;

            /*******************************************************************************************
            -------------------------------------------------- PAGE LOGIN
             *******************************************************************************************/

            // AFFICHAGE DU FORMULAIRE DE CONNEXION DE L'UTILISATEUR
        case 'login':
            $controller = new Controllers\LoginController;
            $controller->displayFormLogin();
            break;

            // SOUMISSION DU FORMULAIRE DE CONNEXION DE L'UTILISATEUR
        case 'submitFormLogin':
            $controller = new Controllers\LoginController;
            $controller->connectionUser();
            break;

            /*******************************************************************************************
            -------------------------------------------------- PAGE ESPACE UTILISATEUR
             *******************************************************************************************/

            // AFFICHAGE DU COMPTE DE L'UTILISATEUR  
        case 'userAccount':
            if (isset($_SESSION['user']) && $_SESSION['user']['role_id'] == 2) {
                $controller = new Controllers\OrdersController;
                $controller->displayAccountAdmin($errors = []);
                break;
            } else {
                $controller = new Controllers\OrdersController;
                $controller->displayAccountUser($errors = []);
                break;
            }

            // SOUMISSION DU FORMULAIRE DE MODIFICATION DE DONNEES DE L'UTILISATEUR
        case 'submitFormUpdateUser':
            $controller = new Controllers\CustomersController;
            $controller->submitFormUpdateUser();
            break;

            // SUPPRESSION D'UNE COMMANDE (ADMIN)
        case 'deleteOrder':
            $controller = new Controllers\OrdersController;
            $controller->deleteOrder();
            break;

            // DECONNEXION DE L'UTILISATEUR
        case 'disconnect':
            session_destroy();
            header('location: index.php?route=home');
            exit;
            break;

            /* SI LA ROUTE N'EXISTE PAS, REDIRECTION VERS L'ACCUEIL DU SITE */
        default:
            header('location: index.php?route=home');
            exit;
            break;
    }
else :
    /* SI Y'A PAS DE ROUTE REDIRIGE VERS L'ACCUEIL DU SITE */
    header('Location: index.php?route=home');
    exit;
endif;
