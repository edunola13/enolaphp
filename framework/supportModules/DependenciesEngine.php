<?php
namespace Enola\Support;
/**
 * Esta clase representa el motor de dependencias que permite adminsitrar la inyeccion de dependencias entre las clases
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class DependenciesEngine {
    /** @var \EnolaContext */
    protected $context;
    protected $singletons;
    
    protected $dependencies;
    protected $loadDependencies;
    /** Constructor */
    public function __construct() {
        $this->context= \EnolaContext::getInstance();
        $this->init();
    }
    /** Realiza lar carga inicial */
    protected function init(){
        //Carga las dependencias en base a la lista de archivos definidos en configuracion global
        $this->dependencies= array();
        foreach ($this->context->getDependenciesFile() as $nameFile) {
            $this->dependencies= array_merge($this->dependencies, $this->context->readConfigurationFile($nameFile));
        }
        //Define las dependencias que se cargan segun Type
        $this->loadDependencies= array();
        foreach ($this->dependencies as $key => $value) {
            if(isset($value['load_in'])){
                $this->loadDependencies[$key]= $value;
            }
        }
    }
    /**
     * Recorre las dependencias con load_in y analiza si carga o no una instancia de la dependencia en el objeto.
     * Las nombres de las propiedades son las claves en la definicion de cada dependencia
     * Es llamado por GenericLoader en su construccion para inyectar las clases correspondientes.
     * Esta funcion supone que las Clases de la dependencia ya se encuentran importadas.
     * @param type $object
     * @param string $type
     */
    public function injectDependenciesOfType($object, $type){
        //Analiza las dependencies que tienen seteado "load_in"
        foreach ($this->loadDependencies as $name => $dependency) {
            $types= explode(",", $dependency['load_in']);
            //Si la libreria contiene el tipo se carga
            if(in_array($type, $types)){
                $this->loadDependencyInObject($object, $name, $name, $dependency);
            }
        }
    }
    /**
     * Carga cada una de las dependencias indicadas en su correspondiente propiedad del objeto
     * Esta funcion supone que las Clases de las dependencias ya se encuentran importadas.
     * @param type $object
     * @param array $dependencies
     */
    public function injectDependencies($object, array $dependencies){
        $dependenciesDefinition= $this->dependencies;
        //Recorre las dependencias indicadas y las que existe las carga
        foreach ($dependencies as $property => $dependencyName) {
            if(isset($dependenciesDefinition[$dependencyName])){
                $dependency= $dependenciesDefinition[$dependencyName];
                $this->loadDependencyInObject($object, $property, $dependencyName, $dependency);
            }
        }
    }
    /**
     * Carga la dependencias indicada en la propiedad del objeto
     * Esta funcion supone que la Clase de la dependencia ya se encuentra importada.
     * @param type $object
     * @param string $propertyName
     * @param string $dependencyName
     */
    public function injectDependency($object, $propertyName, $dependencyName){
        $this->injectDependencies($object, array($propertyName => $dependencyName));
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
        //Inyecto la dependencia
        $this->setPropertiesToObject($object, $properties);
    }
    /**
     * Realiza la carga de una dependencia que luego va a ser inyectada
     * @param string $name
     * @param array $dependencyDefinition
     * @param array &$loadedDependencies
     * @return type
     */
    protected function loadDependency($name, $dependencyDefinition, &$loadedDependencies = array()){
        $newInstance= NULL;
        //Si es singleton analiza si existe y si puede devuelve la msima
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
                //Parseo los parametros correctamente
                $params= $this->parseProperties($dependencyDefinition['constructor']);
            }
            //Creo una instancia con el constructor correspondiente en base a los parametros
            $reflection= new \ReflectionClass($class);
            $newInstance= $reflection->newInstanceArgs($params);
            //La agrego a loadedDependencies para dependencias circulares
            $loadedDependencies[$name]= $newInstance;
            
            //Si es un singleton la guardo como tal
            if(isset($dependencyDefinition['singleton']) && ($dependencyDefinition['singleton'] == "TRUE" || $dependencyDefinition['singleton'] == "true")){
                $this->singletons[$name]= $newInstance;
            }
            
            //Injecto las dependencias a las propiedades
            //Primero veo si hay Referencia a otras dependencias y cargo las mismas y luego guardo las propiedades
            if(isset($dependencyDefinition['properties'])){
                $properties= $this->parseProperties($dependencyDefinition['properties'], $loadedDependencies);
                $this->setPropertiesToObject($newInstance, $properties);
            }
        }        
        return $newInstance;
    }
    /**
     * Parsea los valores en string al tipo que corresponda segun el valor y el tipo definido.
     * @param array $propertiesDefinition
     * @param array &$loadedDependencies
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
                //Casteo el valor al tipo indicado
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
     * @param array &$loadedDependencies
     * @return Object o NULL
     */
    protected function getDependency($name, &$loadedDependencies = array()){
        $dependency= NULL;
        $dependencies= $this->dependencies;
        if(isset($dependencies[$name])){
            //Si la dependencia ya fue cargada anteriormente de forma circular se usa la misma, si no se carga y se 
            //guarda en la lista de dependencias cargadas en la iteracion
            if(isset($loadedDependencies[$name])){
                $dependency= $loadedDependencies[$name];                                
            }else{
                $dependency= $this->loadDependency($name, $dependencies[$name], $loadedDependencies);
            }
        }
        return $dependency;
    }    
    /**
     * Setea las propiedades de un objeto mediante metodo set y en caso de no existir directamente con la variable que
     * debe ser publica. En caso de que no exista la variable se crea.
     * Si no se cumple ninguna de las condiciones anteriores no se setea.
     * @param type $object
     * @param array $properties
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