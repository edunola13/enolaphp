<?php
namespace Enola\Support;
/**
 * Esta clase representa el comportamiento basico de una respuesta de la aplicacion.
 * El modulo HTTP debe extender para proveer todo el comportamiento pero el modulo Cron usa directamente esta clase
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class Response {
    /** Instancia de el mismo. Singleton 
     * @var Response */
    protected static $instance;
    /** Cuerpo de la respuesta
     * @var string */
    protected static $body;
    /**
     * Devuelve la isntancia que se esta utilizando
     */
    public static function getInstance(){
        if(self::$instance == NULL){
            self::$instance= new Response();
        }
        return self::$instance;
    }
    /**
     * Setea el contenido de la respuesta
     * @param string $content
     */
    public function setContent($content){
        self::$body= $content;
    }
    /**
     * Agrega contenido a la respuesta
     * @param string $content
     */
    public function appendContent($content){
        self::$body.= $content;
    }
    /**
     * Setea el contenido de la respuesta en formato JSON
     * @param type $content
     * @param int $jsonOptions
     */
    public function setJsonContent($content, $jsonOptions=0, $depth=512){
        self::$body= json_encode($content, $jsonOptions, $depth);
    }
    /**
     * Devuelve el contenido de la respuesta
     * @return string
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