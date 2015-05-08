<?php
/**
 * @author Enola
 */
abstract class Enola {
    protected $type;  
    
    public function __construct($type) {
        $this->type= $type;
        $this->loadLibraries();
    }  
    /**
     * Agrega la instancia de una Clase a la instancia actual
     * @param Clase $clase
     * @param string $nombre
     */
    protected function loasClass($clase, $nombre= ""){
        add_instance($clase, $this, $nombre);
    }    
    /**
     * Metodo llamado en el constructor de la clase que carga las librerias correspondientes
     */
    protected function loadLibraries(){
        //Realiza el llamado a la funcion que se encarga de esto
        load_librarie_in_class($this, $this->type);
    }    
}