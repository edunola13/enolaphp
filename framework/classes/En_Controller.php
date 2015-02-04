<?php
/**
 * Clase de la que deben extender los controladores de la aplicacion para que se asegure el funcioneamiento del mismo
 * @author Enola
 */
class En_Controller extends Enola implements Controller{
    protected $request;     
    protected $view_folder;
    //errores
    public $errores;    

    function __construct(){
        parent::__construct('controller');
        $this->request= En_HttpRequest::getInstance();
        $this->view_folder= PATHAPP . 'source/view/';
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
     * Funcion lee los campos de un formulario y asigna a una variable el objeto con todos sus atributos
     */
    protected function read_fields($var_name, $class = NULL){
        $vars= array();
        if($this->request->request_method == 'POST'){
            $vars= $this->request->post_params;
        }
        else{
            $vars= $this->request->get_params;
        }
        if($class != NULL){                    
            $object= new $class();
            foreach ($vars as $key => $value) {
                if(property_exists($object, $key)){
                    $object->$key= $value;
                }
            }
            $this->$var_name= $object;
        }
        else{
            $this->$var_name= $vars;
        }
    }    
    /**
     * Funcion que valido las variables de un objeto en base a una configuracion de validacion
     */
    protected function validate($var){
        $validacion= new Validation();        
        $reglas= $this->config_validation();
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
            $this->errores= $validacion->error_messages();
            return FALSE;
        }
        else{
            return TRUE;            
        }
    }
    /**
     * Funcion que arma una configuracion para la validacion
     */
    protected function config_validation(){
        return array();
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
        execute_controller($con);
        if($filtrar){
            execute_filters($GLOBALS['filters_after_processing'], $uri);
        }
    }
}
?>