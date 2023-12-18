<?php

namespace Controllers;

class LoginController extends BaseController
{
    // AFFICHAGE DU FORMULAIRE DE CONNEXION
    public function displayFormLogin()
    {
        $token = $this->randomString->getRandomString(25);
        $_SESSION['shield'] = $token;

        $template = "login.phtml";
        include_once 'views/layout.phtml';
    }
    // VERIFICATION DES DONNEES DE CONNEXION
    public function connectionUser()
    {
        $errors = [];
        // Verification champs rempli
        if (
            array_key_exists('email', $_POST) &&
            array_key_exists('password', $_POST) &&
            array_key_exists('shield', $_POST)
        ) {
            // EMAIL
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = 'Veuillez renseigner une adresse mail valide.';
            }
            // PASSWORD
            if (strlen($_POST['password']) < 8) {
                $errors[] = 'Votre mot de passe contient au minimum 8 caractères.';
            }
            // SHIELD -> TOKEN
            if ($_POST['shield'] != $_SESSION['shield']) {
                $errors[] = 'Un problème est survenu lors de la soumission du formulaire.';
            }
            // Si pas d'erreur 
            if (count($errors) == 0) {
                $userExist = $this->customersModel->getOneCustomer($_POST['email']);
                if ($userExist !== false) {
                    // affectation des données dans la session
                    $_SESSION['user'] = [
                        'id' => $userExist['id'],
                        'firstname' => $userExist['firstname'],
                        'lastname' => $userExist['lastname'],
                        'email' => $userExist['email'],
                        'role_id' => $userExist['role_id'],
                        'phone' => $userExist['phone'],
                        'adress' => $userExist['adress'],
                        'postcode' => $userExist['postcode'],
                        'city' => $userExist['city'],
                        'newsletter' => $userExist['newsletter']
                    ];
                    header('Location: index.php?route=home');
                    exit;
                } else {
                    $errors[] = "Erreur d'identification.";
                }
            } else {
                $errors[] = "Un problème d'authentification est survenu";
            }
        }
        $token = $this->randomString->getRandomString(25);
        $_SESSION['shield'] = $token;

        $template = "login.phtml";
        include_once 'views/layout.phtml';
    }
}
