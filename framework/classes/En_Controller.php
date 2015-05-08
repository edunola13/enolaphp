<?php
/**
 * Clase de la que deben extender los controladores de la aplicacion para que se asegure el funcioneamiento del mismo
 * @author Enola
 */
class En_Controller extends Enola implements Controller{
    protected $request;
    protected $uriParams;
    protected $viewFolder;
    //errors
    public $errors;    

    function __construct(){
        parent::__construct('controller');
        $this->request= En_HttpRequest::getInstance();
        $this->viewFolder= PATHAPP . 'source/view/';
    }    
    /**
     * Funcion que es llamada cuando el metodo HTTP es GET
     */
    public function doGet(){        
    }    
    /**
     * Funcion que es llamada cuando el metodo HTTP es POST
     */
    public function doPost(){        
    }    
    /**
     * Funcion que es llamada cuando el metodo HTTP es DELETE
     */
    public function doDelete(){        
    }    
    /**
     * Funcion que es llamada cuando el metodo HTTP es PUT
     */
    public function doPut(){        
    }
    /**
     * Funcion que es llamada cuando el metodo HTTP es HEAD
     */
    public function doHead(){        
    }
    /**
     * Funcion que es llamada cuando el metodo HTTP es TRACE
     */
    public function doTrace(){        
    }
    /**
     * Funcion que es llamada cuando el metodo HTTP es OPTIONS
     */
    public function doOptions(){        
    }
    /**
     * Funcion que es llamada cuando el metodo HTTP es CONNECT
     */
    public function doConnect(){        
    }
    
    /**
     * Setea los uri params
     * @param type $uri_params
     */
    public function setUriParams($uri_params){
        $this->uriParams= $uri_params;
    }
    /**
     * Funcion lee los campos de un formulario y asigna a una variable el objeto con todos sus atributos o un array asociativo
     */
    protected function readFields(&$var, $class = NULL){
        $vars= array();
        if($this->request->requestMethod == 'POST'){
            $vars= $this->request->postParams;
        }
        else{
            $vars= $this->request->getParams;
        }
        if($class != NULL){                    
            $object= new $class();
            foreach ($vars as $key => $value) {
                if(property_exists($object, $key)){
                    $object->$key= $value;
                }
            }
            $var= $object;
        }
        else{
            $var= $vars;
        }
        return $var;
    }    
    /**
     * Funcion que valida las variables de un objeto o de un array en base a una configuracion de validacion
     */
    protected function validate($var){
        $validacion= new Validation(LOCALE_URI);        
        $reglas= $this->configValidation();
        if(is_object($var)){
            foreach ($reglas as $key => $regla) {
                $validacion->add_rule($key, $var->$key, $regla);
            }
        }
        else{
            foreach ($reglas as $key => $regla) {
                $validacion->add_rule($key, $var[$key], $regla);
            }
        }
        if(! $validacion->validate()){
            //Consigo los errores y retorno FALSE
            $this->errors= $validacion->error_messages();
            return FALSE;
        }
        else{
            return TRUE;            
        }
    }
    /**
     * Funcion que arma una configuracion para la validacion
     */
    protected function configValidation(){
        return array();
    }
    /**
     * Funcion que actua cuando acurre un error en el controlador
     */
    protected function error(){        
    }    
    /**
     * Funcion que carga los datos usados por la vista
     */
    protected function loadData(){        
    }    
    /**
     * Funcion que carga los datos usados por la vista de error
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
    
    /**
     * Redireccion interna a otro Controlador.
     * Se indica si se debe filtrar o no la nueva solicitud
     * @param type $uri
     * @param Bool $filtrar= FALSE. Indica si filtra
     */
    protected function fordward($uri, $filtrar = FALSE){
        if($filtrar){
            execute_filters($GLOBALS['filters'], $uri);
        }
        $con= mapping_controller($GLOBALS['controllers'], $uri);
        execute_controller($con, $uri);
        if($filtrar){
            execute_filters($GLOBALS['filters_after_processing'], $uri);
        }
    }
}