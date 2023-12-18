<?php

namespace Models;

class Events extends Database
{
    // RECUPERATION DE TOUS LES EVENEMENTS
    public function getAllEvents(): array
    {
        $req = "SELECT * FROM events ORDER BY eventDate DESC";
        return $this->findAll($req);
    }
    // CREATION D'UN EVENEMENT
    public function addNewEvent(array $data)
    {
        $this->addOne(
            'events',
            'eventDate, title, city, description, cover',
            '?,?,?,?,?',
            $data
        );
    }
    // RECUPERATION D'UN EVENEMENT PAR SON ID
    public function getEventById($eventId)
    {
        $req = "SELECT * FROM events WHERE id = ?";
        return $this->findOne($req, [$eventId]);
    }
    // SUPPRESSION D'UN EVENEMENT
    public function deleteEventById($eventId)
    {
        $this->deleteOne(
            'events', 
            'events',
            'id', 
            $eventId
        );
    }
}
