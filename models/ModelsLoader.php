<?php

namespace Models;

class ModelsLoader
{
    public function __construct()
    {
        require_once 'models/Customers.php';
        require_once 'models/Events.php';
        require_once 'models/Medias.php';
        require_once 'models/Orders.php';
        require_once 'models/Products.php';
        require_once 'models/RandomString.php';
        require_once 'models/Uploads.php';
    }
}
