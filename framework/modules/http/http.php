<?php
namespace Enola\Http;
use Enola\Error;

/*
 * Este modulo se encarga de cargar todas las clases necesarias para los requerimientos HTTP
 * Esto incluye los filtros , los controladores y todos los datos de los mismo.
 */
//Carga de modulo URL-URI
require 'url_uri.php';
require 'class/Session.php';
require 'class/En_HttpRequest.php';    
//Interface y Clase base de la que deben extender todos los filtros
require 'class/Filter.php';
require 'class/En_Filter.php';
//Interface y Clase base de la que deben extender todos los Controllers
require 'class/Controller.php';
require 'class/En_Controller.php';
    
class HttpCore{
    public $core;
    public $httpRequest;
    
    public function __construct($core) {
        //Defino la aplicacion URI y otros valores
        $config= UrlUri::defineApplicationUri($core->context);
        //Creo el Http request
        $this->httpRequest= new En_HttpRequest($config);
        $this->core= $core;
    }
    /*
     * Seccion de Filtros
     */
    /**
     * Analiza los filtros correspondientes y ejecuta los que correspondan
     * @param array[array] $filtros
     */
    public function executeFilters($filtros, $uriapp = NULL){
        //Analizo los filtros y los aplico en caso de que corresponda
        foreach ($filtros as $filtro_esp) {
            $filtrar= UrlUri::mapsActualUrl($filtro_esp['filtered'], $uriapp);
            //Si debe filtrar carga el filtro correspondiente y realiza el llamo al metodo filtrar()
            if($filtrar){
                $dir= $this->buildDir($filtro_esp,'filters');
                $class= $this->buildClass($filtro_esp);
                if(!class_exists($class)){
                    //Si la clase no existe intento cargarla
                    if(file_exists($dir)){
                        require_once $dir;
                    }else{
                        //Avisa que el archivo no existe
                        Error::general_error('Controller Error', 'The controller ' . $filtro_esp['class'] . ' dont exists');
                    } 
                }
                $filtro= new $class();
                //Analiza si existe el metodo filtrar
                if(method_exists($filtro, 'filter')){
                    $filtro->filter();
                }
                else{
                    Error::general_error('Filter Error', 'The filter ' . $filtro_esp['class'] . ' dont implement the method filter()');
                }
            }
        }
    }    
    /**
     * Seccion controladores
     */
    /**
     * Encuentra el controlador que mapea
     * @param type $controladores
     * @return type 
     */
    public function mappingController($controladores, $uriapp = NULL){
        $mapea= FALSE;
        //Recorre todos los controladores hasta que uno coincida con la URI actual
        foreach ($controladores as $controlador_esp) {            
            //Analiza si el controlador mapea con la uri actual
            $mapea= UrlUri::mapsActualUrl($controlador_esp['url'], $uriapp);
            if($mapea){
                return $controlador_esp;
            }
        }
        //si ningun controlador mapeo avisa el problema
        if(! $mapea){
            Error::error_404();
        }
    }
    /**
     * Ejecuta el controlador que mapeo anteriormente
     * @param type $controlador_esp 
     */
    public function executeController($controlador_esp, $uriapp = NULL){
        $dir= $this->buildDir($controlador_esp);
        $class= $this->buildClass($controlador_esp);
        if(!class_exists($class)){
            //Si la clase no existe intento cargarla
            if(file_exists($dir)){
                require_once $dir;
            }else{
                //Avisa que el archivo no existe
                Error::general_error('Controller Error', 'The controller ' . $controlador_esp['class'] . ' dont exists');
            } 
        }
        $controlador= new $class();
        //Agrego los parametros URI
        $uri_params= UrlUri::uriParams($controlador_esp['url'], $uriapp);
        $dinamic_method= $uri_params['dinamic'];
        $method= $uri_params['method'];
        $controlador->setUriParams($uri_params['params']);
        //Analizo si hay parametros en la configuracion
        if(isset($controlador_esp['params'])){
            foreach ($controlador_esp['params'] as $key => $value) {
                $controlador->$key= $value;
            }
        }       
        //Saca el metodo HTPP y en base a eso hace una llamada al metodo correspondiente
        $metodo= $_SERVER['REQUEST_METHOD'];
        if($dinamic_method){
            if(method_exists($controlador, $method)){
                $controlador->$method();
            }else{
                Error::general_error('HTTP Method Error', "The HTTP method $method is not supported");
            }
        }else{
           switch ($metodo) {
            case 'GET':
                $controlador->doGet();
                break;
            case 'POST':
                $controlador->doPost();
                break;
            case 'UPDATE':
                $controlador->doUpdate();
                break;
            case 'DELETE':
                $controlador->doDelete();
                break;
            case 'HEAD':
                $controlador->doHead();
                break;
            case 'TRACE':
                $controlador->doTrace();
                break;
            case 'URI':
                $controlador->doUri();
                break;
            case "OPTIONS":
                $controlador->doOptions();
                break;
            case 'CONNECT':
                $controlador->doConnect();
                break;
            default :                
                Error::general_error('HTTP Method Error', "The HTTP method $metodo is not supported");
            }
        }
    }
    
    function buildDir($definicion, $folder="controllers"){
        $dir= "";
        if(! isset($definicion['location'])){
            $dir= $this->core->context->getPathApp() . 'source/' . $folder . '/' . $definicion['class'] . '.php';
        }else{
            $dir= $this->core->context->getPathRoot() . $definicion['location'] . '/' . $definicion['class'] . '.php';
        }
        return $dir;
    }
    function buildClass($definicion){
        $namespace= (isset($definicion['namespace']) ? $definicion['namespace'] : '');
        //Empiezo la carga del controlador
        $dirExplode= explode("/", $definicion['class']);
        $class= $dirExplode[count($dirExplode) - 1];
        if($namespace != '') $class= "\\" . $namespace . "\\" . $class;
        return $class;
    }
}