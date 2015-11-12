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

    protected $uriParams;
    protected $viewFolder;
    //errors
    public $errors;    
    /**
     * Inicializa el controlador llamando al constructor de su padre y seteando el HttpRequest correspondiente
     */
    function __construct(){
        parent::__construct('controller');
        $this->viewFolder= $this->context->getPathApp() . 'source/view/';
    }  

    /**
     * Atiende la peticion HTTP de tipo GET
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function doGet(En_HttpRequest $request, En_HttpResponse $response){}  
    /**
     * Atiende la peticion HTTP de tipo POST
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function doPost(En_HttpRequest $request, En_HttpResponse $response){}
    /**
     * Atiende la peticion HTTP de tipo DELETE
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function doDelete(En_HttpRequest $request, En_HttpResponse $response){}
    /**
     * Atiende la peticion HTTP de tipo PUT
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function doPut(En_HttpRequest $request, En_HttpResponse $response){}
    /**
     * Atiende la peticion HTTP de tipo HEAD
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function doHead(En_HttpRequest $request, En_HttpResponse $response){}
    /**
     * Atiende la peticion HTTP de tipo TRACE
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function doTrace(En_HttpRequest $request, En_HttpResponse $response){}
    /**
     * Atiende la peticion HTTP de tipo OPTIONS
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function doOptions(En_HttpRequest $request, En_HttpResponse $response){}
    /**
     * Atiende la peticion HTTP de tipo CONNECT
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function doConnect(En_HttpRequest $request, En_HttpResponse $response){}
    /**
     * Setea los uri_params de la peticion actual
     * @param type $uri_params
     */
    public function setUriParams($uri_params){
        $this->uriParams= $uri_params;
    }
    /**
     * Devuelve un uri param si existe y si no devuelve NULL
     * @param string $key
     * @return null o string
     */
    protected function getUriParam($key){
        if(isset($this->uriParams[$key])){
            return $this->uriParams[$key];
        }else{
            return NULL;
        }
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
     * @param string $uri
     * @param boolean $filter
     */
    protected function forward($uri, $filter = FALSE){
        $this->context->app->httpCore->executeHttpRequest(NULL, $uri, $filter);
    }
}