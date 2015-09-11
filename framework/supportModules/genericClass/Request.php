<?php
namespace Enola\Support;
/**
 * Esta clase representa el comportamiento basico de un requerimiento a la aplicacion.
 * La clase es abstracta ya que debe ser extendida por el modulo correspondiente para que preste el servicio adecuado 
 * para el modulo.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
abstract class Request {
    protected static $instance;
    public $attributes;
    /**
     * Devuelve la isntancia que se esta utilizando
     */
    public static function getInstance(){
        if(self::$instance == NULL){
            self::$instance= new En_HttpResponse();
        }
        return self::$instance;
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