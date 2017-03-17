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
    /** Referencia al HttpRequest actual 
     * @var En_HttpRequest */
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
     * @return array
     */
    public function getCookies(){
        return filter_input_array(INPUT_COOKIE);        
    }
    /**
     * Devuelve la cookie asociado con un nombre.
     * @param string $name
     * @return array - null
     */
    public function getCookie($name){
        return filter_input(INPUT_COOKIE, $name);
    }
    /**
     * Setea parametros de cookie
     * @param int $lifetime
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return bool
     */
    public function setCookieParams($lifetime, $path=NULL, $domain=NULL, $secure=FALSE, $httponly= FALSE){
        session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly);
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
        $this->setHeader("Content-Type", $contentType);
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
     * Metodo para API REST.
     * Envia una respuesta json con un codigo de respuesta codificando los datos
     * @param int $code
     * @param string $data
     * @param int $options
     * @param string $contentType
     */
    public function sendApiRestEncode($code=200, $data = NULL, $options= 0, $contentType='application/json'){        
        $this->sendApiRest($code, json_encode($data, $options), $contentType);
    }
    /**
     * Metodo para API REST.
     * Envia una respuesta json con un codigo de respuesta
     * @param int $code
     * @param string $jsonString
     * @param string $contentType
     */
    public function sendApiRest($code=200, $jsonString= '', $contentType='application/json'){
        $this->setStatusCode($code);
        $this->setContentType($contentType);
        $this->setContent($jsonString);
        $this->sendContent();
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