<?php

namespace Models;

class Uploads extends Mimes
{
    // VERIFICATION ET UPLOAD D'UN FICHIER 
    public function uploadingFiles(
        array $filetoverify,
        string $path,
        array &$errors,
        // valeurs par defaut, écrasées selon l'envoi de paramètres 
        int $maxSize = 800000,
        array $ext_authorized = ["jpeg", "jpg", "png", "heic", "gif", "webp"]
    ) {
        $filename = '';
        // Liste des extensions de fichiers autorisées
        $allowedTypes = $this->listMimes();

        // VERIFICATIONS DU FICHIER

        // Création des variables relatives au fichier uploadé :     
        extract($filetoverify);  // $name, $full_path, $type, $tmp_name, $error, $size
        // Verification de la taille
        if ($size <= $maxSize) {
            // Récupération de l'extension
            $getExt = explode('.', $name);
            $file_ext = strtolower(end($getExt));
            // Si l'extension du fichier est autorisée
            if (in_array($file_ext, $ext_authorized)) {
                // Comparaison avec la liste des Mimes
                if ($allowedTypes[$file_ext] === mime_content_type($tmp_name)) {
                    // Utilisation de uniqid pour éviter les doublons dans le dossier
                    $filename = uniqid() . '.' . $file_ext; // génère un nom de fichier unique en ajoutant l'extension
                    if (!move_uploaded_file($tmp_name, "libraries/assets/" . $path . '/' . $filename)) {
                        $errors[] = 'Un problème est survenu lors du téléchargement du fichier.';
                    }
                } else {
                    $errors[] = 'Un problème est survenu lors du téléchargement du fichier.';
                }
            } else {
                $errors[] = 'Extension non autorisé.';
            }
        } else {
            $errors[] = 'Le fichier est trop volumineux';
        }
        return $filename;
    }
}
