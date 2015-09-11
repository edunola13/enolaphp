<?php
namespace Enola\Component;
use Enola\Support\Request;
use Enola\Support\Response;

/**
 * Esta interface establece los metodos que debe proveer un Componente para que el framework lo pueda administrar correctamente.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Component
 */
interface Component {
    /**
     * Realiza el renderizado del componente
     * @param \Enola\Support\En_HttpRequest $request
     * @param \Enola\Support\En_HttpResponse $response
     * @param type $params
     */
    public function rendering(Request $request, Response $response, $params = NULL);
}