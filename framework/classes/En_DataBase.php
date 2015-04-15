<?php
/**
 * Clase que se encarga de la configuracion de la BD.
 * Para utilizar la configuracion del Framework es necesario que las clases extiendan de esta clase
 * @author Enola
 */
class En_DataBase extends Enola{
    protected static $config_db;
    protected $conexion;
    protected $currentDB;
    protected $currentConfiguration;
    
    protected $select= "*";
    protected $from= '';
    protected $where= '';
    protected $where_values= array();
    protected $group= '';
    protected $having= '';
    protected $order= '';
    protected $limit= '';
    
    public $stateTran= TRUE;
    public $errorTran= array();
    public $lastError= NULL;
    /**
     * Constructor que conecta a la bd y carga las librerias que se indicaron en el archivo de configuracion
     */
    function __construct($conect = TRUE, $nameDB = NULL) {
        parent::__construct('db');
	if($conect)$this->conexion= $this->getConexion($nameDB);
    }
    /**
     * Abre una conexion en base a la configuracion de la BD
     * @return \PDO
     */
    protected function getConexion($nameDB = NULL){
	//Leo archivo de configuracion de BD si es la primera vez
        if(self::$config_db == NULL){            
            if(defined('JSON_CONFIG_BD')){
                $json_basededatos= file_get_contents(PATHAPP . CONFIGURATION . JSON_CONFIG_BD);
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
            // 5.3.5 o < y luego 5.3.6 o >
            //Cuidado que charset=utf8 puede no funcar para versiones viejas y luego en opciones
            //superiores habria q usar PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
            //Por ahora uso las 2 y anda la que anda
            //Creo el dsn
            $dsn=  $cbd['driverbd'].':host='.$cbd['hostname'].';dbname='.$cbd['database'].';charset='.$cbd['charset'];
            //Abro la conexion                
            $gbd = new PDO($dsn, $cbd['user'], $cbd['pass'], array(PDO::ATTR_PERSISTENT => $cbd['persistente'], PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES '.$cbd['charset']));
            if(ENVIRONMENT == 'development'){
                $gbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }else{
                $gbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
            }
            //Retorno la conexion 
            return $gbd;
        } 
        catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
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
    /** Elimina elementos de $vars que tengan como clave el valor de un elemento de $excepts_vars */
    private function deleteVars($vars, $excepts_vars){
        foreach ($excepts_vars as $value) {
            unset($vars[$value]);
        }
        return $vars;
    }
    /** Almacena los errores */
    protected function catchError($error){
        if($this->conexion->inTransaction()){
            $this->errorTran[]= $error;
            $this->stateTran= FALSE;
        }
        $this->lastError= $error;    
        
    }
    
    /** Cierra la conexion */
    public function closeConexion(){
        $this->conexion= NULL;
    }
    /** Cambia la conexion actual */
    public function changeConexion($nameDB = NULL){
        $this->conexion= $this->getConexion($nameDB);
    }
    /** Comienza una Transaccion */
    public function beginTransaction(){
        $this->stateTran= TRUE;
        $this->errorTran= array();
        $this->conexion->beginTransaction();
    }
    /** Finaliza una Transaccion - Si fue todo bien realiza commit, en caso contrario rolllBack */
    public function finishTransaction(){
        if($this->stateTran){
            $this->conexion->commit();
        }else{
            $this->conexion->rollBack();
        }
    }
    
    /*
     * ACTIVE RECORD and MORE
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
        if($this->where != '')$this->where.='AND ';
        $this->where.= $conditions . ' ';
        $this->where_values = array_merge($this->where_values, $values);
    }
    /**
     * Arma el where con or de la consulta
     * @param string $conditions
     * @param array $values
     */
    public function or_where($conditions, array $values){
        if($this->where != '')$this->where.='OR ';
        $this->where.= $conditions . ' ';
        $this->where_values = array_merge($this->where_values, $values);
    }
    /**
     * Arma el where like de la consutla
     * @param string $field
     * @param type $match
     * @param string $joker
     * @param bool $not
     */
    public function where_like($field, $match, $joker='both', $not=FALSE){
        if($this->where != '')$this->where.='AND ';
        $this->like($field, $match, $joker, $not);
    }
    /**
     * Arma el where like con or de la consutla
     * @param string $field
     * @param type $match
     * @param string $joker
     * @param bool $not
     */
    public function or_where_like($field, $match, $joker='both', $not=FALSE){
        if($this->where != '')$this->where.='OR ';
        $this->like($field, $match, $joker, $not);
    }    
    protected function like($field, $match, $joker='both', $not=FALSE){
        $this->where.= $field . ' ';
        if($not)$this->where.= 'NOT ';
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
        if($this->where != '')$this->where.='AND ';
        $this->in($field, $values, $not);       
    }
    /**
     * Arma el where in con or de la consulta
     * @param string $field
     * @param array $values
     * @param bool $not
     */
    public function or_where_in($field, array $values, $not=FALSE){
        if($this->where != '')$this->where.='OR ';
        $this->in($field, $values, $not);
    }    
    protected function in($field, array $values, $not=FALSE){
        $this->where.= $field . ' ';
        if($not)$this->where.= 'NOT ';
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
        if($this->having != '')$this->having.='AND ';
        $this->having.= $conditions . ' ';
        $this->where_values = array_merge($this->where_values, $values);
    } 
    /**
     * Arma el having con or de la consulta
     * @param string $conditions
     * @param array $values
     */
    public function or_having($conditions, array $values){
        if($this->having != '')$this->having.='OR ';
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
        if($offset != NULL)$this->limit.= ' OFFSET ' . $offset.' ';
    }
    /**
     * Devuelve el resultado de la consulta armada de forma ActiveRecord
     * @return PDOStatement
     * @throws PDOException
     */
    public function get(){
        $res= FALSE;
        try{
            $sql= "";           
            $sql.= "SELECT " . $this->select;
            $sql.= " FROM " . $this->from;
            if($this->where != '')$sql.= "WHERE " . $this->where;                
            $sql.= $this->group;
            if($this->having != '')$sql.= "HAVING " . $this->having;
            $sql.= $this->order;
            $sql.= $this->limit;
            
            $consulta= $this->conexion->prepare($sql);        
            foreach ($this->where_values as $key => $value){
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }else{
                    $consulta->bindValue($key, $value);
                }
            }
            $consulta->execute();
            $error= $consulta->errorInfo();
            if($error[0] == '00000'){
                $res= $consulta;
            }else{
                $this->catchError($error);
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }
        $this->cleanVars();
        return $res;
    }
    /**
     * Devuelve un conjunto de objetos de la clase especificada en base a la consulta armada de la forma ActiveRecord
     * @param string $class
     * @return PDOStatement
     */
    public function getInObjects($class){
        $res= $this->get();
        if($res !== FALSE){
            return $this->resultsInObjects($res, $class);
        }
        return $res;
    }
    /**
     * Devuelve el resultado de la consulta armada
     * @param string $from
     * @param string $where
     * @param array $where_values
     * @param string $order
     * @param type $limit
     * @param type $offset
     * @return 
     * @throws PDOStatement
     */
    public function getFromWhere($from, $where=NULL, $where_values=array(), $order=NULL, $limit=NULL, $offset=NULL){
        $res= FALSE;
        try{
            $sql= "";           
            $sql.= "SELECT " . $this->select;
            $sql.= " FROM " . $from;
            if($where != NULL)$sql.= " WHERE " . $where;
            if($order != NULL)$sql.= " ORDER BY " . $order;
            if($limit != NULL){
                $sql.= " LIMIT " . $limit;
                if($offset != NULL)$sql.= ' OFFSET ' . $offset;
            }
            
            $consulta= $this->conexion->prepare($sql);        
            foreach ($where_values as $key => $value){
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }else{
                    $consulta->bindValue($key, $value);
                }
            }
            $consulta->execute();
            $error= $consulta->errorInfo();
            if($error[0] == '00000'){
                $res= $consulta;
            }else{
                $this->catchError($error);
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }
        $this->cleanVars();
        return $res;
    }
    /**
     * Devuelve un conjunto de objetos de la clase especificada en base a la consulta armada
     * @param string $class
     * @param string $from
     * @param string $where
     * @param array $where_values
     * @param string $order
     * @param type $limit
     * @param type $offset
     * @return 
     * @throws PDOStatement
     */
    public function getFromWhereInObjects($class, $from, $where=NULL, $where_values=array(), $order=NULL, $limit=NULL, $offset=NULL){
        $res= $this->getFromWhere($from,$where,$where_values,$order,$limit,$offset);
        if($res !== FALSE){
            return $this->resultsInObjects($res, $class);
        }
        return $res;
    }
    /**
     * Inserta en una tabla los valores indicados
     * @param string $table
     * @param array $values
     * @return boolean
     * @throws PDOException
     */
    public function insert($table, array $values){
        try{
            $sql= 'INSERT INTO ' . $table . ' (';
            $value= 'values(';
            foreach ($values as $key => $val) {
                $sql.= $key . ',';
                $value.= ':' . $key . ',';
            }
            $sql = trim($sql, ',');
            $value = trim($value, ',');
            $sql .= ') ' . $value . ')';
            $consulta= $this->conexion->prepare($sql);
            foreach ($values as $key => $value) {
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }else{
                    $consulta->bindValue($key, $value);
                }
            }
            $consulta->execute();
            $error= $consulta->errorInfo();
            if($error[0] != '00000'){
                $this->catchError($error);
                return FALSE;
            }
            else{
                return TRUE;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }
    }
    /**
     * Actualiza una tabla en base a los datos indicados y la consulta armada en forma ActiveRecord
     * @param string $table
     * @param array $values
     * @return boolean
     * @throws PDOException
     */
    public function update($table, array $values){
        $res= FALSE;
        try{
            $sql= 'UPDATE ' . $table . ' SET ';
            foreach ($values as $key => $value) {
                $sql .= $key . '=:' . $key . ',';
            }
            $sql = trim($sql, ',');
            if($this->where != '')$sql.= " WHERE " . $this->where; 

            $consulta= $this->conexion->prepare($sql);
            $values= array_merge($values, $this->where_values);
            foreach ($values as $key => $value){
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }else{
                    $consulta->bindValue($key, $value);
                }
            }
            $consulta->execute();
            $error= $consulta->errorInfo();
            if($error[0] == '00000'){
                $res= TRUE;
            }else{
                $res= FALSE;
                $this->catchError($error);
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }
        $this->cleanVars();
        return $res;
    }
    /**
     * Elimina tuplas de una tabla en base a la consulta armada de la forma ActiveRecord
     * @param string $table
     * @return boolean
     * @throws PDOException
     */
    public function delete($table){        
        $res= FALSE;
        try{
            $sql= 'DELETE FROM ' . $table . ' ';            
            if($this->where != '')$sql.= " WHERE " . $this->where; 

            $consulta= $this->conexion->prepare($sql);
            foreach ($this->where_values as $key => $value){
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }else{
                    $consulta->bindValue($key, $value);
                }
            }
            $consulta->execute();            
            $error= $consulta->errorInfo();
            if($error[0] != '00000'){
                $res= FALSE;
                $this->catch_error($error);                
            }else{
                $res= TRUE;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode());
        }
        $this->cleanVars();
        return $res;
    }
    
    /**
     * En base a la ejecucion de una consulta y una clase devuelve un arreglo con instancias de la clase pasada
     * con los respectivos valores que trajo la consulta
     * @param type $PdoStatement
     * @param type $class
     * @return \class
     */
    public function resultsInObjects($PdoStatement, $class){
        $result= array();
        while($reg= $PdoStatement->fetchObject()){
            $instanciaClase= new $class();
            foreach ($reg as $key => $value) {
                if(property_exists($instanciaClase, $key)){
                   $ref= new ReflectionProperty($instanciaClase,$key);
                   if($ref->isPublic())$instanciaClase->$key= $value;
                }
            }
            $result[]= $instanciaClase;
        }
        return $result;
    }
    /**
     * En base a la ejecucion de una consulta y una clase devuelve una instancia de la clase pasada
     * con los respectivos valores que trajo la consulta
     * @param type $PdoStatement
     * @param type $class
     * @return null|\class
     */
    public function firstResultInObject($PdoStatement, $class){
        $tupla= $PdoStatement->fetchObject();
        if($tupla == NULL){
            return NULL;
        }
        else{
            $instanciaClase= new $class();
            foreach ($tupla as $key => $value) {
                if(property_exists($instanciaClase, $key)){
                    $ref= new ReflectionProperty($instanciaClase,$key);
                    if($ref->isPublic())$instanciaClase->$key= $value;
                }
            }
            return $instanciaClase;
        }
    }
    /**
     * En base a una tabla especificada y un objeto agrega el objeto en la tabla. 
     * Usa todos los atributos publicos del objeto
     * @param type $table
     * @param type $object
     * @param type $excepts_vars
     * @return boolean
     */
    public function insertObject($table, $object, $excepts_vars = array()){
        try{
            //Consigo las variables publicas del objeto
            $vars= get_object_vars($object);
            $vars= $this->deleteVars($vars, $excepts_vars);
            $sql= 'INSERT INTO ' . $table . ' (';
            $values= 'values(';
            foreach ($vars as $key => $value) {
                $sql.= $key . ',';
                $values.= ':' . $key . ',';
            }
            $sql = trim($sql, ',');
            $values = trim($values, ',');
            $sql .= ') ' . $values . ')';
            $consulta= $this->conexion->prepare($sql);
            foreach ($vars as $key => $value) {
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }else{
                    $consulta->bindValue($key, $value);
                }
            }
            $consulta->execute();
            $error= $consulta->errorInfo();
            if($error[0] != '00000'){
                $this->catchError($error);
                return FALSE;
            }
            else{
                return TRUE;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }
    }
    /**
     * En base a una tabla especificada y un objeto modifica el objeto en la tabla. 
     * Usa todos los atributos publicos del objeto
     */
    public function updateObject($table, $object, $where = '', $where_values = array(), $excepts_vars = array()){
        try{
            $vars= get_object_vars($object);
            //Consigo las variables publicas del objeto
            $vars= $this->deleteVars($vars, $excepts_vars);
            $sql= 'UPDATE ' . $table . ' SET ';
            foreach ($vars as $key => $value) {
                $sql .= $key . '=:' . $key . ',';
            }
            $sql = trim($sql, ',');
            if($where != ''){
                $sql .= ' WHERE ' . $where;
            }
            $consulta= $this->conexion->prepare($sql);
            $vars = array_merge($vars, $where_values);
            foreach ($vars as $key => $value){
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }else{
                    $consulta->bindValue($key, $value);
                }
            }
            $consulta->execute();
            $error= $consulta->errorInfo();
            if($error[0] != '00000'){
                $this->catchError($error);
                return FALSE;
            }
            else{
                return TRUE;
            }
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), $e->getCode());
        }
    }
}
?>