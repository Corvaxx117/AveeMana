<?php

namespace Models;

class Customers extends Database
{
    // RECHERCHE UN UTILISATEUR PAR SON ADRESSE EMAIL
    public function getOneCustomer(string $email)
    {
        $req = "SELECT * FROM users WHERE email = ?";
        return $this->findOne($req, [$email]);
    }
    // AJOUTE UN NOUVEL UTILISATEUR 
    public function addNewUser(array $data): void
    {
        $this->addOne(
            'users',
            'firstname, lastname, email, password, phone, adress, postcode, city, newsletter',
            '?,?,?,?,?,?,?,?,?',
            $data
        );
    }
    // MISE A JOUR DES DONNEES DE L'UTILISATEUR
    public function updateUser(array $data, int $id): void
    {
        $this->updateOne('users', $data, 'id', $id);
    }
}
