<?php
namespace Enola\Http;
use Enola\Security;

/**
 * @author Enola
 */
class En_HttpRequest {
    private static $instance;    
    public $getParams;
    public $postParams;
    public $attributes;
    public $session;
    public $requestMethod;
    public $queryString;
    public $requestUri;
    public $httpHost;    
    public $httpAccept;
    public $httpAcceptLanguage;
    public $httpUserAgent;
    
    public $realBaseUrl;
    public $baseUrlLocale;
    public $uriApp;
    public $uriAppLocale;    
    public $localeUri;
    public $locale;
    
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
     * @param string $nombre
     * @return null o string
     */
    public function getParam($nombre){
        if(isset($this->getParams[$nombre])){
            return $this->getParams[$nombre];
        }
        else{
            return NULL;
        }
    }    
    /**
     * Devuelve un parametro POST si existe y si no devuelve NULL
     * @param string $nombre
     * @return null o string
     */
    public function postParam($nombre){
        if(isset($this->postParams[$nombre])){
            return $this->postParams[$nombre];
        }
        else{
            return NULL;
        }
    }
    /**
     * Devuelve un parametro GET limpiado si existe y si no devuelve NULL
     * @param string $nombre
     * @return null o string
     */
    public function getCleanParam($nombre){
        if(isset($this->getParams[$nombre])){            
            return Security::clean_vars($this->getParams[$nombre]);
        }
        else{
            return NULL;
        }
    }    
    /**
     * Devuelve un parametro POST limpiado si existe y si no devuelve NULL
     * @param string $nombre
     * @return null o string
     */
    public function postCleanParam($nombre){
        if(isset($this->postParams[$nombre])){
            return Security::clean_vars($this->postParams[$nombre]);
        }
        else{
            return NULL;
        }
    }
    /**
     * Devuelve un atributo, si existe y si no devuelve NULL
     * @param string $nombre
     * @return null o string
     */
    public function getAttributes($nombre){
        if(isset($this->attributes[$nombre])){            
            return $this->attributes[$nombre];
        }
        else{
            return NULL;
        }
    }
    /**
     * Redireccionar a otra pagina pasando una uri relativa a la aplicacion
     * @param string $uri
     */
    public function redirect($uri){
        UrlUri::redirect($this, $uri);
    }
    /**
     * Redirecciona a una pagina externa a la aplicacion actual
     * @param string $url
     */
    public function external_redirect($url){
        UrlUri::externalRedirect($url);
    }
}