<?php
namespace UiServices;
/*
 * Se indica la ubicacion donde se guardaran los componentes una vez que se lean del servidor o del archivo 
 * con definiciones
 */
$pathThemes= PATHAPP . '/source/view/themes/';
$pathJavaScript= PATHAPP . '/source/view/javascript/';
$pathComponents= PATHAPP . '/source/view/components/';    
define('PATH_THEME', $pathThemes);
define('PATH_JAVASCRIPT', $pathJavaScript);    
define('PATH_COMPONENT', $pathComponents);
//Direccion del servidor de UI que se va a consumir
//define('SERVER_URL', 'http://www.edunola.com.ar/serviciosui/');
define('SERVER_URL', 'http://localhost/uiservices/');
/*
 * Si no se desea conectar con el servidor para la primer carga de componentes se puede usar una definicion del servidor.
 * Se debe indicar SERVER_DEFINITION = TRUE
 */
$pathServerDefinition= realpath(dirname(__FILE__)) . '/../ServerDefinition-2015-03-12.txt';
define('SERVER_DEFINITION', FALSE);
define('SERVER_DEFINITION_FILE', NULL);
/*
 * Indica como se ejecutaran los componentes
 * Los valores posibles son 'eval' y 'filephp'
 * eval: se crea un unico archivo con todos los codigos php y ejecuta codigo php mediante la funcion eval()
 * filephp: se crea un archivo .php por cada componente y este es incluido
 * eval es mas rapido.
 */
define('UI_API_MODE', 'eval');
require 'ApiUi.php';
require 'Tags.php';