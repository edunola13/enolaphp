<?php
/**
 * Clase de la que deben extender los controladores cron de la aplicacion para que se asegure el funcioneamiento del mismo
 * @author Enola
 */
class En_CronController extends Enola{
    protected $params;
    protected $clean_params;
    protected $view_folder;
    //errores
    public $errores;    

    function __construct(){
        parent::__construct('cron');
        $this->view_folder= PATHAPP . 'source/view/';
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
    protected function load_data(){        
    }    
    /**
     * Funcion que carga los datos usados por la vista de 
     */
    protected function load_data_error(){        
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
