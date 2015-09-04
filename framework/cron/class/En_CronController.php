<?php
namespace Enola\Cron;
use Enola\Support;

/**
 * Esta clase representa a un Cron Job. Agrega propiedades y comportamiento propia del modulo Cron y de los modulos
 * de soporte mediante distintas clases para que luego los nuevos crons del usuario puedan extender de esta y aprovechar 
 * toda la funcionalidad provista por el Core del Framework y el modulo Cron. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cron
 */
class En_CronController extends Support\GenericLoader{
    use Support\GenericBehavior;
    
    protected $request;
    protected $viewFolder;
    //errores
    public $errors;    
    /**
     * Inicializa el controlador llamando al constructor de su padre
     */
    function __construct(){
        parent::__construct('cron');
        $this->request= En_CronRequest::getInstance();
        $this->viewFolder= $this->context->getPathApp() . 'source/view/';
    }
    /**
     * Funcion que actua cuando acurre un error en la validacion
     */
    protected function error(){        
    }    
    /**
     * Funcion que carga los datos usados por la vista
     */
    protected function loadData(){        
    }    
    /**
     * Funcion que carga los datos usados por la vista de 
     */
    protected function loadDataError(){        
    }
}