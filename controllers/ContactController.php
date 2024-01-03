<?php

namespace Controllers;

// use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class ContactController extends BaseController
{
    // AFFICHAGE DU FORMULAIRE DE CONNEXION
    public function displayFormContact(array $errors, array $valids)
    {
        $token = $this->randomString->getRandomString(25);
        $_SESSION['shield'] = $token;

        $template = "contact.phtml";
        include_once 'views/layout.phtml';
    }
    // VERIFICATION DES CHAMPS ENVOYES PAR UN UTILISATEUR
    public function formContactValidator(array &$errors)
    {
        // Si tous les champs sont remplis :
        if (
            array_key_exists('name', $_POST) &&
            array_key_exists('email', $_POST) &&
            array_key_exists('message', $_POST) &&
            array_key_exists('shield', $_POST)
        ) {
            // NAME
            if (mb_strlen($_POST['name']) < 2 || mb_strlen($_POST['name']) > 25) {
                $errors[] = "Le champ nom doit contenir entre 2 et 25 caractères";
            }

            // EMAIL
            if (!filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Veuillez renseigner un email valide SVP !";
            }

            // MESSAGE 
            if (mb_strlen($_POST['message']) < 2 || mb_strlen($_POST['message']) > 500) {
                $errors[] = "Le champ message doit contenir entre 2 et 500 caractères";
            }

            // SHIELD -> TOKEN
            if ($_POST['shield'] != $_SESSION['shield']) {
                $errors[] = 'Le formulaire a rencontré une erreur de sécurité. Veuillez réessayer.';
            }
        }
    }
    // SOUMISSION DU FORMULAIRE ET ENVOI DU MESSAGE DANS LE MAIL DU GROUPE 
    public function submitFormContact()
    {
        $errors = [];
        $valids = [];

        // Vérifier si le formulaire est soumis correctement
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            // Valider les données du formulaire
            $this->formContactValidator($errors);

            // S'il n'y a pas d'erreurs, procéder à l'envoi de l'e-mail
            if (empty($errors)) {
                $name = htmlspecialchars($_POST['name']);
                $email = htmlspecialchars($_POST['email']);
                $messageContent = htmlspecialchars($_POST['message']);

                // Envoi de l'e-mail
                $to = 'julien.amiel117@gmail.com';
                $subject = 'Nouveau message depuis le formulaire de contact site Avee Mana';

                try {
                    // Créer une instance de classe PHPMailer
                    $mail = new PHPMailer();

                    // Authentification via SMTP
                    $mail->isSMTP();
                    $mail->SMTPAuth = true;

                    // Connexion
                    $mail->Host = "smtp.gmail.com";
                    $mail->Port = 465;
                    // $mail->SMTPAutoTLS = true;
                    $mail->SMTPSecure = "ssl";

                    // Vos informations d'authentification SMTP (si nécessaire)
                    $mail->Username = "julien.amiel117@gmail.com";
                    $mail->Password = "vqae kcbc imhh nxyb";

                    // Protocole de sécurité (commentez ou choisissez le bon)
                    // $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;

                    // Configuration du message
                    $mail->CharSet = 'UTF-8';
                    $mail->Encoding = 'base64';
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $messageContent;
                    $mail->AltBody = $messageContent;

                    // Ajouter l'expéditeur
                    $mail->setFrom($email, $name);

                    // Ajout des destinataires
                    $mail->addAddress($to);

                    // Envoi du message
                    $mail->send();
                    // $mail->SMTPDebug = 3;

                    $valids[] = 'Votre message a été envoyé avec succès. Nous y répondrons dans les plus brefs délais';
                } catch (Exception $e) {
                    echo "Le message n'a pas pu être transmis, une erreur est survenue." . $mail->ErrorInfo;
                }
            } else {
                $errors[] = "Le message n'a pas pu être transmis, une erreur est survenue.";
            }
        }

        // Générer un nouveau jeton CSRF
        $token = $this->randomString->getRandomString(25);
        $_SESSION['shield'] = $token;

        // Afficher le formulaire avec les erreurs ou validations
        $this->displayFormContact($errors, $valids);
    }
}
