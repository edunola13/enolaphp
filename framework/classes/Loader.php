<?php
namespace Enola;

/**
 * @author Enola
 */
abstract class Loader {
    protected $type;  
    
    public function __construct($type) {
        $this->type= $type;
        $this->loadLibraries();
    }  
    /**
     * Agrega la instancia de una Clase a la instancia actual
     * @param Clase $clase
     * @param string $nombre
     */
    protected function loasClass($clase, $nombre= ""){
        add_instance($clase, $this, $nombre);
    }    
    /**
     * Metodo llamado en el constructor de la clase que carga las librerias correspondientes
     */
    protected function loadLibraries(){
        //Realiza el llamado a la funcion que se encarga de esto
        load_librarie_in_class($this, $this->type);
    }
    
    
    
    /*
     * CommonsFunction - Tal vez separar en varios
     * Esto tiene que ir en un Trait para versiones >=5.4
     * 
     */
    
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
    protected function validate($var, $lib= '\Enola\Lib\Validation'){
        $validacion= new $lib(LOCALE_URI);
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
    
    /*
     * Carga la instancia de objeto en una variable del objeto pasado como parametro
     * Supone que la clase ya se encuentra importada
     */
    function add_instance($class, $name = ""){
        if($name == ""){
            $name= $class;
        }
        $this->$name= new $class();
    }
}