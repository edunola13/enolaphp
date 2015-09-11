<?php
namespace Enola\Http;
use Enola\Support;

/**
 * Esta clase implementa la interface Filter dejando el metodo filter vacio para que el usuario sobrescriba. Ademas agrega 
 * propiedades y comportamiento propia del modulo HTTP y de los modulos de soporte mediante distintas clases para que luego
 * los nuevos controllers del usuario puedan extender de esta y aprovechar  * toda la funcionalidad provista por el Core 
 * del Framework y el modulo Http. 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
class En_Filter extends Support\GenericLoader implements Filter{
    use Support\GenericBehavior;

    /**
     * Inicializa el controlador llamando al constructor de su padre y seteando el HttpRequest correspondiente
     */
    function __construct() {
        parent::__construct('filter');
    }
    /**
     * Realiza la ejecucion del filtro
     * @param \Enola\Http\En_HttpRequest $request
     * @param \Enola\Http\En_HttpResponse $response
     */
    public function filter(En_HttpRequest $request, En_HttpResponse $response){}    
}