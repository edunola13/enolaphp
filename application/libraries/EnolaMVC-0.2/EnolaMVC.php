<?php
/**
 * 
 * @author Enola
 */
class EnolaMVC implements Controller{
    public $uriapp_completa;
    public $folder_controllers;
    
    public function __construct() {
        $this->uriapp_completa= URIAPP;
        $this->folder_controllers= PATHAPP . 'source/controllers';
    }
    
    /**
     * Devuelve la uriapp relativa desde la base url en la que trabaja el MVC 
     */
    private function uriapp_mvc($base_mvc){
        //Armo la URI MVC entre la URIAPP y el BASE MVC        
        $base_mvc= trim($base_mvc, "/");
        $uriapp= "";
        if($base_mvc == ""){
            $uriapp= $this->uriapp_completa;
        }
        else{
            $base_mvc= explode("/", $base_mvc);
            $count_base_mvc= count($base_mvc);
            $uriapp= explode("/", $this->uriapp_completa);
            $new_uri= "";
            $count_uriapp= count($uriapp);
            for($j= 1; $j < $count_uriapp; $j++){
                if($j + 1 > $count_base_mvc){
                    $new_uri.= "/" . $uriapp[$j];
                }
            }
            $new_uri= trim($new_uri, "/");
            $uriapp= $new_uri;
        }        
        return $uriapp;
    }
    
    /**
     * Devuelve la posicion actual en la queda el indice en el arreglo partes_uri_actual si mapea
     * FALSE en caso contrario 
     */
    private function maps_controller($partes_url, $partes_uri_actual){
        $mapea= TRUE;
        //Mantiene la pos actual para luego empezar a recorrer el mensaje y los parametros
        $pos_actual= 0;
        if(count($partes_uri_actual) >= count($partes_url)){
            $count_partes_uri_actual= count($partes_uri_actual);
            //Recorro todas las partes de la uri actual
            for($i= 0; $i < $count_partes_uri_actual; $i++){            
                $pos_actual= $i;
                //Si partes url tiene partes se controla
                if(count($partes_url) >= ($i + 1)){                
                    if($partes_url[$i] != $partes_uri_actual[$i]){
			$mapea= FALSE;
			break;
                    }
                }
                else{
                    //Si no tiene mas partes coinciden
                    $mapea= TRUE;
                    break;
                }
            }
        }
	else{
            //Si es mas grande la url del archivo de configuracion no coinciden
            $mapea= FALSE;
	}
        if($mapea){
            //Devuelve la posicion actual de donde termina la coincidencia
            return $pos_actual;
        }
        else{
            return FALSE;
        }
    }
    
    
    public function doGet(){        
        if(! isset($this->config)){
            $this->config= 'enolamvc.json';
        }
        $json_configuration= file_get_contents(PATHAPP . CONFIGURATION . $this->config);
        $config= json_decode($json_configuration, true);
        //Base desde donde trabaja el MVC
        $base_mvc= $config['base_url'];
        //URIAPP relativa al MVC        
        $uriapp= $this->uriapp_mvc($base_mvc);
        /*
         * Analizo los controladores para ver cual mapea
         */
        $ejecutado= FALSE;
        foreach ($config['controllers'] as $controller) {
            $url= $controller['url'];
            $url= trim($url, "/"); 
            $partes_url= explode("/", $url);
            if($partes_url[0] == ''){
                $partes_url= array();
            }

            //Saco de la uri actual los parametros
            $uri_explode= explode("?", $uriapp);
            $uri_front= $uri_explode[0];
            //Separo la uri actual
            $partes_uri_actual= explode("/", $uri_front);    
            if($partes_uri_actual[0] == ''){
                $partes_uri_actual= array();
            }
            //Llama al metodo para ver si mapea
            $mapea= $this->maps_controller($partes_url, $partes_uri_actual);
                        
            if($mapea !== FALSE){
                //Mapea contiene la posicion
                $pos_actual= $mapea;
                //Sacar el nombre del mensaje
                $mensaje= "";
                if(count($partes_uri_actual) == count($partes_url) || count($partes_uri_actual) == 0){
                    $mensaje= 'index';
                }
                else{
                    $mensaje= $partes_uri_actual[$pos_actual];
                }
                //Consigue la clase del controlador, analiza que contenga el metodo y lo ejecuta pasandole los
                //parametros correspondiente
                $dir= $this->folder_controllers . '/' . $controller['class'] . '.php';
                require $dir;
                $dir= explode("/", $controller['class']);
                $class= $dir[count($dir) - 1];
                $controlador= new $class();
                if(method_exists($controlador, $mensaje)){
                    //Sacar los parametros
                    $pos_actual+= 1;
                    $params= array();
                    for($i= $pos_actual; $i < count($partes_uri_actual); $i++){
                        $params[]= $partes_uri_actual[$i];
                    }
                    $controlador->params= $params;
                    $controlador->$mensaje();
                    $ejecutado= TRUE;
                    break;
                }
                else{
                    unset($controlador);
                }
                //Si el mensaje no existe pasa al proximo controlador
            }
        }        
        if(! $ejecutado){
            throw new Exception('Error Enola MVC - ' . 'Any controller map with the actual requirement');
        }
    }
    
    public function doPost(){
        $this->doGet();
    }    
    public function doPut(){
        $this->doGet();
    }    
    public function doDelete(){
        $this->doGet();
    }    
    public function doHead(){
        $this->doGet();
    }
    public function doTrace(){
        $this->doGet();
    }
    public function doOptions(){
        $this->doGet();
    }  
    public function doConnect(){
        $this->doGet();
    }
    /**
     * Setea los uri params
     * @param type $uri_params
     */
    public function setUriParams($uri_params){
        $this->uri_params= $uri_params;
    }
}

?>
