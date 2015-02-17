<?php
/**
 * @author Enola
 */
class En_HttpRequest {
    private static $instancia;    
    public $get_params;
    public $post_params;
    public $session;
    public $request_method;
    
    protected function __construct($uri){
        $this->get_params= $_GET;
        $this->post_params= $_POST;
        //Aca le tengo que pasar la url del controlador que se mapeo        
        $this->session= new Session();
        $this->request_method= $_SERVER['REQUEST_METHOD'];
    }
    /**
     * Crea una unica instancia y/o devuelve la actual
     */
    public static function getInstance($uri = NULL){
        if(!self::$instancia instanceof self){
            self::$instancia = new self($uri);
        }
        return self::$instancia;
    }    
    /**
     * Devuelve un parametro GET si existe y si no devuelve NULL
     * @param string $nombre
     * @return null o string
     */
    public function get_param($nombre){
        if(isset($this->get_params[$nombre])){
            return $this->get_params[$nombre];
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
    public function post_param($nombre){
        if(isset($this->post_params[$nombre])){
            return $this->post_params[$nombre];
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
    public function get_clean_param($nombre){
        if(isset($this->get_params[$nombre])){            
            return clean_vars($this->get_params[$nombre]);
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
    public function post_clean_param($nombre){
        if(isset($this->post_params[$nombre])){
            return clean_vars($this->post_params[$nombre]);
        }
        else{
            return NULL;
        }
    }
}
?>
