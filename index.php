<?php
    /**
     * Este archivo es el primero en ser llamado, carga la configuracion inicial y delega el trabajo a nucleo.php
     */    
    /**
     *  Path donde se encuentra la carpeta con todos los archivos del framework
     *  Si la carpeta es cambiada de lugar es necesario modificar esta variable
     */
    $path_framework= 'framework';
    if (realpath($path_framework) !== FALSE){
        //Asigna la direccion real de $path_framework
	$path_framework= realpath($path_framework).'/';
    }else{
        //Si fallo el realpath porque no tiene permisos lo armo en base al directorio actual
        $path_framework= realpath(dirname(__FILE__) . '/' . $path_framework . '/');
    }
    // Asegura que no quedan espacios en blanco
    $path_framework= rtrim($path_framework, '/').'/';    
    /**
     *  Path donde se encuentra la carpeta los archivos de la aplicacion que son usados por el framework
     *  Si la carpeta es cambiada de lugar es necesario modificar esta variable
     */
    $path_application= 'application';    
    if (realpath($path_application) !== FALSE){
        //Asigna la direccion real de $path_aplicacion
	$path_application= realpath($path_application).'/';
    }else{
        //Si fallo el realpath porque no tiene permisos lo armo en base al directorio actual
        $path_application= realpath(dirname(__FILE__) . '/' . $path_application . '/');
    }    
    // Asegura que no quedan espacios en blanco
    $path_application= rtrim($path_application, '/').'/';
    //Path Root
    $path_root= realpath(dirname(__FILE__)) . '/';  
    /**
     * Delega el trabajo al nucleo del framework
     */
    require $path_framework . 'core.php';