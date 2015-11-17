<?php
namespace Enola\Support;
    
/**
 * Esta clase realiza acciones de seguridad basicas el comportamiento que provee es utilizada tanto por el framework 
 * como para el usuario.
 * Como no se necesita tener ningun estado para el comportamiento todos los metodos estan disponibles estaticamente.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class Security{    
    /**
     * Codificar datos en md5
     * @param string $value
     * @return string
     */
    public static function encode_md5($value){
        return md5($value);
    }
    /**
     * Codificar datos en sha1
     * @param string $value
     * @return string
     */
    public static function encode_sha_1($value){
        return sha1($value);
    }     
    /**
     * Codificar datos en md5 y sha1
     * @param string $value
     * @return string
     */
    public static function encode_md5_y_sha_1($value){
        $value= md5($value);
        return sha1($value);
    }     
    /**
     * Simple filtro que saca las '' y "" para que no se pueda realizar xss
     * @param string $value
     * @return string
     */
    public static function filter_simple_xss($value){
        $value= str_replace('"','',$value);
        return str_replace("'","",$value);
    }     
    /**
     * Realiza la limpieza de un string o conjunto de string llamando a la funcion filtro_xss
     * @param string o array[string] $value
     * @return string o array[string]
     */
    public static function clean_vars($value){
        if(is_array($value)){
            foreach($value as $key => $val) {
                $value[$key] = self::clean_vars($val);
            }
        }
        else{
            $value= self::filter_simple_xss($value);
        }
        return $value;
    }
}