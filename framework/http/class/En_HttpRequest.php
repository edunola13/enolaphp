<?php
namespace Enola\Http;
use Enola\Support\Request;
use Enola\Support\Security;

/**
 * Esta clase representa una solicitud HTTP y por lo tanto provee todas las propiedades basicas de una peticion HTTP como
 * asi tambien propiedades de peticion propias del framework (como es baseUrlLocale, etc).
 * Ademas provee comportamiento basico para leer parametros. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
class En_HttpRequest extends Request{
    //Propias de la peticion HTTP
    /** Parametros GET de la peticion
     * @var array */
    public $getParams;
    /** Parametros POST de la peticion
     * @var array */
    public $postParams;
    /** Referencia a la Session 
     * @var Session */
    public $session;
    /** Metodo HTTP
     * @var string */
    public $requestMethod;
    /** Query string de la peticion
     * @var string */
    public $queryString;
    /** Request URI de la peticion
     * @var string */
    public $requestUri;
    /** HTTP Host de la peticion
     * @var string */
    public $httpHost;
    /** HTTP Accept de la peticion
     * @var string */
    public $httpAccept;
    /** HTTP Accept Language
     * @var string */
    public $httpAcceptLanguage;
    /** HTTP User Agent
     * @var string */
    public $httpUserAgent;
    //Propias del Framework
    /** Base URL de la aplicacion
     * @var string */
    public $realBaseUrl;
    /** Base URL Locale de la aplicacion
     * @var string */
    public $baseUrlLocale;
    /** URI actual de la peticion
     * @var string */
    public $uriApp;
    /** URI actual de la peticion con el fragmento de i18n
     * @var string */
    public $uriAppLocale;
    /** URI Locale
     * @var string */
    public $localeUri;
    /** Locale
     * @var string */
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
     * Setea todas las propiedades de la instancia
     * GET - POST - SERVER y FRAMEWORK
     * @param array $config
     */
    private function init($config){
        //Configuro valores basicos-genericos
        $this->getParams= filter_input_array(INPUT_GET);
        $this->postParams= filter_input_array(INPUT_POST);
        $this->attributes= array();
        $this->session= new Session($config['SESSION_AUTOSTART']);
        $this->requestMethod= filter_input(INPUT_SERVER, 'REQUEST_METHOD');
        $this->queryString= filter_input(INPUT_SERVER, 'QUERY_STRING');
        $this->requestUri= filter_input(INPUT_SERVER, 'REQUEST_URI');
        $this->httpHost= filter_input(INPUT_SERVER, 'HTTP_HOST');        
        $this->httpAccept= filter_input(INPUT_SERVER, 'HTTP_ACCEPT');
        $this->httpAcceptLanguage= filter_input(INPUT_SERVER, 'HTTP_ACCEPT_LANGUAGE');
        $this->httpUserAgent= filter_input(INPUT_SERVER, 'HTTP_USER_AGENT');
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
     * Retorna todos los headers del request
     * @return string[]
     */
    public function getHeaders(){
        return getallheaders();
    }
    /**
     * Retorna un header especifico o null si no existe
     * @param string $name
     * @return string
     */
    public function getHeader($name){
        $headers= $this->getHeaders();
        if(isset($headers[$name])){
            return $headers[$name];
        }else{
            return NULL;
        }
    }
    /**
     * Lee los campos de un formulario y devuelve un objeto o un array con todos los valores correspondientes
     * si se devuelve un objeto los nombres de los campos deben coincidir con los setters y/o propiedades de la clase (publicos).
     * @param type $var
     * @param string $class
     * @return array - object
     */
    public function readFields(&$var, $class = NULL){
        $vars= array();
        if($this->requestMethod == 'POST'){
            $vars= $this->postParams;
        }
        else{
            $vars= $this->getParams;
        }
        if($class != NULL && $vars){
            $object= new $class();
            $reflection= new \Enola\Support\Reflection($object);
            foreach ($vars as $key => $value) {
                $reflection->setProperty($key, $value);
            }
            $var= $object;
        }
        else{
            $var= $vars;
        }
        return $var;
    }
}