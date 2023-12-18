<?php

namespace Models;

class Mimes
{
    public function listMimes(): array
    {
        // Liste des extensions de fichiers autorisées pour upload
        $allowed_types = array(

            // images
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',

            // audio/video
            'mp3' => 'audio/mpeg',
            'qt' => 'video/quicktime',
            'mov' => 'video/quicktime',
            'avi' => 'video/x-msvideo',
            'wave' => 'audio/x-wav'

        );
        return $allowed_types;
    }
}
