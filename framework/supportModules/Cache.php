<?php
namespace Enola\Cache;
use Enola\DB\DataBaseAR;

/*
 * En este modulo se encuentra definido todo el sistema de Cache.
 * En este se define la interfaz de acceso, los drivers segun el tipo de cache elegido y la clase que implementa la cache.
 */
/**
 * Esta interface define los metodos de acceso a la cache, digamos que puede hacer el sistema de cache
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cache
 */
interface CacheInterface {
    /**
     * Devuelve si existe un dato guardado en cache con esa clave
     * @param string $key
     */
    public function exists($key);
    /**
     * Devuelve un valor guardado en cache o null si no existe
     * @param string $key
     */
    public function get($key);
    /**
     * Almacena un valor en cache asociado a una clave
     * @param string $key
     * @param type $data
     * @param int $ttl
     */
    public function store($key, $data, $ttl=0);
    /**
     * Elimina un valor en cache asociado a una clave
     * @param string $key
     */
    public function delete($key);
}
/**
 * Esta clase abstracta contiene funcionalidad compartida entre todas las instancias de cache
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cache
 */
abstract class CacheUtils{
    /**
     * Prepara los datos antes de guardarlos en cache
     * @param mixed $data
     * @return string
     */
    protected function preperaData($data){
        if(is_string($data)){
            return $data;
        }else{
            return serialize($data);
        }
    }
    /**
     * Retorna los datos exactamente como se cachearon
     * @param string $data
     * @return mixed
     */
    protected function unPrepareData($data){
        $dataUn = @unserialize($data);
        if ($data === 'b:0;' || $dataUn !== false) {
            return $dataUn;
        } else {
            echo $data;
        }
    }
}
/**
 * Esta clase implementa el sistema de cache. Implementa la interface de cache y responde a todos los metodos segun el
 * driver actual que tenga seteado.
 * Esta administra los nombres de las claves en base al prefijo utilizado
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cache
 */
class Cache implements CacheInterface{
    /** Contiene los datos del archivo de configuracion
     * @var array */
    private static $config;
    /** Prefijo a utilizar
     * @var string */
    public $prefix;
    /** Referencia al driver 
     * @var CacheInterface */
    public $store;
    /**
     * Constructor del sistema de cahce. Levanta la configuracion del archivo o de la variable estatica e instancia al
     * driver correspondiente.
     * @param string $store
     */
    public function __construct($store = "Default") {        
        $context= \EnolaContext::getInstance();
        if(self::$config == NULL){
            self::$config= $context->readConfigurationFile('cache');
        }
        $this->prefix= self::$config["prefix"];
        $this->setCacheStore($store);
    }
    /**
     * Setea el driver indicado
     * @param string $store
     */
    public function setCacheStore($store = "Default"){
        if($store == "Default"){
            $store= self::$config['defaultStore'];            
        }
        $config= self::$config['stores'][$store];
        switch ($config['driver']) {
            case 'file':
                $this->store= new CacheFileSystem($config['folder']);
                break;
            case 'database':
                $this->store= new CacheDataBase($config['connection'], $config['table']);
                break;
            case 'apc':
                $this->store= new CacheApc();
                break;
            case 'memcached':
                $persistenceId= isset($config['persistenceId']) ? $config['persistenceId'] : NULL;
                $this->store= new CacheMemCached($config["servers"], $persistenceId);
                break;
            case 'redis':
                $this->store= new CacheRedis($config['schema'], $config['host'], $config['port']);
                break;
            default:
                \Enola\Error::general_error("Cache Configuration", "Driver specified unsupported");
                break;
        }
    }
    /**
     * Codifica la clave y la une al prefijo actual
     * @param string $key
     * @return string
     */
    protected function realKey($key){
        return $this->prefix . md5($key);
    }    
    /**
     * Devuelve si existe un dato guardado en cache con esa clave
     * @param string $key
     */
    public function exists($key){
        return $this->store->exists($this->realKey($key));
    }
    /**
     * Devuelve un valor guardado en cache o null si no existe
     * @param string $key
     */
    public function get($key){
        return $this->store->get($this->realKey($key));
    }
    /**
     * Almacena un valor en cache asociado a una clave
     * @param string $key
     * @param type $data
     * @param int $ttl
     */
    public function store($key, $data, $ttl = 0) {
        return $this->store->store($this->realKey($key), $data, $ttl);
    }
    /**
     * Elimina un valor en cache asociado a una clave
     * @param string $key
     */
    public function delete($key){
        return $this->store->delete($this->realKey($key));
    }    
}
/**
 * Esta clase representa al driver para la cache mediante FileSystem
 * Implementa la interface cache accediendo a la carpeta seleccionada
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cache
 */
class CacheFileSystem extends CacheUtils implements CacheInterface{
    /** Carpeta donde se almacenara la cache
     * @var string */
    public $folder;
    /**
     * Constructor
     * Guarda la carpeta a utilizar, la carpeta se debe indicar desde PathApp
     * @param string $folder
     */
    public function __construct($folder) {
        $this->folder= \EnolaContext::getInstance()->getPathApp() . $folder . '/';
    } 
    /**
     * Retorna la ubicacion de la clave
     * @param string $key
     * @return string
     */
    protected function getFileName($key) {        
        return $this->folder . $key;
    }
    /**
     * Devuelve si existe un dato guardado en cache con esa clave
     * @param string $key
     */
    public function exists($key) {
        return (bool) $this->get($key);
    }
    /**
     * Devuelve un valor guardado en cache o null si no existe
     * @param string $key
     */
    public function get($key) {
        //Consigo la ubicacion del archivo
        $filename = $this->getFileName($key);
        //Si no existe devuelvo NULL
        if (!file_exists($filename)){return NULL;}
        //Abro el archivo en solo lectura
        $file = fopen($filename,'r');
        if(!$file){return NULL;}
        //Consigo un bloqueo compartido de solo lectura
        flock($file,LOCK_SH);
        //Leo el contenido y cierro el archivo
        $fileString = file_get_contents($filename);
        fclose($file);
        //Unserialize los datos y veo que no esten corrompidos o se haya expirado el tiempo
        $data = $this->unPrepareData($fileString);
        if (!$data) {
           //Datos corrompidos, elimino el archivo
           unlink($filename);
           return NULL;
        }else if(time() > $data[0] && $data[0] != 0){
           //Se expiraron los datos, elimino el archivo
           unlink($filename);
           return NULL;
        }
        return $data[1];
    }
    /**
     * Almacena un valor en cache asociado a una clave
     * @param string $key
     * @param type $data
     * @param int $ttl
     */
    public function store($key,$data,$ttl=0) {
        //Abro/Creo el archivo en modo lectura/escritura
        $file = fopen($this->getFileName($key),'a+');
        if(!$file){return FALSE;}
        //Consigo un bloqueo exclusivo
        flock($file,LOCK_EX);
        //Trunco el archivo en caso de que existieran datos viejos
        ftruncate($file,0);
        //Serializo los datos y los guardo con una fecha de expiracion , si es 0 se lo deja indefinidamente
        if($ttl != 0){
            $ttl= time() + $ttl;
        }
        $data = $this->preperaData(array($ttl,$data));
        if (fwrite($file,$data)===false) {return FALSE;}
        return fclose($file);
    }
    /**
     * Elimina un valor en cache asociado a una clave
     * @param string $key
     */
    public function delete($key) {
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }else{
            return false;
        }
    }    
}
/**
 * Esta clase representa al driver para la cache mediante Base de Datos
 * Implementa la interface cache accediendo a la base de datos especificada mediante la clase provista por el framework
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cache
 */
class CacheDataBase extends CacheUtils implements CacheInterface{
    /** Conexion a Base de Datos a utilizar
     * @var string */
    public $nameDB;
    /** Tabla de la Base de Datos a utilizar
     * @var string */
    public $table;
    /** Referencia a la DataBaseAR 
     * @var DataBaseAR */
    public $connection;
    /**
     * Constructor - Inicia una conexion a la base de datos en base a la definicion seleccionada
     * @param string $folder
     */
    public function __construct($nameDB, $table) {
        $this->nameDB= $nameDB;
        $this->table= $table;
        $this->connection= new DataBaseAR(TRUE, $nameDB);
    }
    /**
     * Setea la conexion a la base de datos en base a la definicion seleccionada y la tabla indicada
     * @param string $nameDB
     * @param string $table
     */
    public function setConnection($nameDB, $table){
        $this->nameDB= $nameDB;
        $this->table= $table;
        $this->connection= $this->connection->changeConnection($nameDB);
    }
    /**
     * Devuelve si existe un dato guardado en cache con esa clave
     * @param string $key
     * @return boolean
     */
    public function exists($key){
        return (bool)$this->get($key);
    }
    /**
     * Devuelve un valor guardado en cache o null si no existe
     * @param string $key
     * @return type
     */
    public function get($key){
        $result= $this->connection->getFromWhere($this->table, 'keyCache = :key', array('key' => $key));
        $fila= $result->fetch();
        if($fila != NULL){
            //Unserialize los datos y veo que no esten corrompidos o se haya expirado el tiempo
            $data = $this->unPrepareData($fila['data']);
            if (!$data) {
                //Datos corrompidos, elimino la fila
                $this->delete($key);
                return NULL;
            }else if(time() > $data[0] && $data[0] != 0){
                //Se expiraron los datos, elimino el archivo
                $this->delete($key);
                return NULL;
            }
            return $data[1];
        }
        return NULL;
    }
    /**
     * Almacena un valor en cache asociado a una clave
     * @param string $key
     * @param type $data
     * @param int $ttl
     */
    public function store($key, $data, $ttl = 0) {
        if($ttl != 0){
            $ttl= time() + $ttl;
        }
        $data = $this->preperaData(array($ttl,$data));
        $this->delete($key);
        return $this->connection->insert($this->table, array('keyCache' => $key, 'data' => $data));
    }
    /**
     * Elimina un valor en cache asociado a una clave
     * @param string $key
     * @return boolean
     */
    public function delete($key){
        $this->connection->where('keyCache = :key', array('key' => $key));
        return $this->connection->delete($this->table);
    }
}
/**
 * Esta clase representa al driver para APC (Alternative PHP Cache)
 * Implementa la interface cache llamando a las funciones provistas por la extension APC
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cache
 */
class CacheApc extends CacheUtils implements CacheInterface{
    /**
     * Constructor
     */
    public function __construct() {
    }
    /**
     * Devuelve si existe un dato guardado en cache con esa clave
     * @param string $key
     * @return boolean
     */
    public function exists($key) {
        //Version viejas no contiene la funcion apc_exists
        if(function_exists("apc_exists")){
            return apc_exists($key);
        }else{
            return (bool)apc_fetch($key);
        }
    }
    /**
     * Devuelve un valor guardado en cache o null si no existe
     * @param string $key
     * @return type
     */
    public function get($key){
        return $this->unPrepareData(apc_fetch($key));
    }
    /**
     * Almacena un valor en cache asociado a una clave
     * @param string $key
     * @param type $data
     * @param int $ttl
     */
    public function store($key, $data, $ttl=0){
        return apc_store($key, $this->preperaData($data), $ttl);
    }
    /**
     * Elimina un valor en cache asociado a una clave
     * @param string $key
     * @return boolean
     */
    public function delete($key){
        return apc_delete($key);
    }
}
/**
 * Esta clase representa al driver para MemCached
 * Implementa la interface instanciando al manejador y ejecutando el comportamiento correspondiente del mismo.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cache
 */
class CacheMemCached extends CacheUtils implements CacheInterface{
    /** Referencia a Memcached
     * @var \Memcached */
    public $connection;
    /**
     * Constructor - Instancia la clase Memcached y agrega los servidores indicados
     * @param arry $servers
     * @param type $persistentId 
     */
    public function __construct($servers = array(), $persistentId = NULL) {
        $this->connection= new \Memcached($persistentId);
        foreach ($servers as $value) {
            $this->addServer($value['host'], $value['port'], $value['weight']);
        }
    }
    /**
     * Devuelve si existe un dato guardado en cache con esa clave
     * @param string $key
     * @return boolean
     */
    public function exists($key) {
        return (bool)$this->connection->get($key);
    }
    /**
     * Devuelve un valor guardado en cache o null si no existe
     * @param string $key
     * @return type
     */
    public function get($key){
        return $this->unPrepareData($this->connection->get($key));
    }
    /**
     * Almacena un valor en cache asociado a una clave
     * @param string $key
     * @param type $data
     * @param int $ttl
     */
    public function store($key, $data, $ttl=0){
        return $this->connection->set($key, $this->preperaData($data), $ttl);
    }
    /**
     * Elimina un valor en cache asociado a una clave
     * @param string $key
     * @return boolean
     */
    public function delete($key){
        return $this->connection->delete($key);
    }
    /**
     * Agrega un servidor al sistema memcache
     * @param type $host
     * @param type $port
     * @param type $weight
     * @return boolean
     */
    public function addServer($host, $port, $weight=0){
        return $this->connection->addServer($host, $port, $weight);
    }
}

/**
 * Esta clase representa al driver para Redis
 * Implementa la interface instanciando al manejador y ejecutando el comportamiento correspondiente del mismo.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Cache
 */
class CacheRedis extends CacheUtils implements CacheInterface{
    /** Referencia a Memcached
     * @var \Predis\Client */
    public $connection;
    /**
     * Constructor - Instancia la clase Memcached y agrega los servidores indicados
     * @param arry $servers
     * @param type $persistentId 
     */
    public function __construct($schema, $host, $port) {
        //PredisAutoloader::register();
        $this->connection= new \Predis\Client(array('schema' => $schema, 'host' => $host, 'port' => $port));

    }
    /**
     * Devuelve si existe un dato guardado en cache con esa clave
     * @param string $key
     * @return boolean
     */
    public function exists($key) {
        return $this->connection->exists($key);
    }
    /**
     * Devuelve un valor guardado en cache o null si no existe
     * @param string $key
     * @return type
     */
    public function get($key){
        return $this->unPrepareData($this->connection->get($key));
    }
    /**
     * Almacena un valor en cache asociado a una clave
     * @param string $key
     * @param type $data
     * @param int $ttl
     */
    public function store($key, $data, $ttl=0){
        $this->connection->set($key, $this->preperaData($data));
        return $this->connection->expire($key, $ttl);
    }
    /**
     * Elimina un valor en cache asociado a una clave
     * @param string $key
     * @return boolean
     */
    public function delete($key){
        return $this->connection->del($key);
    }
}