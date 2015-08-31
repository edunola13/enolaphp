<?php
namespace Enola\Component;

/**
 * @author Enola
 */
interface Component {
    /**
     * Funcion que es llamada para que el componente realice su trabajo
     */
    public function rendering($params = NULL);
}