<?php
namespace Enola\Common;

/**
 * @author Enola
 */
abstract class GenericLoader {
    protected $type;  
    
    public function __construct($type) {
        $this->type= $type;
        $this->loadLibraries();
    }   
    /**
     * Metodo llamado en el constructor de la clase que carga las librerias correspondientes
     */
    protected function loadLibraries(){
        //Realiza el llamado a la funcion que se encarga de esto
        load_librarie_in_class($this, $this->type);
    }
}