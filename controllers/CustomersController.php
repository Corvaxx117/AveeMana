<?php

namespace Controllers;

class CustomersController extends BaseController
{
    // AFFICHE LE FORMULAIRE D'AJOUT D'UTILISATEUR
    public function displayFormAddUsers()
    {
        $token = $this->randomString->getRandomString(35);
        $_SESSION['shield'] = $token;

        // Vérifier s'il y a des données temporaires enregistrées dans la session
        $tempData = [];
        if (isset($_SESSION['temp_data'])) {
            $tempData = $_SESSION['temp_data'];
            // Supprimer les données temporaires après les avoir récupérées
            unset($_SESSION['temp_data']);
        }
        $template = "formAddUser.phtml";
        include_once 'views/layout.phtml';
    }
    // VERIFICATION DES DONNEES ENVOYEES PAR L'UTILISATEUR
    public function formUserValidator(&$errors, $excludeEmail = false)
    {
        // Si tous les champs sont rempli :
        if (
            array_key_exists('firstname', $_POST) &&
            array_key_exists('lastname', $_POST) &&
            array_key_exists('password', $_POST) &&
            array_key_exists('phone', $_POST) &&
            array_key_exists('adress', $_POST) &&
            array_key_exists('postcode', $_POST) &&
            array_key_exists('city', $_POST) &&
            array_key_exists('shield', $_POST)
        ) {
            // FIRSTNAME
            if (strlen($_POST['firstname']) < 2 || strlen($_POST['firstname']) > 25)
                $errors[] = "Le champ prénom doit contenir entre 2 et 25 caractères";
            // LASTNAME
            if (strlen($_POST['lastname']) < 2 || strlen($_POST['lastname']) > 25)
                $errors[] = "Le champ nom doit contenir entre 2 et 25 caractères";
            // EMAIL (uniquement si $excludeEmail est false)
            if (array_key_exists('email', $_POST) && !$excludeEmail && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Veuillez renseigner un email valide SVP !";
            }
            // PASSWORD
            // 8 caractères min / 1 majuscule - 1 minuscule - 1 chiffre
            if (strlen($_POST['password']) < 8 && !preg_match('/^((?:(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).*)).{8,}$/', $_POST['password']))
                $errors[] = "Le mot de passe ne respecte pas le format demandé (8 caractères min / 1 majuscule - 1 minuscule - 1 chiffre)";
            // CONFIRMATION MDP
            if ($_POST['password'] != $_POST['password_confirme'])
                $errors[] = "Vous n'avez pas confirmé correctement votre mot de passe !";
            // PHONE 
            if (!preg_match("#(^\+[0-9]{2}|^\+[0-9]{2}\(0\)|^\(\+[0-9]{2}\)\(0\)|^00[0-9]{2}|^0)([0-9]{9}$|[0-9\-\s]{10}$)#", $_POST['phone']))
                $errors[] = "Le numéro de telephone n'est pas valide";
            // ADRESS
            if (!preg_match("/^[a-zA-Z0-9\s\,\''-]+$/", $_POST['adress']))
                $errors[] = "Veuillez rentrer une adresse postale valide";
            // CODE POSTAL (INTERATIONAL)
            $code_postal = htmlspecialchars($_POST['postcode'], ENT_QUOTES, 'UTF-8');
            if (!preg_match('/^(\d{5}|\d{4}|\d{3})[- ]?([A-Za-z]{2})?$/', $code_postal))
                $errors[] = "Veuillez rentrer un code postal valide";
            // SHIELD -> TOKEN
            if ($_POST['shield'] != $_SESSION['shield']) {
                $errors[] = 'Un problème est survenu lors de la soumission du formulaire.';
            }
        }
    }
    // SOUMISSION DES DONNEES POUR AJOUT D'UTILISATEUR
    public function submitFormAddUser()
    {
        $errors = [];
        $valids = [];

        // Vérifier si le formulaire est soumis correctement
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            // Valider les données du formulaire
            $this->formUserValidator($errors);

            if (count($errors) == 0) {
                // Vérifier si l'email n'existe pas déjà dans la bdd pour éviter les doublons
                $customerExist = $this->customersModel->getOneCustomer($_POST['email']);

                if (!empty($customerExist)) {
                    $errors[] = 'Cette adresse e-mail existe déjà !';
                    // Stocker les données dans la session temporaire pour pré-remplir le formulaire
                    $_SESSION['temp_data'] = $_POST;
                } else {
                    // SI PAS D'ERREUR :
                    // Hashage du password
                    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
                    $data = [
                        trim(strtoupper($_POST['firstname'])),
                        trim(ucfirst($_POST['lastname'])),
                        trim(strtolower($_POST['email'])),
                        trim($password),
                        trim($_POST['phone']),
                        trim($_POST['adress']),
                        trim($_POST['postcode']),
                        trim($_POST['city']),
                        ($_POST['newsletter'] == 'yes') ? 1 : 0
                    ];
                    // Ajout des données du nouvel utilisateur dans la bdd
                    $resultUserMail = $this->customersModel->addNewUser($data);
                    $valids[] = 'Votre demande de création de compte a bien été enregistrée.';
                    $valids[] = 'Un E-mail vient de vous être envoyé avec vos identifiants.';
                }
            } else {
                // Si des erreurs sont présentes, stocker les données dans la session temporaire
                $_SESSION['temp_data'] = $_POST;
            }
        }

        $token = $this->randomString->getRandomString(35);
        $_SESSION['shield'] = $token;

        if (count($errors) == 0) {
            $template = "login.phtml";
            include_once 'views/layout.phtml';
        } else {
            $template = "formAddUser.phtml";
            include_once 'views/layout.phtml';
        }
    }
    // MODIFICATION DES DONNEES DE L'UTILISATEUR 
    public function submitFormUpdateUser()
    {
        $errors = [];
        $valids = [];

        // Si l'utilisateur n'est pas connecté, on le redirige vers la page de connection
        if (!isset($_SESSION['user']['id']) && empty($_SESSION['user']['id'])) {

            header('location:index.php?route=login');
            exit;
        }
        // Vérifie que tous les champs du formulaire sont remplis
        foreach ($_POST as $key => $value) {
            if (empty(trim($value))) {
                $errors[] = 'Le champ ' . $key . ' est obligatoire.';
            }
        }
        // Stocker les données dans la session temporaire pour pré-remplir le formulaire
        $_SESSION['temp_data'] = $_POST;
        // Vérifie la validité des données du formulaire
        $this->formUserValidator($errors, true);
        // Si il y a une erreur, on reaffiche la page
        if (!empty($errors)) {
            $this->displayAccountUser($errors);
            return;
        } else {
            // Hashage du nouveau password
            $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

            $newData = [
                'firstname' => trim($_POST['firstname']),
                'lastname' => trim($_POST['lastname']),
                'password' => trim($password),
                'phone' => trim($_POST['phone']),
                'adress' => trim($_POST['adress']),
                'postcode' => trim($_POST['postcode']),
                'city' => trim($_POST['city']),
                'newsletter' => ($_POST['newsletter'] == 'yes') ? 1 : 0
            ];

            $userId = $_SESSION['user']['id'];
            // Ajout des nouvelles données de l'utilisateur dans la bdd
            $this->customersModel->updateUser($newData, $userId);

            // soit : Update de $_SESSION ou alors on detruit la session et on redirige vers login

            $_SESSION['user']['firstname'] = trim($_POST['firstname']);
            $_SESSION['user']['lastname'] = trim($_POST['lastname']);
            $_SESSION['user']['password'] = trim($_POST['password']);
            $_SESSION['user']['phone'] = trim($_POST['phone']);
            $_SESSION['user']['adress'] = trim($_POST['adress']);
            $_SESSION['user']['postcode'] = trim($_POST['postcode']);
            $_SESSION['user']['city'] = trim($_POST['city']);
            $_SESSION['user']['newsletter'] = ($_POST['newsletter'] == 'yes') ? 1 : 0;


            $token = $this->randomString->getRandomString(35);
            $_SESSION['shield'] = $token;

            $this->displayAccountUser($errors);
            // ou alors ... 
            // session_destroy();
            // header('location:index.php?route=login');
            // exit;
        }
    }
}
