<?php
namespace Enola\Handler;

/*$sess = new Enola\Handler\SessionHandlerDb();
session_set_save_handler($sess);*/

/**
 * Manejador de Session a traves de Base de Datos
 * Basado de http://vardump.es/2016/01/guardar-sesiones-en-base-de-datos/
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Handler
 * @version 1.0
 */
class SessionHandlerDB implements \SessionHandlerInterface{
    /**
     * @var \Enola\DB\DataBaseAR
     */
    protected $connection;
    /** Indica la conexion a la base de datos
     * @var string
     */
    protected $dataBase= 'Local';
    /** Indica si la conexion esta abierta
     * @var boolean
     */
    protected $opened = FALSE;
    /** Indica el tiempo de expiration de las sessiones
     * @var string
     */
    protected $expirationTime= 'P10D';    
    
    public function __construct(){
        // marcar la sesión como cerrada al inicio
        $this->opened = FALSE;
    }
    /**
     * Abre la conexion a la base de datos si no esta abierta
     * @param string $sessionPath
     * @param string $sessionName
     * @return boolean
     */
    public function open($sessionPath, $sessionName){        
        //si la sesión no está abierta tengo que abrir conexión a base de datos
        if(!$this->opened){
            $this->connection= new \Enola\DB\DataBaseAR(FALSE);
            $this->connection->connect($this->dataBase);
            $this->opened= TRUE;
            return TRUE;
        }
        else{
            return TRUE;
        }
    }
    /**
     * Cierra la conexion a la base de datos
     * @return boolean
     */
    public function close(){
        $this->connection->closeConnection();
        $this->opened= FALSE;
        return TRUE;
    }
    /**
     * Lee los datos de la session de la base de datos
     * Si no hay nada retorna ""
     * @param string $sessionId
     * @return string
     */  
    public function read($sessionId){
        //si la sesión está abierta y tengo lock 
        if($this->opened){
            $sql= $this->connection->connection->prepare('SELECT session_data FROM sessions WHERE session_id = :id');
            $sql->bindValue('id', $sessionId);
            $sql->execute();
            $result= $sql->fetch(\PDO::FETCH_ASSOC);
            if($result != NULL){
                return $result['session_data'];                
            }
            return '';
        }
        else{
            return '';
        }
    }
    /**
     * Escribe los datos de la session en la base de datos
     * @param string $sessionId
     * @param string $sessionData
     * @return boolean
     */
    public function write($sessionId, $sessionData){
        //si la sesión está abierta y tengo lock 
        if($this->opened){
            $sql= $this->connection->connection->prepare('REPLACE INTO sessions (session_id, session_data, session_expiration) VALUES(:id, :data, :expiration)');
            $sql->bindValue('id', $sessionId);
            $sql->bindValue('data', $sessionData);
            $fecha= new \DateTime();
            $fecha->add(new \DateInterval($this->expirationTime));
            $sql->bindValue('expiration', $fecha->format('Y-m-d H:i:s'));
            return $sql->execute() === TRUE;
        }
        else{
            return FALSE;
        }
    }
    /**
     * Destruye los datos e la session de la base de datos
     * @param string $sessionId
     * @return boolean
     */    
    public function destroy($sessionId){
        //si la sesión está abierta y tengo lock 
        if($this->opened){
            $sql= $this->connection->connection->prepare('DELETE FROM sessions WHERE session_id = :id');
            $sql->bindValue('id', $sessionId);
            return $sql->execute() === TRUE;
        }
        else{
            return FALSE;
        }
    }
    /**
     * Elimina las session en la que su tiempo de expiration sea menor que el tiempo actual
     * @param int $lifetime
     * @return boolean
     */
    public function gc($lifetime){
        //si la sesión está abierta y tengo lock 
        if($this->opened){
            $fecha= new \DateTime();
            $sql= $this->connection->connection->prepare('DELETE FROM sessions WHERE session_expiration < :fecha');
            $sql->bindValue('fecha', $fecha->format('Y-m-d H:i:s'));
            return $sql->execute() === TRUE;
        }   
        else{
            return FALSE;
        }
    }
}

class SessionsDaoMysql extends \Enola\DB\DataBaseAR{
    
    public function __construct($conect = FALSE, $nameDB = NULL, $configFile = NULL) {
        parent::__construct($conect, $nameDB, $configFile);
    }
        
    public function sessionData($id, $key){
        $this->select('d.session_id, d.key_data, d.data');
        $this->from('sessions_data d');
        $this->join('sessions s', 'd.session_id = s.session_id');
        $this->where('d.session_id = :id AND d.key_data = :key', array('id' => $id, 'key' => $key));
        return $this->get()->fetch(PDO::FETCH_ASSOC);
    }
    
    public function storeSessionData($data){
        return $this->insert('sessions_data', $data);
    }
    
    public function updateSessionData($id, $key, $data){
        $this->where('session_id = :id AND key_data = :key', array('id' => $id, 'key' => $key));
        return $this->update('sessions_data', $data);
    }
    
    public function deleteSessionData($id){
        $this->where('session_id = :id', array('id' => $id));
        return $this->delete('sessions_data');
    }
    
    public function deleteSessionDataSpecific($id, $key){
        $this->where('session_id = :id AND key_data = :key', array('id' => $id, 'key' => $key));
        return $this->delete('sessions_data');
    }
}