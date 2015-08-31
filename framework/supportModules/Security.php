<?php
    namespace Enola\Common;
    
    /**
     * Este modulo realiza acciones de seguridad
     * Contiene funciones que son utilizadas por el framework
     * Con tiene funciones tambien que sirven para el usuario
     */
    class Security{    
        /**
         * Funcion para codificar datos en md5
         * @param string $value
         * @return string
        */
        public static function encode_md5($value){
            return md5($value);
        }
        /**
         * Funcion para codificar datos en sha1
         * @param string $value
         * @return string
         */
        public static function encode_sha_1($value){
            return sha1($value);
        }     
        /**
         * Funcion para codificar datos en md5 y sha1
         * @param string $value
         * @return string
         */
        public static function encode_md5_y_sha_1($value){
            $value= md5($value);
            return sha1($value);
        }     
        /**
         * Simple filtro que saca las '' y "" para que no se pueda realizar xss
         * Hay que mejorarlo
         * @param string $value
         * @return string
         */
        public static function filter_simple_xss($value){
            $value= str_replace('"','',$value);
            return str_replace("'","",$value);
        }     
        /**
         * Realiza la limpieza de un string o conjunto de string llamando a la funcion filtro_xss
         * @param string o array[string] $valor
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