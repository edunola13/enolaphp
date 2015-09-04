<?php
namespace Enola\Cron;
use Enola\Support\Security;

/**
 * Esta clase representa una solicitud Cron y por lo tanto provee todas las propiedades basicas de una peticion Cron como
 * asi tambien propiedades de peticion propias del framework (como es cleanParams, etc).
 * Ademas provee comportamiento basico para leer parametros. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cron
 */
class En_CronRequest {
    private static $instance;
    protected $params;
    protected $allParams;    
    public $attributes;
 
    /**
     * Crea la instancia del request en base a la configuracion pasada
     * @param type $config
     */
    public function __construct($config) {
        $this->params= $config['clean'];
        $this->allParams= $config['real'];
        self::$instance= $this;
    }    
    /**
     * Devuelve la isntancia que se esta utilizando
     */
    public static function getInstance(){
        return self::$instance;
    }
    
    /**
     * Retorna todos los parameters de la linea de comandos que no fueron utilizados por el framework para la toma
     * de deciciones.
     * @return array[string]
     */
    public function getParams(){
        return $this->params;
    }
    /**
     * Retorna todos los parameters de la linea de comandos
     * @return array[string]
     */
    public function getAllParams(){
        return $this->allParams;
    }
    /**
     * Devuelve un parametro en base a un indice - solo parametros no utilizados por el framework
     * @param string $index
     * @return null o string
     */
    public function getParam($index){
        if(isset($this->params[$index])){
            return $this->params[$index];
        }else{
            return NULL;
        }
    }    
    /**
     * Devuelve un parametro limpiado en base a un indice - solo parametros no utilizados por el framework
     * @param string $index
     * @return null o string
     */
    public function getParamClean($index){
        if(isset($this->params[$index])){
            return $this->params[$index];
        }else{
            return NULL;
        }
    }
    /**
     * Devuelve un parametro en base a un indice - incluye todos los parametros
     * @param string $index
     * @return null o string
     */
    public function getParamAll($index){
        if(isset($this->allParams[$index])){            
            return Security::clean_vars($this->allParams[$index]);
        }
        else{
            return NULL;
        }
    }    
    /**
     * Devuelve un parametro limpiado en base a un indice - incluye todos los parametros
     * @param string $index
     * @return null o string
     */
    public function getParamAllClean($index){
        if(isset($this->allParams[$index])){
            return Security::clean_vars($this->allParams[$index]);
        }
        else{
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
        }
        else{
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