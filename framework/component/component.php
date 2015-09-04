<?php
namespace Enola\Component;
use Enola\Error;
    
/**
 * Este modulo es el encargado de todo lo referente a las solicitudes HTTP
 * Importa todos los modulos de soporte - clases que necesita para el correcto funcionamiento
 */
//Interface y Clase de la que deben extender todos los components
require 'class/Component.php';
require 'class/En_Component.php';
/**
 * Esta clase representa el Nucleo del modulo Component y es donde se encuentra toda la funcionalidad del mismo.
 * Este proveera metodos para saber si la URL mapea con la de los componentes y ejecutar un component via URL o
 * internamente
 * que controlador mapea segun determinada URI y ejecutar un controlador aplicando o no
 * los filtros correspondientes. Luego estas delegaran trabajo a los diferentes metodos privados.
 * Esta clase tiene una dependencia de la clase UrlUri para resolver cuestiones de URLs y URIs. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Component
 * @internal
 */
class ComponentCore{
    public $app;
    /** 
     * @param Application $app
     */
    public function __construct($app) {
        $this->app= $app;
    }    
    /**
     * Analiza si mapea la URI actual con la URL de componentes definida
     * @param En_HttpRequest $httpRequest
     * @return boolean 
     */
    public function mapsComponents($httpRequest){
        $uri_parts= explode("/", $httpRequest->uriApp);
        if($uri_parts[0] == $this->app->context->getComponentUrl()){
            return TRUE; 
        }
        return FALSE;
    }    
    /**
     * Ejecuta un componente en base a una URL.
     * Una vez definido el componente a ejecutar y sus parametros delega el trabajo a executeComponent
     * @param En_HttpRequest $httpRequest
     */
    public function executeUrlComponent($httpRequest){        
        $uri_parts= explode("/", $httpRequest->uriApp);
        $name= "";
        $params= array();
        $action= NULL;
        if(count($uri_parts) > 1){
            $name= $uri_parts[1];
            unset($uri_parts[0]);
            unset($uri_parts[1]);
            //Reseteo los indices
            $uri_parts= array_values($uri_parts);            
            if(isset($uri_parts[0]) && $uri_parts[0] == 'actionComponent' && isset($uri_parts[1])){
                $action= $uri_parts[1];
                unset($uri_parts[0]);
                unset($uri_parts[1]);
                $uri_parts= array_values($uri_parts);
            }
            //Consigue los parametros
            foreach ($uri_parts as $value) {
                $params[]= $value;
            }
        }
        if($name != ""){
            $components= $this->app->context->getComponentsDefinition();
            //Evalua si el componente existe y si se encuentra habilitado via URL
            if(isset($components[$name])){
                $comp= $components[$name];
                if($comp['enabled-url'] == 'TRUE' || $comp['enabled-url'] == 'true'){
                    $this->executeComponent($name, $params, $action);
                }
                else{
                    echo "The component is disabled via URL";
                }
            }
            else{
                echo "There isent a component with the name: " + $name;
            }
        }
        else{  
            echo "Enola Components";
        }
    }    
    /**
     * Ejecuta un componente que se indique mediante su nombre. Le pasa los parametros que correspondan.
     * En base a si se le pasa la accion o no ejecuta la misma y luego el render o solo en render de componente
     * Este metodo esta abstracto a si el componente se esta ejecutando via URL o no.
     * @param string $name
     * @param array $parameters
     * @param string url
     */ 
    public function executeComponent($name, $parameters = NULL, $action = NULL){
        $components= $this->app->context->getComponentsDefinition();
        $componente= NULL;
        if(isset($components[$name])){
            $comp= $components[$name];
            $dir= $this->buildDir($comp, 'components');
            $class= $this->buildClass($comp);
            if(!class_exists($class)){
                //Si la clase no existe intento cargarla
                if(file_exists($dir)){
                    require_once $dir;
                }else{
                    //Avisa que el archivo no existe
                    Error::general_error('Component Error', 'The component ' . $comp['class'] . ' dont exists');
                } 
            }
            $componente= new $class();
        }
        if($componente != NULL){
            //Analiza si existe el metodo render
            if(method_exists($componente, 'rendering')){
                if($action != NULL){
                    if(method_exists($componente, $action)){
                        $componente->$action($parameters);
                    }else{
                        Error::general_error('Component Error', 'The component ' . $name . ' dont implement the action ' . $action . '()');
                    }
                }
                return $componente->rendering($parameters);
            }
            else{
                Error::general_error('Component Error', 'The component ' . $name . ' dont implement the method rendering()');
            }          
        }
        else{
            Error::general_error('Component Error', "The component $name dont exists");
        }
    }
    /**
     * Retorna el path de la carpeta donde se encuentra el component en base a su definicion
     * @param type $definition
     * @param type $folder
     * @return string
     */
    protected function buildDir($definition, $folder="controllers"){
        $dir= "";
        if(! isset($definition['location'])){
            $dir= $this->app->context->getPathApp() . 'source/' . $folder . '/' . $definition['class'] . '.php';
        }else{
            $dir= $this->app->context->getPathRoot() . $definition['location'] . '/' . $definition['class'] . '.php';
        }
        return $dir;
    }
    /**
     * Retorna el nombre de la clase completo (con namespace) del component en base a su definicion
     * @param array $definition
     * @return string
     */
    protected function buildClass($definition){
        $namespace= (isset($definition['namespace']) ? $definition['namespace'] : '');
        //Empiezo la carga del controlador
        $dirExplode= explode("/", $definition['class']);
        $class= $dirExplode[count($dirExplode) - 1];
        if($namespace != '') $class= "\\" . $namespace . "\\" . $class;
        return $class;
    }
}