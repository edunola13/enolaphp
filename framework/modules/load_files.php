<?php
    /*
     * Conjunto de funciones para cargar archivos del framewrok y de la aplicacion
     */
    /**
     * Carga una libreria pasando una direccion
     * @param string $dir
     */
    function import_librarie($dir){
        $dir= PATHAPP . 'libraries/' . $dir . '.php';
        require_once $dir;
    }
    /**
     * Carga un archivo de la aplicacion pasando una direccion
     * @param string $dir
     */
    function import_aplication_file($dir){
        $dir= PATHAPP . $dir . '.php';
        require_once $dir;
    }    
    /**
     * Carga un archivo que luego podras ser asignado a una variable
     * @param string $dir
     * @return type
     */
    function load_application_file($dir){
        $dir= PATHAPP . $dir;
        return file($dir);
    }    
    /**
     * Carga un archivo de configuracion que luego podra ser asignado a una variable
     * @param string $dir
     * @return type
     */
    function load_application_config_file($dir){
        $dir= PATHAPP . $dir;
        return parse_ini_file($dir);
    }   
    /**
     * Carga un archivo que luego podras ser asignado a una variable
     * @param string $dir
     * @return type
     */
    function load_framework_file($dir){
        $dir= PATHFRA . $dir;
        return file($dir);
    }    
    /**
     * Carga un archivo de configuracion que luego podras ser asignado a una variable
     * @param string $dir
     * @return type
     */
    function load_frameworks_config_file($dir){
        $dir= PATHFRA . $dir;
        return parse_ini_file($dir);
    }       
    /*
     * Recorre las librerias y analiza si carga o no la libreria en la determinada clase
     * Es llamado por el controlador en su construccion para cargar las librerias correspondientes
     * Esta funcion supone que la ibreria ya se encuentra importada
     */
    function load_librarie_in_class($object, $type){
        //Analiza las librerias
        foreach ($GLOBALS['libraries_file'] as $name => $libreria) { 
            if(isset($libreria['load_in'])){
                //Si esta seteada la variable cargar_en y contiene la definicion $type carga la libreria
                if(strpos($libreria['load_in'], $type) !== FALSE){
                    $dir= $libreria['class'];
                    $dir= explode("/", $dir);
                    $class= $dir[count($dir) - 1];
                    add_instance($class, $object, $name);
                }
            }
        }
    }    
    /*
     * Carga la instancia de objeto en una variable del objeto pasado como parametro
     * Supone que la clase ya se encuentra importada
     */
    function add_instance($class, $obj, $name = ""){
        if($name == ""){
            $name= $class;
        }
        $obj->$name= new $class();
    }
?>