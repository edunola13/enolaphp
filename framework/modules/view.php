<?php
    /*
     * Este modulo tiene funciones utiles para usar en la vista de la aplicacion
     */       
    /**
     * Retorna la baseurl
     * @return string
     */
    function base(){
        return BASEURL;
    }
    /**
     * Retorna la real_baseurl
     * @return string
     */
    function real_base(){
        return REAL_BASE_URL;
    }    
    /**
     * Retorna la base url con el locale actual
     * @return string
     */
    function base_locale(){
        return BASEURL_LOCALE;
    }
    /**
     * Arma una url para un recurso
     * @param type $internalUri
     * @return string 
     */
    function urlResourceFor($internalUri){
        $internalUri= ltrim($internalUri, '/');
        return BASEURL . 'resources/' . $internalUri;
    }
    /**
     * Arma una url para una URI interna
     * @param type $internalUri
     * @param type $locale
     * @return string 
     */
    function urlFor($internalUri, $locale = NULL){
        $internalUri= ltrim($internalUri, '/');
        if($locale == NULL)return REAL_BASE_URL . $internalUri;
        else return REAL_BASE_URL . $locale . '/' . $internalUri;
    }
    /**
     * Arma una url internacionalizada para una URI interna
     * @param type $internalUri
     * @return string 
     */
    function urlLocaleFor($internalUri){
        $internalUri= ltrim($internalUri, '/');
        return BASEURL_LOCALE . $internalUri;
    }
    /**
     * Arma una url para acceder a un componente
     * @param type $component
     * @param string $params
     * @param type $locale
     * @return string 
     */
    function urlComponentFor($component, $params = "", $locale = NULL){
        $params= '/' . ltrim($params, '/');
        $url_component= trim(URL_COMPONENT, '/');
        if($locale == NULL)return REAL_BASE_URL . $url_component . '/' . $component . $params;
        else return REAL_BASE_URL . $locale . '/' . $url_component . '/' . $component . $params;
    }
    /**
     * Arma un url para ejecutar una accion de un componente
     * @param type $component
     * @param type $action
     * @param string $params
     * @param type $locale
     * @return string 
     */
    function urlComponentActionFor($component, $action, $params = "", $locale = NULL){
        $params= '/' . ltrim($params, '/');
        $url_component= trim(URL_COMPONENT, '/');
        if($locale == NULL)return REAL_BASE_URL . $url_component . '/' . $component . '/actionComponent/' . $action . $params;
        else return REAL_BASE_URL . $locale . '/' . $url_component . '/' . $component . '/actionComponent/' . $action . $params;
    }
    /**
     * Retorna el locale actual.
     * En caso de que el locale este indicado en la URL sera igual a locale_uri, si no sera igual al locale definido por defecto.
     * @return string
     */
    function locale(){
        return LOCALE;
    }    
    /**
     * Retorna el locale actual de la url
     * @return string
     */
    function locale_uri(){
        return LOCALE_URI;
    }
    /**
     * reemplaza $for por $replace en el string $string
     * @param string $replace
     * @param string $for
     * @param string $string
     * @return string
     */
    function replace($replace, $for, $string){
        return str_replace($for, $replace, $string);
    }    
    /**
     * Quita los blancos del string por -
     * @param string $string
     * @return string
     */
    function replace_spaces($string){
        return str_replace(" ", "-", $string);
    }    
    /**
     * Realiza el llamado a la funcion que ejecuta el metodo renderizar del componente
     * @param type $nombre
     * @param type $parametros
     */
    function component($name, $params = NULL, $action = NULL){
        //Llama a la funcion que ejecuta el componente definido en el modulo Componente
        return execute_component($name, $params, $action);
    }    
    /**
     * Carga un archivo de internacionalizacion. Si no se especifica el locale carga el archivo por defecto, si no le agrega el locale pasado
     * @param type $archivo
     * @param type $locale
     */
    function i18n($file, $locale = NULL){
        $archivo_cargado= NULL;
        if($locale != NULL){
            if(file_exists(PATHAPP . 'source/content/' . $file . "_$locale" . '.txt')){
                $archivo_cargado= load_application_file('source/content/' . $file . "_$locale" . '.txt');
                $archivo_cargado= parse_properties($archivo_cargado);
                $GLOBALS['i18n_locale']= $locale;
            }
        }
        if($archivo_cargado == NULL){
            $archivo_cargado= load_application_file('source/content/' . $file . '.txt');
            $archivo_cargado= parse_properties($archivo_cargado);
            $GLOBALS['i18n_locale']= 'Default';
        }
        $GLOBALS['i18n_language_file']= $archivo_cargado;
        $GLOBALS['i18n_file']= $file;
    }    
    /**
     * Cambia el archivo de internacionalizacion cargado. Lo cambia segun el locale pasado
     * @param type $locale
     */
    function i18n_change_locale($locale){
        if(isset($GLOBALS['i18n_file'])){
            i18n($GLOBALS['i18n_file'], $locale);
        }
        else{
            general_error('I18n Error', 'Before call i18n_change_locale is necesary call i18n');
        }
    }    
    /**
     * Devuelve el valor segun el archivo de internacionalizacion que se encuentre cargado
     * @param type $clave
     * @return type
     */
    function i18n_value($val_key, $params = NULL){
        if(isset($GLOBALS['i18n_language_file'])){
            if(isset($GLOBALS['i18n_language_file'][$val_key])){
                $mensaje= $GLOBALS['i18n_language_file'][$val_key];
                
                //Analiza si se pasaron parametros y si se pasaron cambia los valores correspondientes
                if($params != NULL){
                    foreach ($params as $key => $valor) {
                        $mensaje= str_replace(":$key", $valor, $mensaje);
                    }
                }                
                return $mensaje;
            }
        }
        else{
            general_error('I18n Error', 'Not specified any I18n file to make it run the i18n function');
        }
    }    
    /**
     * Retorna el locale configurado para el contenido internacionalizado
     */
    function i18n_locale(){
        if(isset($GLOBALS['i18n_locale'])){
            return $GLOBALS['i18n_locale'];
        }
        else{
            return 'Default';
        }
    }    
    /**
     * Este proceso analiza de a una las lineas del archivo de internacionalizacion usado. En este caso txt file y me arma lo que seria
     * un array asociativo clave valor en base a la linea.
     * @param type $lineas
     * @return type
     */
    function parse_properties($lineas) {
        $result= NULL;
        $isWaitingOtherLine = false;
        $value= NULL;
        foreach($lineas as $i=>$linea) {
            if(empty($linea) || !isset($linea) || strpos($linea,"#") === 0){
                continue;
            }
            if(!$isWaitingOtherLine) {
                $key= substr($linea,0,strpos($linea,'='));
                $value= substr($linea,strpos($linea,'=') + 1, strlen($linea));
            }else {
                $value.= $linea;
            }           
            
            /* Check if ends with single '\' */
            if(strrpos($value,"\\") === strlen($value)-strlen("\\")) {
                $value= substr($value, 0, strlen($value)-1)."\n";
                $isWaitingOtherLine= true;
            }else {
                $result[$key]= preg_replace("/\r\n+|\r+|\n+|\t+/i", "", $value); 
                $isWaitingOtherLine= false;
            }                       
        }
        return $result;
   }