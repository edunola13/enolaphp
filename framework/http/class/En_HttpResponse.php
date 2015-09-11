<?php
namespace Enola\Http;
use Enola\Support\Response;

/**
 * Esta clase representa una respuesta HTTP y por lo tanto provee todas las propiedades basicas de una respuesta HTTP como
 * asi tambien propiedades de respuesta propias del framework.
 * Ademas provee comportamiento basico para redireccionar solicitudes. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
class En_HttpResponse extends Response{
    protected $httpRequest;
    /**
     * Constructor
     */
    public function __construct($request) {
        $this->httpRequest= $request;
        self::$instance= $this;
    }
    /**
     * Devuelve el codigo de respuesta actual
     * @return int
     */
    public function getStatusCode(){
        return http_response_code();
    }
    /**
     * Setea el codigo de respuesta
     * @param int $code
     */
    public function setStatusCode($code){
        http_response_code($code);
    }
    /*
     * Retorna si la respuesta esta todo bien. digamos si el codigo de respuesta es 200
     * @return boolean
     */
    public function isOk(){
        return (http_response_code() == 200);
    }
    /*
     * Devuelve los headers de la peticion
     * @return array
     */
    public function getHeaders(){
        //getallheaders() ver esto
        return headers_list();
    }
    /**
     * Setea los headers de la respuesta
     * @param string $header
     */
    public function setHeaders($header){
        header($header);
    }
    /**
     * Setea un parametro del header de la respuesta
     * @param string $name
     * @param string $value
     */
    public function setHeader($name, $value){
        header($name . ': ' . $value);
    }
    /**
     * Remueve el parametro indicado o todos los parametros del header de la respuesta
     * @param string $name
     */
    public function removeHeader($name = NULL){
        header_remove($name);
    }
    /**
     * Devuelve todas las cookies
     * @return type
     */
    public function getCookies(){
        return $_COOKIE;        
    }
    /**
     * Devuelve la cookie asociado con un nombre.
     * @param string $name
     * @return type
     */
    public function getCookie($name){
        if(isset($_COOKIE[$name])){
            return $_COOKIE[$name];
        }else{
            return NULL;
        }
    }
    /**
     * Setea una cookie
     * @param string $name
     * @param string $value
     * @param int $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return bool
     */
    public function setCookie($name, $value, $expire=0, $path="/", $domain="", $secure=FALSE, $httponly= FALSE){
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
    public function setExpires($expire=0){
        header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + ($expire)).'GMT');
    }
    /**
     * Setea el tipo de contenido a enviar
     * @param string $contentType
     * @param string $charset
     */
    public function setContentType($contentType, $charset=NULL){
        $this->setHeader("Content-type", $contentType);
        if($charset != NULL){
            $this->setHeader("charset", $charset);
        }
    }
    /**
     * Retorna si el header de la respuesta ya fue enviada
     * @return boolean
     */
    public function isSent(){
        return headers_sent();
    }
    /**
     * Envia un archivo como respuesta. Se indican distintos parametros del header
     * @param string $file
     * @param string $name
     * @param string $contentType
     * @param string $contentDisposition
     */
    public function sendFile($file, $name=NULL, $contentType='application/octet-stream', $contentDisposition='attachment'){
        if($name == NULL){
            $name= basename($file);
        }
        header('Content-Description: File Transfer');
        header('Content-Type: '.$contentType);
        header('Content-Disposition: '.$contentDisposition.'; filename="'.$name.'"');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);        
    }
    /**
     * Redirecciona a otra pagina pasando una uri relativa a la aplicacion
     * @param string $uri
     */
    public function redirect($uri){
        UrlUri::redirect($this->httpRequest, $uri);
    }
    /**
     * Redirecciona a una pagina externa a la aplicacion actual
     * @param string $url
     */
    public function external_redirect($url){
        UrlUri::externalRedirect($url);
    }
}