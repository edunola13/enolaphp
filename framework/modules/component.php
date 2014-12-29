<?php
    /**
     * Importa todo lo necesario para manejar los componentes
     * Contiene funciones para manejar los componentes por el framework y por el usuario
     */
    //Interface y Clase de la que deben extender todos los components
    require PATHFRA . 'classes/Component.php';
    require PATHFRA . 'classes/En_Component.php';
    
    /**
     * Analiza si mapea la URL de componentes
     * @return boolean 
     */
    function maps_components(){
        $partes_uri= explode("/", URIAPP);
        if($partes_uri[0] == URL_COMPONENT){
            return TRUE; 
        }
        return FALSE;
    }
    
    /**
     * ejecuta el componente en base a una URL 
     */
    function execute_url_component(){
        $partes_uri= explode("/", URIAPP);
        $nombre= "";
        $params= array();
        if(count($partes_uri) > 1){
            $nombre= $partes_uri[1];
            unset($partes_uri[0]);
            unset($partes_uri[1]);
            //Consigue los parametros
            foreach ($partes_uri as $value) {
                $params[]= $value;
            }
        }
        if($nombre != ""){
            //Evalua si el componente existe y si se encuentra habilitado via URL
            if(isset($GLOBALS['componentes'][$nombre])){
                $comp= $GLOBALS['componentes'][$nombre];
                if($comp['enabled-url'] == 'TRUE' || $comp['enabled-url'] == 'true'){
                    execute_component($nombre, $params, TRUE);
                }
                else{
                    echo "The component is disabled via URL";
                }
            }
            else{
                echo "There isent a component with the name: " + $nombre;
            }
        }
        else{  
            echo "Enola Components";
        }
    }
    
    /**
     * Ejecuta el metodo renderizar de un componente
     * @param type $nombre
     * @param type $parametros
     * @param type url
     */ 
    function execute_component($nombre, $parametros = NULL, $url = FALSE){
        $componente= NULL;
        if(isset($GLOBALS['componentes'][$nombre])){
            $comp= $GLOBALS['componentes'][$nombre];
            $dir= "";
            if(! isset($comp['location'])){
                $dir= PATHAPP . 'source/components/' . $comp['class'] . '.php';
            }
            else{
                $dir= PATHAPP . $comp['location'] . '/' . $comp['class'] . '.php';
            }
            require_once $dir;
            $dir= explode("/", $comp['class']);
            $class= $dir[count($dir) - 1];
            $componente= new $class();
        }
        if($componente != NULL){
            //Analiza si existe el metodo filtrar
            if(method_exists($componente, 'rendering')){
                if($parametros == NULL){
                    return $componente->rendering();
                }
                else{
                    return $componente->rendering($parametros);
                }
            }
            else{
                general_error('Component Error', 'The component ' . $nombre . ' dont implement the method rendering()');
            }          
        }
        else{
            general_error('Component Error', "The component $nombre dont exists");
        }
    }
?>