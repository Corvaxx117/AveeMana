<?php

namespace Controllers;

use Exception;

class HomeController extends BaseController
{
    // AFFICHAGE DE LA PAGE D'ACCUEIL
    public function displayHome()
    {
        // token
        $token = $this->randomString->getRandomString(20);
        $_SESSION['shield'] = $token;
        // Tous les Evènements
        $events = $this->eventsModel->getAllEvents();
        $template = 'home.phtml';
        include_once 'views/layout.phtml';
    }
    // SOUMISSIONN DU FORMULAIRE D'AJOUT D'EVENEMENT (ADMIN)
    public function submitFormAddEvent()
    {
        $errors = [];
        $valids = [];

        if (
            array_key_exists('title', $_POST) &&
            array_key_exists('description', $_POST) &&
            array_key_exists('date', $_POST) &&
            array_key_exists('city', $_POST) &&
            array_key_exists('cover', $_FILES)
        ) {
            // VERIFICATION DU NOM 
            if (strlen($_POST['title']) < 3 || strlen($_POST['title']) > 150)
                $errors[] = "Le titre de l'évènement doit contenir entre 3 et 150 caractères";

            // VERIFICATION DE LA TAILLE DE LA DESCRIPTION
            if (strlen($_POST['description']) > 400)
                $errors[] = "La description de l'évènement doit contenir moins de 400 caractères";

            // VERIFICATION LA DATE 
            if (!preg_match('/^(?:(?:1[6-9]|[2-9]\d)?\d{2})-(?:0?[1-9]|1[0-2])-(?:0?[1-9]|[12]\d|3[01])$/', $_POST['date'])) {
                $errors[] = "Format de la date non valide.";
            }

            // SHIELD -> TOKEN
            if ($_POST['shield'] != $_SESSION['shield']) {
                $errors[] = 'Un problème est survenu lors de la soumission du formulaire.';
            }
            // SI PAS D'ERREUR 
            if (count($errors) == 0) {
                $newNameFile = $this->uploadsModel->uploadingfiles($_FILES['cover'], 'image', $errors);
                if (count($errors) == 0) {
                    $data = [

                        trim($_POST['date']),
                        trim(ucfirst($_POST['title'])),
                        trim($_POST['city']),
                        trim(ucfirst($_POST['description'])),
                        trim($newNameFile)

                    ];
                    $resultNewEvent = $this->eventsModel->addNewEvent($data);
                    $valids[] = 'Votre demande d\'ajout d\'évènement a bien été enregistrée.';
                }
            }
        }
        $events = $this->eventsModel->getAllEvents();
        // token
        $token = $this->randomString->getRandomString(20);
        $_SESSION['shield'] = $token;

        $template = 'home.phtml';
        include_once 'views/layout.phtml';
    }
    // SUPPRESSION D'UN EVENEMENT (ADMIN)
    public function deleteEvent()
    {
        $errors = [];
        $valids = [];

        if ($_SERVER["REQUEST_METHOD"] === "POST") {
            if (isset($_POST['eventId'])) {
                $eventId = $_POST['eventId'];

                try {
                    // Récupérer l'événement à supprimer
                    $event = $this->eventsModel->getEventById($eventId);

                    if ($event) {
                        // Récupérer le nom du fichier de l'image associée à l'événement
                        $imageName = $event['cover'];
                        // Supprimer l'image du dossier
                        if (file_exists('libraries/assets/image/' . $imageName)) {
                            unlink('libraries/assets/image/' . $imageName);
                        }
                        // Supprimer l'événement de la base de données
                        $this->eventsModel->deleteEventById($eventId);
                        $valids[] = "L'événement a été supprimé avec succès.";
                    } else {
                        $errors[] = "L'événement n'existe pas.";
                    }
                } catch (Exception $e) {
                    $errors[] = "Une erreur est survenue lors de la suppression de l'événement : " . $e->getMessage();
                }
            }
        }
        // Actualiser la liste des événements
        $events = $this->eventsModel->getAllEvents();
        // token
        $token = $this->randomString->getRandomString(20);
        $_SESSION['shield'] = $token;

        $template = 'home.phtml';
        include_once 'views/layout.phtml';
    }
}
