<?php
namespace Enola\Component;
use Enola\Error;
use Enola\Http\En_HttpRequest;
    
/**
 * Este modulo es el encargado de todo lo referente a los componentes
 * Importa todos los modulos de soporte - clases que necesita para el correcto funcionamiento
 */
//Interface y Clase de la que deben extender todos los components
require 'class/Component.php';
require 'class/En_Component.php';
/**
 * Esta clase representa el Nucleo del modulo Component y es donde se encuentra toda la funcionalidad del mismo.
 * Este proveera metodos para saber si la URL mapea con la de los componentes y ejecutar un component via URL o
 * internamente 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Component
 * @internal
 */
class ComponentCore{
    /** Referencia al nucleo de la aplicacion 
     * @var \Enola\Application */
    public $app;
    /** Referencia al Request actual 
     * @var \Enola\Support\Request */
    public $request;
    /** Referencia al Response actual 
     * @var \Enola\Support\Response */
    public $response;
    /** 
     * 
     * @param \Enola\Application $app
     * @param \Enola\Support\Request $request
     * @param \Enola\Support\Response $response
     */
    public function __construct($app, $request, $response) {
        $this->app= $app;
        $this->request= $request;
        $this->response= $response;
    }    
    /**
     * Analiza si mapea la URI actual con la URL de componentes definida
     * @param \Enola\Http\En_HttpRequest $httpRequest
     * @return boolean 
     */
    public function mapsComponents(\Enola\Http\En_HttpRequest $httpRequest){
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
    public function executeUrlComponent(\Enola\Http\En_HttpRequest $httpRequest){        
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
                if($this->isEnabledUrl($comp) && $this->havaAuthorization($comp)){
                    $this->executeComponent($name, $params, $action);
                }else{
                    echo "The component is disabled via URL or you dont have authorization";
                }
            }else{
                echo "There isent a component with the name: " + $name;
            }
        }else{  
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
        $component= NULL;
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
            $component= new $class();
            //Analizo si hay parametros en la configuracion
            if(isset($comp['properties'])){
                $this->app->dependenciesEngine->injectProperties($component, $comp['properties']);
            } 
        }
        if($component != NULL){
            //Analiza si existe el metodo render
            if(method_exists($component, 'rendering')){
                if($action != NULL){
                    if(method_exists($component, $action)){
                        $component->$action($this->request, $this->response, $parameters);
                    }else{
                        Error::general_error('Component Error', 'The component ' . $name . ' dont implement the action ' . $action . '()');
                    }
                }
                return $component->rendering($this->request, $this->response, $parameters);
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
     * Indica si el componente indicado esta habilitado via URL
     * @param array $component
     * @return boolean
     */
    protected function isEnabledUrl($component){
        return (isset($component['enabled-url']) && ($component['enabled-url'] == 'TRUE' || $component['enabled-url'] == 'true'));
    }
    /**
     * Indica si el usuario actual tiene permiso para ejecutar el componente
     * @param array $component
     * @return boolean
     */
    protected function havaAuthorization($component){
        $auth= \Enola\Support\Authorization::getInstance();
        return $auth->userHasAccessToComponentDefinition($this->request, $component);
    }
    /**
     * Retorna el path de la carpeta donde se encuentra el component en base a su definicion
     * @param type $definition
     * @param string $folder
     * @return string
     */
    protected function buildDir($definition, $folder="components"){
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