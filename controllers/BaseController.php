<?php

namespace Controllers;

use Models\ModelsLoader;
use Models\Orders;
use Models\Events;
use Models\RandomString;
use Models\Products;
use Models\Uploads;
use Models\Customers;
use Models\Medias;

abstract class BaseController extends ModelsLoader
{
    protected $loaderModels;
    protected $ordersModel;
    protected $eventsModel;
    protected $randomString;
    protected $productsModel;
    protected $uploadsModel;
    protected $customersModel;
    protected $mediasModel;


    public function __construct()
    {
        $this->loaderModels = new ModelsLoader();
        $this->ordersModel = new Orders();
        $this->eventsModel = new Events();
        $this->randomString = new RandomString();
        $this->productsModel = new Products();
        $this->uploadsModel = new Uploads();
        $this->customersModel = new Customers();
        $this->mediasModel = new Medias();
    }
}
