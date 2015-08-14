<?php
namespace Enola\Http;

/**
 * Clase de la que deben extender los filtros de la aplicacion para funcionar correctamente
 * @author Enola
 */
class En_Filter extends \Enola\Loader implements Filter{
    protected $request;

    function __construct() {
        parent::__construct('filter');
        $this->request= En_HttpRequest::getInstance();
    }    
    /**
     * Funcion que es llamada para realizar el filtro correspondiente
     */
    public function filter(){        
    }    
}