<?php
namespace Enola;
use Enola\Error;

/*
 * Empieza a trabajar el core del Framework
 * Futuro: Instanciar una solo vez la clase, luego sacarla de cache
 */
$core= new Core($path_root, $path_framework, $path_application);
//Ejecuto el requerimiento
$core->request();
    
class Core{
    //Global    
    public $context;
    //Request
    public $httpCore;
    public $componentCore;
    public $cronCore;
    //Controller
    public $actualController;
    //Performance
    public $performance;
    
    public function __construct($path_root, $path_framework, $path_application) {
        $this->init($path_root, $path_framework, $path_application);
    }
    
    /**
     * Realiza la carga inicial
     * Futuro: La idea es que esta clase solo ejecute el metodo init una vez y lueg la saque de cache
     */
    protected function init($path_root, $path_framework, $path_application){
        //Creo la configuracion
        $this->createContext($path_root, $path_framework, $path_application);
    }
    
    /**
     * Responde al requerimiento sin importar el tipo del mismo, HTTP,CLI,COMPONENT,ETC.
     */
    public function request(){
        //Inicializo el caluclo de la performance, si corresponde
        $this->initPerformance();
        
        //Realizo la carga de modulos requeridos
        $this->requiredModules();
        //Cargo las librerias
        $this->loadLibraries();
        //Cargo el modulo Component
        $this->loadComponentModule();
        if(ENOLA_MODE == 'HTTP'){
            //Cargo el modulo Http
            $this->loadInitialiceHttpModule();
        }
        //Cargo la configuracion del usuario
        $this->loadUserConfig();
        //Analizo si estoy en modo HTTP o CLI
        if(ENOLA_MODE == 'HTTP'){
            //Analizo la ejecucion de componente via URL
            if($this->componentCore != NULL){
                $this->executeComponentByUrl();
            }
            //Filters before controller
            $this->filter($this->context->getFiltersBeforeDefinition());
            //Consigo el primer controlador que mapea y lo ejecuto
            $this->actualController();
            $this->httpCore->executeController($this->actualController);
            //Filters after controller
            $this->filter($this->context->getFiltersAfterDefinition());
        }else{
            //Cargo el modulo cron y ejecuto el cron correspondiente
            $this->executeCron();
        }
        
        //Termino e imprimo el calculo de la performance, si corresponde
        $this->finishPerformance();
    }

    /**
     * Lee la configuracion
     */
    protected function createContext($path_root, $path_framework, $path_application){
        require 'modules/commonFunctionality/class/EnolaContext.php';
        $this->context= new \EnolaContext($path_root, $path_framework, $path_application);
        //Seteo la codificacion de caracteres, casi siempre es o debe ser UTF-8
        ini_set('default_charset', $this->context->getCharset());
        //Seteo Default Zoine si no esta seteada
        if(! ini_get('date.timezone')){
            date_default_timezone_set('GMT');
        }
        /*
         * Algunas constantes - La idea es ir sacandolas
         */
        // BASE_URL: Base url de la aplicacion - definida por el usuario en el archivo de configuracion 
        define('BASEURL', $this->context->getBaseUrl());
        //PATHFRA: direccion de la carpeta del framework - definida en index.php
        define('PATHFRA', $this->context->getPathFra());    
        //PATHAPP: direccion de la carpeta de la aplicacion - definida en index.php
        define('PATHAPP', $this->context->getPathApp());
        //ENOLA_MODE: Indica si la aplicacion se esta ejecutando via HTTP o CLI
        if(PHP_SAPI == 'cli' || !isset($_SERVER['REQUEST_METHOD'])){
            define('ENOLA_MODE', 'CLI');
        }else{
            define('ENOLA_MODE', 'HTTP');
        }
    }
    
    /**
     * Carga de modulos obligatorios para que el framework trabaje correctamente
     */ 
    protected function requiredModules(){           
        //Carga del modulo errores - se definen manejadores de errores
        require $this->context->getPathFra() . 'modules/commonFunctionality/errors.php';    
        //Carga de modulo para carga de archivos
        require $this->context->getPathFra() . 'modules/commonFunctionality/load_files.php';
        //Carga de modulo con funciones para la vista
        require $this->context->getPathFra() . 'modules/commonFunctionality/view.php';
        //Carga de modulo de seguridad
        require $this->context->getPathFra() . 'modules/commonFunctionality/security.php';
        //Carga de modulo URL-URI
        require $this->context->getPathFra() . 'modules/http/url_uri.php';
        //Carga Clase Base Loader
        require $this->context->getPathFra() . 'modules/commonFunctionality/class/GenericLoader.php';
        //Carga Trait de funciones Comunes
        require $this->context->getPathFra() . 'modules/commonFunctionality/class/GenericBehavior.php'; 
        //Carga Clase En_DataBase - Si se definio configuracion para la misma
        if($this->context->isDatabaseDefined())require $this->context->getPathFra() . 'modules/commonFunctionality/class/En_DataBase.php';
    }
      
    /*
     * Cargo todas las librerias particulares de la aplicacion que se cargaran automaticamente indicadas en el archivo de configuracion
     */
    protected function loadLibraries(){
        //Import el archivo autload de composer si se indico el mismo
        if($this->context->isAutoloadDefined()){
            require_once $this->context->getPathRoot() . 'vendor/' . $this->context->getComposerAutoload();
        }
        //Creo la variable global con la configuracion de librerias
        $load_libraries= array();
        //Recorro de a una las librerias y las importo
        foreach ($this->context->getLibrariesDefinition() as $name => $libreria) {
            //$libreria['class'] tiene la direccion completa desde LIBRARIE, no solo el nombre
            $dir= $libreria['class'];
            if(isset($libreria['load_in']))$load_libraries[$name]= $libreria;
            import_librarie($dir);
        }
        //Creo la variable global con las librerias que son cargables
        $this->context->setLoadLibraries($load_libraries);
    }
    
    protected function loadComponentModule(){
        //Analizo la carga del modulo component segun si hay o no definiciones
        if(count($this->context->getComponentsDefinition())){            
            //Cargo el modulo componente e instancia el Core
            require $this->context->getPathFra() . 'modules/component/component.php';
            $this->componentCore= new Component\ComponentCore();
        }
    }
    
    protected function loadInitialiceHttpModule(){
        //Analiza el paso de un error HTTP
        Error::catch_server_error();
        //Cargo el modulo HTTP e instancio el Core que se encarga de crear el HttpRequest que representa el requerimiento HTTP
        require $this->context->getPathFra() . 'modules/http/http.php';
        $this->httpCore= new Http\HttpCore($this);
    }
    
    
    /**
     * Configuracion Inicial: Despues de la carga inicial y las libreria permite que el usuario realice su propia configuracion
     * Antes de atender el requerimiento HTTP o CLI
     */
    protected function loadUserConfig(){
        require $this->context->getPathApp() . 'load_user_config.php';    
    }
    
    protected function executeComponentByUrl(){
        //Analiza si se ejecuta un componente via URL
        if(ENOLA_MODE == 'HTTP' && $this->componentCore->mapsComponents($this->httpCore->httpRequest)){
            $this->componentCore->executeUrlComponent($this->httpCore->httpRequest);
            //Termina la ejecucion
            exit;
        }
    }
    
    protected function actualController(){
        /*
         * Lee los controladores. En caso de que no haya controladores avisa del error
         * Me quedo con el controlador que mapea
         */
        $this->actualController= NULL;
        if(count($this->context->getControllersDefinition()) > 0){
            $this->actualController= $this->httpCore->mappingController($this->context->getControllersDefinition());
        }
        else{
            general_error('Controller Error', 'There isent define any controller');
        }
    }
    
    protected function filter($filters){
        /*
         * Lee los filtros que se deben ejecutar antes del procesamiento y delega trabajo a archivo filtros.php
         * En caso de que no haya filtros asignados no delega ningun trabajo
         */
        if(count($filters) > 0){
            $this->httpCore->executeFilters($filters);
        }
    }
    
    protected function executeCron(){
        //Analizo si se pasa por lo menos un parametros (nombre cron), el primer parametros es el nombre del archivo y el segundo en nombre de la clase
        //pregunta por >= 2
        if($argc >= 2){
            require $this->context->getPathFra() . 'modules/cron/cron.php';
            $this->cronCore->executeCronController($argv);
        }else{
            general_error('Cron Controller', 'There isent define any cron controller name');
        }    
    }

    protected function initPerformance(){
        //Carga la clase Rendimiento
        require $this->context->getPathFra() . 'modules/commonFunctionality/class/Performance.php';
        //Analiza si calcula el tiempo que tarda la aplicacion en ejecutarse
        $this->performance= NULL;
        if($this->context->CalculatePerformance()){
            //Incluye la clase Rendimiento 
            $this->performance= new Common\Performance();
            $this->performance->start();
        }
    }
    
    protected function finishPerformance(){
        /*
         * Si se esta calculando el tiempo, realiza el calculo y envia la respuesta
         */
        if($this->performance != NULL){
            $this->performance->terminate();
            $mensaje= 'The execution time of the APP is: ' . $this->performance->elapsed();
            $titulo= 'Performance';
            //Muestra la informacion al usuario
            Error::display_information($titulo, $mensaje);
        }
    }
}