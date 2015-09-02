<?php
/**
 * Esta clase es la encargada de administrar toda la configuracion global de la aplicacion de una manera central y sencilla.
 * Esta va a ser instanciada una unica vez.
 *
 * @author Enola
 */
class EnolaContext {
    private static $instance;
    //Referencia al Nucleo
    public $app;
    //Path basicos
    private $pathRoot;
    private $pathFra;
    private $pathApp;
    //URLs base
    private $baseUrl;
    private $indexPage;
    private $componentUrl;
    //Variables simples
    private $error;
    private $calculatePerformance;
    private $environment;
    private $charset;
    private $configurationFolder;
    private $databaseConfiguration;
    private $composerAutoload;
    //Definiciones de diferentes aspectos/partes
    private $librariesDefinition;
    private $loadLibraries;
    private $controllersDefinition;
    private $filtersBeforeDefinition;
    private $filtersAfterDefinition;
    private $componentsDefinition;
    //I18n
    private $i18nDefaultLocale;
    private $i18nLocales;
    
    /**
     * Crea una instancia de la clase, llama al metodo init y guarda la instancia
     * @param string $path_root
     * @param string $path_framework
     * @param string $path_application
     */
    public function __construct($path_root, $path_framework, $path_application) {
        $this->init($path_root, $path_framework, $path_application);
        //Guardo la instancia para qienes quieran consultar desde cualqueir ubicacion
        self::$instance= $this;
    }
    
    /**
     * Devuelve la instancia creada automaticamente por el framework en su carga
     * @return EnolaContext
     */
    public static function getInstance(){
        return self::$instance;
    }
    /**
     * Setea la instancia de la clase
     * @param EnolaContext $instance
     */
    public static function setInstance($instance){
        self::$instance= $instance;
    }
    
    /**
     * Carga la configuracion global de la aplicacion 
     * @param string $path_root
     * @param string $path_framework
     * @param string $path_application
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
        //PATH_ROOT: direccion base donde se encuentra la aplicacion completa, es el directorio donde se encuentra el archivo index.php        
        $this->pathRoot= $path_root;
        //PATHFRA: direccion de la carpeta del framework - definida en index.php
        $this->pathFra= $path_framework; 
        //PATHAPP: direccion de la carpeta de la aplicacion - definida en index.php
        $this->pathApp= $path_application;
        
        // BASE_URL: Base url de la aplicacion - definida por el usuario en el archivo de configuracion    
        $pos= strlen($config['base_url']) - 1;
        if($config['base_url'][$pos] != '/'){
            $config['base_url'] .= '/';
        }
        $this->baseUrl= $config['base_url'];
        //INDEX_PAGE: Pagina inicial. En blanco si se utiliza mod_rewrite
        $this->indexPage= $config['index_page']; 
        //URL_COMPONENT: URL con la cual se deben mapear los componentes via URL
        $this->componentUrl= trim($config['url-components'], '/');
        
        //CALCULATE_PERFORMANCE: Indica si el framework debe calcular el tiempo de respuesta o no
        $this->calculatePerformance= $config['calculate_performance'];
        //ENVIRONMENT: Indica el ambiente de la aplicacion
        $this->environment= $config['environment'];
        //CHARSET: Indica el charset que se esta utilizando en PHP
        $this->charset= $config['charset'];
        //CONFIGURATION: Carpeta base de configuracion - definida por el usuario en el archivo de configuracion
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
        
        //Internacionalizacion: En caso que se defina se setea el locale por defecto y todos los locales soportados
        if(isset($config['i18n'])){
            $this->i18nDefaultLocale= $config['i18n']['default'];
            if(isset($config['i18n']['locales'])){
                $locales= str_replace(" ", "", $config['i18n']['locales']);
                $this->i18nLocales= explode(",", $locales);
            }
        }
        
        //Diferentes definiciones
        $this->librariesDefinition= $config['libraries'];
        $this->controllersDefinition= $config['controllers'];
        $this->filtersBeforeDefinition= $config['filters'];
        $this->filtersAfterDefinition= $config['filters_after_processing'];
        $this->componentsDefinition= $config['components'];
    }
    
    /*
     * Getters
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
    /**
     * Retorna si se debe o no calcular el tiempo de respuesta
     * @return boolean
     */
    public function CalculatePerformance(){
        return ($this->calculatePerformance == 'TRUE' || $this->calculatePerformance == 'true');
    }
    /**
     * Retorna si se definicio configuracion de base de datos
     * @return boolean
     */
    public function isDatabaseDefined(){
        if($this->databaseConfiguration != NULL){
            return TRUE;
        }
        return FALSE;
    }
    /**
     * Retorna si se definio e archivo autoload de composer
     * @return boolean
     */
    public function isAutoloadDefined(){
        if($this->composerAutoload != NULL){
            return TRUE;
        }
        return FALSE;
    }
    /**
     * Retorna si se definieron los locales soportados
     * @return boolean
     */
    public function isLocalesDefined(){
        if($this->i18nLocales != NULL){
            return TRUE;
        }
        return FALSE;
    }
    /**
     * Setea las librerias que se definieron para que se carguen automaticamente en diferentes componentes
     * @param type $libraries
     */
    public function setLoadLibraries($libraries){
        $this->loadLibraries= $libraries;
    }
}