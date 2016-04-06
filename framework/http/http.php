<?php
namespace Enola\Http;
use Enola\Error;

/*
 * Este modulo es el encargado de todo lo referente a las solicitudes HTTP
 * Importa todos los modulos de soporte - clases que necesita para el correcto funcionamiento
 */
//Carga de modulo URL-URI
require 'url_uri.php';
//Carga de clases Basicas
require 'class/Session.php';
require 'class/En_HttpRequest.php';   
require 'class/En_HttpResponse.php';
//Interface y Clase base de la que deben extender todos los filtros
require 'class/Filter.php';
require 'class/En_Filter.php';
//Interface y Clase base de la que deben extender todos los Controllers
require 'class/Controller.php';
require 'class/En_Controller.php';
/**
 * Esta clase representa el Nucleo del modulo HTTP y es donde se encuentra toda la funcionalidad del mismo
 * En su instanciacion definira la URI actual y el HttpRequest.
 * Luego proveera metodos para saber que controlador mapea segun determinada URI y ejecutar un controlador aplicando o no
 * los filtros correspondientes. Luego estas delegaran trabajo a los diferentes metodos privados.
 * Esta clase tiene una dependencia de la clase UrlUri para resolver cuestiones de URLs y URIs * 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 * @internal
 */
class HttpCore{
    /** Referencia al nucleo de la aplicacion 
     * @var \Enola\Application */
    public $app;
    /** Referencia al HttpRequest actual 
     * @var En_HttpRequest */
    public $httpRequest;
    /** Referencia al HttpResponse actual 
     * @var En_HttpResponse */
    public $httpResponse;
    /**
     * Se instancia el nucleo.
     * Se define todo lo respectivo a la URI y se define el Http Request actual
     * @param Application $app
     */
    public function __construct($app) {
        //Defino la aplicacion URI y otros valores
        $config= UrlUri::defineApplicationUri($app->context);
        //Creo el Http request
        $this->httpRequest= new En_HttpRequest($config);
        $this->httpResponse= new En_HttpResponse($this->httpRequest);
        $this->app= $app;
    }
    /**
     * Retorna la especificacion del controlador que mapea con la URI actual
     * Levanta error 404 si ningun controlador mapeame voy a comer 
     * @param string $uriapp
     * @return array 
     */
    public function mappingController($uriapp = NULL){
        $controllers= $this->app->context->getControllersDefinition();
        $maps= FALSE;
        //Recorre todos los controladores hasta que uno coincida con la URI actual
        foreach ($controllers as $controller_esp) {            
            //Analiza si el controlador mapea con la uri actual
            $maps= UrlUri::mapsActualUrl($controller_esp['url'], $uriapp);
            if($maps){
                return $controller_esp;
            }
        }
        //si ningun controlador mapeo avisa el problema
        if(! $maps){
            Error::error_404();
        }
    }
    /**
     * Ejecuta la especificacion de controlador pasada como parametro en base a una URI ejecutando o no filtros. En caso de 
     * que no se le pase el controlador lo consigue en base a la URI y en caso de que no se pase la URI especifica se usa 
     * la de la peticion actual.  
     * @param array $actualController
     * @param string $uriapp
     * @param boolean $filter
     */
    public function executeHttpRequest($actualController = NULL, $uriapp = NULL, $filter = TRUE){
        //Si no se paso controlador, se busca el correspondiente
        if($actualController == NULL){
            $actualController= $this->mappingController($uriapp);
        }
        //Ejecuto los filtros pre-procesamiento
        if($filter){
            $this->executeFilters($this->app->context->getFiltersBeforeDefinition());
        }
        //Ejecuto el controlador
        $this->executeController($actualController, $uriapp);
        //Ejecuto los filtros post-procesamiento
        if($filter){
            $this->executeFilters($this->app->context->getFiltersAfterDefinition());
        }
    }
    /**
     * Analiza los filtros que mapean con la URI pasada y ejecuta los que correspondan. En caso de no pasar URI se utiliza
     * la de la peticion actual.
     * @param array[array] $filters
     * @param string $uriapp
     */
    protected function executeFilters($filters, $uriapp = NULL){
        //Analizo los filtros y los aplico en caso de que corresponda
        foreach ($filters as $filter_esp) {
            $filter= UrlUri::mapsActualUrl($filter_esp['filtered'], $uriapp);
            //Si debe filtrar carga el filtro correspondiente y realiza el llamo al metodo filtrar()
            if($filter){
                $dir= $this->buildDir($filter_esp,'filters');
                $class= $this->buildClass($filter_esp);
                if(!class_exists($class)){
                    //Si la clase no existe intento cargarla
                    if(file_exists($dir)){
                        require_once $dir;
                    }else{
                        //Avisa que el archivo no existe
                        Error::general_error('Controller Error', 'The controller ' . $filter_esp['class'] . ' dont exists');
                    } 
                }
                $filterIns= new $class();
                //Analizo si hay parametros en la configuracion
                if(isset($filter_esp['properties'])){
                    $this->app->dependenciesEngine->injectProperties($filterIns, $filter_esp['properties']);
                }
                //Analiza si existe el metodo filtrar
                if(method_exists($filterIns, 'filter')){
                    $filterIns->filter($this->httpRequest, $this->httpResponse);
                }
                else{
                    Error::general_error('Filter Error', 'The filter ' . $filter_esp['class'] . ' dont implement the method filter()');
                }
            }
        }
    }
    /**
     * Ejecuta el controlador que mapeo anteriormente. Segun su definicion en la configuracion se ejecutara al estilo REST
     * o mediante nombre de funciones
     * @param array $controller_esp 
     * @param string $uriapp
     */
    protected function executeController($controller_esp, $uriapp = NULL){
        $dir= $this->buildDir($controller_esp);
        $class= $this->buildClass($controller_esp);
        if(!class_exists($class)){
            //Si la clase no existe intento cargarla
            if(file_exists($dir)){
                require_once $dir;
            }else{
                //Avisa que el archivo no existe
                Error::general_error('Controller Error', 'The controller ' . $controller_esp['class'] . ' dont exists');
            } 
        }
        $controller= new $class();
        //Agrego los parametros URI
        $uri_params= UrlUri::uriParams($controller_esp['url'], $uriapp);
        $dinamic_method= $uri_params['dinamic'];
        $method= $uri_params['method'];
        $controller->setUriParams($uri_params['params']);
        //Analizo si hay parametros en la configuracion
        if(isset($controller_esp['properties'])){
            $this->app->dependenciesEngine->injectProperties($controller, $controller_esp['properties']);
        }       
        //Saca el metodo HTPP y en base a eso hace una llamada al metodo correspondiente
        $methodHttp= $_SERVER['REQUEST_METHOD'];
        if($dinamic_method){
            if(method_exists($controller, $methodHttp . '_' . $method)){
                $method= $methodHttp . '_' . $method;
            }
        }else{
            $method= "do" . ucfirst(strtolower($methodHttp));
        }
        if(method_exists($controller, $method)){
            $controller->$method($this->httpRequest, $this->httpResponse);
        }else{
            Error::general_error('HTTP Method Error', "The HTTP method $method is not supported");
        }
    }    
    /**
     * Retorna el path de la carpeta donde se encuentra el controlador/filtro en base a su definicion
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
     * Retorna el nombre de la clase completo (con namespace) del controlador/filtro en base a su definicion
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