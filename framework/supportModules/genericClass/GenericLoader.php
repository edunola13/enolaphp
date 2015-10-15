<?php
namespace Enola\Support;

/**
 * Esta clase abstracta se encarga de realizar la inyeccion de dependencia en base al tipo.
 * Solo es nbecesario extender esta y llamar a su contructor. Esta cargara las dependencias correspondientes a su tipo y
 * asignara el contexto de la aplicacion.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
abstract class GenericLoader {
    /** @var \EnolaContext */
    protected $context;
    /**
     * Constructor que realiza la carga inicial
     * @param string $type
     */
    public function __construct($type) {
        $this->context= \EnolaContext::getInstance();
        //Inyecta las dependencias por tipo
        \EnolaContext::getInstance()->app->dependenciesEngine->injectDependenciesOfType($this, $type);        
    }
}