<?php
session_start();
/**
 * Libreria que maneja los datos y la seguridad de la sesion
 * @author Enola
 */
class Session {    
    /**
     * Constructor que realiza la comprobacion de identidad
     */
    public function __constructor(){
        $this->checkIdentity();
    }    
    /**
     * Setea un dato a la sesion
     * @param string $nombre
     * @param DATO $valor
     */
    public function set($name,$value){
        $_SESSION[$name] = $value;
    }
    /**
     * Setea un dato a la sesion serializandolo previamente
     * @param type $nombre
     * @param type $valor
     */
    public function setSerialize($name,$value){
        $_SESSION[$name]= serialize($value);
    }
    /**
     * Devuelve un dato de la sesion o NULL si no existe
     * @param string $nombre
     * @return NULL o DATO
     */
    public function get($name){
        if (isset ($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        else {
            return NULL;
        }
    }
    /**
     * Devuelve un dato deserializado de la sesion o NULL si no existe
     * @param type $name
     * @return null 
     */
    public function getUnserialize($name){
        if (isset ($_SESSION[$name])) {
            return unserialize($_SESSION[$name]);
        }
        else {
            return NULL;
        }
    }
    /**
     * Analza si existe un determinado dato asociado a la sesion
     * @param string $nombre
     * @return boolean
     */
    public function exist($name){
        if (isset ($_SESSION[$name])) {
            return TRUE;
        }
        else{
            return FALSE;
        }
    }    
    /**
     * Borra un dato asociado a la sesion
     * @param string $nombre
     */
    public function unsetVar($name){
        unset ($_SESSION[$name] ) ;
    }    
    /**
     * Borra la sesion
     */
    public function deleteSession(){
        $_SESSION = array() ;
        session_destroy();
    }    
    /**
     * Realiza una comprobacion de identidad
     * Analiza que no se este suplantando la identidad del verdadero usuario
     */
    private function checkIdentity(){
        if(isset($_SESSION['REMOTE_ADDR']) && isset($_SESSION['HTTP_USER_AGENT'])){
            if($_SESSION['REMOTE_ADDR'] != $_SERVER['REMOTE_ADDR'] || $_SESSION['HTTP_USER_AGENT'] != $_SERVER['HTTP_USER_AGENT']) {
                general_error('Session - Identity', 'There are a proble with the Sesion identity');
            }
        }
        else{
            $_SESSION['REMOTE_ADDR'] = $_SERVER['REMOTE_ADDR'];
            $_SESSION['HTTP_USER_AGENT'] = $_SERVER['HTTP_USER_AGENT'];
        }
    }    
}
?>