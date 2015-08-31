<?php
namespace Enola\Http;

/**
 * @author Enola
 */
interface Filter {
    /**
     * Funcion que es llamada para realizar el filtro correspondiente
     */
    public function filter();
}