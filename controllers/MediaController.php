<?php

namespace Controllers;

class MediaController extends BaseController
{
    // AFFICHER TOUS LES MEDIAS DANS LA GALLERIE 
    public function displayAllMedias()
    {
        $errors = [];
        $valids = [];
        $medias = $this->mediaModel->getAllMedias();
        $mediasByType = $this->groupMediasByType($medias);
        $token = $this->randomString->getRandomString(15);
        $_SESSION['shield'] = $token;

        $this->render('gallery', 'layout', [
            'errors' => $errors,
            'valids' => $valids,
            'medias_audio' => $mediasByType['audio'],
            'medias_image' => $mediasByType['image'],
            'medias_video' => $mediasByType['video'],
            'token' => $token
        ]);
    }
    // SOUMISSION DU FORMULAIRE D'AJOUT D'UN MEDIA 
    public function submitFormAddMedia()
    {
        $errors = [];
        $valids = [];

        $formData = $_FILES;
        if (
            array_key_exists('name', $_POST) &&
            array_key_exists('media_path', $formData) &&
            array_key_exists('shield', $_POST)
        ) {
            $errors = $this->validateFormData($formData);
            $medias = $this->mediaModel->getAllMedias();

            $mediasByType = $this->groupMediasByType($medias);

            $data = [
                'errors' => $errors,
                'valids' => $valids,
                'formData' => $formData,
                'medias_audio' => $mediasByType['audio'],
                'medias_image' => $mediasByType['image'],
                'medias_video' => $mediasByType['video'],
                'token' => $_SESSION['shield']
            ];

            if (!empty($errors)) {
                $this->redirect('index.php?route=gallery', $errors);
            } else {
            // Vérification de la taille du fichier
                if ($_FILES['media_path']['error'] == UPLOAD_ERR_INI_SIZE || $_FILES['media_path']['error'] == UPLOAD_ERR_FORM_SIZE) {
                    $errors[] = 'Le fichier est trop volumineux. La taille maximale autorisée est de 2mo.';
                    $this->redirect('index.php?route=gallery', $errors);
                } else {
                    $newFileName = $this->uploadMedia($formData);
                    if (!$newFileName) {
                        $errors[] = 'Une erreur est survenue lors de l\'envoi du fichier.';
                        $this->redirect('index.php?route=gallery', $errors);
                    } else {
                        $mediaData = $this->prepareMediaData($formData, $newFileName);
                        $result = $this->mediaModel->addNewMedia($mediaData);
    
                        if (!$result) {
                            $errors[] = 'Une erreur est survenue lors de l\'enregistrement du nouveau média.';
                            $this->redirect('index.php?route=gallery', $errors);
                        } else {
                            $valids[] = 'Votre demande d\'ajout de média a bien été enregistrée.';
                            $_SESSION['valids'] = $valids;
                            $this->redirect('index.php?route=gallery');
                        }
                    }
                }
            }
        } else {
            $errors[] = 'Fichier non autorisé.';
            $_SESSION['errors'] = $errors;
            $this->redirect('index.php?route=gallery');
        }
    }
    // TRI DES MEDIA PAR TYPE
    public function groupMediasByType($medias)
    {
        $mediasByType = [
            'audio' => [],
            'image' => [],
            'video' => []
        ];
    
        foreach ($medias as $media) {
            switch ($media['media_type']) {
                case 'audio':
                    $mediasByType['audio'][] = $media;
                    break;
                case 'image':
                    $mediasByType['image'][] = $media;
                    break;
                case 'video':
                    $mediasByType['video'][] = $media;
                    break;
                default:
                    // Si le type de média n'est pas audio, image ou vidéo, on l'ajoute aux images
                    $mediasByType['image'][] = $media;
                    break;
            }
        }
    
        return $mediasByType;
    }
    // REDIRECTION / AFFICHAGE 
    public function render($viewName, $layoutName, $data = [])
    {
        foreach ($data as $key => $value) {
            $$key = $value;
        }
        $template = $viewName . '.phtml';
        include_once 'views/' . $layoutName . '.phtml';
    }
    // VERIFICATION ET VALIDATION DES DONNEES DU FORMULAIRE
    public function validateFormData($formData)
    {
        $errors = [];
        // VERIFICATION DU NOM 
        if (strlen($_POST['name']) < 3 || strlen($_POST['name']) > 150) {
            $errors[] = 'Le nom doit être compris entre 3 et 150 caractères.';
        }
        // SHIELD -> TOKEN
        if ($_POST['shield'] != $_SESSION['shield']) {
            $errors[] = 'Un problème de sécurité est survenu lors de la soumission du formulaire.';
        }
        if (!isset($formData['media_path']) || $formData['media_path']['error'] !== UPLOAD_ERR_OK) {
            $errors[] = 'Une erreur est survenue lors de l\'envoi du fichier.';
        }
        if (empty($formData['media_path'])) {
            $errors[] = 'Le fichier est requis.';
        }
        return $errors = [];
    }
    // UPLOAD DU FICHIER DANS DOSSIER ADEQUAT
    public function uploadMedia($formData)
    {
        $errors = [];
        // Récupération du type de fichier pour le ranger dans le dossier adequat
        $getFileType =  explode('/', $formData['media_path']['type']);
        $fileType = $getFileType[0];
        
        $newFileName = $this->uploadsModel->uploadingFiles(
            $formData['media_path'],
            'gallery_' . $fileType,
            $errors,
            1000000000,
            ["jpeg", "jpg", "JPG", "png", "heic", "gif", "mp3", "wave", "qt", "mov", "mpeg", "avi"]
        );
        if (!empty($newFileName)) {
            return $newFileName;
        } else {
            $errors[] = 'Une erreur est survenue lors de l\'envoi du fichier.';
            $medias = $this->mediaModel->getAllMedias();
            $mediasByType = $this->groupMediasByType($medias);
            $data = [
                'errors' => $errors,
                'formData' => $formData,
                'medias_audio' => $mediasByType['audio'],
                'medias_image' => $mediasByType['image'],
                'medias_video' => $mediasByType['video'],
                'token' => $_SESSION['shield']
            ];
            $this->render('gallery', 'layout', $data);
            exit;
        }
    }
    // PREPARATION DU MEDIA AVANT ENREGISTREMENT BDD
    public function prepareMediaData($formData, $newFileName)
    {

        $fileTypeFull =  explode('/', $formData['media_path']['type']);
        $fileType = strtolower($fileTypeFull[0]);

        $mediaData = [
            trim(ucfirst($_POST['name'])),
            trim($newFileName),
            $fileType
        ];
        return $mediaData;
    }
    // REDIRECTION AVEC TRANSMISSION DES ALERTES
    public function redirect($url, $errors = [], $valids = [])
    {
        if (!empty($errors)) {
            $_SESSION['errors'] = $errors;
        }
        if (!empty($valids)) {
            $_SESSION['valids'] = $valids;
        }
        header("Location: $url");
        exit;
    }
}
