<?php
    namespace Enola;

    /*
     * Este modulo Maneja los errores de la aplicacion
     * Contiene tambien la seccion de Informacion del Framework hacia el usuario
     */    
    /**
     * Funcion para manejar los errores php. 
     * Esta se superpone a la propia de php cuando es seteada en el nucleo.php
     * @param $level
     * @param string $message
     * @param string $file
     * @param int $line
     * @return boolean
     */
    function _error_handler($level, $message, $file, $line){
        if (!(error_reporting() & $level)) {
            //Agrega el Log
            Error::write_log($message, 'Level Error: '. $level, $file, $line);
            // Segun el nivel de error veo si agarro o no la excepcion. si entra aca no hago nada
            return;
        }
        //Analizo el error que se produjo y aviso del mismo.
        //Segun el error termino el flujo de ejecucion o continua
        switch ($level) {
            case E_USER_ERROR:
                Error::error_php('Error', $level, $message, $file, $line);
                exit(1);
                break;

            case E_USER_WARNING:
                Error::error_php('Warning', $level, $message, $file, $line);
                break;

            case E_USER_NOTICE:
                Error::error_php('Notice', $level, $message, $file, $line);
                break;

            default:
                Error::error_php('Unknown', $level, $message, $file, $line);
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
                    Error::write_log($e['message'], $e['type'], $e['file'], $e['line']);
                }
                else{
                    Error::error_php('Error Fatal - Parse - Strict', $e['type'], $e['message'], $e['file'], $e['line']);
                }
            }
        }
    }
    
    //Define un manejador de excepciones - definido en el modulo errores
    set_error_handler('Enola\_error_handler');
    //Define un manejador de fin de cierre - definido en el modulo de errores
    register_shutdown_function('Enola\_shutdown'); 
    /**
     * Esta clase prove funciones para registrar los errores
     * Como no es necesario mantener ningun estado los metodos se pueden acceder estaticamente
     * @author Eduardo Sebastian Nola <edunola13@gmail.com>
     * @category Enola\Support
     */
    class Error{
        /**
         * Crea una respuesta de error php - usada por el manejador de errores definido por el framework
         * Escribe en log
         * @param string $type
         * @param $level
         * @param string $message
         * @param string $file
         * @param int $line
         */
        public static function error_php($type, $level, $message, $file, $line){
            self::write_log($message, $type, $file, $line);
            if(error_reporting()){
                include PATHAPP . 'errors/error_php.php';
            }
        }    
        /**
         * Crea una respuesta de error 404
         * Usada por el framework y/o el usuario
         * Escribe en log
         */
        public static function error_404(){
            $head= '404 Pagina no Encontrada';
            $message= 'La pagina que solicitaste no existe';
            Http\UrlUri::setEstadoHeader(404);
            include PATHAPP . 'errors/error_404.php';
            exit;
        }    
        /**
         * Crea una respuesta de error general
         * Usada por el framework y/o el usuario
         * Escribe en log
         * @param string $head
         * @param string $message
         * @param string $template
         * @param int $code_error Solo aplica si esta en modo HTTP
         */
        public static function general_error($head, $message, $template = 'general_error', $code_error = 500){
            self::write_log($message, 'General Error');
            if(ENOLA_MODE == 'HTTP'){Http\UrlUri::setEstadoHeader($code_error);}
            if(error_reporting()){
                include PATHAPP . 'errors/' . $template . '.php'; 
            }        
        }
        /**
         * Crea o abre un archivo de log y escribe el error correspondiente
         * Escribe en log
         * @param String $chain
         * @param String $type
         * @param string $file
         * @param string $line
         */
        public static function write_log($chain, $type, $file="", $line=""){
            /*if(filesize(PATHAPP . 'logs/log.txt') > 100000){           
                $arch= fopen(PATHAPP . 'logs/log.txt', "w");
                fclose($arch); 
            }*/
            $arch = fopen(PATHAPP . 'logs/log-' . date('Y-m-d') . '.txt', "a+"); 
            if(ENOLA_MODE == 'HTTP'){
                fwrite($arch, "[".date("Y-m-d H:i:s.u")." ".filter_input(INPUT_SERVER, 'REMOTE_ADDR')." "." - $type ] ".$chain." - $file - $line \n");
            }else{
                fwrite($arch, "[".date("Y-m-d H:i:s.u")." MODE CLI "." - $type ] ".$chain." - $file - $line \n");
            }
            fwrite($arch, "---------- \n");
            fclose($arch);
        }    
        /**
         * Analiza si se envia a traves de un parametro get un error HTTP
         * Escribe en log
         */
        public static function catch_server_error(){
            $enolaError= filter_input(INPUT_GET, 'error_apache_enola');
            if($enolaError){
                //Cargo el archivo con los errores
                $errores= \E_fn\load_framework_file('information/errorsHTTP.ini');
                $errores= \E_fn\parse_properties($errores);
                //Escribo el Log
                self::write_log('error_http', $errores[$enolaError]);
                //Muestro el error correspondiente
                self::general_error('Error ' . $enolaError, $errores[$enolaError] , 'general_error', $enolaError);
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
         * @param string $title
         * @param string $message
         */ 
        public static function display_information($title, $message){
            include PATHFRA . 'information/information.php';
        }
    }