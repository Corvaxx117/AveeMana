<?php

namespace Models;

class Products extends Database
{
    // RECUPERATION DE TOUS LES PRODUITS
    public function getAllProducts(): array
    {
        $req = "SELECT * FROM products ORDER BY created_at DESC";
        return $this->findAll($req);
        // $this->findAll("SELECT * FROM articles ORDER BY art_date_sortie DESC LIMIT 50");
    }
    // AJOUT D'UN NOUVEAU PRODUIT
    public function addNewProduct(array $data): void
    {
        $this->addOne(
            'products',
            'name, description, price, quantity_in_stock, picture_path',
            '?,?,?,?,?',
            $data
        );
    }
    // RECUPERATION D'UN PRODUIT
    public function getOneProductById($id)
    {
        $req = "SELECT * FROM products WHERE id = :id";
        $params = ['id' => $id];
        return $this->findOne($req, $params);
    }
    // MISE A JOUR DE LA QUANTITE EN STOCK 
    public function updateProductQuantity($newStock, $productId)
    {
        $this->updateOne('products', ['quantity_in_stock' => $newStock], 'id', $productId);
    }
    // MODIFICATION DU STATUT D'UN PRODUIT
    public function changeStatusProduct($id, $status)
    {
        $this->updateOne('products', ['activate' => $status], 'id', $id);
    }
}
