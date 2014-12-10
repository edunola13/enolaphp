<?php
    /*
     * Conjunto de funciones que ayudan al framework a realizar tareas con URL y URI
     * Tambien pueden ser utilizadas por el usuario del framework
     */
    /**
     * Creo la URI ACTUAL de la aplicacion
     * Crea una URI con el Locale y otra sin Locale
     * Almacena el LOCALE de la URI
     * Usada solo por el framework en la etapa de configuracion
     */
    function define_application_uri(){
        //Cargo la URI segun el servidor - Esta siempre es todo lo que esta despues de www.edunola.com.ar o localhost/
        $uri_actual= $_SERVER['REQUEST_URI'];
        //Analizo la cantidad de partes de la baseurl para poder crear la URI correspondiente para la aplicacion
        $url_base= explode("/", BASEURL);
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
        $base_url_locale= BASEURL;        
        //Si hay configuracion de internacionalizacion realiza analisis
        if(isset($GLOBALS['i18n']['locales'])){
            //Consigue el locale por defecto
            $locale_actual= $GLOBALS['i18n']['default'];
            //Consigo la primer parte de la URI para ver si esta internacionalizada
            $uri_locale= explode("/", $uri_actual);
            $uri_locale= $uri_locale[0];
            
            //Consigo el resto de los posibles LOCALES
            $locales= str_replace(" ", "", $GLOBALS['i18n']['locales']);
            //Separo los LOCALE
            $locales= explode(",", $locales);

            //Recorro todos los locale para ver si alguno coincide con la primer parte de la URL
            foreach ($locales as $locale) {
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
        define('URIAPP', $uri_actual);
        //URIAPP_LOCALE: Contiene la URI de peticion con el fragmento de internacionalizacion
        define('URIAPP_LOCALE', $uri_app_locale);
        //LOCALE_URI: Contiene el LOCALE segun la URI actual
        define('LOCALE_URI', $locale_uri);
        //LOCALE: Contiene el Locale actual. Si no se encuentra ninguno en la URL es igual al por defecto, si no es igual a LOCALE_URI
        define('LOCALE', $locale_actual);
        //BASEURL_LOCALE: Contiene la base URL con el LOCALE correspondiente. Si el locale es el por defecto esta es igual a BASEURL
        define('BASEURL_LOCALE', $base_url_locale);
    }
    /**
     * En base a una URL pasada como parametro ve si esta mapea a la URL actual de la aplicacion
     * Se pasan las url-uri definidas en los archivos de configuracion
     * @param string $url
     * @param string uriapp - Si no deseo utilizar el URIAPP por defecto de la aplicacion, sirve para el MVC 
     * @return boolean
     */
    function maps_actual_url($url, $uriapp = NULL){
        //Elimino el primer caracter si es igual a "/"
        if(substr($url, 0, 1) == "/"){
            $url= substr($url, 1);
        }        
        //Separa la url pasada y la uri en partes para poder analizarlas
        $partes_url= explode("/", $url);
        
        //Saco de la uri actual los parametros
        $uri_explode= explode("?", URIAPP);
        if($uriapp != NULL){
            $uri_explode= explode("?", $uriapp);
        }
        $uri_front= $uri_explode[0];
        //Separo la uri actual
        $partes_uri_actual= explode("/", $uri_front);        
        $mapea= TRUE;        
        //Analiza que url-uri tiene mas elementos
        if(count($partes_url) >= count($partes_uri_actual)){
            //Si el tamano de la url es igual o mayor que la uri actual uso el for recorriendo las partes de la url
            $count_partes_uri= count($partes_url);
            for($i= 0; $i < $count_partes_uri; $i++) {
                if(count($partes_uri_actual) >= ($i + 1)){
                    //Si hay un * no me importa que viene despues, mapea todo, no deberia haber nada despues
                    if($partes_url[$i] != "*"){
                        $pos_ocurrencia= strpos($partes_url[$i], "*");
                        if($pos_ocurrencia != FALSE){
                            $parte_url= explode("*", $partes_url[$i]);
                            $parte_url= $parte_url[0];
                            if(strlen($partes_uri_actual[$i]) >= strlen($parte_url)){
                                $parte_uri_actual= substr($partes_uri_actual[$i], 0, strlen($parte_url));
                                if($parte_url == $parte_uri_actual){
                                    break;
                                }
                                else{
                                    $mapea= FALSE;
                                    break;
                                }
                            }
                            else{
                                $mapea= FALSE;
                                break;
                            }
                        }
                        //Si alguna esta vacia no compara el mapeo con () y voy directo a la comparacion
                        if(empty($partes_url[$i]) || empty($partes_uri_actual[$i])){
                            //Si no coinciden las partes no mapean
                            if($partes_url[$i] != $partes_uri_actual[$i]){
                                $mapea= FALSE;
                                break;
                            }
                        }
                        else{
                            //Si la parte de la uri empieza con ( y termina con ) puede ir cualquier string ahi por lo que pasa directamente esta parte de la validacion
                            if(! ($partes_url[$i]{0} == "(" and $partes_url[$i]{strlen($partes_url[$i]) -1} == ")")){
                                //Si no contiene ( y ) debe mapear
                                //Si no coinciden las partes no mapean
                                if($partes_url[$i] != $partes_uri_actual[$i]){
                                    $mapea= FALSE;
                                    break;
                                }
                            }
                        }
                    }
                    else{
                        break;
                    }
                }
                else{
                    //La uri actual no tiene mas partes y no hay coincidencia completa
                    $mapea= FALSE;
                    break;
                }
            }            
        }
        else{
            //Si el tamano de la url pasada es menor que la uri uso el for recorriendo las partes de la uri
            $count_partes_uri_actual= count($partes_uri_actual);
            for($i= 0; $i < $count_partes_uri_actual; $i++){
                if(count($partes_url) >= ($i + 1)){                
                    //Si hay un * no me importa que viene despues, mapea todo, no deberia haber nada despues
                    if($partes_url[$i] != "*"){
                        $pos_ocurrencia= strpos($partes_url[$i], "*");
                        if($pos_ocurrencia != FALSE){
                            $parte_url= explode("*", $partes_url[$i]);
                            $parte_url= $parte_url[0];
                            if(strlen($partes_uri_actual[$i]) >= strlen($parte_url)){
                                $parte_uri_actual= substr($partes_uri_actual[$i], 0, strlen($parte_url));
                                if($parte_url == $parte_uri_actual){
                                    break;
                                }
                                else{
                                    $mapea= FALSE;
                                    break;
                                }
                            }
                            else{
                                $mapea= FALSE;
                                break;
                            }
                        }
                        //Si alguna esta vacia no compara el mapeo con () y voy directo a la comparacion
                        if(empty($partes_url[$i]) || empty($partes_uri_actual[$i])){
                            //Si no coinciden las partes no mapean
                            if($partes_url[$i] != $partes_uri_actual[$i]){
                                $mapea= FALSE;
                                break;
                            }
                        }
                        else{
                            //Si la parte de la uri empieza con ( y termina con ) puede ir cualquier string ahi por lo que pasa directamente esta parte de la validacion
                            if(! ($partes_url[$i]{0} == "(" and $partes_url[$i]{strlen($partes_url[$i]) -1} == ")")){
                                //Si no contiene ( y ) debe mapear                        
                                //Si no coinciden las partes no mapean
                                if($partes_url[$i] != $partes_uri_actual[$i]){
                                    $mapea= FALSE;
                                    break;
                                }
                            }
                        }
                    }
                    else{
                        break;
                    }
                }
                else{
                    //La url pasada no tiene mas partes y no hay coincidencia completa
                    $mapea= FALSE;
                    break;
                }
            }
        }        
        return $mapea;
    }    
    /**
     * Redireccionar a otra pagina pasando una uri relativa a la aplicacion
     * @param string $uri
     */
    function redirect($uri){
        //Le quita '/' si es que tiene al principio y al final
        $uri= trim($uri, "/");
        header('Location:' . BASEURL . $uri);
        //Detiene el flujo
        exit;
    }    
    /**
     * Redirecciona a una pagina externa a la aplicacion actual
     * @param string url
     */
    function external_redirect($url){
        header('Location:' . $url);
        //Detiene el flujo
        exit;
    }    
    /**
     * Esta funcion devuelve los parametros de la uri, estos son los parametros que en mapeo fueron definidos entre ()
     * No son los parametros GET ni POST
     * @param string $url
     * @return array[string]
     */
    function uri_params($url){
        $parametros= NULL;        
        //Elimino el primer caracter si es igual a "/"
        if(substr($url, 0, 1) == "/"){
            $url= substr($url, 1);
        }        
        //Separa la url y la uri en partes para poder analizarlas
        $partes_url= explode("/", $url);
        $uri_explode= explode("?", URIAPP);
        $uri_front= $uri_explode[0];
        $partes_uri_actual= explode("/", $uri_front);
        $conut_partes_uri= count($partes_url);
        for($i= 0; $i < $conut_partes_uri; $i++){
            if(empty($partes_url[$i]) || empty($partes_uri_actual[$i])){
                //Si alguno esta vacio ya no hay parametros uri, es decir ()
                break;
            }
            else{
                //Si la parte de la url comienza con ( y termina con ) le paso el parametro
                if($partes_url[$i]{0} == "(" and $partes_url[$i]{strlen($partes_url[$i]) -1} == ")"){
                    $nombre= trim($partes_url[$i], "()");
                    $parametros[$nombre]= $partes_uri_actual[$i];
                }
            }
        }
        return clean_vars($parametros);
    }
    /**
     * Funcion para setear el codigo del header HTTP
     * @param int $codigo
     * @param string $text
     */
    function set_estado_header($codigo = 200, $text = ''){
        //Arreglo con todos los codigos y su respectivo texto
        $estados = array(
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
        if ($codigo == '' OR ! is_numeric($codigo)){
            general_error('Error Estado HTTP', 'El codigo de estado debe ser numerico');
	}
        //Veo si se paso o no texto y si no, le asigo el del codigo
	if (isset($estados[$codigo]) AND $text == ''){
            $text = $estados[$codigo];
	}
        //Me fijo que el texto no este vacio
        if ($text == ''){
            general_error('Error Estado HTTP', 'No status text available.  Please check your status code number or supply your own message text.');
	}
        //Cargo el protocolo
	$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

        //Segun el protocolo modifico el header HTTP
	if (substr(php_sapi_name(), 0, 3) == 'cgi'){
            header("Status: {$codigo} {$text}", TRUE);
	}
	elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0'){
            header($server_protocol." {$codigo} {$text}", TRUE, $codigo);
	}
	else{
            header("HTTP/1.1 {$codigo} {$text}", TRUE, $codigo);
	}
    }
?>