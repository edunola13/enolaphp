<?php
namespace Enola\Cron;
use Enola\Error;

/*
 * Este modulo es el encargado de todo lo referente a los Cron Jobs
 * Importa todos los modulos de soporte - clases que necesita para el correcto funcionamiento
 */
//Clase base de la que deben extender todos los Cron
require 'class/En_CronController.php';
require 'class/En_CronRequest.php';
/**
 * Esta clase representa el Nucleo del modulo Cron y es donde se encuentra toda la funcionalidad del mismo.
 * Este provee un unico metodo para ejecutar el cron correspondiente en base a los parametros pasados por la linea de comandos
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cron
 * @internal
 */
class CronCore{
    public $app;
    public $cronRequest;
    
    /** 
     * Se instancia el nucleo.
     * Se definen los parametros y se define el Cron Request actual
     * @param Application $app
     */
    public function __construct($app, $params) {
        $this->app= $app;
        $config= $this->analyzeParameters($params);
        $this->cronRequest= new En_CronRequest($config);
    }    
    /**
     * Analiza los parametros y devuelvo los parametros ordenados para su posterior uso
     * El primer parametros es el index.php, el segundo es el nombre (clase) del cron y despues puede ser el metodo a ejecutar
     * y luego son todos parametros de entrada al Cron correspondiente. 
     * @param type $params
     * @return array[array]
     */
    private function analyzeParameters($params){
        //Va a contener todos los parametros sin los guiones "-" iniciales
        $realParams= array();
        //Va a contener los parametros reales. Los no usados por el framework
        $cleanParams= array();
        $indActual= 0;
        foreach ($params as $value) {
            //Le quito los guiones iniciales a todos
            $value= ltrim($value, '-');
            //Analizo si debo guardar en cleanParams
            if($indActual == 2 && substr($value, 0, 1) == '?'){
                //Le quito el "?"
                $value= substr($value, 1);
            }else if($indActual >= 2){
                //Guardo los parametros que no usa el framework
                $cleanParams[]= $value;
            }
            //Guardo a todos en realParams
            $realParams[]= $value;
            //Aumento el indice
            $indActual++;
        }
        return array("real" => $realParams, "clean" => $cleanParams);
    }
    /**
     * Ejecuta el cron correspondiente en base a los parametros pasados por la linea de comandos
     * Utilizado solo por el framework
     * @param array[string] $params
     */
    public function executeCronController(){
        $cron= $this->cronRequest->getParamAll(1);
        $method= "index";
        //Si la diferencia es mayor a 2 entre ambos arreglos de parametros quiere decir que se indico el nombre del metodo
        if(count($this->cronRequest->getAllParams()) - count($this->cronRequest->getParams()) > 2){
            $method= $this->cronRequest->getParamAll(2);
        }
        //Ejecuto el cron
        $this->executeCron($cron, $method);            
    }
    /**
     * Ejecuta el Cron mediante el metodo indicado
     * Este es utilizado por el metodo fordward del CronController y de uso interno al modulo
     * @param type $cron
     * @param type $method
     */
    public function executeCron($cron, $method= "index"){
        $dir= PATHAPP . 'source/crons/' . $cron . '.php';
        //Analiza si existe el archivo
        if(file_exists($dir)){
            require $dir;
            $dir= explode("/", $cron);
            $class= $dir[count($dir) - 1];
            $cron= new $class();
            //Analiza si existe el metodo indicado
            if(method_exists($cron, $method)){
                $cron->$method();                
            }else{
                Error::general_error('Cron Controller Error', 'The Cron Controller ' . $cron . ' dont implement the method ' . $method . '()');
            }
        }else{
            Error::general_error('Cron Controller Error', 'The Cron Controller ' . $cron . ' dont exist');
        }
    }
}