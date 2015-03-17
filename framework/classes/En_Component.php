<?php
/**
 * @author Enola
 */
class En_Component extends Enola implements Component{ 
    protected $viewFolder;
    protected $session;
    
    public function __construct() {        
        parent::__construct('component');
        if(ENOLA_MODE == 'HTTP')$this->session= new Session();
        $this->viewFolder= PATHAPP . 'source/view/';
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
    protected function loadView($view, $params = NULL, $returnData = FALSE){
        if($params != NULL && is_array($params)){
            foreach ($params as $key => $value) {
                $$key= $value;
            }
        }
        if($returnData){
            ob_start();            
        }
        include $this->viewFolder . $view . '.php';
        if($returnData){
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
    }
}
?>
