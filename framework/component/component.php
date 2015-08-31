<?php
namespace Enola\Component;
use Enola\Error;
    
/**
 * Importa todo lo necesario para manejar los componentes
 * Contiene funciones para manejar los componentes por el framework y por el usuario
 */
//Interface y Clase de la que deben extender todos los components
require 'class/Component.php';
require 'class/En_Component.php';

class ComponentCore{
    public $app;
    
    public function __construct($app) {
        $this->app= $app;
    }
    
    /**
     * Analiza si mapea la URL de componentes
     * @return boolean 
     */
    public function mapsComponents($httpRequest){
        $partes_uri= explode("/", $httpRequest->uriApp);
        if($partes_uri[0] == $this->app->context->getComponentUrl()){
            return TRUE; 
        }
        return FALSE;
    }
    
    /**
     * Ejecuta el componente en base a una URL 
     */
    public function executeUrlComponent($httpRequest){        
        $partes_uri= explode("/", $httpRequest->uriApp);
        $nombre= "";
        $params= array();
        $action= NULL;
        if(count($partes_uri) > 1){
            $nombre= $partes_uri[1];
            unset($partes_uri[0]);
            unset($partes_uri[1]);
            //Reseteo los indices
            $partes_uri= array_values($partes_uri);            
            if(isset($partes_uri[0]) && $partes_uri[0] == 'actionComponent' && isset($partes_uri[1])){
                $action= $partes_uri[1];
                unset($partes_uri[0]);
                unset($partes_uri[1]);
                $partes_uri= array_values($partes_uri);
            }
            //Consigue los parametros
            foreach ($partes_uri as $value) {
                $params[]= $value;
            }
        }
        if($nombre != ""){
            $components= $this->app->context->getComponentsDefinition();
            //Evalua si el componente existe y si se encuentra habilitado via URL
            if(isset($components[$nombre])){
                $comp= $components[$nombre];
                if($comp['enabled-url'] == 'TRUE' || $comp['enabled-url'] == 'true'){
                    $this->executeComponent($nombre, $params, $action);
                }
                else{
                    echo "The component is disabled via URL";
                }
            }
            else{
                echo "There isent a component with the name: " + $nombre;
            }
        }
        else{  
            echo "Enola Components";
        }
    }
    
    /**
     * Ejecuta el metodo renderizar de un componente
     * @param type $nombre
     * @param type $parametros
     * @param type url
     */ 
    public function executeComponent($nombre, $parametros = NULL, $action = NULL){
        $components= $this->app->context->getComponentsDefinition();
        $componente= NULL;
        if(isset($components[$nombre])){
            $comp= $components[$nombre];
            $dir= $this->buildDir($comp, 'components');
            $class= $this->buildClass($comp);
            if(!class_exists($class)){
                //Si la clase no existe intento cargarla
                if(file_exists($dir)){
                    require_once $dir;
                }else{
                    //Avisa que el archivo no existe
                    Error::general_error('Component Error', 'The component ' . $comp['class'] . ' dont exists');
                } 
            }
            $componente= new $class();
        }
        if($componente != NULL){
            //Analiza si existe el metodo render
            if(method_exists($componente, 'rendering')){
                if($action != NULL){
                    if(method_exists($componente, $action)){
                        $componente->$action($parametros);
                    }else{
                        Error::general_error('Component Error', 'The component ' . $nombre . ' dont implement the action ' . $action . '()');
                    }
                }
                return $componente->rendering($parametros);
            }
            else{
                Error::general_error('Component Error', 'The component ' . $nombre . ' dont implement the method rendering()');
            }          
        }
        else{
            Error::general_error('Component Error', "The component $nombre dont exists");
        }
    }
    
    protected function buildDir($definicion, $folder="controllers"){
        $dir= "";
        if(! isset($definicion['location'])){
            $dir= $this->app->context->getPathApp() . 'source/' . $folder . '/' . $definicion['class'] . '.php';
        }else{
            $dir= $this->app->context->getPathRoot() . $definicion['location'] . '/' . $definicion['class'] . '.php';
        }
        return $dir;
    }
    protected function buildClass($definicion){
        $namespace= (isset($definicion['namespace']) ? $definicion['namespace'] : '');
        //Empiezo la carga del controlador
        $dirExplode= explode("/", $definicion['class']);
        $class= $dirExplode[count($dirExplode) - 1];
        if($namespace != '') $class= "\\" . $namespace . "\\" . $class;
        return $class;
    }
}