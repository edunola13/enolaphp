<?php
namespace Enola\Support;

/**
 * Clase utilizada para calcular el rendimiento de la aplicacion-framework
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class Performance {
    /** Tiempo de inicio en microtime
     * @var float */
    protected $timeBegin;
    /** Tiempo de fin en microtime
     * @var float */
    protected $timeEnd;
    /**
     * Constructor
     */
    public function __construct($timeBegin = NULL) {
        $this->timeBegin= $timeBegin;
    }
    /**
     * Resetea el analisis 
     */
    public function reset(){
        $this->timeBegin= NULL;
        $this->timeEnd= NULL;
    }
    /**
     * Inicia el calculo del tiempo para luego poder terminar y calcular el tiempo
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
     * Calcula el tiempo consumido entre el inicio y fin del calculo
     * @return float o NULL
     */
    public function elapsed(){
        if(isset($this->timeBegin) && isset($this->timeEnd)){
            return number_format($this->timeEnd - $this->timeBegin, 5);
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