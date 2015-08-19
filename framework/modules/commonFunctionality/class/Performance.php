<?php
namespace Enola\Common;

/**
 * Clase utilizada para calcular el rendimiento de la aplicacion-framework
 * @author Enola
 */
class Performance {
    protected $timeBegin;
    protected $timeEnd;
    /**
     * Constructor
     */
    public function __construct() {        
    }
    /**
     * Resetea el analisis 
     */
    public function reset(){
        $this->timeBegin= NULL;
        $this->timeEnd= NULL;
    }
    /**
     * Inicial el calculo del tiempo para luego poder terminar y calcular el tiempo
     */
    public function start(){
        //Guarda el tiempo actual en segundos
        $this->timeBegin = microtime(TRUE);
    }    
    /**
     * Finaliza el calculo del tiempo
     */
    public function terminate(){
        //Guarda el tiempo actual en segundos
        $this->timeEnd = microtime(TRUE);
    }    
    /**
     * Calcula el tiempo consumido entre el inicio fin de calculo
     * @return float o NULL
     */
    public function elapsed(){
        if(isset($this->timeBegin) && isset($this->timeEnd)){
            return $this->timeEnd - $this->timeBegin;
        }
        else{
            if(! isset($this->timeBegin)){
                echo "It is necesary execute the method 'start' first";
            }
            else{
                echo "It is necesary execute the method 'terminate' before";
            }
        }
    }    
}