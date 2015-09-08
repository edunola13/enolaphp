<?php
namespace Enola\Http;
use Enola\Support\Security;

/**
 * Esta clase representa una solicitud HTTP y por lo tanto provee todas las propiedades basicas de una peticion HTTP como
 * asi tambien propiedades de peticion propias del framework (como es baseUrlLocale, etc).
 * Ademas provee comportamiento basico para leer parametros. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
class En_HttpRequest {
    private static $instance;
    public $attributes;
    //Propias de la peticion HTTP
    public $getParams;
    public $postParams;    
    public $session;
    public $requestMethod;
    public $queryString;
    public $requestUri;
    public $httpHost;    
    public $httpAccept;
    public $httpAcceptLanguage;
    public $httpUserAgent;
    //Propias del Framework
    public $realBaseUrl;
    public $baseUrlLocale;
    public $uriApp;
    public $uriAppLocale;    
    public $localeUri;
    public $locale;
    
    /**
     * Crea la instancia del request en base a la configuracion pasada
     * @param type $config
     */
    public function __construct($config){
        $this->init($config);
        self::$instance= $this;
    }    
    /**
     * Devuelve la isntancia que se esta utilizando
     */
    public static function getInstance(){
        return self::$instance;
    } 
    /**
     * Setea todas las propiedades de la instancia
     * GET - POST - SERVER y FRAMEWORK
     * @param type $config
     */
    private function init($config){
        //Configuro valores basicos-genericos
        $this->getParams= $_GET;
        $this->postParams= $_POST;
        $this->attributes= array();
        $this->session= new Session();
        $this->requestMethod= $_SERVER['REQUEST_METHOD'];
        $this->queryString= $_SERVER['QUERY_STRING'];
        $this->requestUri= $_SERVER['REQUEST_URI'];
        $this->httpHost= $_SERVER['HTTP_HOST'];        
        $this->httpAccept= $_SERVER['HTTP_ACCEPT'];
        $this->httpAcceptLanguage= $_SERVER['HTTP_ACCEPT_LANGUAGE'];
        $this->httpUserAgent= $_SERVER['HTTP_USER_AGENT'];
        //Configuro la URIAPP y defino varios valores propios de la Aplicación        
        $this->realBaseUrl= $config['REAL_BASE_URL'];
        $this->baseUrlLocale= $config['BASEURL_LOCALE'];
        $this->uriApp= $config['URIAPP'];
        $this->uriAppLocale= $config['URIAPP_LOCALE'];
        $this->localeUri= $config['LOCALE_URI'];
        $this->locale= $config['LOCALE'];
    }
       
    /**
     * Devuelve un parametro GET si existe y si no devuelve NULL
     * @param string $name
     * @return null o string
     */
    public function getParam($name){
        if(isset($this->getParams[$name])){
            return $this->getParams[$name];
        }else{
            return NULL;
        }
    }    
    /**
     * Devuelve un parametro POST si existe y si no devuelve NULL
     * @param string $name
     * @return null o string
     */
    public function postParam($name){
        if(isset($this->postParams[$name])){
            return $this->postParams[$name];
        }else{
            return NULL;
        }
    }
    /**
     * Devuelve un parametro GET limpiado si existe y si no devuelve NULL
     * @param string $name
     * @return null o string
     */
    public function getCleanParam($name){
        if(isset($this->getParams[$name])){            
            return Security::clean_vars($this->getParams[$name]);
        }else{
            return NULL;
        }
    }    
    /**
     * Devuelve un parametro POST limpiado si existe y si no devuelve NULL
     * @param string $name
     * @return null o string
     */
    public function postCleanParam($name){
        if(isset($this->postParams[$name])){
            return Security::clean_vars($this->postParams[$name]);
        }else{
            return NULL;
        }
    }
    /**
     * Devuelve un atributo, si existe y si no devuelve NULL
     * @param string $key
     * @return null o string
     */
    public function getAttribute($key){
        if(isset($this->attributes[$key])){            
            return $this->attributes[$key];
        }else{
            return NULL;
        }
    }
    /**
     * Setea un atributo al requerimiento
     * @param type $key
     * @param type $value
     */
    public function setAttribute($key, $value){
        $this->attributes[$key]= $value;
    }
}