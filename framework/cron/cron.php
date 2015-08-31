<?php
namespace Enola\Cron;
use Enola\Error;

/*
 * Este modulo se encarga de cargar todas las clases necesarias para ejecutar Crons
 */
//Interface y Clase base de la que deben extender todos los Cron
require 'class/En_CronController.php';

class CronCore{
    public $app;
    
    public function __construct($app) {
        $this->app= $app;
    }
    
    /**
     * Ejecuta el cron indicado en el primer parametro pasado al cron
     */
    public function executeCronController($params){
        //Quito guiones iniciales
        $cronClass= ltrim($params[1], '-');
        $dir= PATHAPP . 'source/crons/' . $cronClass . '.php';
        //Analiza si existe el archivo
        if(file_exists($dir)){
            require $dir;
            $dir= explode("/", $cronClass);
            $class= $dir[count($dir) - 1];
            $cron= new $class();
            $method= 'index';
            //El segundo parametro puede ser el metodo a ejecutar o una variable
            if(isset($params[2])){
                //Quito posibles guiones iniciales y si el primer caracter es '?' indica que es metodo
                $arg2= ltrim($params[2], '-');
                if(substr($arg2, 0, 1) == '?'){
                    $method= substr($arg2, 1);
                }
            }
            //Analiza si existe el metodo indicado
            if(method_exists($cron, $method)){
                //Seteo los parametros, los parametros limpiados(los no usados aca) y ejecuto el metodo
                $cron->setParams($params);
                $ini= 2;
                if($method != 'index'){$ini= 3;}
                $cron->setCleanParams(array_slice($params, $ini));
                $cron->$method();                
            }else{
                Error::general_error('Cron Controller Error', 'The Cron Controller ' . $cronClass . ' dont implement the method ' . $method . '()');
            }
        }else{
            Error::general_error('Cron Controller Error', 'The Cron Controller ' . $cronClass . ' dont exist');
        }
    }
}