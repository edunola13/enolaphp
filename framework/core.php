<?php
namespace Enola;
use Enola\Error;

//Carga el setup de la aplicacion
include $path_application . 'setup.php';
//Tiempo de Inicio de la aplicación
$timeBegin= microtime(TRUE);
require $path_framework . 'EnolaContext.php';

//Instancio la Clase EnolaContext que carga la configuracion de la aplicacion
$context= new \EnolaContext($path_root, $path_framework, $path_application, $configurationType, $configurationFolder, $charset, $timeZone, $cache);
//Una vez realizada la carga de la configuracion empieza a trabajar el core del Framework
$app= new Application($context);

//Seteo el caluclo de la performance, si corresponde
$app->initPerformance($timeBegin);

//Ejecuto el requerimiento actual
$app->request();

/**
 * Esta clase representa el Nucleo del framework. En esta se cuentra la funcionalidad principal del framework
 * En su instanciacion cargara todos los modulos de soporte, librerias definidas por el usuario y demas comportamiento
 * sin importar el tipo de requerimiento.
 * Mediante el metodo request atendera el requerimiento actual donde segun el tipo del mismo cargara los modulos principales
 * correspondientes y les cedera el control a cada uno como corresponda.
 * Permite la administracion de variables de tipo aplicacion mediante la cache. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola
 * @internal
 */
class Application{
    /** Referencia a la clase EnolaContext 
     * @var \EnolaContext */
    public $context;
    /** Instancia del Sistema de Cache de uso interno 
     * @var Cache\CacheInterface */
    public $cache;
    /** Prefijo a utilizar en el Sistema de Cache 
     * @var string */
    private $prefixApp= 'APP';
    
    /** Referencia al nucleo HTTP 
     * @var Http\HttpCore */
    public $httpCore;
    /** Referencia al nucleo Component 
     * @var Component\ComponentCore */
    public $componentCore;
    /** Referencia al nucleo Core 
     * @var Cron\CronCore */
    public $cronCore;
    /** Referencia a la clase View 
     * @var Support\View */
    public $view;
    
    /** Instancia del motor de dependencias
     * @var Support\DependenciesEngine */
    public $dependenciesEngine;
    /** Instancia de la clase Performance 
     * @var Support\Performance */
    private $performance;
    /**
     * Constructor - Ejecuta metodo init
     * @param EnolaContext $context
     */
    public function __construct($context) {
        $this->context= $context;
        $this->context->app= $this;
        $this->init();
    }
    /**
     * Destructor - Termina el calculo de performance
     */
    public function __destruct() {
        //Termino e imprimo el calculo de la performance, si corresponde
        $this->displayPerformance();
    }    
    /**
     * Responde al requerimiento analizando el tipo del mismo, HTTP,CLI,COMPONENT,ETC.
     */
    public function request(){        
        //Cargo el modulo correspondiente en base al tipo de requerimiento
        if(ENOLA_MODE == 'HTTP'){
            //Cargo el modulo Http
            $this->loadHttpModule();
        }else{
            //Cargo el modulo Cron
            $this->loadCronModule();
        }
        //Cargo el modulo Component
        $this->loadComponentModule();        
        //Luego de la carga de todos los modulos creo una instancia de Support\View
        $this->view= new Support\View();
        //Cargo la configuracion del usuario
        $this->loadUserConfig();
        //Analizo si estoy en modo HTTP o CLI
        if(ENOLA_MODE == 'HTTP'){
            //Analizo la ejecucion de componente via URL - Veo si hay componentes y si alguno mapea
            if($this->componentCore != NULL && $this->componentCore->mapsComponents($this->httpCore->httpRequest)){
                //Ejecuto el componente via URL
                $this->componentCore->executeUrlComponent($this->httpCore->httpRequest);
            }else{
                //Ejecuto el controlador correspondiente
                $this->httpCore->executeHttpRequest();
            }
        }else{
            //Ejecuta el cron controller
            $this->cronCore->executeCronController();
        }        
    }    
    /**
     * Realiza la carga de modulos, librerias y soporte que necesita el framework para su correcto funcionamiento
     * sin importar el tipo de requerimiento (HTTP, COMPONENT, CLI, Etc).
     */
    private function init(){        
        //Realizo la carga de modulos de soporte
        $this->supportModules();
        //Instancio el sistema de Cache
        $this->cache= new Cache\Cache();
        //Enolacontext->init(): Cargo las configuraciones de contexto faltante
        $this->context->init();
        //Instancio el motor de Dependencias
        $this->dependenciesEngine= new Support\DependenciesEngine();              
        //Cargo las librerias definidas por el usuario
        $this->loadLibraries();        
    }    
    /**
     * Carga de modulos de soporte para que el framework trabaje correctamente
     */ 
    protected function supportModules(){           
        //Carga del modulo errores - se definen manejadores de errores
        require $this->context->getPathFra() . 'supportModules/Errors.php';    
        //Carga de modulo para carga de archivos
        require $this->context->getPathFra() . 'supportModules/fn_load_files.php';
        //Carga de modulo con funciones para la vista
        require $this->context->getPathFra() . 'supportModules/View.php';        
        //Carga el modulo de funciones de vista exportadas al usuario de manera simple
        require $this->context->getPathFra() . 'supportModules/fn_view.php';
        //Carga de modulo de seguridad
        require $this->context->getPathFra() . 'supportModules/Security.php';
        //Carga la clase Performance
        require $this->context->getPathFra() . 'supportModules/Performance.php';
        //Carga Clase Base Loader
        require $this->context->getPathFra() . 'supportModules/genericClass/GenericLoader.php';
        //Carga Trait de funciones Comunes
        require $this->context->getPathFra() . 'supportModules/genericClass/GenericBehavior.php';
        //Carga Clase Base Requerimiento
        require $this->context->getPathFra() . 'supportModules/genericClass/Request.php';
        //Carga Clase Base Response
        require $this->context->getPathFra() . 'supportModules/genericClass/Response.php';
        //Cargo el modulo DataBase
        require $this->context->getPathFra() . 'supportModules/DataBaseAR.php';
        //Carga el modulo Cache
        require $this->context->getPathFra() . 'supportModules/Cache.php';
        //Carga el motor de Dependencias
        require $this->context->getPathFra() . 'supportModules/DependenciesEngine.php';        
    }      
    /**
     * Carga todas las librerias particulares de la aplicacion que se cargaran automaticamente indicadas en el archivo de configuracion
     */
    protected function loadLibraries(){
        //Import el archivo autload de composer si se indico el mismo
        if($this->context->isAutoloadDefined()){
            require_once $this->context->getPathRoot() . 'vendor/' . $this->context->getComposerAutoload();
        }        
        //Recorro de a una las librerias, las importo
        foreach ($this->context->getLibrariesDefinition() as $libreria) {
            //$libreria['class'] tiene la direccion completa desde LIBRARIE, no solo el nombre
            $dir= $libreria['path'];
            \E_fn\import_librarie($dir);
        }
    }
    /**
     * Carga e inicializa el modulo HTTP
     */
    protected function loadHttpModule(){
        //Analiza el paso de un error HTTP
        Error::catch_server_error();
        //Cargo el modulo HTTP e instancio el Core que se encarga de crear el HttpRequest que representa el requerimiento HTTP
        require $this->context->getPathFra() . 'http/http.php';
        $this->httpCore= new Http\HttpCore($this);
    }
    /**
     * Carga el modulo cron y ejecuta el Cron correspondiente
     * @global array $argv
     * @global array $argc
     */
    protected function loadCronModule(){
        //Consigo las variables globales para linea de comandos
        global $argv, $argc;
        //Analizo si se pasa por lo menos un parametros (nombre cron), el primer parametros es el nombre del archivo y el segundo en nombre de la clase
        //pregunta por >= 2
        if($argc >= 2){
            require $this->context->getPathFra() . 'cron/cron.php';
            $this->cronCore= new Cron\CronCore($this, $argv);            
        }else{
            Error::general_error('Cron Controller', 'There isent define any cron controller name');
        }    
    }
    /**
     * Carga el modulo Component si se definido por lo menos un component
     */
    protected function loadComponentModule(){
        //Analizo la carga del modulo component segun si hay o no definiciones
        if(count($this->context->getComponentsDefinition())){            
            //Cargo el modulo componente e instancia el Core
            require $this->context->getPathFra() . 'component/component.php';
            $this->componentCore= new Component\ComponentCore($this,$this->getRequest(),$this->getResponse());
        }
    }       
    /**
     * Despues de la carga inicial y las libreria permite que el usuario realice su propia configuracion
     * Antes de atender el requerimiento HTTP o CLI
     */
    protected function loadUserConfig(){
        require $this->context->getPathApp() . 'load_user_config.php';    
    }  
    /**
     * Si corresponde:
     * Inicializa el calculo del tiempo de respuesta
     */
    public function initPerformance($timeBegin = NULL){        
        //Analiza si calcula el tiempo que tarda la aplicacion en ejecutarse
        $this->performance= NULL;
        if($this->context->CalculatePerformance()){
            //Incluye la clase Rendimiento 
            $this->performance= new Support\Performance($timeBegin);
        }
    }    
    /**
     * Si corresponde:
     * Finaliza el calculo del tiempo de respuesta e imprime el resultado
     */
    public function displayPerformance(){
        if($this->performance != NULL){
            $this->performance->terminate();
            $mensaje= 'The execution time of the APP is: ' . $this->performance->elapsed() . ' seconds';
            $titulo= 'Performance';
            //Muestra la informacion al usuario
            Error::display_information($titulo, $mensaje);
        }
    }
    
    /**
     * Retorna el Requerimiento actual
     * @return Support\Request
     */
    public function getRequest(){
        if($this->httpCore != NULL){
            return $this->httpCore->httpRequest;
        }else{
            return $this->cronCore->cronRequest;
        }
    }
    /**
     * Retorna el Response actual
     * @return Support\Response
     */
    public function getResponse(){
        if($this->httpCore != NULL){
            return $this->httpCore->httpResponse;
        }else{
            return $this->cronCore->cronResponse;
        }
    }
    /**
     * Devuelve un atributo en cache a nivel aplicacion. Si no existe devuelve NULL.
     * @param string $key
     * @return data
     */
    public function getAttribute($key){
        return $this->cache->get($this->prefixApp . $key);
    }
    /**
     * Guarda un atributo en cache a nivel aplicacion. Por tiempo indefinido.
     * @param string $key
     * @param data $value
     */
    public function setAttribute($key, $value){
        return $this->cache->store($this->prefixApp . $key, $value);
    }
    /**
     * Elimina un atributo en cache a nivel aplicacion.
     * @param string $key
     * @return boolean
     */
    public function deleteAttribute($key){
        return $this->cache->delete($this->prefixApp . $key);
    }
}