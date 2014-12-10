<?php
/**
 * 
 * @author Enola
 */
class EnolaMVC implements Controller{
    
    /**
     *Devuelve la uriapp relativa desde la bse url en la que trabaja el MVC 
     */
    private function uriapp_mvc($base_mvc){
        //Armo la URI MVC entre la URIAPP y el BASE MVC        
        $base_mvc= trim($base_mvc, "/");
        $uriapp= "";
        if($base_mvc == ""){
            $uriapp= URIAPP;
        }
        else{
            $base_mvc= explode("/", $base_mvc);
            $count_base_mvc= count($base_mvc);
            $uriapp= explode("/", URIAPP);
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
     * false en caso contrario 
     */
    private function maps_controller($partes_url, $partes_uri_actual){
        $mapea= TRUE;
        //Mantiene la pos actual para luego empezar a recorrer el mensaje y los parametros
        $pos_actual= 0;
        if(count($partes_url) >= count($partes_uri_actual)){
            //Si el tamano de la url es igual o mayor que la uri actual uso el for recorriendo las partes de la url
            $count_partes_url= count($partes_url);
            for($i= 0; $i < $count_partes_url; $i++) {
                $pos_actual= $i;
                if(count($partes_uri_actual) >= ($i + 1)){
                    //Si hay un * no me importa que viene despues, filtra todo, no deberia haber nada despues
                    if($partes_url[$i] != "*"){
                        $pos_ocurrencia= strpos($partes_url[$i], "*");
                        if($pos_ocurrencia != FALSE){
                            $parte_url= explode("*", $partes_url[$i]);
                            $parte_url= $parte_url[0];
                            if(strlen($partes_uri_actual[$i]) >= strlen($parte_url)){
                                $parte_uri_actual= substr($partes_uri_actual[$i], 0, strlen($parte_url));
                                if($parte_url == $parte_uri_actual){
                                    break;
                                }
                                else{
                                    $mapea= FALSE;
                                    break;
                                }
                            }
                            else{
                                $mapea= FALSE;
                                break;
                            }
                        }
                        //Si alguna esta vacia no compara el mapeo con () y voy directo a la comparacion
                        if(empty($partes_url[$i]) || empty($partes_uri_actual[$i])){
                            //Si no coinciden las partes el filtro no debe aplicarse
                            if($partes_url[$i] != $partes_uri_actual[$i]){
                                $mapea= FALSE;
                                break;
                            }
                        }
                        else{
                            //Si la parte de la uri empieza con ( y termina con ) puede ir cualquier string ahi por lo que pasa directamente esta parte de la validacion
                            if(! ($partes_url[$i]{0} == "(" and $partes_url[$i]{strlen($partes_url[$i]) -1} == ")")){
                                //Si no contiene ( y ) debe mapear
                                //Si no coinciden las partes no mapea
                                if($partes_url[$i] != $partes_uri_actual[$i]){
                                    $mapea= FALSE;
                                    break;
                                }
                            }
                        }
                    }
                    else{
                        $pos_actual+= 1;
                        break;
                    }
                }
                else{
                    //La uri actual no tiene mas partes y no hay coincidencia completa
                    $mapea= FALSE;
                    break;
                }
            }            
        }
        else{
            $count_partes_uri_actual= count($partes_uri_actual);
            for($i= 0; $i < $count_partes_uri_actual; $i++){            
                $pos_actual= $i;
                if(count($partes_url) >= ($i + 1)){                
                    //Si hay un * no me importa que viene despues, filtra todo, no deberia haber nada despues
                    if($partes_url[$i] != "*"){
                        $pos_ocurrencia= strpos($partes_url[$i], "*");
                        if($pos_ocurrencia != FALSE){
                            $parte_url= explode("*", $partes_url[$i]);
                            $parte_url= $parte_url[0];
                            if(strlen($partes_uri_actual[$i]) >= strlen($parte_url)){
                                $parte_uri_actual= substr($partes_uri_actual[$i], 0, strlen($parte_url));
                                if($parte_url == $parte_uri_actual){
                                    break;
                                }
                                else{
                                    $mapea= FALSE;
                                    break;
                                }
                            }
                            else{
                                $mapea= FALSE;
                                break;
                            }
                        }
                        //Si alguna esta vacia no compara el mapeo con () y voy directo a la comparacion
                        if(empty($partes_url[$i]) || empty($partes_uri_actual[$i])){
                            //Si no coinciden las partes el filtro no debe aplicarse
                            if($partes_url[$i] != $partes_uri_actual[$i]){
                                $mapea= FALSE;
                                break;
                            }
                        }
                        else{
                            //Si la parte de la uri empieza con ( y termina con ) puede ir cualquier string ahi por lo que pasa directamente esta parte de la validacion
                            if(! ($partes_url[$i]{0} == "(" and $partes_url[$i]{strlen($partes_url[$i]) -1} == ")")){
                                //Si no contiene ( y ) debe mapear
                                //Si no coinciden las partes el filtro no debe aplicarse
                                if($partes_url[$i] != $partes_uri_actual[$i]){
                                    $mapea= FALSE;
                                    break;
                                }
                            }
                        }
                    }
                    else{
                        $pos_actual+= 1;
                        break;
                    }
                }
                else{
                    $mapea= TRUE;
                    break;
                }
            }
        }
        if($mapea){
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

            //Saco de la uri actual los parametros
            $uri_explode= explode("?", $uriapp);
            $uri_front= $uri_explode[0];
            //Separo la uri actual
            $partes_uri_actual= explode("/", $uri_front);        
            //Llama al metodo para ver si mapea
            $mapea= $this->maps_controller($partes_url, $partes_uri_actual);
                        
            if($mapea !== FALSE){
                //Mapea contiene la posicion
                $pos_actual= $mapea;
                //Sacar el nombre del mensaje
                $mensaje= "";
                if(count($partes_uri_actual) == count($partes_url)){
                    $mensaje= 'index';
                }
                else{
                    $mensaje= $partes_uri_actual[$pos_actual];
                }
                //Consigue la clase del controlador, analiza que contenga el metodo y lo ejecuta pasandole los
                //parametros correspondiente
                $dir= PATHAPP . 'source/controllers/' . $controller['class'] . '.php';
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
                //Si el mensaje no existe pasa al proximo controlador
            }
        }        
        if(! $ejecutado){
            general_error('Error Enola MVC', 'Any controller map with the actual requirement');
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
}

?>
