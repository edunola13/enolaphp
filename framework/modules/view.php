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
            if(file_exists(PATHAPP . 'source/content/' . $file . "_$locale" . '.ini')){
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
        foreach($lineas as $i=>$linea) {
            if(empty($linea) || !isset($linea) || strpos($linea,"#") === 0){
                continue;
            }
            $key = substr($linea,0,strpos($linea,'='));
            $value = substr($linea,strpos($linea,'=') + 1, strlen($linea));
            $result[$key] = $value;
        }
        return $result;
   }
?>