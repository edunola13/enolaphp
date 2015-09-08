<?php
namespace Enola\Http;

/**
 * Esta clase representa una respuesta HTTP y por lo tanto provee todas las propiedades basicas de una respuesta HTTP como
 * asi tambien propiedades de respuesta propias del framework.
 * Ademas provee comportamiento basico para redireccionar solicitudes. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
class En_HttpResponse {
    private static $instance;
    protected static $body;
    
    public function __construct() {
        self::$instance= $this;
    }
    public static function getInstance(){
        if(self::$instance == NULL){
            self::$instance= new En_HttpResponse();
        }
        return self::$instance;
    }
    public function getStatusCode(){
        http_response_code();
    }
    public function setStatusCode($code){
        http_response_code($code);
    }
    public function isOk(){
        return (http_response_code() == 200);
    }
    public function getHeaders(){
        //getallheaders() ver esto
        return headers_list();
    }
    public function setHeaders($header){
        header($header);
    }
    public function setHeader($name, $value){
        header($name . ': ' . $value);
    }
    public function removeHeader($name = NULL){
        header_remove($name);
    }
    public function getCookies(){
        return $_COOKIE;        
    }
    public function getCookie($name){
        if(isset($_COOKIE[$name])){
            return $_COOKIE[$name];
        }else{
            return NULL;
        }
    }
    public function setCookie($name, $value, $expire=0, $path="/", $domain="", $secure=FALSE, $httponly= FALSE){
        return setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
    }
    public function setExpires(){
        ///hacer
    }
    public function setContentType($contentType, $charset=NULL){
        $this->setHeader("Content-type", $contentType);
        if($charset != NULL){
            $this->setHeader("charset", $charset);
        }
    }
    public function setContent($content){
        self::$body= $content;
    }
    public function appendContent($content){
        self::$body.= $content;
    }
    public function setJsonContent($content, $jsonOptions=0){
        self::$body= json_encode($content, $jsonOptions);
    }
    public function getContent(){
        return self::$body;
    }
    public function isSent(){
        return headers_sent();
    }
    public function sendContent(){
        echo self::$body;
    }
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
        UrlUri::redirect($this, $uri);
    }
    /**
     * Redirecciona a una pagina externa a la aplicacion actual
     * @param string $url
     */
    public function external_redirect($url){
        UrlUri::externalRedirect($url);
    }
}

?>
