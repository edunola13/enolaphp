<?php
namespace Enola\Http;
use Enola\Support;

/**
 * Clase de la que deben extender los controladores de la aplicacion para que se asegure el funcioneamiento del mismo
 * @author Enola
 */
class En_Controller extends Support\GenericLoader implements Controller{
    use Support\GenericBehavior;
    
    protected $request;
    protected $uriParams;
    protected $viewFolder;
    //errors
    public $errors;    

    function __construct(){
        parent::__construct('controller');
        $this->request= En_HttpRequest::getInstance();
        $this->viewFolder= $this->context->getPathApp() . 'source/view/';
    }    
    /**
     * Funcion que es llamada cuando el metodo HTTP es GET
     */
    public function doGet(){        
    }    
    /**
     * Funcion que es llamada cuando el metodo HTTP es POST
     */
    public function doPost(){        
    }    
    /**
     * Funcion que es llamada cuando el metodo HTTP es DELETE
     */
    public function doDelete(){        
    }    
    /**
     * Funcion que es llamada cuando el metodo HTTP es PUT
     */
    public function doPut(){        
    }
    /**
     * Funcion que es llamada cuando el metodo HTTP es HEAD
     */
    public function doHead(){        
    }
    /**
     * Funcion que es llamada cuando el metodo HTTP es TRACE
     */
    public function doTrace(){        
    }
    /**
     * Funcion que es llamada cuando el metodo HTTP es OPTIONS
     */
    public function doOptions(){        
    }
    /**
     * Funcion que es llamada cuando el metodo HTTP es CONNECT
     */
    public function doConnect(){        
    }
    
    /**
     * Setea los uri params
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
     * @param Bool $filtrar= FALSE. Indica si filtra
     */
    protected function fordward($uri, $filter = FALSE){
        $this->context->app->httpCore->executeHttpRequest(NULL, $uri, $filter);
    }
}