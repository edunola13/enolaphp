<?php
    /*
     * Maneja los errores del framework
     * Contiene tambien la seccion de Informacion del Framework hacia el usuario
     */    
    /**
     * Funcion para manejar los errores php. 
     * Esta se superpone a la propia de php cuando es seteada en el nucleo.php
     * @param $nivel_error
     * @param string $mensaje
     * @param string $archivo
     * @param int $linea
     * @return boolean
     */
    function _error_handler($level, $message, $file, $line){
        if (!(error_reporting() & $level)) {
            //Agrega el Log
            write_log($message, 'Level Error: '. $level);
            // Segun el nivel de error veo si agarro o no la excepcion. si entra aca no hago nada
            return;
        }
        //Analizo el error que se produjo y aviso del mismo.
        //Segun el error termino el flujo de ejecucion o continua
        switch ($level) {
            case E_USER_ERROR:
                error_php('Error', $level, $message, $file, $line);
                exit(1);
                break;

            case E_USER_WARNING:
                error_php('Warning', $level, $message, $file, $line);
                break;

            case E_USER_NOTICE:
                error_php('Notice', $level, $message, $file, $line);
                break;

            default:
                error_php('Unknown', $level, $message, $file, $line);
                break;
        }
        // No ejecutar el gestor de errores interno de PHP
        return true;      
    }    
    /**
     * Funcion que se va a ejecutar en el cierre de ejecucion de la aplicacion.
     * La vamos a utilizar para manejar los errores fatales
     */
    function _shutdown(){
        if(!is_null($e = error_get_last())){
            //Se podria agregar mas errores en el IF, ver set error handler en PHP para ver cuales no son manejados con esa funcion
            //Si no son manejados con esa funcion todos cierran el programa directamente
            if($e['type'] == E_ERROR || $e['type'] == E_PARSE || $e['type'] == E_STRICT){
                if(!(error_reporting() & $e['type'])){
                    write_log($e['message'], $e['type']);
                }
                else{
                    error_php('Error Fatal - Parse - Strict', $e['type'], $e['message'], $e['file'], $e['line']);
                }
            }
        }  
    }    
    /**
     * Funcion que es llamada para crear una respuesta de error php - usada por el manejador de errores definido por el framework
     * @param string $tipo_error
     * @param $nivel_error
     * @param string $mensaje
     * @param string $archivo
     * @param int $linea
     */
    function error_php($type, $level, $message, $file, $line){
        write_log($message, $type);
        if(error_reporting()){
            require_once PATHAPP . 'errors/error_php.php';
        }
    }    
    /**
     * Funcion que es llamada para crear un respuesta de error 404
     * Usada por el framework y/o el usuario
     */
    function error_404(){
        $head= '404 Pagina no Encontrada';
        $message= 'La pagina que solicitaste no existe';
        set_estado_header(404);
        require_once PATHAPP . 'errors/error_404.php';
        exit;
    }    
    /**
     * Funcion que es llamada para crear una respuesta de error general
     * Usada por el framework y/o el usuario
     * @param string $cabecera
     * @param string $mensaje
     * @param string $template
     * @param int $codigo_error
     */
    function general_error($head, $message, $template = 'general_error', $code_error = 500){
        write_log('general_error', $message);
        set_estado_header($code_error);
        if(error_reporting()){
            require_once PATHAPP . 'errors/' . $template . '.php'; 
        }        
    }
    /**
     * Crea o abre un archivo de log y escribe el error correspondiente
     * @param String $cadena
     * @param String $tipo
     */
    function write_log($cadena, $tipo){
        if(filesize(PATHAPP . 'logs/log.txt') > 100000){           
            $arch= fopen(PATHAPP . 'logs/log.txt', "w");
            fclose($arch); 
        }
	$arch = fopen(PATHAPP . 'logs/log.txt', "a+");        
	fwrite($arch, "[".date("Y-m-d H:i:s.u")." ".$_SERVER['REMOTE_ADDR']." ".
                   " - $tipo ] ".$cadena."\n");
        fwrite($arch, '----------\n');
	fclose($arch);
    }    
    /**
     * Analiza si se envia a traves de un parametro get un error HTTP
     */
    function catch_server_error(){
        if(isset($_GET['error_apache_enola'])){
            //Cargo el archivo con los errores
            $errores= load_framework_file('information/errorsHTTP.ini');
            $errores= parse_properties($errores);
            //Escribo el Log
            write_log('error_http', $errores[$_GET['error_apache_enola']]);
            //Muestro el error correspondiente
            general_error('Error ' . $_GET['error_apache_enola'], $errores[$_GET['error_apache_enola']] , 'general_error', $_GET['error_apache_enola']);
            //No continuo la ejecucion
            exit;
        }
    }    
    /*
     * Sector Informacion
     * Este modulo contiene funciones utilizadas por el framework para mostrar informacion al usuario
     */
    /**
     * Muestra un mensaje al usuario
     * @param string $titulo
     * @param string $mensaje
     */ 
    function display_information($title, $message){
        require_once PATHFRA . 'information/information.php';
    }
?>
