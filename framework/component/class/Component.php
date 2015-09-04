<?php
namespace Enola\Component;

/**
 * Esta interface establece los metodos que debe proveer un Componente para que el framework lo pueda administrar correctamente.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Component
 */
interface Component {
    /**
     * Realiza el renderizado del componente
     * @param type $params
     */
    public function rendering($params = NULL);
}