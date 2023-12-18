<?php

namespace Models;

require('config/config.php');

abstract class Database
{
    protected $bdd;

    public function __construct()
    {
        // PARAMETRE DE CONNEXION A LA BDD
        $this->bdd = new \PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8",
            DB_USER,
            DB_PASS,
            [
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC, // retourne un tableau indexé par le nom de la colonne
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION // lance PDOExeptions
            ]
        );
    }
    // RECUPERATION DE TOUS LES ELEMENTS D'UNE TABLE
    protected function findAll(string $req, array $params = []): array
    {
        $query = $this->bdd->prepare($req);
        $query->execute($params);
        return $query->fetchAll();
    }
    // RECUPERATION D'UN ELEMENT 
    protected function findOne($req, $params = [])
    {
        $query = $this->bdd->prepare($req);
        $query->execute($params);
        return $query->fetch();
    }
    // AJOUT D'UN ELEMENT
    protected function addOne(string $table, string $columns, string $values, $data)
    {
        $query = $this->bdd->prepare('INSERT INTO ' . $table . '(' . $columns . ') values (' . $values . ')');
        $query->execute($data);
        return $this->bdd->lastInsertId();
    }
    // MISE A JOUR D'UN ELEMENT
    protected function updateOne(string $table, array $newData, string $condition, int|string $val)
    {
        // On initialise les sets comme étant une chaine de caractères vide
        $sets = '';
        // On boucle sur les data à mettre à jour pour préparer le data binding
        foreach ($newData as $key => $value) {
            // On concatène le nom des colonnes et le paramètre du data binding:  clé = :clé,
            $sets .= $key . ' = :' . $key . ',';
        }
        // On supprime le dernier caractère, donc la derniere virgule
        $sets = substr($sets, 0, -1);
        // On indique la requete SQL
        $sql = "UPDATE " . $table . " SET " . $sets . " WHERE " . $condition . " = :" . $condition;
        // On prépare la requete SQL
        $query = $this->bdd->prepare($sql);
        // Pour chaque valeur de la recette, on lie la valeur de la clé à chaque :clé
        foreach ($newData as $key => $value) {
            $query->bindValue(':' . $key, $value);
        }
        // On lie la valeur (par ex, l'id) de la condition à  :condition
        $query->bindValue(':' . $condition, $val);
        // On execute la requete
        $query->execute();
        // On indique au serveur que notre requete est terminée
        $query->closeCursor();
    }
    // SUPPRESSION D'UN ELEMENT
    protected function deleteOne(string $table, string $columns, string $condition, $val)
    {
        // Vérifier si la table est liée à d'autres tables pour la suppression en cascade
        if ($table == 'orders') {
            // Supprimer d'abord les enregistrements associés dans la table "orderDetails"
            $this->deleteOrderDetails($val);
        }
        // Exécution de la suppression de l'élément dans la table spécifiée
        $query = $this->bdd->prepare("DELETE FROM " . $table . " WHERE " . $columns . "." . $condition . " = :val");
        $query->bindValue(':val', $val);
        $query->execute();
        $query->closeCursor();
    }
    // SUPPRESSION DES ENREGISTREMENTS ASSOCIÉS DANS LA TABLE ORDERDETAILS
    protected function deleteOrderDetails($orderId)
    {
        $query = $this->bdd->prepare("DELETE FROM ordersDetails WHERE orders_id = :orderId");
        $query->bindValue(':orderId', $orderId);
        $query->execute();
        $query->closeCursor();
    }
}
