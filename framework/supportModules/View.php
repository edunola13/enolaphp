<?php
namespace Enola\Support;
use EnolaContext;

/**
 * Esta clase provee comportamiento para facilitar el armado de la vista proveyendo diferentes metodos que simplifican situacines
 * tipicas en el armado de la vista.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class View{
    /** @var \Enola\Application */
    public $app;
    /** @var EnolaContext */
    public $context;
    /** @var Request */
    public $request;
    /** @var Response */
    public $response;
    //i18n
    protected $locale;
    protected $fileName;
    protected $i18nContent;
    /*
     * Constructor - Setea variables que necesitara luego para resolver su comportamiento 
     */
    public function __construct() {
        $this->context= EnolaContext::getInstance();
        $this->app= $this->context->app;
        $this->request= $this->app->getRequest();
        $this->response= $this->app->getResponse();
    }
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
    function realBase(){
        return $this->request->realBaseUrl;
    }    
    /**
     * Retorna la base url con el locale actual
     * @return string
     */
    function baseLocale(){
        return $this->request->baseUrlLocale;
    }
    /**
     * Arma una url para un recurso
     * @param string $internalUri
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
        if($locale == NULL)return $this->request->realBaseUrl . $internalUri;
        else return $this->request->realBaseUrl . $locale . '/' . $internalUri;
    }
    /**
     * Arma una url internacionalizada (locale actual) para una URI interna
     * @param string $internalUri
     * @return string 
     */
    function urlLocaleFor($internalUri){
        $internalUri= ltrim($internalUri, '/');
        return $this->request->baseUrlLocale . $internalUri;
    }
    /**
     * Arma una url para renderizar un componente
     * @param string $component
     * @param string $params
     * @param string $locale
     * @return string 
     */
    function urlComponentFor($component, $params = "", $locale = NULL){
        $params= '/' . ltrim($params, '/');
        $url_component= $this->context->getComponentUrl();
        if($locale == NULL)return $this->request->realBaseUrl . $url_component . '/' . $component . $params;
        else return $this->request->realBaseUrl . $locale . '/' . $url_component . '/' . $component . $params;
    }
    /**
     * Arma un url para ejecutar una accion de un componente
     * @param string $component
     * @param string $action
     * @param string $params
     * @param string $locale
     * @return string 
     */
    function urlComponentActionFor($component, $action, $params = "", $locale = NULL){
        $params= '/' . ltrim($params, '/');
        $url_component= $this->context->getComponentUrl();
        if($locale == NULL)return $this->request->realBaseUrl . $url_component . '/' . $component . '/actionComponent/' . $action . $params;
        else return $this->request->realBaseUrl . $locale . '/' . $url_component . '/' . $component . '/actionComponent/' . $action . $params;
    }
    /**
     * Retorna el locale actual.
     * En caso de que el locale este indicado en la URL sera igual a locale_uri, si no sera igual al locale definido por defecto.
     * @return string
     */
    function locale(){
        return $this->request->locale;
    }    
    /**
     * Retorna el locale actual de la url
     * @return string o null
     */
    function localeUri(){
        return $this->request->localeUri;
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
    function replaceSpaces($string){
        return str_replace(" ", "-", $string);
    }    
    /**
     * Ejecuta un componente en base la especificacion indicada
     * @param string $name
     * @param array $params
     * @param string action
     * @param bool $buffer
     * @return void - string
     */
    function component($name, $params = NULL, $action = NULL, $buffer = FALSE){
        if($buffer){
            ob_start();            
        }
        //Llama a la funcion que ejecuta el componente definido en el modulo Componente
        $this->app->componentCore->executeComponent($name, $params, $action);
        if($buffer){
            $output = ob_get_contents();
            ob_end_clean();
            return $output;
        }
    }    
    /**
     * Carga un archivo de internacionalizacion. Si no se especifica el locale carga el archivo por defecto, si no
     * le agrega el locale pasado como parametro
     * @param string $file
     * @param string $locale
     */
    function i18n($file, $locale = NULL){
        $this->fileName= $file;
        $this->i18nContent= NULL;
        if($locale != NULL){
            if(file_exists(PATHAPP . 'source/content/' . $file . "_$locale" . '.txt')){
                $this->i18nContent= load_application_file('source/content/' . $file . "_$locale" . '.txt');
                $this->i18nContent= $this->parse_properties($this->i18nContent);
                $this->locale= $locale;
            }
        }
        if($this->i18nContent == NULL){
            $this->i18nContent= load_application_file('source/content/' . $file . '.txt');
            $this->i18nContent= $this->parse_properties($this->i18nContent);
            $this->locale= 'Default';
        }
    }    
    /**
     * Cambia el archivo de internacionalizacion cargado. Lo cambia segun el locale pasado
     * @param string $locale
     */
    function i18n_change_locale($locale){
        if(isset($this->fileName)){
            i18n($this->fileName, $locale);
        }
        else{
            general_error('I18n Error', 'Before call i18n_change_locale is necesary call i18n');
        }
    }    
    /**
     * Devuelve el valor segun el archivo de internacionalizacion que se encuentre cargado
     * @param string $val_key
     * @param array $params
     * @return string
     */
    function i18n_value($val_key, $params = NULL){
        if(isset($this->i18nContent)){
            if(isset($this->i18nContent[$val_key])){
                $mensaje= $this->i18nContent[$val_key];
                
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
     * @return string
     */
    function i18n_locale(){
        if(isset($this->locale)){
            return $this->locale;
        }else{
            return 'Default';
        }
    }    
    /**
     * Este proceso analiza de a una las lineas del archivo de internacionalizacion usado. En este caso txt file y me arma lo que seria
     * un array asociativo clave valor en base a la linea.
     * @param array[string] $lineas
     * @return array[string]
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
}