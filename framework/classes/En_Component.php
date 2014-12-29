<?php
/**
 * @author Enola
 */
class En_Component extends Enola implements Component{ 
    protected $view_folder;
    protected $session;
    
    public function __construct() {        
        parent::__construct('component');
        $this->session= new Session();
        $this->view_folder= PATHAPP . 'source/view/';
    }    
    /**
     * Funcion que es llamada para que el componente realice su trabajo
     */
    public function rendering($params = NULL){        
    }
    
    /**
     * Carga una vista PHP
     * @param type $view 
     */
    protected function load_view($view, $params = NULL){
        include $this->view_folder . $view . '.php';
    }
}
?>
