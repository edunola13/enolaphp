<?php
/**
 * Description of EnolaContext
 *
 * @author Enola
 */
class EnolaContext {
    private static $instance;
    public $app;
    
    private $pathRoot;
    private $pathFra;
    private $pathApp;
    
    private $baseUrl;
    private $indexPage;
    private $componentUrl;
    
    private $error;
    private $calculatePerformance;
    private $environment;
    private $charset;
    private $configurationFolder;
    private $databaseConfiguration;
    private $composerAutoload;
    
    private $librariesDefinition;
    private $loadLibraries;
    private $controllersDefinition;
    private $filtersBeforeDefinition;
    private $filtersAfterDefinition;
    private $componentsDefinition;
    
    private $i18nDefaultLocale;
    private $i18nLocales;
    
    public function __construct($path_root, $path_framework, $path_application) {
        $this->init($path_root, $path_framework, $path_application);
        //Guardo la instancia para qienes quieran consultar desde cualqueir ubicacion
        self::$instance= $this;
    }
    
    public static function getInstance(){
        return self::$instance;
    }
    
    public static function setInstance($instance){
        self::$instance= $instance;
    }
    
    /**
     * Realizar la carga del configuration.json
     */
    private function init($path_root, $path_framework, $path_application){
        /*
         * Lee archivo configuracion.json donde se encuentra toda la configuracion de variables, filtros, controladores, 
         * librerias, helpers, etc.
         */
        $json_configuration= file_get_contents($path_application . 'configuration.json');
        $config= json_decode($json_configuration, true);    
        if(! is_array($config)){
            //Arma una respuesta de error de configuracion.
            //No realiza el llamado a funciones de error porque todavia no se cargo el modulo de errores
            $head= 'Configuration Error';
            $message= 'The file configuration.json is not available or is misspelled';
            require $path_application . 'errors/general_error.php';
            //Cierra la aplicacion
            exit;
        }    
        //Define si muestra o no los errores y en que nivel de detalle dependiendo en que fase se encuentre la aplicacion
        switch ($config['environment']){
            case 'development':
                error_reporting(E_ALL);
                $this->error= 'ALL';
                break;
            case 'production':
                error_reporting(0);
                $this->error= 'NONE';
                break;
            default:
                //No realiza el llamado a funcion de error porque todavia no se cargo el modulo de errores
                $head= 'Configuration Erron';
                $message= 'The environment is not defined in configuration.json';
                require $path_application . 'errors/general_error.php';
                exit;
        }
        //ROOT_PATH: direccion base donde se encuentra la aplicacion completa, es el directorio donde se encuentra el archivo index.php        
        $this->pathRoot= $path_root;
        //PATHFRA: direccion de la carpeta del framework - definida en index.php
        $this->pathFra= $path_framework; 
        //PATHAPP: direccion de la carpeta de la aplicacion - definida en index.php
        $this->pathApp= $path_application;
        
        // Define las constantes del sistema
        // BASE_URL: Base url de la aplicacion - definida por el usuario en el archivo de configuracion    
        $pos= strlen($config['base_url']) - 1;
        if($config['base_url'][$pos] != '/'){
            $config['base_url'] .= '/';
        }
        $this->baseUrl= $config['base_url'];
        //INDEX_PAGE: Pagina inicial. En blanco si se utiliza mod_rewrite
        $this->indexPage= $config['index_page']; 
        //URL_COMPONENT: URL con la cual se deben mapear los controladores
        $this->componentUrl= trim($config['url-components'], '/');
        
        //CALCULATe_PERFORMANCE
        $this->calculatePerformance= $config['calculate_performance'];
        //ENVIRONMENT: Indica el ambiente de la aplicacion
        $this->environment= $config['environment'];
        //CHARSET: Indica el charset que se esta utilizando en PHP
        $this->charset= $config['charset'];
        //CONFIGURATION: carpeta base de configuracion - definida por el usuario en el archivo de configuracion
        $this->configurationFolder= $config['configuration'];  
        //JSON_CONFIG_BD: archivo de configuracion para la base de datos
        //Si el usuario definio que va a tener bd, en el archivo de configuracion guarda el archivo de configuracion de la BD
        if(isset($config['database']['configuration'])){
            $this->databaseConfiguration= $config['database']['configuration'];
        }
        //AUTOLOAD_FILE: Indica la direccion del archivo autoload de composer
        if(isset($config['composer']['autoload_file'])){
            $this->composerAutoload= $config['composer']['autoload_file'];
        }
        
        //Internacionalizacion
        if(isset($config['i18n'])){
            $this->i18nDefaultLocale= $config['i18n']['default'];
            if(isset($config['i18n']['locales'])){
                $locales= str_replace(" ", "", $config['i18n']['locales']);
                $this->i18nLocales= explode(",", $locales);
            }
        }
        
        //Especificaciones
        $this->librariesDefinition= $config['libraries'];
        $this->controllersDefinition= $config['controllers'];
        $this->filtersBeforeDefinition= $config['filters'];
        $this->filtersAfterDefinition= $config['filters_after_processing'];
        $this->componentsDefinition= $config['components'];
    }
    
    /*
     * Getters y Setters
     */
    
    public function getPathRoot(){
        return $this->pathRoot;
    }
    public function getPathFra(){
        return $this->pathFra;
    }
    public function getPathApp(){
        return $this->pathApp;
    }
    public function getBaseUrL(){
        return $this->baseUrl;
    }
    public function getIndexPage(){
        return $this->indexPage;
    }
    public function getComponentUrl(){
        return $this->componentUrl;
    }
    public function getError(){
        return $this->error;
    }
    public function getEnvironment(){
        return $this->environment;
    }
    public function getCalculatePerformance(){
        return $this->calculatePerformance;
    }
    public function getCharset(){
        return $this->charset;
    }
    public function getConfigurationFolder(){
        return $this->configurationFolder;
    }
    public function getDatabaseConfiguration(){
        return $this->databaseConfiguration;
    }
    public function getComposerAutoload(){
        return $this->composerAutoload;
    }
    public function getLibrariesDefinition(){
        return $this->librariesDefinition;
    }
    public function getLoadLibraries(){
        return $this->loadLibraries;
    }
    public function getControllersDefinition(){
        return $this->controllersDefinition;
    }
    public function getFiltersBeforeDefinition(){
        return $this->filtersBeforeDefinition;
    }
    public function getFiltersAfterDefinition(){
        return $this->filtersAfterDefinition;
    }
    public function getComponentsDefinition(){
        return $this->componentsDefinition;
    }
    public function getI18nDefaultLocale(){
        return $this->i18nDefaultLocale;
    }
    public function getI18nLocales(){
        return $this->i18nLocales;
    }
        
    /*
     * Metodos facilitadores
     */
    public function CalculatePerformance(){
        return ($this->calculatePerformance == 'TRUE' || $this->calculatePerformance == 'true');
    }
    
    public function isDatabaseDefined(){
        if($this->databaseConfiguration != NULL){
            return TRUE;
        }
        return FALSE;
    }
    
    public function isAutoloadDefined(){
        if($this->composerAutoload != NULL){
            return TRUE;
        }
        return FALSE;
    }
    
    public function isLocalesDefined(){
        if($this->i18nLocales != NULL){
            return TRUE;
        }
        return FALSE;
    }
    
    public function setLoadLibraries($libraries){
        $this->loadLibraries= $libraries;
    }
}