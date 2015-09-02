<?php
namespace Enola\Cache;
use Enola\DB\En_DataBase;

interface CacheInterface {
    /**
     * Devuelve si existe un dato guardado en cache con esa clave
     * @param type $key
     */
    public function exists($key);
    /**
     * Devuelve un valor guardado en cache o null si no existe
     * @param type $key
     */
    public function get($key);
    /**
     * Almacena un valor en cache asociado a una clave
     * @param type $key
     * @param type $value
     */
    public function store($key, $data, $ttl=0);
    /**
     * Elimina un valor en cache asociado a una clave
     * @param type $key
     */
    public function delete($key);
}

class Cache implements CacheInterface{
    private static $config;
    public $prefix;
    public $store;
    
    public function __construct($store = "Default") {
        $context= \EnolaContext::getInstance();
        if(self::$config == NULL){
            $json_cache= file_get_contents(PATHAPP . $context->getConfigurationFolder() . 'cache.json');
            self::$config= json_decode($json_cache, TRUE);
        }
        $this->prefix= self::$config["prefix"];
        $this->setCacheStore($store);
    }
    
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
                $this->store= new CacheMemCache($this->prefix, $config["servers"]);
                break;
            default:
                break;
        }
    }
    
    public function exists($key){
        return $this->store->exists($this->realKey($key));
    }
    public function get($key){
        return $this->store->get($this->realKey($key));
    }
    public function store($key, $data, $ttl = 0) {
        return $this->store->store($this->realKey($key), $data, $ttl);
    }
    public function delete($key){
        return $this->store->delete($this->realKey($key));
    }
    
    protected function realKey($key){
        return $this->prefix . md5($key);
    }
}

class CacheFileSystem implements CacheInterface{
    public $folder;
    
    public function __construct($folder) {
        $this->folder= \EnolaContext::getInstance()->getPathApp() . $folder . '/';
    }    
    
    public function exists($key) {
        return (bool) $this->get($key);
    }
    
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
        $data = unserialize($fileString);
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
    
    public function store($key,$data,$ttl=0) {
        //Abro/Creo el archivo en modo lectura/escritura
        $file = fopen($this->getFileName($key),'a+');
        if(!$file){throw new Exception('Could not write to cache');}
        //Consigo un bloqueo exclusivo
        flock($file,LOCK_EX);
        //Trunco el archivo en caso de que existieran datos viejos
        ftruncate($file,0);
        //Serializo los datos y los guardo con una fecha de expiracion , si es 0 se lo deja indefinidamente
        if($ttl != 0){
            $ttl= time() + $ttl;
        }
        $data = serialize(array($ttl,$data));
        if (fwrite($file,$data)===false) {
          throw new Exception('Could not write to cache');
        }
        fclose($file);
    }

    public function delete($key) {
        $filename = $this->getFileName($key);
        if (file_exists($filename)) {
            return unlink($filename);
        }else{
            return false;
        }
    }

    private function getFileName($key) {        
        return $this->folder . $key;
    }
}

class CacheDataBase implements CacheInterface{
    public $nameDB;
    public $table;
    public $connection;
    
    public function __construct($nameDB, $table) {
        $this->nameDB= $nameDB;
        $this->table= $table;
        $this->connection= new En_DataBase(TRUE, $nameDB);
    }
    
    public function setConnection($nameDB, $table){
        $this->nameDB= $nameDB;
        $this->table= $table;
        $this->connection= $this->connection->changeConnection($nameDB);
    }
    
    public function exists($key){
        $result= $this->connection->getFromWhere($this->table, 'keyCache = :key', array('key' => $key));
        $fila= $result->fetch();
        if($fila != NULL){
            //Unserialize los datos y veo que no esten corrompidos o se haya expirado el tiempo
            $data = unserialize($fila['data']);
            if (!$data) {
                //Datos corrompidos, elimino la fila
                $this->delete($key);
                return FALSE;
            }else if(time() > $data[0] && $data[0] != 0){
                //Se expiraron los datos, elimino el archivo
                $this->delete($key);
                return FALSE;
            }
            return TRUE;
        }
        return FALSE;
    }
    
    public function get($key){
        $result= $this->connection->getFromWhere($this->table, 'keyCache = :key', array('key' => $key));
        $fila= $result->fetch();
        if($fila != NULL){
            //Unserialize los datos y veo que no esten corrompidos o se haya expirado el tiempo
            $data = unserialize($fila['data']);
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
    
    public function store($key, $data, $ttl = 0) {
        if($ttl != 0){
            $ttl= time() + $ttl;
        }
        $data = serialize(array($ttl,$data));
        $this->connection->insert($this->table, array('keyCache' => $key, 'data' => $data));
    }
    
    public function delete($key){
        $this->connection->where('keyCache = :key', array('key' => $key));
        return $this->connection->delete($this->table);
    }
}

class CacheApc implements CacheInterface{
    public function __construct() {
    }
    
    public function exists($key) {
        //Version viejas no contiene la funcion apc_exists
        if(function_exists("apc_exists")){
            return apc_exists($key);
        }else{
            return (bool)apc_fetch($key);
        }
    }
    
    public function get($key){
        return apc_fetch($key);
    }
    
    public function store($key, $data, $ttl=0){
        return apc_store($key, $data, $ttl);
    }
    
    public function delete($key){
        return apc_delete($key);
    }
}

class CacheMemCache implements CacheInterface{
    public $connection;

    public function __construct($persistent_id = NULL, $servers = array()) {
        $this->connection= new Memcached($persistent_id);
        foreach ($servers as $value) {
            $this->addServer($value['host'], $value['port'], $value['weight']);
        }
    }
    
    public function exists($key) {
        return (bool)$this->connection->get($key);
    }
    
    public function get($key){
        $this->connection->get($key);
    }
    
    public function store($key, $data, $ttl=0){
        $this->connection->set($key, $data, $ttl);
    }
    
    public function delete($key){
        $this->connection->delete($key);
    }
    
    public function addServer($host, $port, $weight=0){
        $this->connection->addServer($host, $port, $weight);
    }
}

class CacheRedis implements CacheInterface{
    public $connection;

    public function __construct() {
    }
    
    public function exists($key) {
        
    }
    
    public function get($key){

    }
    
    public function store($key, $data, $ttl=0){

    }
    
    public function delete($key){

    }
}