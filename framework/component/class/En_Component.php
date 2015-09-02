<?php
namespace Enola\Component;
use Enola\Http\En_HttpRequest;
use Enola\Support;

/**
 * @author Enola
 */
class En_Component extends Support\GenericLoader implements Component{ 
    use Support\GenericBehavior;
    
    protected $viewFolder;
    protected $request;
    
    public function __construct() {        
        parent::__construct('component');
        if(ENOLA_MODE == 'HTTP')$this->request= En_HttpRequest::getInstance();
        $this->viewFolder= $this->context->getPathApp() . 'source/view/';
    }    
    /**
     * Funcion que es llamada para que el componente realice su trabajo
     */
    public function rendering($params = NULL){        
    }
}