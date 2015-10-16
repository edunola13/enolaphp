<?php
namespace Enola\Http;

/**
 * Esta interface establece los metodos que debe proveer un Filter para que el framework lo pueda administrar correctamente.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Http
 */
interface Filter {
    /**
     * Realiza la ejecucion del filtro
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function filter(En_HttpRequest $request, En_HttpResponse $response);
}