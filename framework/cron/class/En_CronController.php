<?php
namespace Enola\Cron;
use Enola\Support;

/**
 * Clase de la que deben extender los controladores cron de la aplicacion para que se asegure el funcioneamiento del mismo
 * @author Enola
 */
class En_CronController extends Support\GenericLoader{
    use Support\GenericBehavior;
    
    protected $params;
    protected $cleanParams;
    protected $viewFolder;
    //errores
    public $errors;    

    function __construct(){
        parent::__construct('cron');
        $this->viewFolder= $this->context->getPathApp() . 'source/view/';
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
        $this->cleanParams= $params;
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
}