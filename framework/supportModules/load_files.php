<?php
    /*
     * Conjunto de funciones para cargar archivos y clases del framewrok y de la aplicacion
     */
    /**
     * Carga una libreria pasando una direccion desde librearies folder
     * @param string $dir
     */
    function import_librarie($dir){
        $dir= PATHAPP . 'libraries/' . $dir . '.php';
        require_once $dir;
    }
    /**
     * Cargo una libreria de composer pasando una direccion desde vendor folder
     * @param string $dir
     */
    function import_librarie_composer($dir){
        $dir= EnolaContext::getInstance()->getPathRoot() . 'vendor/' . $dir . '.php';
        require_once $dir;
    }
    /**
     * Carga un archivo de la aplicacion pasando una direccion desde application folder
     * @param string $dir
     */
    function import_aplication_file($dir){
        $dir= PATHAPP . $dir . '.php';
        require_once $dir;
    }
    /**
     * Carga un archivo que luego podras ser asignado a una variable desde application folder
     * @param string $dir
     * @param boolean $byLine
     * @return string or array[string]
     */
    function load_application_file($dir, $byLine = TRUE){
        $dir= PATHAPP . $dir;
        if($byLine){
            return file($dir);
        }else{
            return file_get_contents($dir);
        }
    }    
    /**
     * Carga un archivo de configuracion que luego podra ser asignado a una variable desde application folder
     * @param string $dir
     * @return array
     */
    function load_application_config_file($dir){
        $dir= PATHAPP . $dir;
        return parse_ini_file($dir);
    }   
    /**
     * Carga un archivo que luego podras ser asignado a una variable desde framework folder
     * @param string $dir
     * @param boolean $byLine
     * @return string or array[string]
     */
    function load_framework_file($dir, $byLine = TRUE){
        $dir= PATHFRA . $dir;
        if($byLine){
            return file($dir);
        }else{
            return file_get_contents($dir);
        }
    }    
    /**
     * Carga un archivo de configuracion que luego podras ser asignado a una variable desde framework folder
     * @param string $dir
     * @return array
     */
    function load_frameworks_config_file($dir){
        $dir= PATHFRA . $dir;
        return parse_ini_file($dir);
    }
    /**
     * Carga la instancia de una clase pasada como parametro en una variable del objeto pasado como parametro.
     * Supone que la clase ya se encuentra importada. 
     * @param string $class
     * @param type $obj
     * @param string $name
     */
    function add_property_instance($class, $obj, $name = ""){
        if($name == ""){
            $name= $class;
        }
        $obj->$name= new $class();
    }