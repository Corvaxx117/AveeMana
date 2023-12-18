<?php

namespace Models;

class Medias extends Database
{
    // RECUPERATION DE TOUS LES MEDIAS
    public function getAllMedias()
    {
        $req = "SELECT * FROM medias ORDER BY media_type ASC, created_at DESC";
        return $this->findAll($req);
    }
    // AJOUT D'UN NOUVEAU MEDIA DANS LA GALLERIE
    public function addNewMedia($data)
    {
        $this->addOne(
            'medias',
            'name, path, media_type',
            '?,?,?',
            $data
        );
        return true;
    }
}
