<?php
namespace Enola\Component;
use Http\En_HttpRequest;
use Enola\Common;

/**
 * @author Enola
 */
class En_Component extends Common\GenericLoader implements Component{ 
    use Common\GenericBehavior;
    
    protected $viewFolder;
    protected $request;
    
    public function __construct() {        
        parent::__construct('component');
        if(ENOLA_MODE == 'HTTP')$this->request= En_HttpRequest::getInstance();
        $this->viewFolder= PATHAPP . 'source/view/';
    }    
    /**
     * Funcion que es llamada para que el componente realice su trabajo
     */
    public function rendering($params = NULL){        
    }
}