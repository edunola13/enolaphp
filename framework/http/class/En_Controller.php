<?php
namespace Enola\Http;
use Enola\Support;

/**
 * Esta clase implementa la interface Controller dejando todos los metodos que atienden peticiones HTTP vacios para que el
 * usuario sobrescriba los que correspondan. Ademas agrega propiedades y comportamiento propia del modulo HTTP y de los modulos
 * de soporte mediante distintas clases para que luego los nuevos controllers del usuario puedan extender de esta y aprovechar 
 * toda la funcionalidad provista por el Core del Framework y el modulo Http. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
class En_Controller extends Support\GenericLoader implements Controller{
    use Support\GenericBehavior;
    
    protected $request;
    protected $uriParams;
    protected $viewFolder;
    //errors
    public $errors;    
    /**
     * Inicializa el controlador llamando al constructor de su padre y seteando el HttpRequest correspondiente
     */
    function __construct(){
        parent::__construct('controller');
        $this->request= En_HttpRequest::getInstance();
        $this->viewFolder= $this->context->getPathApp() . 'source/view/';
    }  

    /**
     * Atiende la peticion HTTP de tipo GET
     */
    public function doGet(){}  
    /**
     * Atiende la peticion HTTP de tipo POST
     */
    public function doPost(){}
    /**
     * Atiende la peticion HTTP de tipo DELETE
     */
    public function doDelete(){}
    /**
     * Atiende la peticion HTTP de tipo PUT
     */
    public function doPut(){}
    /**
     * Atiende la peticion HTTP de tipo HEAD
     */
    public function doHead(){}
    /**
     * Atiende la peticion HTTP de tipo TRACE
     */
    public function doTrace(){}
    /**
     * Atiende la peticion HTTP de tipo OPTIONS
     */
    public function doOptions(){}
    /**
     * Atiende la peticion HTTP de tipo CONNECT
     */
    public function doConnect(){}
    /**
     * Setea los uri_params de la peticion actual
     * @param type $uri_params
     */
    public function setUriParams($uri_params){
        $this->uriParams= $uri_params;
    }
    /**
     * Funcion que actua cuando acurre un error en el controlador
     */
    protected function error(){        
    }    
    /**
     * Funcion que carga los datos usados por la vista
     */
    protected function loadData(){        
    }    
    /**
     * Funcion que carga los datos usados por la vista de error
     */
    protected function loadDataError(){        
    }    
    /**
     * Redireccion interna a otro Controlador.
     * Se indica si se debe filtrar o no la nueva solicitud
     * @param type $uri
     * @param Bool $filter
     */
    protected function fordward($uri, $filter = FALSE){
        $this->context->app->httpCore->executeHttpRequest(NULL, $uri, $filter);
    }
}