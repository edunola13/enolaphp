<?php
/**
 * Clase que se encarga de la configuracion de la BD.
 * Para utilizar la configuracion del Framework es necesario que las clases extiendan de esta clase
 * @author Enola
 */
class En_DataBase extends Enola{
    protected $config_db;
    protected $conexion;
    
    protected $select= "*";
    protected $from;
    protected $where;
    protected $where_values;
    protected $group;
    protected $having;
    protected $order;
    protected $limit;
    /**
     * Constructor que conecta a la bd y carga las librerias que se indicaron en el archivo de configuracion
     */
    function __construct($conect = TRUE) {
        parent::__construct('db');
	if($conect){
            $this->conexion= $this->get_conexion();
	}
    }
    /**
     * Abre una conexion en base a la configuracion de la BD
     * @return \PDO
     */
    protected function get_conexion($opcion = NULL){
	//Leo archivo de configuracion de BD si es la primera vez
        if($this->config_db == NULL){
            if(defined('JSON_CONFIG_BD')){
                $json_basededatos= file_get_contents(PATHAPP . CONFIGURATION . JSON_CONFIG_BD);
            }
            else {
                general_error('Data Base', 'The configuration file of the Data Base is not especified', 'error_bd');
            }
            $this->config_db= json_decode($json_basededatos, TRUE);
        }
        //Consulta la bd actual si no se indico opcion
        if($opcion == NULL)$opcion= $this->config_db['actual_db'];
        //Cargo las opciones de la bd actual
        $cbd= $this->config_db[$opcion];
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
            $gbd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            //Guarda la conexion en un variable global
            $GLOBALS['gbd']= $gbd;
            //Retorno la conexion 
            return $gbd;
        } 
        catch (PDOException $e) {
            general_error('Conexion Error', $e->getMessage(), 'error_bd');
        }
    }
    /**
     * Cierra la conexion
     */
    protected function close_conexion(){
        $this->conexion= NULL;
    }
    
    public function select($select, $distinct = FALSE){
        if($distinct){
            $this->select= 'DISTINCT ';
        }else{
            $this->select= '';
        }
        $this->select .= $select;
    }
    
    public function from($table){
        $this->from= $table . ' ';
    }
    
    public function join($table, $condition, $type='INNER JOIN'){
        $this->from.= $type.' '.$table.' '.$condition.' ';
    }
    
    public function where($where){
        //Estos deberian ser un par de metodos
    }
    
    public function group($group){
        if(is_array($group)){
            $this->group= 'GROUP BY ';
            foreach ($group as $value) {
                $this->group .= $value.',';
            }
            $this->group= rtrim($this->group, ',');
        }else{
            $this->group= $group;
        }
    }
    
    public function having($having){
        //Igual que el where
    }
    
    public function order($order){
        $this->order= 'ORDER BY ' . $order;
    }
    
    public function limit($limit, $offset = NULL){
        $this->limit= 'LIMIT ' . $limit;
        if($offset != NULL)$this->limit.= ',' . $offset;
    }
    
    public function get($from = NULL, $order = NULL, $limit = NULL){
        try{
            $sql= "";           
            $sql.= "SELECT " . $this->select;
            if($from != NULL)$this->from= $from;
            $sql.= " FROM " . $this->from;
            $sql.= $this->join;
            if($this->where != '')$sql.= " WHERE " . $this->where;                
            $sql.= $this->group;
            if($this->having != '')$sql.= " HAVING " . $this->having;
            $sql.= $this->having;
            $sql.= $this->order;
            $sql.= $this->limit;

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
            if($error[0] != 00000){            
                return FALSE;
            }else{
                return $consulta;
            }
        } catch (PDOException $e) {
            general_error('PDO Error', $e->getMessage(), 'error_bd');
            return FALSE;
        }
    }
    
    /**
     * En base a la ejecucion de una consulta y una clase devuelve un arreglo con instancias de la clase pasada
     * con los respectivos valores que trajo la consulta
     * @param type $PdoStatement
     * @param type $class
     * @return \class
     */
    protected function results_in_objects($PdoStatement, $class){
        $result= array();
        while($reg= $PdoStatement->fetchObject()){
            $instanciaClase= new $class();
            foreach ($reg as $key => $value) {
                if(property_exists($instanciaClase, $key)){
                    $instanciaClase->$key= $value;
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
    protected function first_result_in_object($PdoStatement, $class){
        $tupla= $PdoStatement->fetchObject();
        if($tupla == NULL){
            return NULL;
        }
        else{
            $instanciaClase= new $class();
            foreach ($tupla as $key => $value) {
                if(property_exists($instanciaClase, $key)){
                    $instanciaClase->$key= $value;
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
    protected function add_object($table, $object, $excepts_vars = array()){
        try{
            //Consigo las variables publicas del objeto
            $vars= get_object_vars($object);
            $vars= $this->delete_vars($vars, $excepts_vars);
            $sql= 'INSERT INTO ' . $table . ' (';
            foreach ($vars as $key => $value) {
                $sql .= $key . ',';
            }
            $sql = trim($sql, ',');
            $sql .= ') values(';
            foreach ($vars as $key => $value) {
                $sql .= ':' . $key . ',';
            }
            $sql = trim($sql, ',');
            $sql .= ')';
            $consulta= $this->conexion->prepare($sql);
            foreach ($vars as $key => $value) {
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }
                else{
                    $consulta->bindValue($key, $value);
                }
            }
            $consulta->execute();
            $error= $consulta->errorInfo();
            if($error[0] != 00000){            
                return FALSE;
            }
            else{
                return TRUE;
            }
        } catch (PDOException $e) {
            general_error('PDO Error', $e->getMessage(), 'error_bd');
            return FALSE;
        }
    }
    /**
     * En base a una tabla especificada y un objeto modifica el objeto en la tabla. 
     * Usa todos los atributos publicos del objeto
     */
    protected function update_object($table, $object, $where = '', $where_values = array(), $excepts_vars = array()){
        try{
            $vars= get_object_vars($object);
            //Consigo las variables publicas del objeto
            $vars= $this->delete_vars($vars, $excepts_vars);
            $sql= 'UPDATE ' . $table . ' SET ';
            foreach ($vars as $key => $value) {
                $sql .= $key . '=:' . $key . ',';
            }
            $sql = trim($sql, ',');
            if($where != ''){
                $sql .= ' WHERE ' . $where;
            }
            $consulta= $this->conexion->prepare($sql);
            foreach ($vars as $key => $value){
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }
                else{
                    $consulta->bindValue($key, $value);
                }
            }
            foreach ($where_values as $key => $value){
                if($value === FALSE){
                    $consulta->bindValue($key, 0);
                }
                else{
                    $consulta->bindValue($key, $value);
                }
            }
            $consulta->execute();
            $error= $consulta->errorInfo();
            if($error[0] != 00000){
                return FALSE;
            }
            else{
                return TRUE;
            }
        } catch (PDOException $e) {
            general_error('PDO Error', $e->getMessage(), 'error_bd');
            return FALSE;
        }
    }
    /**
     * Elimina elementos de $vars que tengan como clave el valor de un elemento de $excepts_vars
     * @param type $vars
     * @param type $excepts_vars
     * @return type
     */
    private function delete_vars($vars, $excepts_vars){
        foreach ($excepts_vars as $value) {
            unset($vars[$value]);
        }
        return $vars;
    }
}
?>