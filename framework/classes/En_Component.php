<?php
namespace Enola\Component;
use Enola\Http;

/**
 * @author Enola
 */
class En_Component extends \Enola\Loader implements Component{ 
    protected $viewFolder;
    protected $session;
    
    public function __construct() {        
        parent::__construct('component');
        if(ENOLA_MODE == 'HTTP')$this->session= new Http\Session();
        $this->viewFolder= PATHAPP . 'source/view/';
    }    
    /**
     * Funcion que es llamada para que el componente realice su trabajo
     */
    public function rendering($params = NULL){        
    }
}