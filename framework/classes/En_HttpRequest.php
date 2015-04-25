<?php
/**
 * @author Enola
 */
class En_HttpRequest {
    private static $instancia;    
    public $getParams;
    public $postParams;
    public $attributes;
    public $session;
    public $requestMethod;
    
    protected function __construct(){
        $this->getParams= $_GET;
        $this->postParams= $_POST;
        $this->attributes= array();
        $this->session= new Session();
        $this->requestMethod= $_SERVER['REQUEST_METHOD'];
    }
    /**
     * Crea una unica instancia y/o devuelve la actual
     */
    public static function getInstance($uri = NULL){
        if(!self::$instancia instanceof self){
            self::$instancia = new self();
        }
        return self::$instancia;
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
            return clean_vars($this->getParams[$nombre]);
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
            return clean_vars($this->postParams[$nombre]);
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
}
?>