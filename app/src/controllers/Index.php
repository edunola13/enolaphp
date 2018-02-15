<?php
namespace YourApp\Controllers;
use Enola\Http\Models;
use Enola\Http\Models\En_HttpRequest,Enola\Http\Models\En_HttpResponse;

class Index extends Models\En_Controller{
    public function __construct() {        
        parent::__construct();
    }
    
    public function doGet(En_HttpRequest $request, En_HttpResponse $response){
        $this->loadView("index", NULL);
    }
}