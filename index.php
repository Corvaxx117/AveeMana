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
            $controller->displayAllProducts($errors = [], $valids = []);
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
            // Vérifier si les paramètres requis sont présents
            if (isset($_GET['id']) && isset($_GET['action'])) {
                $controller->addToBasket($_GET['id'], $_GET['action']);
            } else {
                // Rediriger vers la route 'basketProducts'
                header('Location: index.php?route=basketProducts');
                exit;
            }
            break;

            // SUPPRIMER UN ARTICLE DU PANIER
        case 'removeFromBasket':
            $controller = new Controllers\BasketController;
            // Vérifier si les paramètres requis sont présents
            if (isset($_GET['id'])) {
                $controller->removeFromBasket($_GET['id']);
            } else {
                // Rediriger vers la route 'basketProducts'
                header('Location: index.php?route=basketProducts');
                exit;
            }
            break;

            // SUPPRIMER L'INTEGRALITE DU PANIER
        case 'deleteAllBasket':
            $controller = new Controllers\BasketController;
            $controller->deleteAllBasket();
            break;

            // VERIFICATION DU PANIER AVANT REDIRECTION PAGE PAIEMENT
        case 'checkOrderBeforePayment':
            $controller = new Controllers\OrdersController;
            $controller->isBasketValid();
            break;

            // PROCEDER A LA COMMANDE
        case 'proceedOrder':
            $controller = new Controllers\OrdersController;
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
            $controller = new Controllers\MediasController;
            $controller->displayAllMedias($errors = [], $valids = []);
            break;

            // SOUMISSION DU FORMULAIRE D'AJOUT DE MEDIA
        case 'submitFormAddMedia':
            $controller = new Controllers\MediasController;
            $controller->submitFormAddMedia();
            break;

        case 'deleteMedia':
            $controller = new Controllers\MediasController;
            $controller->deleteMedia();
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
            -------------------------------------------------- PAGE ESPACE UTILISATEUR / RECAP COMMANDES
             *******************************************************************************************/

            // AFFICHAGE DU COMPTE DE L'UTILISATEUR  
        case 'userAccount':
            $controller = new Controllers\OrdersController;
            $controller->displayAccount($valids = [], $errors = []);
            break;

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


            /*******************************************************************************************
            -------------------------------------------------- PAGE CONTACT
             *******************************************************************************************/

            // AFFICHAGE PAGE CONTACT
        case 'contact':
            $controller = new Controllers\ContactController();
            $controller->displayFormContact($errors = [], $valids = []);
            break;
            // SOUMISSION DU FORMULAIRE 
        case 'submitFormContact':
            $controller = new Controllers\ContactController();
            $controller->submitFormContact();
            break;


            /* SI LA ROUTE N'EXISTE PAS, REDIRECTION VERS L'ACCUEIL DU SITE */
        default:
            header('location: index.php?route=home');
            exit;
            break;
    }
else :
    /* S'IL N'Y A PAS DE ROUTE REDIRIGE VERS L'ACCUEIL DU SITE */
    header('Location: index.php?route=home');
    exit;
endif;
