<?php
    namespace Enola\Http;
    use Enola\Error;

    /*
     * Este modulo se encarga de cargar todas las clases necesarias para los requerimientos HTTP
     * Esto incluye los filtros , los controladores y todos los datos de los mismo.
     */
    require 'class/Session.php';
    require 'class/En_HttpRequest.php';    
    /*
     * Seccion de Filtros
     */
    //Interface y Clase base de la que deben extender todos los filtros
    require 'class/Filter.php';
    require 'class/En_Filter.php';
    /**
     * Analiza los filtros correspondientes y ejecuta los que correspondan
     * @param array[array] $filtros
     */
    function execute_filters($filtros, $uriapp = NULL){
        //Analizo los filtros y los aplico en caso de que corresponda
        foreach ($filtros as $filtro_esp) {
            $filtrar= maps_actual_url($filtro_esp['filtered'], $uriapp);
            //Si debe filtrar carga el filtro correspondiente y realiza el llamo al metodo filtrar()
            if($filtrar){
                $dir= "";
                if(! isset($filtro_esp['location'])){
                    $dir= PATHAPP . 'source/filters/' . $filtro_esp['class'] . '.php';
                }
                else{
                    $dir= PATHAPP . $filtro_esp['location'] . '/' . $filtro_esp['class'] . '.php';
                }
                //Analiza si existe el archivo
                if(file_exists($dir)){
                    require_once $dir;
                    $dir= explode("/", $filtro_esp['class']);
                    $class= $dir[count($dir) - 1];
                    $filtro= new $class();
                    //Analiza si existe el metodo filtrar
                    if(method_exists($filtro, 'filter')){
                        $filtro->filter();
                    }
                    else{
                        Error::general_error('Filter Error', 'The filter ' . $filtro_esp['class'] . ' dont implement the method filter()');
                    }
                }
                else{
                    Error::general_error('Filter Error', 'The filter ' . $filtro_esp['class'] . ' dont exist');
                }
            }
        }
    }    
    /**
     * Seccion controladores
     */
    //Interface y Clase base de la que deben extender todos los Controllers
    require 'class/Controller.php';
    require 'class/En_Controller.php';
    /**
     * Encuentra el controlador que mapea
     * @param type $controladores
     * @return type 
     */
    function mapping_controller($controladores, $uriapp = NULL){
        $mapea= FALSE;
        //Recorre todos los controladores hasta que uno coincida con la URI actual
        foreach ($controladores as $controlador_esp) {
            //Analiza si el controlador mapea con la uri actual
            $mapea= maps_actual_url($controlador_esp['url'], $uriapp);
            if($mapea){
                return $controlador_esp;
            }
        }
        //si ningun controlador mapeo avisa el problema
        if(! $mapea){
            Error::error_404();
        }
    }
    /**
     * Ejecuta el controlador que mapeo anteriormente
     * @param type $controlador_esp 
     */
    function execute_controller($controlador_esp, $uriapp = NULL){
        $dir= "";
        if(! isset($controlador_esp['location'])){
            $dir= PATHAPP . 'source/controllers/' . $controlador_esp['class'] . '.php';
        }
        else{
            $dir= PATHAPP . $controlador_esp['location'] . '/' . $controlador_esp['class'] . '.php';
        }
        $controlador= NULL;
        $dinamic_method= FALSE;
        $method= NULL;
        //Analiza si existe el archivo
        if(file_exists($dir)){
            require_once $dir;
            $dir= explode("/", $controlador_esp['class']);
            $class= $dir[count($dir) - 1];
            $controlador= new $class();
            //Agrego los parametros URI
            $uri_params= uri_params($controlador_esp['url'], $uriapp);
            $dinamic_method= $uri_params['dinamic'];
            $method= $uri_params['method'];
            $controlador->setUriParams($uri_params['params']);
            //Analizo si hay parametros en la configuracion
            if(isset($controlador_esp['params'])){
                foreach ($controlador_esp['params'] as $key => $value) {
                    $controlador->$key= $value;
                }
            }
        }
        else{
            //Avisa que el archivo no existe
            Error::general_error('Controller Error', 'The controller ' . $controlador_esp['class'] . ' dont exists');
        }        
        //Saca el metodo HTPP y en base a eso hace una llamada al metodo correspondiente
        $metodo= $_SERVER['REQUEST_METHOD'];
        if($dinamic_method){
            if(method_exists($controlador, $method)){
                $controlador->$method();
            }else{
                Error::general_error('HTTP Method Error', "The HTTP method $method is not supported");
            }
        }else{
           switch ($metodo) {
            case 'GET':
                $controlador->doGet();
                break;
            case 'POST':
                $controlador->doPost();
                break;
            case 'UPDATE':
                $controlador->doUpdate();
                break;
            case 'DELETE':
                $controlador->doDelete();
                break;
            case 'HEAD':
                $controlador->doHead();
                break;
            case 'TRACE':
                $controlador->doTrace();
                break;
            case 'URI':
                $controlador->doUri();
                break;
            case "OPTIONS":
                $controlador->doOptions();
                break;
            case 'CONNECT':
                $controlador->doConnect();
                break;
            default :                
                Error::general_error('HTTP Method Error', "The HTTP method $metodo is not supported");
            }
        }
    }