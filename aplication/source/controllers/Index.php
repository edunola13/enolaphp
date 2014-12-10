<?php

/**
 * Description of Index
 *
 * @author Enola
 */

class Index extends En_Controller{
    public function __construct() {
        parent::__construct();
    }
    
    public function doGet(){
        $this->load_view("index");
    }
}
?>