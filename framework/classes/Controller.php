<?php
/**
 * @author Enola
 */
interface Controller {
    /**
     * Funcion que es llamada cuando el metodo HTTP es GET
     */
    public function doGet();    
    /**
     * Funcion que es llamada cuando el metodo HTTP es POST
     */
    public function doPost();
    /**
     * Funcion que es llamada cuando el metodo HTTP es DELETE
     */
    public function doDelete();
    /**
     * Funcion que es llamada cuando el metodo HTTP es PUT
     */
    public function doPut();
    /**
     * Funcion que es llamada cuando el metodo HTTP es HEAD
     */
    public function doHead();
    /**
     * Funcion que es llamada cuando el metodo HTTP es TRACE
     */
    public function doTrace();
    /**
     * Funcion que es llamada cuando el metodo HTTP es OPTIONS
     */
    public function doOptions();
    /**
     * Funcion que es llamada cuando el metodo HTTP es CONNECT
     */
    public function doConnect();
}
?>
