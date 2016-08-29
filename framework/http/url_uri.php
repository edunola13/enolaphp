<?php
namespace Enola\Http;
use Enola\Support\Security;
use Enola;

/**
 * En esta clase se encuentra toda la funcionalidad para analizar las distintas URLs y URIs que trata la aplicacion.
 * Esta clase contiene todos sus metodos estaticos y toda su comportamiento le realiza soporte al modulo HTTP
 * Esta mediante su comportamiento define la URI de la aplicacion (real base url, locale url, etc), permite consultar
 * si una URL mapea con una URI, quitar los parametros de una URL en base a una URI, etc. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 * @internal
 */
class UrlUri{
    public static function defineBaseUrl($relativeUri, $use_forwarded_host = false){
        $server= filter_input_array(INPUT_SERVER);
        $ssl= (! empty($server['HTTPS']) && $server['HTTPS'] == 'on');
        $sp= strtolower( $server['SERVER_PROTOCOL'] );
        $protocol= substr( $sp, 0, strpos( $sp, '/' ) ) . ( ( $ssl ) ? 's' : '' );
        $port= $server['SERVER_PORT'];
        $port= ((! $ssl && $port=='80') || ($ssl && $port=='443') ) ? '' : ':'.$port;
        $host= ($use_forwarded_host && isset($server['HTTP_X_FORWARDED_HOST'])) ? $server['HTTP_X_FORWARDED_HOST'] : (isset($server['HTTP_HOST']) ? $server['HTTP_HOST'] : null );
        $host= isset( $host ) ? $host : $server['SERVER_NAME'] . $port;
        $baseUrl= $protocol . '://' . $host . '/' . ltrim($relativeUri, '/');
        //BASEURL: Defino la base url segun la url desde donde se inicia la ejecucion de la aplicacion
        define('BASEURL', $baseUrl);
        return $baseUrl;
    }
    /**
     * Se crea la URI ACTUAL de la aplicacion, una URI con el Locale y otra sin Locale, se define la base url real, etc.
     * @param \EnolaContext $context
     * @return array
     */
    public static function defineApplicationUri($context){
        //Resultado de Configuracion - DefinirURI
        $result= array();        
        //Cargo la URI segun el servidor
        $uri_actual= filter_input(INPUT_SERVER, 'REQUEST_URI');
        //Analizo la cantidad de partes de la baseurl + indexpage(si corresponde) para poder crear la URI correspondiente para la aplicacion
        $url_base= self::defineBaseUrl($context->getRelativeUrL());
        //Defino en el contexto el base url
        $context->setBaseUrL($url_base);
        
        $index_page= $context->getIndexPage();
        if($index_page != ''){
            $url_base .= trim($index_page, '/') . '/';
        }
        //REAL_BASE_URL: Real Base Url de la aplicacion - Es la union de BASE_URL y INDEX_PAGE
        $result['REAL_BASE_URL']= $url_base;
        $url_base= explode("/", $url_base);
        $uri_app= "";
        //0= http, 1= //, 2= dominio, >3= una carpeta cualquiera
        if(count($url_base) > 3){
           //Trabajar desde el 4to elemento del arreglo
           $count_url_base= count($url_base);
           for($i = 3; $i < $count_url_base; $i++){
              $uri_app .= "/" . $url_base[$i];
           }
        }
        //Elimino la parte de la URI que es fija y no es utilizada - Esto seria en caso de que haya carpeta
        $uri_actual= substr($uri_actual, strlen($uri_app));
        //Elimino "/" en caso de que haya una al final de la cadena
        $uri_actual= trim($uri_actual, "/");       
        /*
        * Analizar Internacionalizacion
        */        
        //Crea las variable LOCALE, LOCALE_URI, URI_LOCALE, BASEURL_LOCALE
        $locale_actual= NULL;
        $locale_uri= NULL;
        $uri_app_locale= $uri_actual;
        $base_url_locale= $result['REAL_BASE_URL'];        
        //Si hay configuracion de internacionalizacion realiza analisis
        if($context->isLocalesDefined()){
            //Consigue el locale por defecto
            $locale_actual= $context->getI18nDefaultLocale();
            //Consigo la primer parte de la URI para ver si esta internacionalizada
            $uri_locale= explode("/", $uri_actual);
            $uri_locale= $uri_locale[0];            
            //Consigo el resto de los posibles LOCALES
            //Recorro todos los locale para ver si alguno coincide con la primer parte de la URL
            foreach ($context->getI18nLocales() as $locale) {
                //Cuando un locale coincide armo los datos correspondientes
                if($uri_locale == $locale){
                    //Cambio el locale y el locale uri por el correspondiente
                    $locale_actual= $locale;
                    $locale_uri= $locale;                                        
                    //Salgo del For
                    break;
                }
            }            
            if($locale_actual == $uri_locale){
                //Le quito la parte de internacionalizacion a la uri actual
                $uri_actual= substr($uri_actual, strlen($locale_actual));
                $uri_actual= trim($uri_actual, "/");
                //Le agrego la parte internacinalizada al base locale
                if($locale_uri != NULL){
                    $base_url_locale= $base_url_locale . $locale_uri . "/";
                }
            }
        }       
        //URIAPP: Contiene la URI de peticion sin el fragmento de internacionalizacion
        $result['URIAPP']= $uri_actual;
        define('URIAPP', $uri_actual);
        //URIAPP_LOCALE: Contiene la URI de peticion con el fragmento de internacionalizacion
        $result['URIAPP_LOCALE']= $uri_app_locale;
        //LOCALE_URI: Contiene el LOCALE segun la URI actual
        $result['LOCALE_URI']= $locale_uri;
        //LOCALE: Contiene el Locale actual. Si no se encuentra ninguno en la URL es igual al por defecto, si no es igual a LOCALE_URI
        $result['LOCALE']= $locale_actual;
        //BASEURL_LOCALE: Contiene la base URL con el LOCALE correspondiente. Si el locale es el por defecto esta es igual a REAL_BASE_URL
        $result['BASEURL_LOCALE']= $base_url_locale;
        return $result;
    }
    /**
     * En base a una URL pasada como parametro ve si esta mapea a la URI actual de la aplicacion
     * Se pasan las url-uri definidas en los archivos de configuracion
     * @param string $url - del controlador o filtro
     * @param string uriapp - URIAPP de la aplicacion por defecto
     * @return boolean
     */
    public static function mapsActualUrl($url, $uriapp = NULL){
        //Elimino el primer caracter si es igual a "/"
        if(substr($url, 0, 1) == "/"){
            $url= substr($url, 1);
        }        
        //Separa la url pasada y la uri en partes para poder analizarlas
        $url_parts= explode("/", $url);
        
        //Saco de la uri actual o uriapp pasada como parametro los parametros
        $uri_explode= explode("?", En_HttpRequest::getInstance()->uriApp);
        if($uriapp !== NULL){
            //Quito la barra si se agrego
            $uriapp= ltrim($uriapp, "/");
            $uri_explode= explode("?", $uriapp);
        }
        $uri_front= $uri_explode[0];
        //Separo la uri actual
        $actual_uri_parts= explode("/", $uri_front);
        $maps= TRUE;        
        //Analiza que url-uri tiene mas elementos
        if(count($url_parts) >= count($actual_uri_parts)){
            //Si el tamano de la url es igual o mayor que la uri actual uso el for recorriendo las partes de la url
            $count_uri_parts= count($url_parts);
            $count_actual_uri_parts= count($actual_uri_parts);
            for($i= 0; $i < $count_uri_parts; $i++) {
                $url_part= $url_parts[$i];                
                if($count_actual_uri_parts >= ($i + 1)){
                    $actual_uri_part= $actual_uri_parts[$i];
                    $pos_ocurrencia= strpos($url_part, "*");
                    //Si $url_part es vacio entonces deben coincidir
                    if($url_part == ''){
                        //Si no coinciden las partes no mapean
                        if($url_part != $actual_uri_parts[$i]){
                            $maps= FALSE;
                            break;
                        }
                    //Si la url no contiene ningun caracter especial deben coincidir
                    }else if($url_part != "*" && $url_part != "-" && !$pos_ocurrencia && $url_part{0} != ":"){
                        //Si no coinciden las partes no mapean
                        if($url_part != $actual_uri_parts[$i]){
                            $maps= FALSE;
                            break;
                        } 
                    }else{
                        if($url_part == "*" || $url_part == "-"){
                            break;
                        }
                        //Si contiene ocurrencia de * debe coincidir la primer parte
                        if($pos_ocurrencia){
                            $url= explode("*", $url_parts[$i]);
                            $url= $url[0];
                            if(strlen($actual_uri_part) >= strlen($url)){
                                $actual_uri= substr($actual_uri_part, 0, strlen($url));
                                if($url == $actual_uri){
                                    break;
                                }else{
                                    $maps= FALSE;
                                    break;
                                }
                            }else{
                                $maps= FALSE;
                                break;
                            }
                        }
                        //Si la parte de la uri empieza con : puede ir cualquier string ahi por lo que pasa directamente esta parte de la validacion
                        if($url_part{0} == ":"){
                            //Si es distinto de vacio pasa a la siguiente seccion
                            if($actual_uri_parts[$i] == '' && $url_part{strlen($url_part)} == '?' ){
                                break;
                            }else if($actual_uri_parts[$i] == ''){
                                $maps= FALSE;
                                break;
                            }
                        }
                    }
                }else{
                    //La uri actual no tiene mas partes y no hay coincidencia completa
                    //Si lo que sigue es un - o un * o un :-? mapea
                    if($url_part != "-" && $url_part != "*" && $url_part != ''){
                        if($url_part{0} == ':' && $url_part{strlen($url_part) - 1} == '?'){
                            break;
                        }
                        $maps= FALSE;
                    }
                    break;                 
                }
            }            
        }else{
            //Si el tamano de la url pasada es menor que la uri uso el for recorriendo las partes de la uri
            $count_uri_parts= count($url_parts);
            $count_actual_uri_parts= count($actual_uri_parts);
            for($i= 0; $i < $count_actual_uri_parts; $i++){
                $actual_uri_part= $actual_uri_parts[$i];
                if($count_uri_parts >= ($i + 1)){
                    $url_part= $url_parts[$i];
                    $pos_ocurrencia= strpos($url_part, "*");
                    //Si $url_part es vacio entonces deben coincidir
                    if($url_part == ''){
                        //Si no coinciden las partes no mapean
                        if($url_part != $actual_uri_part){
                            $maps= FALSE;
                            break;
                        }
                    //Si la url no contiene ningun caracter especial deben coincidir
                    }else if($url_part != "*" && $url_part != "-" && !$pos_ocurrencia && $url_part{0} != ":"){
                        //Si no coinciden las partes no mapean
                        if($url_part != $actual_uri_part){
                            $maps= FALSE;
                            break;
                        } 
                    }else{
                        if($url_part == "*" || $url_part == "-"){                          
                            break;
                        }
                        //Si contiene ocurrencia de * debe coincidir la primer parte
                        if($pos_ocurrencia){
                            $url= explode("*", $url_parts[$i]);
                            $url= $url[0];
                            if(strlen($actual_uri_part) >= strlen($url)){
                                $actual_uri= substr($actual_uri_part, 0, strlen($url));
                                if($url == $actual_uri){
                                    break;
                                }else{
                                    $maps= FALSE;
                                    break;
                                }
                            }else{
                                $maps= FALSE;
                                break;
                            }
                        }
                    }
                }else{
                    //La url pasada no tiene mas partes y no hay coincidencia completa
                    $maps= FALSE;
                    break;
                }
            }
        }        
        return $maps;
    } 
    /**
     * Indica si un metodo o conjunto de ellos mapea con el metodo actual de la app o el parametrizado
     * @param string $method
     * @param string $methodApp
     * @return boolean
     */
    public static function mapsActualMethod($method, $methodApp = NULL){
        if($method == "*"){
            return TRUE;
        }else{
            if($methodApp == NULL){
                $methodApp= En_HttpRequest::getInstance()->requestMethod;
            }
            $method= str_replace(' ', '', $method);
            $methods= explode(',', $method);
            if(in_array($methodApp, $methods)){
                return TRUE;
            }else{
                return FALSE;
            }
        }
    }
    /**
     * Redireccionar a otra pagina pasando una uri relativa a la aplicacion
     * @param En_HttpRequest $httpRequest
     * @param string $uri
     */
    public static function redirect($httpRequest, $uri){
        //Le quita '/' si es que tiene al principio y al final
        $uri= trim($uri, "/");
        header('Location:' . $httpRequest->realBaseUrl . $uri);
        //Detiene el flujo
        exit;
    }    
    /**
     * Redirecciona a una pagina externa a la aplicacion actual
     * @param string url
     */
    public static function externalRedirect($url){
        header('Location:' . $url);
        //Detiene el flujo
        exit;
    }    
    /**
     * Esta funcion devuelve los parametros de la URI en base a la URL que mapeo
     * Devuelve los distintos tipos de parametros que se pueden armar en la configuracion, los que se pueden armar entre ()
     * o en caso de definir la url con un "-" final todos lo que viene despues de este separado por "/"
     * No son los parametros GET ni POST
     * @param string $url
     * @return array[string]
     */
    public static function uriParams($url, $uriapp = NULL){
        $parameters= NULL;
        $method= 'index';
        $dinamic= FALSE;
        //Elimino el primer caracter si es igual a "/"
        $url= ltrim($url, '/');
        //Separa la url y la uri en partes para poder analizarlas
        $url_parts= explode("/", $url);
        $uri_explode= explode("?", En_HttpRequest::getInstance()->uriApp);
        if($uriapp !== NULL){
            $uri_explode= explode("?", $uriapp);
        }
        $uri_front= $uri_explode[0];
        $actual_uri_parts= explode("/", $uri_front);
        //Si en la url hay un * o un - limpio paso todo lo que venga desde la url real como un parametro
        $in_array_guion= in_array('-', $url_parts);
        $in_array_aster= in_array('*', $url_parts);
        if($in_array_guion || $in_array_aster){
            $count_uri_parts_actual= count($actual_uri_parts);
            $found= FALSE;
            for($i= 0; $i < $count_uri_parts_actual; $i++){
                if(!$found){
                    if($url_parts[$i] == '-' || $url_parts[$i] == '*'){
                        $found= TRUE;
                        if($url_parts[$i] == '-' && $actual_uri_parts[$i] != ''){
                            $method= $actual_uri_parts[$i];                            
                            continue;
                        }
                    }else{
                        continue;
                    }
                }
                $parameters[]= $actual_uri_parts[$i];
            }
            if($in_array_guion){
                $dinamic= TRUE;
            }
        }        
        //Pase lo que pase arriba esto puede ir siempre, ya que puede estar anterior al * o al -
        //Analizo si hay parametros asociativos porque se mapean con ":"
        $count_uri_parts= count($url_parts);
        for($i= 0; $i < $count_uri_parts; $i++){
            if(count($actual_uri_parts)-1 < $i){
                //Si la uri de peticion actual no tiene mas partes no agrego nada
                break;
            }
            if($url_parts[$i] == '' || $actual_uri_parts[$i] == ''){
                //Si alguno esta vacio ya no hay parametros uri, es decir :
                break;
            }
            else{
                //Si la parte de la url comienza con : le paso el parametro
                if($url_parts[$i]{0} == ":"){
                    $nombre= ltrim($url_parts[$i], ":");
                    $nombre= rtrim($nombre, '?');
                    $parameters[$nombre]= $actual_uri_parts[$i];
                }
            }
        }        
        if($parameters != NULL){
            $parameters= Security::clean_vars($parameters);
        }
        return array('params' => $parameters, 'method' => $method, 'dinamic' => $dinamic);
    }
    /**
     * Setea el codigo del header HTTP
     * @param int $code
     * @param string $text
     */
    public static function setEstadoHeader($code = 200, $text = ''){
        //Arreglo con todos los codigos y su respectivo texto
        $states = array(
                                                        100     => 'Continue',
                                                        101     => 'Switching Protocols',
                                                        103     => 'Checkpoint',            
							200	=> 'OK',
							201	=> 'Created',
							202	=> 'Accepted',
							203	=> 'Non-Authoritative Information',
							204	=> 'No Content',
							205	=> 'Reset Content',
							206	=> 'Partial Content',
							300	=> 'Multiple Choices',
							301	=> 'Moved Permanently',
							302	=> 'Found',
                                                        303     => 'See Other',
							304	=> 'Not Modified',
							306	=> 'Switch Proxy',
							307	=> 'Temporary Redirect',
                                                        308     => 'Resume Incomplete',
							400	=> 'Bad Request',
							401	=> 'Unauthorized',
							402     => 'Payment Required',
                                                        403	=> 'Forbidden',
							404	=> 'Not Found',
							405	=> 'Method Not Allowed',
							406	=> 'Not Acceptable',
							407	=> 'Proxy Authentication Required',
							408	=> 'Request Timeout',
							409	=> 'Conflict',
							410	=> 'Gone',
							411	=> 'Length Required',
							412	=> 'Precondition Failed',
							413	=> 'Request Entity Too Large',
							414	=> 'Request-URI Too Long',
							415	=> 'Unsupported Media Type',
							416	=> 'Requested Range Not Satisfiable',
							417	=> 'Expectation Failed',
							500	=> 'Internal Server Error',
							501	=> 'Not Implemented',
							502	=> 'Bad Gateway',
							503	=> 'Service Unavailable',
							504	=> 'Gateway Timeout',
							505	=> 'HTTP Version Not Supported',
                                                        511     => 'Network Authentication Required'
						);
        //Me fijo que el codigo no sea un string 
        if ($code == '' OR ! is_numeric($code)){
            Enola\Error::general_error('Error Estado HTTP', 'El codigo de estado debe ser numerico');
	}
        //Veo si se paso o no texto y si no, le asigo el del codigo
	if (isset($states[$code]) AND $text == ''){
            $text = $states[$code];
	}
        //Me fijo que el texto no este vacio
        if ($text == ''){
            Enola\Error::general_error('Error Estado HTTP', 'No status text available.  Please check your status code number or supply your own message text.');
	}
        //Cargo el protocolo
	$server_protocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL');

        //Segun el protocolo modifico el header HTTP
	if (substr(php_sapi_name(), 0, 3) == 'cgi'){
            header("Status: {$code} {$text}", TRUE);
	}
	elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0'){
            header($server_protocol." {$code} {$text}", TRUE, $code);
	}
	else{
            header("HTTP/1.1 {$code} {$text}", TRUE, $code);
	}
    }
}