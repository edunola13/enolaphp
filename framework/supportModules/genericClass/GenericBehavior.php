<?php
namespace Enola\Support;

/**
 * Esta trait contiene comportamiento comun que es utilizado por los diferentes controladores de los diferentes modulos 
 * como el controller http, el component o el controller cron. Ademas se puede utilizar en la clase que el usuario desee
 * si necesita el comportamiento aca definido.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
trait GenericBehavior {
    /**
     * Lee los campos de un formulario y devuelve un objeto o un array con todos los valores correspondientes
     * si se devuelve un objeto los nombres de los campos deben coincidir con el de la clase.
     * @param type $var
     * @param type $class
     * @return type
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
     * Valida las variables de un objeto o de un array en base a una definicion de configuracion de validacion
     * Se puede utilizar la libreria que se desee pere debe respetar la inerfaz de la proporcionada por el framework.
     * @param type $var
     * @param type $lib
     * @param type $locale
     * @return boolean
     */
    protected function validate($var, $lib= '\Enola\Lib\Validation', $locale = NULL){
        $validacion= new $lib($locale);
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
     * Devuelve la configuracion de validacion
     * Deberia ser sobrescrita por la clase que desee validar, si no, no validara nada.
     */
    protected function configValidation(){
        return array();
    }
    /**
     * Carga una vista PHP pasandole parametros y teniendo la oportunidad de guardar de retornar la vista para guardar 
     * en una variable.
     * Se crea una instancia de la clase Enola\Support\View en la variable $view
     * @param type $view_template
     * @param type $params
     * @param type $returnData
     * @return type
     */
    protected function loadView($view_template, $params = NULL, $returnData = FALSE){
        if($params != NULL && is_array($params)){
            foreach ($params as $key => $value) {
                $$key= $value;
            }
        }
        //Creo var view
        $view= new View();
        if($returnData){
            ob_start();            
        }
        include $this->viewFolder . $view_template . '.php';
        if($returnData){
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
    }
    /**
     * Carga la instancia de una clase pasada como parametro en una variable del objeto actual con el nombre indicado
     * @param type $class
     * @param type $name
     */
    protected function add_instance($class, $name = ""){
        if($name == ""){
            $name= $class;
        }
        $this->$name= new $class();
    }
    /**
     * Realiza un llamado al componente indicado con las configuracion especificada
     * @param type $name
     * @param type $params
     * @param type $action
     * @return type
     */
    protected  function component($name, $params = NULL, $action = NULL){
        //Llama a la funcion que ejecuta el componente definido en el modulo Componente
        return $this->context->app->componentCore->executeComponent($name, $params, $action);
    }
}
