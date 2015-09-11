<?php
namespace Enola\Component;
use Enola\Support\Request;
use Enola\Support\Response;
use Enola\Support;

/**
 * Esta clase implementa la interface Component dejando el metodo renderin vacio para que el usuario sobrescriba. Ademas
 * agrega propiedades y comportamiento propia del modulo HTTP y Component y de los modulos de soporte mediante distintas 
 * clases para que luego los nuevos components del usuario puedan extender de esta y aprovechar toda la funcionalidad 
 * provista por el Core del Framework y el modulo Component.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Component
 */
class En_Component extends Support\GenericLoader implements Component{ 
    use Support\GenericBehavior;
    
    protected $viewFolder;
    /**
     * Inicializa el component llamando al constructor de su padre y seteando el HttpRequest correspondiente
     */
    public function __construct() {        
        parent::__construct('component');
        $this->viewFolder= $this->context->getPathApp() . 'source/view/';
    }    
    /**
     * Realiza el renderizado del componente
     * @param \Enola\Support\Request $request
     * @param \Enola\Support\Response $response
     * @param type $params
     */
    public function rendering(Request $request, Response $response, $params = NULL){        
    }
}