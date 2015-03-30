<?php
/**
 * Clase de la que deben extender los controladores cron de la aplicacion para que se asegure el funcioneamiento del mismo
 * @author Enola
 */
class En_CronController extends Enola{
    protected $params;
    protected $cleanParams;
    protected $viewFolder;
    //errores
    public $errors;    

    function __construct(){
        parent::__construct('cron');
        $this->viewFolder= PATHAPP . 'source/view/';
    }
    
    /**
     * Setea los parametros de linea de comando
     */
    public function setParams($params){
        $this->params= $params;
    }
    /**
     * Setea los parametros limpiados de linea de comando
     */
    public function setCleanParams($params){
        $this->clean_params= $params;
    }
    /**
     * Funcion que actua cuando acurre un error en la validacion
     */
    protected function error(){        
    }    
    /**
     * Funcion que carga los datos usados por la vista
     */
    protected function loadData(){        
    }    
    /**
     * Funcion que carga los datos usados por la vista de 
     */
    protected function loadDataError(){        
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
