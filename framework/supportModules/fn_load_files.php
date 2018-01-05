<?php
namespace E_fn;
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
 * Carga todos los archivos de una determinada carpeta de la aplicacion pasando una direccion desde application folder
 * @param string $dir
 */
function import_aplication_folder($dir){
    $dir= PATHAPP . $dir;
    $dir= rtrim($dir, '/') . '/';
    foreach (glob($dir."*.php") as $filename){
        require_once $filename;
    }
}
/**
 * Carga todos los archivos de una determinada carpeta y sus subcarpetas de la aplicacion pasando una direccion desde application folder
 * @param string $dir
 */
function import_aplication_folder_subfolders($dir){
    $dir= PATHAPP . $dir;
    $dir= rtrim($dir, '/') . '/';
    $dh= opendir($dir);
    $dir_list= array($dir);
    while (false !== ($filename = readdir($dh))) {
        if($filename!="." && $filename!=".." && is_dir($dir.$filename)){
            array_push($dir_list, $dir.$filename . "/");
        }
    }
    foreach ($dir_list as $dir) {        
        foreach (glob($dir."*.php") as $filename){
            require_once $filename;
        }
    }
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
/**
 * Lee archivos de una carpeta, se le puede indicar que traiga tambien de las subcarpetas y por determinadas extensiones
 * @param string $folder
 * @param bool $includeSubFolders
 * @param string[] $extensions
 * @return string[]
 */
function get_files_from_folder($folder, $includeSubFolders= true, $extensions= null){
    $dir= PATHAPP . $folder;
    $dir= rtrim($dir, '/') . '/';
    $dh= opendir($dir);
    $dir_list= array();
    while (false !== ($filename = readdir($dh))) {
        if($filename!="." && $filename!=".." && $filename){
            if(!is_dir($dir.$filename)){
                array_push($dir_list, $dir.$filename . "/");
            }else if($includeSubFolders){
                $dir_list= array_merge($dir_list, get_files_from_folder($folder.$filename, true));
            }
        }
    }
    $matchList= array();
    foreach ($dir_list as $dir) {
        if($extensions == null){
            $matchList[]= $dir;
            continue;
        }
        //Si hay extensiones comparo
        $info= new \SplFileInfo($dir);
        if(in_array($info->getExtension(), $extensions)){
            $matchList[]= $dir;
        }
    }
    
    return $matchList;
}
/**
 * Retorna todas las carpetas de un directorio
 * @param string $folder
 * @return string[]
 */
function get_folders_from_folder($folder){
    $dir= PATHAPP . $folder;
    $dir= rtrim($dir, '/') . '/';
    $dh= opendir($dir);
    $dir_list= array();
    while (false !== ($filename = readdir($dh))) {
        if(is_dir($dir.$filename)){
            array_push($dir_list, $dir.$filename . "/");
        }
    }
    return $dir_list;
}