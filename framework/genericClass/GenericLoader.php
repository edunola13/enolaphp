<?php
namespace Enola\CommonInternal;

/**
 * @author Enola
 */
abstract class GenericLoader {
    protected $type;
    protected $context;
    
    public function __construct($type) {
        $this->type= $type;
        $this->loadLibraries();
        $this->context= \EnolaContext::getInstance();
    }   
    /**
     * Metodo llamado en el constructor de la clase que carga las librerias correspondientes
     */
    protected function loadLibraries(){
        //Realiza el llamado a la funcion que se encarga de esto
        load_libraries_in_class($this, $this->type);
    }
}