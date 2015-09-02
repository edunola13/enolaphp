<?php
use Enola\Http;

/**
 * Description of Index
 *
 * @author Enola
 */

class Index extends Http\En_Controller{
    public function __construct() {
        parent::__construct();
    }
    
    public function doGet(){
        $this->loadView("index", NULL);
    }

}