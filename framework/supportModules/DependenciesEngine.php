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
     * Recorre las dependencias con load_in y analiza si carga o no una instncia de la clase determinada.
     * Es llamado por GenericLoader en su construccion para inyectar las clases correspondientes.
     * Esta funcion supone que la Clase ya se encuentra importada.
     * @param type $object
     * @param string $type
     */
    public function injectDependenciesOfType($object, $type){
        //Analiza las dependencies que tienen seteado "load_in"
        foreach ($this->context->getLoadDependencies() as $name => $dependency) {
            $types= explode(",", $dependency['load_in']);
            //Si la libreria contiene el tipo se carga
            if(in_array($type, $types)){
                $this->loadDependencyInObject($object, $name, $name, $dependency);
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
                $this->loadDependencyInObject($object, $name, $name, $dependency);
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
    /**
     * Carga la dependencia y la setea en la propiedad del objeto a inyectar
     * @param type $object
     * @param string $property
     * @param string $dependencyName
     * @param array $dependencyDefinition
     */
    protected function loadDependencyInObject($object, $property, $dependencyName, $dependencyDefinition){
        $dependency= $this->loadDependency($dependencyName, $dependencyDefinition);
        $properties= array($property => $dependency);
        $this->setPropertiesToObject($object, $properties);
    }
    /**
     * Realiza la inyeccion de una dependencia
     * Si $object == NULL se retorna la dependencia en vez de agregarla a un objeto 
     * @param string $name
     * @param array $dependencyDefinition
     * @param type $object
     * @return NULL / Type
     */
    protected function loadDependency($name, $dependencyDefinition, &$loadedDependencies = array()){
        $newInstance= NULL;
        if(isset($this->singletons[$name])){
            $newInstance= $this->singletons[$name];
        }else{            
            //Veo si tiene namespace y si tiene le agrego el mismo
            $namespace= (isset($dependencyDefinition['namespace']) ? $dependencyDefinition['namespace'] : ''); 
            $dir= explode("/", $dependencyDefinition['class']);
            $class= $dir[count($dir) - 1];
            if($namespace != ''){ $class= "\\" . $namespace . "\\" . $class;}
            //Consigo los parametros del constructor
            $params= array();
            if(isset($dependencyDefinition['constructor'])){
                $params= $this->parseProperties($dependencyDefinition['constructor']);
            }
            //Creo una instancia y la agrego a la lista de singleton si es necesario
            $reflection= new \ReflectionClass($class);
            $newInstance= $reflection->newInstanceArgs($params);
            //La agrego a loadedDependencies
            $loadedDependencies[$name]= $newInstance;
            
            //Si es un singleton la guardo como tal
            if(isset($dependencyDefinition['singleton']) && ($dependencyDefinition['singleton'] == "TRUE" || $dependencyDefinition['singleton'] == "true")){
                $this->singletons[$name]= $newInstance;
            }
            
            //Injecto las dependencias a las propiedades
            //Primero veo si hay Referencia a otras dependencias y cargo las mismas
            if(isset($dependencyDefinition['properties'])){
                $properties= $this->parseProperties($dependencyDefinition['properties'], $loadedDependencies);
                $this->setPropertiesToObject($newInstance, $properties);
            }
        }        
        return $newInstance;
    }
    /**
     * Parsea los valores en string a lo que corresponda segun el contenido de la misma.
     * @param array $propertiesDefinition
     * @return array
     */
    protected function parseProperties($propertiesDefinition, &$loadedDependencies = array()){
        $parseProperties= array();
        foreach ($propertiesDefinition as $key => $definition) {
            $property= NULL;
            if(isset($definition['ref'])){
                //Conseguimos la dependencia
                $property= $this->getDependency($definition['ref'], $loadedDependencies);
            }else{
                $property= $definition['value'];
                settype($property, $definition['type']);
            }
            $parseProperties[$key]= $property;
        }
        return $parseProperties;
    }
    /**
     * Devuelve la dependencia en base a un nombre y una lista de dependencias.
     * Si no existe devuelve NULL
     * @param type $name
     * @param type $loadedDependencies
     * @return Object o NULL
     */
    protected function getDependency($name, &$loadedDependencies = array()){
        $dependency= NULL;
        $dependencies= $this->context->getDependencies();
        if(isset($dependencies[$name])){
            if(isset($loadedDependencies[$name])){
                $dependency= $loadedDependencies[$name];                                
            }else{
                $dependency= $this->loadDependency($name, $dependencies[$name], $loadedDependencies);
            }
        }
        return $dependency;
    }    
    /**
     * Setea las propiedades de ub objeto mediante metodos set y en caso de no existir directamente con la variable que
     * debe ser publica.
     * @param type $object
     * @param type $properties
     */
    protected function setPropertiesToObject($object, $properties){
        foreach ($properties as $key => $value) {
            //Busco por set
            $setMethod= 'set' . strtoupper($key[0]) . substr($key, 1);                     
            if(method_exists($object, $setMethod)){
                //Si existe el metodo set lo utilizo
                $object->$setMethod($value);
            }else{
                $reflection= new \ReflectionObject($object);                
                if($reflection->hasProperty($key)){
                    //Si existe la variable veo si tiene visibilidad publica
                    $reflectionProperty= $reflection->getProperty($key);
                    if($reflectionProperty->isPublic()){
                        $object->$key= $value;
                    }
                }else{
                    //Si no existe la variable la creo y la asigno
                    $object->$key= $value;
                }
            }
        }
    }
}