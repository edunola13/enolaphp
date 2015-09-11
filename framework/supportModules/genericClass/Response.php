<?php
namespace Enola\Support;
/**
 * Esta clase representa el comportamiento basico de una respuesta de la aplicacion.
 * El modulo HTTP debe extender para proveer todo el comportamiento pero el modulo Cron usa directamente esta clase
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class Response {
    protected static $instance;
    protected static $body;
    /**
     * Devuelve la isntancia que se esta utilizando
     */
    public static function getInstance(){
        if(self::$instance == NULL){
            self::$instance= new En_HttpResponse();
        }
        return self::$instance;
    }
    /**
     * Setea el contenido de la respuesta
     * @param type $content
     */
    public function setContent($content){
        self::$body= $content;
    }
    /**
     * Agrega contenido a la respuesta
     * @param type $content
     */
    public function appendContent($content){
        self::$body.= $content;
    }
    /**
     * Setea el contenido de la respuesta en formato JSON
     * @param type $content
     * @param type $jsonOptions
     */
    public function setJsonContent($content, $jsonOptions=0){
        self::$body= json_encode($content, $jsonOptions);
    }
    /**
     * Devuelve el contenido de la respuesta
     * @return type
     */
    public function getContent(){
        return self::$body;
    }
    /**
     * Envia el contenido. Lo imprime
     */
    public function sendContent(){
        echo self::$body;
    }
}