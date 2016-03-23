<?php
namespace Enola\Support;
/**
 * Esta clase representa el motor de dependencias que permite adminsitrar la inyeccion de dependencias entre las clases
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class DependenciesEngine {
    /** Referencia al contexto de la aplicacion 
     * @var \EnolaContext */
    protected $context;
    /** Referencia a todos las dependencias que son singletons
     * @var array */
    protected $singletons;
    /** Definicion de todas las dependencias
     * @var array */
    protected $dependencies;
    /** Definicion de todas las dependencias auto cargables por tipo
     * @var type */
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
     * Injecta definicion de propiedades a una instancia
     * @param type $object
     * @param array $propertiesDefinition
     */
    public function injectProperties($object, $propertiesDefinition){
        $properties= $this->parseProperties($propertiesDefinition);
        $reflection= new Reflection($object);
        $reflection->setProperties($properties, TRUE);
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
        $reflection= new Reflection($object);
        $reflection->setProperty($property, $dependency, TRUE);
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
            
            $newInstance= NULL;
            if(isset($dependencyDefinition['factory-method'])){
                $factoryMethod= $dependencyDefinition['factory-method'];
                if(isset($dependencyDefinition['factory-bean'])){
                    $factoryBean= $this->getDependency($dependencyDefinition['factory-bean'], $loadedDependencies);
                    $newInstance= $factoryBean->$factoryMethod();
                }else{
                    $newInstance= $class::$factoryMethod();
                }
            }else{
                //Consigo los parametros del constructor
                $params= array();
                if(isset($dependencyDefinition['construct'])){
                    //Parseo los parametros correctamente
                    $params= $this->parseProperties($dependencyDefinition['construct']);
                }
                //Creo una instancia con el constructor correspondiente en base a los parametros
                $reflection= new \ReflectionClass($class);
                $newInstance= $reflection->newInstanceArgs($params);
                //La agrego a loadedDependencies para dependencias circulares
                $loadedDependencies[$name]= $newInstance;
            }
            
            //Si es un singleton la guardo como tal
            if(isset($dependencyDefinition['singleton']) && ($dependencyDefinition['singleton'] == "TRUE" || $dependencyDefinition['singleton'] == "true")){
                $this->singletons[$name]= $newInstance;
            }

            //Injecto las dependencias a las propiedades
            //Primero veo si hay Referencia a otras dependencias y cargo las mismas y luego guardo las propiedades
            if(isset($dependencyDefinition['properties'])){
                $properties= $this->parseProperties($dependencyDefinition['properties'], $loadedDependencies);
                $reflection= new Reflection($newInstance);
                $reflection->setProperties($properties, TRUE);
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
}

/**
 * Esta clase se encarga de acceder y setar las propiedades de los distintos objetos y clases.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class Reflection{
    protected $object;
    /** @var \ReflectionObject */
    protected $reflection;
    /**
     * Cosntructor - Se le pasa el objeto a tratar
     * @param type $object
     */
    public function __construct($object) {
        $this->setObject($object);
    }
    public function setObject($object){
        $this->object= $object;
        //Crea un Reflection para acceder a las caracteristicas del objeto
        $this->reflection= new \ReflectionObject($object); 
    }
    /**
     * Lee una propiedad de un objeto. Para poder la propiedad debe exister el metodo get public y/o ser una variable
     * de acceso public. Primero intenta con el metodo get.
     * @param string $property
     * @return type
     */
    public function getProperty($property){        
        //Primero Busco por get
        $getMethod= 'get' . strtoupper($property[0]) . substr($property, 1);                     
        if($this->reflection->hasMethod($getMethod)){
            $reflectionMethod= $this->reflection->getMethod($getMethod);
            //Si existe el metodo set y es public lo seteo
            if($reflectionMethod->isPublic()){
                return $this->object->$getMethod();
            }            
        }else if($this->reflection->hasProperty($property)){
            //Si existe la propiedad y es public la seteo
            $reflectionProperty= $this->reflection->getProperty($property);
            if($reflectionProperty->isPublic()){
                return $this->object->$property;
            }
        }
        return NULL;
    }
    /**
     * Retorna un array con todos los valores de las propiedades.
     * Para leer cada propiedad llama al metodo getProperty.
     * @param array $properties
     * @return array - propertyName => value
     */
    public function getProperties($properties){
        $values= array();
        foreach ($properties as $key => $value) {
            $values[$key]= $this->setProperty($key, $value);
        }
        return $values;
    }
    /**
     * Setea la propiedad de un objeto. Para ahcer esto busca que el metodo set exista y sea publica, despues que la
     * variable tenga visibilidad publica y por ultimo si se indica que se cree la propiedad se crea (en caso de que no exista).
     * Si la variable existe pero no se puede aceder no la setea
     * @param string $property
     * @param type $value
     * @param boolean $create
     */
    public function setProperty($property, $value, $create = FALSE){
        //Primero Busco por set
        $setMethod= 'set' . strtoupper($property[0]) . substr($property, 1);                     
        if($this->reflection->hasMethod($setMethod)){
            $reflectionMethod= $this->reflection->getMethod($setMethod);
            //Si existe el metodo set y es public lo seteo
            if($reflectionMethod->isPublic()){
                $this->object->$setMethod($value);
            }            
        }else if($this->reflection->hasProperty($property)){
            //Si existe la propiedad y es public la seteo
            $reflectionProperty= $this->reflection->getProperty($property);
            if($reflectionProperty->isPublic()){
                $this->object->$property= $value;
            }
        }else if($create){
            //Si no existe la variable y esta seteado que se cree, la misma es creada y seteada
            $this->object->$property= $value;
        }
    }
    /**
     * Setea las propiedades de un objeto.
     * Para setear cada valor llama al metodo setProperty
     * @param array $properties
     * @param boolean $create 
     */
    public function setProperties($properties, $create = FALSE){
        foreach ($properties as $key => $value) {
            $this->setProperty($key, $value, $create);
        }
    }   
}