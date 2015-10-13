<?php
namespace Enola\DB;
use Enola\Support;

/**
 * Esta clase provee una abstraccion a la Base de Datos mediante PDO y su armador de consultas al estilo Active Record. 
 * Permite hacer la conexion a la Base de datos de una manera rapida y transparente.
 * Permite realizar consultas al estilo Active Record de una manera sencilla sin tener que escribir codigo SQL.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\DataBase
 */
class DataBaseAR extends Support\GenericLoader{
    protected static $config_db;
    /** @var \PDO */
    public $connection;
    protected $currentDB;
    protected $currentConfiguration;
    //Campos Active Record
    protected $select= "*";
    protected $from= '';
    protected $where= '';
    protected $where_values= array();
    protected $group= '';
    protected $having= '';
    protected $order= '';
    protected $limit= '';
    //Estado de Transaccion
    public $stateTran= TRUE;
    public $errorTran= array();
    public $lastError= NULL;
    /**
     * Constructor que conecta a la bd y carga las librerias que se indicaron en el archivo de configuracion
     * @param bool $conect
     * @param string $nameDB
     */
    function __construct($conect = TRUE, $nameDB = NULL) {
        parent::__construct('db');
	if($conect)$this->connection= $this->getConnection($nameDB);
    }
    /**
     * Abre una conexion en base a la configuracion de la BD
     * @param string $nameDB
     * @return \PDO
     * @throws \PDOException
     */
    protected function getConnection($nameDB = NULL){
        $context= \EnolaContext::getInstance();
	//Leo archivo de configuracion de BD si es la primera vez
        if(self::$config_db == NULL){            
            if($context->isDatabaseDefined()){
                $json_basededatos= file_get_contents(PATHAPP . $context->getConfigurationFolder() . $context->getDatabaseConfiguration());
            }
            else {
                general_error('Data Base', 'The configuration file of the Data Base is not especified', 'error_bd');
            }
            self::$config_db= json_decode($json_basededatos, TRUE);
        }
        //Consulta la bd actual si no se indico opcion
        if($nameDB == NULL)$nameDB= self::$config_db['actual_db'];
        //Cargo las opciones de la bd actual
        $cbd= self::$config_db[$nameDB];
        $this->currentDB= $nameDB;
        $this->currentConfiguration= &self::$config_db[$nameDB];
        //Abro una conexion
        try {
            // Desde 5.3.6 o >
            // $gbd->exec("set names " . $cbd['charset']);
            // Versiones anteriores usaba $cbd['driverbd'].':host='.$cbd['hostname'].';dbname='.$cbd['database'].';charset=utf8';
            
            //Creo el dsn
            $dsn= $cbd['driverbd'].':';
            //Para SQLite y el resto
            if($cbd['driverbd'] == "sqlite"){
                $dsn .= $cbd['database'];
            }else{
                $dsn .= 'host='.$cbd['hostname'].';port='.$cbd['port'].';dbname='.$cbd['database'];
            }
            if($cbd['user'] == ""){
                $cbd['user']= NULL;
                $cbd['pass']= NULL;
            }
            //Abro la conexion                
            $gbd = new \PDO($dsn, $cbd['user'], $cbd['pass'], array(\PDO::ATTR_PERSISTENT => $cbd['persistent']));
            $gbd->exec("SET NAMES '".$cbd['charset']."'");
            if($context->getEnvironment() == 'development'){
                $gbd->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            }else{
                $gbd->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_SILENT);
            }
            //Retorno la conexion 
            return $gbd;
        } 
        catch (\PDOException $e) {
            throw $e;
        }
    }
    /**
     * Almacena los errores
     * @param string $error
     */
    protected function catchError($error){
        if($this->connection->inTransaction()){
            $this->errorTran[]= $error;
            $this->stateTran= FALSE;
        }
        $this->lastError= $error;    
        
    }
    
    /** Cierra la conexion */
    public function closeConnection(){
        $this->connection= NULL;
    }
    /**
     * Realiza la conexion a la base indicada o por defecto
     * @param string $nameDB
     */
    public function connect($nameDB = NULL){
        $this->connection= $this->getConnection($nameDB);
    }
    /** Re conecta a la base actual */
    public function reconnect(){
        $this->connection= $this->getConnection($this->currentDB);
    }
    /** Comienza una Transaccion */
    public function beginTransaction(){
        $this->stateTran= TRUE;
        $this->errorTran= array();
        $this->connection->beginTransaction();
    }
    /** Finaliza una Transaccion - Si fue todo bien realiza commit, en caso contrario rolllBack */
    public function finishTransaction(){
        if($this->stateTran){
            $this->connection->commit();
        }else{
            $this->connection->rollBack();
        }
    }
    
    /*
     * ACTIVE RECORD
     */
    /** 
     * Arma el Select de la consulta
     * @param string $select
     * @param bool $distinct
     */
    public function select($select, $distinct = FALSE){
        if($distinct){
            $this->select= 'DISTINCT ';
        }else{
            $this->select= '';
        }
        $this->select .= $select;
    }
    /**
     * Arma el from de la consulta
     * @param string $table
     */
    public function from($table){
        $this->from= $table . ' ';
    }
    /**
     * Arma Joins de la consulta
     * @param string $table
     * @param string $condition
     * @param string $type
     */
    public function join($table, $condition, $type='INNER JOIN'){
        $this->from.= $type.' '.$table.' ON '.$condition.' ';
    }
    /**
     * Arma el where de la consulta
     * @param string $conditions
     * @param array $values
     */
    public function where($conditions, array $values){
        if($this->where != ''){$this->where.='AND ';}
        $this->where.= $conditions . ' ';
        $this->where_values = array_merge($this->where_values, $values);
    }
    /**
     * Arma el where con or de la consulta
     * @param string $conditions
     * @param array $values
     */
    public function or_where($conditions, array $values){
        if($this->where != ''){$this->where.='OR ';}
        $this->where.= $conditions . ' ';
        $this->where_values = array_merge($this->where_values, $values);
    }
    /**
     * Arma el where like de la consutla
     * @param string $field
     * @param string $match
     * @param string $joker
     * @param bool $not
     */
    public function where_like($field, $match, $joker='both', $not=FALSE){
        if($this->where != ''){$this->where.='AND ';}
        $this->like($field, $match, $joker, $not);
    }
    /**
     * Arma el where like con or de la consutla
     * @param string $field
     * @param string $match
     * @param string $joker
     * @param bool $not
     */
    public function or_where_like($field, $match, $joker='both', $not=FALSE){
        if($this->where != ''){$this->where.='OR ';}
        $this->like($field, $match, $joker, $not);
    }
    /**
     * Arma el Like para el where and o or
     * @param string $field
     * @param string $match
     * @param string $joker
     * @param bool $not
     */
    protected function like($field, $match, $joker='both', $not=FALSE){
        $this->where.= $field . ' ';
        if($not){$this->where.= 'NOT ';}
        $this->where.= 'LIKE ';
        switch ($joker){
            case 'both':
                 $this->where.= "'%$match%' ";
                break;
            case 'after':
                 $this->where.= "'$match%' ";
                break;
            case 'before':
                 $this->where.= "'%$match' ";
                break;
        }
    }
    /**
     * Arma el where in de la consulta
     * @param string $field
     * @param array $values
     * @param bool $not
     */
    public function where_in($field, array $values, $not=FALSE){
        if($this->where != ''){$this->where.='AND ';}
        $this->in($field, $values, $not);       
    }
    /**
     * Arma el where in con or de la consulta
     * @param string $field
     * @param array $values
     * @param bool $not
     */
    public function or_where_in($field, array $values, $not=FALSE){
        if($this->where != ''){$this->where.='OR ';}
        $this->in($field, $values, $not);
    }
    /**
     * Arma el in para el where and o or
     * @param string $field
     * @param array $values
     * @param bool $not
     */
    protected function in($field, array $values, $not=FALSE){
        $this->where.= $field . ' ';
        if($not){$this->where.= 'NOT ';}
        $this->where.= 'IN (';
        foreach ($values as $value) {
            $this->where.= "'$value',";
        }
        $this->where= rtrim($this->where, ',');
        $this->where.= ') ';
    }
    /**
     * Arma el group de la consulta
     * @param string $group
     */
    public function group($group){
        if(is_array($group)){
            $this->group= 'GROUP BY ';
            foreach ($group as $value) {
                $this->group .= $value.',';
            }
            $this->group= rtrim($this->group, ',');
            $this->group.=' ';
        }else{
            $this->group= 'GROUP BY '.$group.' ';
        }
    }
    /**
     * Arma el having de la consulta
     * @param string $conditions
     * @param array $values
     */
    public function having($conditions, array $values){
        if($this->having != ''){$this->having.='AND ';}
        $this->having.= $conditions . ' ';
        $this->where_values = array_merge($this->where_values, $values);
    } 
    /**
     * Arma el having con or de la consulta
     * @param string $conditions
     * @param array $values
     */
    public function or_having($conditions, array $values){
        if($this->having != ''){$this->having.='OR ';}
        $this->having.= $conditions . ' ';
        $this->where_values = array_merge($this->where_values, $values);
    }
    /**
     * Arma el order de la consulta
     * @param string $order
     */
    public function order($order){
        $this->order= 'ORDER BY ' . $order .' ';
    }
    /**
     * Arma el limit de la consulta
     * @param type $limit
     * @param type $offset
     */
    public function limit($limit, $offset = NULL){
        $this->limit= 'LIMIT ' . $limit;
        if($offset != NULL){$this->limit.= ' OFFSET ' . $offset.' ';}
    }
    /**
     * Devuelve el resultado de la consulta armada de forma ActiveRecord
     * @return \PDOStatement o FALSE
     * @throws \PDOException
     */
    public function get(){
        $res= FALSE;
        try{
            //Armo y preparo la consulta
            $query= $this->prepareSelect($this->select, $this->from, $this->where, $this->group, $this->having, $this->order, $this->limit);
            //Ejecuto la consulta
            $query->execute($this->where_values);
            //Controlo que este todo bien
            if($this->isOk($query)){
                $res= $query;
            }
            //Limpio las variables del AR
            $this->cleanVars();
        } catch (\PDOException $e) {
            $this->cleanVars();
            throw $e;
        }
        return $res;
    }    
    /**
     * Devuelve el resultado de la consulta armada en base a los parametros
     * @param string $from
     * @param string $where
     * @param array $where_values
     * @param string $order
     * @param type $limit
     * @param type $offset
     * @return \PDOStatement o FALSE
     * @throws \PDOStatement
     */
    public function getFromWhere($from, $where=NULL, $where_values=array(), $order=NULL, $limit=NULL, $offset=NULL){
        $res= FALSE;
        try{
            //Armo partes de la consulta
            if($order != NULL){$order= " ORDER BY " . $order;}
            if($limit != NULL){
                $limit= " LIMIT " . $limit;
                if($offset != NULL){$limit.= ' OFFSET ' . $offset;}
            }
            //Armo y preparo la consulta
            $query= $this->prepareSelect($this->select, $from, $where, '', '', $order, $limit);
            //Ejecuto la consulta
            $query->execute($where_values);
            //Controlo que este todo bien
            if($this->isOk($query)){
                $res= $query;
            }
            //Limpio las variables del AR
            $this->cleanVars();
        } catch (\PDOException $e) {
            $this->cleanVars();
            throw $e;
        }
        return $res;
    }
    /**
     * Inserta en una tabla los valores indicados
     * @param string $table
     * @param array $values
     * @return boolean
     * @throws \PDOException
     */
    public function insert($table, array $values){
        try{
            //Armo y preparo la consulta
            $query= $this->prepareInsert($table, $values);
            //Ejecuto la consulta
            $query->execute($values);
            $error= $query->errorInfo();
            //Retorno si salio todo bien o no
            return $this->isOk($query);
        } catch (\PDOException $e) {
            throw $e;
        }
    }
    /**
     * Actualiza una tabla en base a los datos indicados y la consulta armada al estilo Active Record
     * @param string $table
     * @param array $values
     * @return boolean
     * @throws \PDOException
     */
    public function update($table, array $values){
        $res= FALSE;
        try{
            $query= $this->prepareUpdate($table, $values, $this->where);
            //Uno los values pasados y los del where
            $values= array_merge($values, $this->where_values);
            //Ejecuto la consulta
            $query->execute($values);
            //Veo si salio todo bien
            $res= $this->isOk($query);
            //Limpio las variables del AR
            $this->cleanVars();
        } catch (\PDOException $e) {
            $this->cleanVars();
            throw $e;
        }
        return $res;
    }    
    /**
     * Elimina tuplas de una tabla en base a la consulta armada de la forma Active Record
     * @param string $table
     * @return boolean
     * @throws \PDOException
     */
    public function delete($table){        
        $res= FALSE;
        try{
            //Armo y preparo la consulta
            $query= $this->prepareDelete($table, $this->where);
            //Ejecuto la consulta
            $query->execute($this->where_values);            
            //Veo si salio todo bien
            $res= $this->isOk($query);
            //Limpio las variables del AR
            $this->cleanVars();
        } catch (\PDOException $e) {
            $this->cleanVars();
            throw $e;
        }
        return $res;
    }    
    /*
     * ACTIVE RECORD - INTERNAS
     */
    /**
     * Retorna si la consulta se realizao con exito, si no ocurrio ningun error.
     * @param \PDOStatement $query
     * @return boolean
     */
    protected function isOk(\PDOStatement $query){
        $error= $query->errorInfo();
        if($error[0] == '00000'){
            return TRUE;
        }else{
            $this->catchError($error);
            return FALSE;
        }
    }
    /** Limpia las variables de instancia del ActiveRecord */
    protected function cleanVars(){
        $this->select= "*";
        $this->from= '';
        $this->where= '';
        $this->where_values= array();
        $this->group= '';
        $this->having= '';
        $this->order= '';
        $this->limit= '';
    }    
    /**
     * Elimina elementos de $vars que tengan como clave el valor de un elemento de $excepts_vars
     * @param array $vars
     * @param array $excepts_vars
     * @return array
     */
    protected function deleteVars($vars, $excepts_vars){
        foreach ($excepts_vars as $value) {
            unset($vars[$value]);
        }
        return $vars;
    }
    /**
     * Retorna un PDOStatement SELECT armado en base a los parametros pasados
     * @param string $select
     * @param string $from
     * @param string $where
     * @param string $group
     * @param string $having
     * @param string $order
     * @param string $limit
     * return PDOStatement
     */
    protected function prepareSelect($select, $from, $where='', $group='', $having='', $order='', $limit=''){     
        $sql= "SELECT " . $select;
        $sql.= " FROM " . $from;
        if($where != '' && $where != NULL){$sql.= "WHERE " . $where;}        
        $sql.= $group;
        if($having != '' && $having != NULL){$sql.= "HAVING " . $having;}
        $sql.= $order;
        $sql.= $limit;
        echo $sql;
        //Preparo la consulta y la retorno
        return $this->connection->prepare($sql);
    }
    /**
     * Retorna un PDOStatement INSERT armado en base a los parametros pasados
     * @param string $table
     * @param array $values
     * @return PDOStatement
     */
    protected function prepareInsert($table, $values){
        $sql= 'INSERT INTO ' . $table . ' (';
        $value= 'values(';
        foreach ($values as $key => $val) {
            $sql.= $key . ',';
            $value.= ':' . $key . ',';
        }
        $sql = trim($sql, ',');
        $value = trim($value, ',');
        $sql .= ') ' . $value . ')';
        //Preparo la consulta y la retorno
        return $this->connection->prepare($sql);
    }
    /**
     * Retorna un PDOStatement UPDATE armado en base a los parametros pasados
     * @param string $table
     * @param array $values
     * @param string $where
     * @return PDOStatement
     */
    protected function prepareUpdate($table, $values, $where){
        $sql= 'UPDATE ' . $table . ' SET ';
        foreach ($values as $key => $value) {
            $sql .= $key . '=:' . $key . ',';
        }
        $sql = trim($sql, ',');
        if($where != ''){$sql.= " WHERE " . $where;}
        //Preparo la consulta y la retorno
        return  $this->connection->prepare($sql);
    }
    /**
     * Retorna un PDOStatement DELETE armado en base a los parametros pasados
     * @param string $table
     * @param string $where
     * @return PDOStatement
     */
    protected function prepareDelete($table, $where){
        $sql= 'DELETE FROM ' . $table . ' ';            
        if($where != ''){$sql.= " WHERE " . $where;}
    }
}