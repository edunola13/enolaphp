<?php
/**
 * @author Enola
 */
abstract class Enola {
    protected $type;  
    
    public function __construct($type) {
        $this->type= $type;
        $this->load_libraries();
    }
    /**
     * Agrega la instancia de una libreria a la instancia de una clase que extienda de Enola
     * @param Clase de la Libreria $clase
     * @param string $nombre
     */
    protected function load_librarie($clase, $nombre = ""){
        add_instance($clase, $this, $nombre);
    }    
    /**
     * Agrega la instancia de una Clase a la instancia de una clase que extienda de Enola
     * @param Clase $clase
     * @param string $nombre
     */
    protected function loas_class($clase, $nombre= ""){
        add_instance($clase, $this, $nombre);
    }    
    /**
     * Metodo llamado en el constructor de la clase que carga las librerias correspondientes
     */
    protected function load_libraries(){
        //Realiza el llamado a la funcion que se encarga de esto
        load_librarie_in_class($this, $this->type);
    }    
}
?>
