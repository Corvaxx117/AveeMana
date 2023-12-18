<?php

namespace Models;

class Orders extends Database
{
    // MISE À JOUR DU PRIX TOTAL D'UNE COMMANDE
    public function updateOrderTotalPrice($orderId, $totalPrice)
    {
        // Utilisez la méthode updateOne pour mettre à jour le prix total dans la table "orders"
        $this->updateOne(
            'orders',
            ['totalPrice' => $totalPrice],
            'id',
            $orderId
        );
    }
    // RECUPERATION DE TOUTES LES COMMANDES 
    public function getAllOrders()
    {
        $req = "SELECT * FROM orders ORDER BY created_at DESC";
        return $this->findAll($req);
    }
    // AJOUT D'UNE COMMANDE
    public function addOrder(array $data)
    {
        return $this->addOne(
            'orders',
            'users_id, totalPrice',
            '?,?',
            $data
        );
    }
    // AJOUT DES DETAILS DE LA COMMANDE
    public function addOrderDetails(array $allDatas)
    {
        $this->addOne(
            'ordersDetails',
            'orders_id, products_id, quantity',
            '?,?,?',
            $allDatas
        );
    }
    // REQUETE JOINTURE ORDERS / ORDER DETAILS
    public function getAllOrdersJoinDetails()
    {
        $req = "SELECT users.id, users.firstname, users.lastname, users.phone, users.postcode, users.adress, users.city, 
        orders.id, orderDate, totalPrice, statusName, products.id AS productId, quantity 
        FROM users 
        JOIN orders ON users.id = orders.users_id 
        JOIN status ON orders.status_id = status.id 
        JOIN ordersDetails ON orders.id = ordersDetails.orders_id 
        JOIN products ON ordersDetails.products_id = products.id
        ORDER BY orderDate DESC;
        ";
        return $this->findAll($req);
    }
    // REQUETE JOINTURE RECUPERATION DE LA COMMANDE UTILISATEUR 
    public function getUserOrdersJoinDetails($userId)
    {
        $req = "SELECT users.id, users.firstname, users.lastname, users.phone, users.postcode, users.adress, users.city, 
        orders.id, orderDate, totalPrice, statusName, products.id AS productId, quantity 
        FROM users 
        JOIN orders ON users.id = orders.users_id 
        JOIN status ON orders.status_id = status.id 
        JOIN ordersDetails ON orders.id = ordersDetails.orders_id 
        JOIN products ON ordersDetails.products_id = products.id
        WHERE users.id = ? AND orders.users_id = ?
        ORDER BY orderDate DESC";

        return $this->findAll($req, [$userId, $userId]);
    }
    // SUPPRESSION D'UNE COMMANDE AVEC SUPPRESSION EN CASCADE
    public function deleteOrderWithCascade($orderId)
    {
        // Supprimer d'abord les enregistrements associés dans la table "ordersDetails"
        $this->deleteOrderDetails($orderId);

        // Ensuite, supprimer la commande de la table "orders"
        $this->deleteOne(
            'orders',
            'orders',
            'id',
            $orderId
        );
    }
}
