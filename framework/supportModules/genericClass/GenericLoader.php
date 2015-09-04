<?php
namespace Enola\Support;

/**
 * Esta clase abstracta se encarga de realizar la inyeccion de dependencia de las clases que usted decidad.
 * Solo es nbecesario extender esta y llamar a su contructor. Esta cargara las librerias correspondientes a su tipo y
 * asignara el contexto de la aplicacion.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
abstract class GenericLoader {
    protected $type;
    protected $context;
    /**
     * Constructor que realiza la carga inicial
     * @param type $type
     */
    public function __construct($type) {
        $this->type= $type;
        $this->loadLibraries();
        $this->context= \EnolaContext::getInstance();
    }   
    /**
     * Inyecta las librerias que corresponde
     */
    protected function loadLibraries(){
        //Realiza el llamado a la funcion que se encarga de esto
        load_libraries_in_class($this, $this->type);
    }
}