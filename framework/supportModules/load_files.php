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
     * @return type
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
     * @return type
     */
    function load_frameworks_config_file($dir){
        $dir= PATHFRA . $dir;
        return parse_ini_file($dir);
    }
    /**
     * Recorre las librerias y analiza si carga o no la libreria en la determinada clase.
     * Es llamado por el Loader en su construccion para inyectar las librerias correspondientes.
     * Esta funcion supone que la libreria ya se encuentra importada.
     * @param type $object
     * @param type $type
     */
    function load_libraries_in_class($object, $type){
        //Analiza las librerias que tienen seteado "load_in"
        foreach (EnolaContext::getInstance()->getLoadLibraries() as $name => $librarie) {
            $types= explode(",", $librarie['load_in']);
            //Si la libreria contiene el tipo se carga
            if(in_array($type, $types)){
                //Veo si tiene namespace y si tiene le agrego el mismo
                $namespace= (isset($librarie['namespace']) ? $librarie['namespace'] : ''); 
                $dir= explode("/", $librarie['class']);
                $class= $dir[count($dir) - 1];
                if($namespace != '') $class= "\\" . $namespace . "\\" . $class;
                add_instance($class, $object, $name);
            }
        }
    }
    /**
     * Carga la instancia de una clase pasada como parametro en una variable del objeto pasado como parametro.
     * Supone que la clase ya se encuentra importada. 
     * @param type $class
     * @param type $obj
     * @param type $name
     */
    function add_instance($class, $obj, $name = ""){
        if($name == ""){
            $name= $class;
        }
        $obj->$name= new $class();
    }