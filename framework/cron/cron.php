<?php
namespace Enola\Cron;
use \Enola\Support\Response;
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
    /** Referencia al nucleo de la aplicacion 
     * @var \Enola\Application */
    public $app;
    /** Referencia al CronRequest actual 
     * @var En_CronRequest */
    public $cronRequest;
    /** Referencia al Response actual 
     * @var Response */
    public $cronResponse;    
    /** 
     * Se instancia el nucleo.
     * Se definen los parametros y se define el Cron Request actual
     * @param \Enola\Application $app
     */
    public function __construct($app, $params) {
        $this->app= $app;
        $config= $this->analyzeParameters($params);
        $this->cronRequest= new En_CronRequest($config);
        $this->cronResponse= new Response();
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
    private function validInterval($frequencyMin, $min){
        $contain= strpos($frequencyMin, '*/');
        if($contain !== FALSE){            
            $interval= intval(substr($frequencyMin, 2));
            return ($min % $interval) == 0;
        }else if($frequencyMin == '*'){
            return TRUE;
        }else{
            return $frequencyMin == $min;
        }
    }
    /**
     * Retorna si una frecuencia esta activa en determinado dateTime
     * @param string $frequency
     * @param array $actualFrequency
     * @return boolean
     */
    public function activeFrequency($frequency, $actualFrequency){
        $frequency= explode(' ', trim($frequency, ' '));        
        $condition= $this->validInterval($frequency[0], $actualFrequency['i']) && $this->validInterval($frequency[1], $actualFrequency['H']) 
                && $this->validInterval($frequency[2], $actualFrequency['d']) && $this->validInterval($frequency[3], $actualFrequency['m']) 
                && $this->validInterval($frequency[4], $actualFrequency['w']);
        return $condition;
    }
    /**
     * Ejecuta el cron correspondiente en base a los parametros pasados por la linea de comandos
     * Utilizado solo por el framework
     */
    public function executeCronController(){
        $cron= $this->cronRequest->getParamAll(1);
        //Analizo si llamo a los controladores del usuario o si llamo al manejador de tareas del framework
        if($cron != 'CronManagement'){
            $method= "index";
            //Si la diferencia es mayor a 2 entre ambos arreglos de parametros quiere decir que se indico el nombre del metodo
            if(count($this->cronRequest->getAllParams()) - count($this->cronRequest->getParams()) > 2){
                $method= $this->cronRequest->getParamAll(2);
            }
            //Ejecuto el cron
            $this->executeCron($cron, $method);
        }else{
            //Ejecuto el CronManagement
            $this->executeCronManagement();
        }
    }
    /**
     * Ejecuta el cron management del framework el cual analiza las tareas definidas en el archivo de configuracion
     * y ejecuta las que corresponda.
     */
    public function executeCronManagement(){
        $dateTime= new \DateTime();
        $actualFrequency= array(
            'i' => $dateTime->format('i'),
            'H' => $dateTime->format('H'),
            'd' => $dateTime->format('d'),
            'm' => $dateTime->format('m'),
            'w' => $dateTime->format('w')
        );        
        $definedCrons= $this->app->context->readConfigurationFile('cronJobs')['crons'];
        $cronsToExeture= array();
        foreach ($definedCrons as $cronEsp) {
            $frecuenciaActiva= $this->activeFrequency($cronEsp['frequency'], $actualFrequency);
            if($frecuenciaActiva){
                $cronsToExeture[]= $cronEsp;
            }
        }
        //Ejecuto las tareas en la que su frecuencia sea activa
        foreach ($cronsToExeture as $cronEsp) {
            $propertiesEsp= isset($cronEsp['properties']) ? $cronEsp['properties'] : NULL;
            $this->executeCron($cronEsp['cronController'], $cronEsp['method'], $propertiesEsp);
        }        
    }
    /**
     * Ejecuta el Cron mediante el metodo indicado
     * Este es utilizado por el metodo forward del CronController y de uso interno al modulo
     * @param string $cron
     * @param string $method
     */
    public function executeCron($cron, $method= "index", $propertiesEsp= NULL){
        $dir= PATHAPP . 'source/crons/' . $cron . '.php';
        //Analiza si existe el archivo
        if(file_exists($dir)){
            require_once $dir;
            $dir= explode("/", $cron);
            $class= $dir[count($dir) - 1];
            $cron= new $class();
            if($propertiesEsp != NULL){
                $this->app->dependenciesEngine->injectProperties($cron, $propertiesEsp);
            }
            //Analiza si existe el metodo indicado
            if(method_exists($cron, $method)){
                $cron->$method($this->cronRequest, $this->cronResponse);                
            }else{
                Error::general_error('Cron Controller Error', 'The Cron Controller ' . $cron . ' dont implement the method ' . $method . '()');
            }
        }else{
            Error::general_error('Cron Controller Error', 'The Cron Controller ' . $cron . ' dont exist');
        }
    }
}