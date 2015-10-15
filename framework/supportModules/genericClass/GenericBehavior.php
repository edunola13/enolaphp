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
     * @param type $buffer
     * @return type
     */
    protected function loadView($view_template, $params = NULL, $buffer = FALSE){
        if($params != NULL && is_array($params)){
            foreach ($params as $key => $value) {
                $$key= $value;
            }
        }
        //Creo var view
        $view= new View();
        if($buffer){
            ob_start();            
        }
        include $this->viewFolder . $view_template . '.php';
        if($buffer){
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
    }
    /**
     * Realiza un llamado al componente indicado con las configuracion especificada
     * @param string $name
     * @param array $params
     * @param string $action
     * @param bool $buffer
     * @return type
     */
    protected  function component($name, $params = NULL, $action = NULL, $buffer = FALSE){
        if($buffer){
            ob_start();            
        }
        //Llama a la funcion que ejecuta el componente definido en el modulo Componente
        $this->context->app->componentCore->executeComponent($name, $params, $action);
        if($buffer){
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
    protected function addInstance($class, $name = ""){
        if($name == ""){
            $name= $class;
        }
        $this->$name= new $class();
    }
    /**
     * Inyecta las dependencias que tienen seteado el tipo en load_in
     * @param \Enola\Application $app
     * @param string $type
     */
    protected function injectDependencyOfType(\Enola\Application $app, $type){
        $app->dependenciesEngine->injectDependencyOfType($this,$type);
    }
    /**
     * Carga las dependencias indicadas en la instancia actual en las propiedades correspondientes
     * @param \Enola\Application $app
     * @param array $dependencies / property => dependency
     */
    protected function injectDependencies(\Enola\Application $app, array $dependencies){
        $app->dependenciesEngine->injectDependencies($this,$dependencies);
    }
    /**
     * Carga la dependencias indicada en la instancia actual en la propiedad indicada
     * @param \Enola\Application $app
     * @param string $dependencyName
     */
    protected function injectDependency(\Enola\Application $app, $propertyName, $dependencyName){
        $app->dependenciesEngine->injectDependency($this,$propertyName,$dependencyName);
    }
}
