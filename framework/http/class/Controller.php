<?php
namespace Enola\Http;

/**
 * Esta interface establece los metodos que debe proveer un Controller que responde peticiones HTTP para que el framework 
 * lo pueda administrar correctamente. * 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
interface Controller {    /**
     * Atiende la peticion HTTP de tipo GET
     */
    /**
     * Atiende la peticion HTTP de tipo GET
     * @param \Enola\Http\En_HttpRequest $request
     * @param \Enola\Http\En_HttpResponse $response
     */
    public function doGet(En_HttpRequest $request, En_HttpResponse $response);    
    /**
     * Atiende la peticion HTTP de tipo POST
     * @param \Enola\Http\En_HttpRequest $request
     * @param \Enola\Http\En_HttpResponse $response
     */
    public function doPost(En_HttpRequest $request, En_HttpResponse $response);
    /**
     * Atiende la peticion HTTP de tipo DELETE
     * @param \Enola\Http\En_HttpRequest $request
     * @param \Enola\Http\En_HttpResponse $response
     */
    public function doDelete(En_HttpRequest $request, En_HttpResponse $response);
    /**
     * Atiende la peticion HTTP de tipo PUT
     * @param \Enola\Http\En_HttpRequest $request
     * @param \Enola\Http\En_HttpResponse $response
     */
    public function doPut(En_HttpRequest $request, En_HttpResponse $response);
    /**
     * Atiende la peticion HTTP de tipo HEAD
     * @param \Enola\Http\En_HttpRequest $request
     * @param \Enola\Http\En_HttpResponse $response
     */
    public function doHead(En_HttpRequest $request, En_HttpResponse $response);
    /**
     * Atiende la peticion HTTP de tipo TRACE
     * @param \Enola\Http\En_HttpRequest $request
     * @param \Enola\Http\En_HttpResponse $response
     */
    public function doTrace(En_HttpRequest $request, En_HttpResponse $response);
    /**
     * Atiende la peticion HTTP de tipo OPTIONS
     * @param \Enola\Http\En_HttpRequest $request
     * @param \Enola\Http\En_HttpResponse $response
     */
    public function doOptions(En_HttpRequest $request, En_HttpResponse $response);
    /**
     * Atiende la peticion HTTP de tipo CONNECT
     * @param \Enola\Http\En_HttpRequest $request
     * @param \Enola\Http\En_HttpResponse $response
     */
    public function doConnect(En_HttpRequest $request, En_HttpResponse $response);
    /**
     * Setea los uri_params de la peticion actual
     * @param type $uri_params
     */
    public function setUriParams($uri_params);
}