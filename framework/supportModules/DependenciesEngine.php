<?php
namespace Enola\Support;
/**
 * Esta clase representa el motor de dependencias que permite adminsitrar la inyeccion de dependencias entre las clases
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class DependenciesEngine {
    protected $context;
    protected $singletons;
    /** Constructor */
    public function __construct() {
        $this->context= \EnolaContext::getInstance();
    }
    /**
     * Realiza la inyeccion de una dependencia
     * Si $object == NULL se retorna la dependencia en vez de agregarla a un objeto 
     * @param string $name
     * @param array $dependency
     * @param type $object
     * @return NULL / Type
     */
    protected function loadDependency($name, $dependency, $object = NULL, &$loadedDependencies = array()){
        $newInstance= NULL;
        if(isset($this->singletons[$name])){
            $newInstance= $this->singletons[$name];
        }else{            
            //Veo si tiene namespace y si tiene le agrego el mismo
            $namespace= (isset($dependency['namespace']) ? $dependency['namespace'] : ''); 
            $dir= explode("/", $dependency['class']);
            $class= $dir[count($dir) - 1];
            if($namespace != ''){ $class= "\\" . $namespace . "\\" . $class;}
            //Consigo los parametros del constructor
            $params= array();
            if(isset($dependency['constructor'])){
                $params= $this->buildParams($dependency['constructor']);
            }
            //Creo una instancia y la agrego a la lista de singleton si es necesario
            $reflection= new \ReflectionClass($class);
            $newInstance= $reflection->newInstanceArgs($params);
            //La agrego a loadedDependencies
            $loadedDependencies[$name]= $newInstance;
            
            //Si es un singleton la guardo como tal
            if(isset($dependency['singleton']) && ($dependency['singleton'] == "TRUE" || $dependency['singleton'] == "true")){
                $this->singletons[$name]= $newInstance;
            }
            
            //Injecto las dependencias a las propiedades
            //Primero veo si hay Referencia a otras dependencias y cargo las mismas
            if(isset($dependency['properties'])){
                $properties= array();
                foreach ($dependency['properties'] as $key => $definition) {
                    $property= NULL;
                    if(isset($definition['ref'])){
                        //Conseguimos la dependencia
                        $ref= $definition['ref'];
                        $dependencies= $this->context->getDependencies();
                        if(isset($dependencies[$ref])){
                            if(isset($loadedDependencies[$ref])){
                                $property= $loadedDependencies[$ref];                                
                            }else{
                                $property= $this->loadDependency($definition['ref'], $dependencies[$definition['ref']], NULL, $loadedDependencies);
                            }
                        }
                    }else{
                        $property= $definition['value'];
                        settype($param, $definition['type']);
                    }
                    $properties[$key]= $property;
                }
                
                foreach ($properties as $key => $value) {
                    //Busco por set
                    $setMethod= 'set' . strtoupper($key[0]) . substr($key, 1);                     
                    if(method_exists($newInstance, $setMethod)){
                        $newInstance->$setMethod($value);
                    }else{
                        $reflectionProperty= new \ReflectionProperty($newInstance,$key);
                        if($reflectionProperty->isPublic()){
                            $newInstance->$key= $value;
                        }
                    }
                }
            }
        }
        if($object != NULL){        
            $object->$name= $newInstance;
        }else{
            return $newInstance;
        }
    }
    /**
     * Crea los parametros para el constructor
     * Parsea los valores en string a lo que corresponda segun el contenido
     * @param array $params
     * @return array
     */
    protected function buildParams($params){
        $buildParams= array();
        foreach ($params as $definition) {
            $param= NULL;
            if(isset($definition['ref'])){
                //Parseamos a objeto                
                $dependencies= $this->context->getDependencies();
                if(isset($dependencies[$definition['ref']])){
                    $param= $this->loadDependency($definition['ref'], $dependencies[$definition['ref']]);
                }
            }else{
                $param= $definition['value'];
                settype($param, $definition['type']);
            }
            $buildParams[]= $param;
        }
        return $buildParams;
    }
    /**
     * Recorre las dependencias con load_in y analiza si carga o no una instncia de la clase determinada.
     * Es llamado por GenericLoader en su construccion para inyectar las clases correspondientes.
     * Esta funcion supone que la Clase ya se encuentra importada.
     * @param type $object
     * @param string $type
     */
    public function injectDependencyOfType($object, $type){
        //Analiza las dependencies que tienen seteado "load_in"
        foreach ($this->context->getLoadDependencies() as $name => $dependency) {
            $types= explode(",", $dependency['load_in']);
            //Si la libreria contiene el tipo se carga
            if(in_array($type, $types)){
                $this->loadDependency($name, $dependency, $object);
            }
        }
    }
    /**
     * Carga las dependencias indicadas en la instancia actual
     * Esta funcion supone que la Clase ya se encuentra importada.
     * @param type $object
     * @param array $dependenciesName
     */
    public function injectDependencies($object, array $dependenciesName){
        $dependencies= $this->context->getDependencies();
        foreach ($dependenciesName as $name) {
            if(isset($dependencies[$name])){
                $dependency= $dependencies[$name];
                $this->loadDependency($name, $dependency, $object);
            }
        }
    }
    /**
     * Carga la dependencias indicada en la instancia actual
     * Esta funcion supone que la Clase ya se encuentra importada.
     * @param type $object
     * @param string $dependencyName
     */
    public function injectDependency($object, $dependencyName){
        $this->injectDependencies($object, array($dependencyName));
    }
}