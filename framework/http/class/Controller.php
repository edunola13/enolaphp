<?php
namespace Enola\Http;

/**
 * Esta interface establece los metodos que debe proveer un Controller que responde peticiones HTTP para que el framework 
 * lo pueda administrar correctamente. * 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
interface Controller {
    /**
     * Atiende la peticion HTTP de tipo GET
     */
    public function doGet();    
    /**
     * Atiende la peticion HTTP de tipo POST
     */
    public function doPost();
    /**
     * Atiende la peticion HTTP de tipo DELETE
     */
    public function doDelete();
    /**
     * Atiende la peticion HTTP de tipo PUT
     */
    public function doPut();
    /**
     * Atiende la peticion HTTP de tipo HEAD
     */
    public function doHead();
    /**
     * Atiende la peticion HTTP de tipo TRACE
     */
    public function doTrace();
    /**
     * Atiende la peticion HTTP de tipo OPTIONS
     */
    public function doOptions();
    /**
     * Atiende la peticion HTTP de tipo CONNECT
     */
    public function doConnect();
    /**
     * Setea los uri_params de la peticion actual
     * @param type $uri_params
     */
    public function setUriParams($uri_params);
}