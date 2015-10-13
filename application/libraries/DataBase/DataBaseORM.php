<?php
namespace Enola\DB;

require_once 'DataBaseAR.php';
/**
 * Esta clase extiende de DataBaseAR por lo que provee lo mismo que la anterior pero le agrega un simple ORM el cual
 * permitira, guardar objetos y sus relaciones, eliminar los mismos y realizar consultas y luego mapear a objetos y recuperar las relaciones. * 
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\DataBase
 */
class DataBaseORM extends DataBaseAR{
    /**
     * Devuelve un conjunto de objetos de la clase especificada en base a la consulta armada de la forma ActiveRecord
     * @param string $class
     * @return array[object]
     */
    public function getInObjects($class){
        $res= $this->get();
        if($res !== FALSE){
            return $this->resultsInObjects($res, $class);
        }
        return $res;
    }
    /**
     * Devuelve un conjunto de objetos de la clase especificada en base a la consulta armada en base a los parametros
     * @param string $class
     * @param string $from
     * @param string $where
     * @param array $where_values
     * @param string $order
     * @param type $limit
     * @param type $offset
     * @return array[object]
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
     * En base a la ejecucion de una consulta y una clase devuelve una instancia de la clase pasada
     * con los respectivos valores que trajo la consulta
     * @param \PdoStatement $PdoStatement
     * @param string $class
     * @return null|object
     */
    public function resultInObject(\PDOStatement $PdoStatement, $class = "stdClass"){
        $row= $PdoStatement->fetch(\PDO::FETCH_ASSOC);
        $obj= new $class();
        $datos= array();
        foreach ($obj->fields() as $value) {
            if(isset($row[$value])){
                $datos= $row[$value];
            }             
        }
        $obj->loadObject($datos);
        return $obj;
    }
    /**
     * En base a la ejecucion de una consulta y una clase devuelve un arreglo con instancias de la clase pasada
     * con los respectivos valores que trajo la consulta
     * @param \PdoStatement $PdoStatement
     * @param string $class
     * @return array[object]
     */
    public function resultsInObjects(\PDOStatement $PdoStatement, $class = "stdClass"){
        $result= array();
        while($row= $PdoStatement->fetch(\PDO::FETCH_ASSOC)){
            $result[]= $this->resultInObject($PdoStatement, $class);
        }
        return $result;
    }
    /**
     * En base a una tabla especificada y un objeto agrega el objeto en la tabla.
     * @param string $table
     * @param \Enola\DB\Storable $object
     * @param array $excepts_vars
     * @return boolean
     */
    public function insertObject(Storable $object, $excepts_vars = array()){
        try{
            //Consigo las propiedades a guardar del objeto y elimino las que no se deseen guardar
            $vars= array_diff_key($object->fields(), $excepts_vars);
            //Armo y preparo la consulta
            $query= $this->prepareInsert($object->table(), $vars);
            //Ejecuto la consulta            
            $query->execute($vars);
            $this->connection->lastInsertId($query);
            /**
             * Ver que devuelve esto y actualizar el objeto
             */
            $error= $query->errorInfo();
            if($error[0] != '00000'){
                $this->catchError($error);
                return FALSE;
            }else{
                return TRUE;
            }
        } catch (\PDOException $e) {
            throw $e;
        }
    }
    /**
     * En base a una tabla especificada y un objeto modifica el objeto en la tabla.
     * @param string $table
     * @param \Enola\DB\Storable $object
     * @param array $excepts_vars
     * @return boolean
     * @throws \PDOException
     */
    public function saveObject($table, Storable $object, $excepts_vars = array()){
        try{
            //Consigo las propiedades a guardar del objeto y elimino las que correspondan
            $vars= array_diff_key($object->fields(), $excepts_vars);
            //Armo el Where
            $where= '';
            foreach ($object->keys() as $key => $value) {
                if($where != ''){$where= 'AND ';}
                $where.= $key . '=:' . $key . ' ';
            }
            //Armo y preparo la consulta
            $query= $this->prepareUpdate($table, $vars, $where);
            //Uno los datos a guardar mas las claves, las claves ya se incluyen en saveObject pero pueden haberse eliminaod con $excepts_vars
            $vars = array_merge($vars, $object->keys());
            //Ejecuto la consulta
            $query->execute($vars);
            $error= $query->errorInfo();
            if($error[0] != '00000'){
                $this->catchError($error);
                return FALSE;
            }else{
                return TRUE;
            }
        } catch (\PDOException $e) {
            throw $e;
        }
    }
    /**
     * 
     * @param \Enola\DB\Storable $object
     * @param array $relation
     * @return boolean
     */
    protected function saveRelationOneToOne(Storable $object, $relation){
        try{
            //Decido cual es el contenedor y cual se referencia
            $container= $object;
            $reference= $relation['object'];
            $field= "";
            if(isset($relation['fieldInverse'])){
                $container= $relation['object'];
                $reference= $object;
                $field= $relation['fieldInverse'];
            }else{
                $field= $relation['field'];
            }
            //Armo el where para actualizar el objeto contenedor
            $where= '';
            foreach ($container->keys() as $key => $value) {
                if($where != ''){$where= 'AND ';}
                $where.= $key . '=:' . $key . ' ';
            }
            //Arma el array de campos a actualizar - Solo el filed indicado en la relacion
            //Lo armo en base al id del objeto reference
            $keys= $reference->key();
            reset($keys);
            $valueKey= current($keys);
            $vars= array($field => $valueKey);
            //Armo y preparo la consulta
            $query= $this->prepareUpdate($container->table, $vars, $where);
            //Uno los datos a guardar mas las claves, las claves del objeto contenedor
            $vars = array_merge($vars, $container->keys());
            //Ejecuto la consulta
            $query->execute($vars);
            $error= $query->errorInfo();
            if($error[0] != '00000'){
                $this->catchError($error);
                return FALSE;
            }else{
                return TRUE;
            }
        } catch (\PDOException $e) {
            throw $e;
        }
    }
    /**
     * Salva las relaciones oneToMany o manyToOne cuando no hay tablas intermedias
     * @param \Enola\DB\Storable $object
     * @param array $relation
     * @return boolean
     */
    protected function saveRelationOneToMany(Storable $object, $relation){
        try{
            //Decido cual es el objeto del lado de uno y cual el de muchos
            $objectOne= NULL;
            //objectMany = objeto contenedor
            $objectsMany= NULL;
            $field= "";
            if(isset($relation['fieldInverse'])){
                $objectOne= $object;
                $objectsMany= $relation['objects'];
                $field= $relation['fieldInverse'];
            }else{
                $objectOne= $relation['object'];
                $objectsMany= $object;
                $field= $relation['field'];
            }
            //Armo el where para actualizar el objeto contenedor
            $where= '';
            foreach ($objectsMany->keys() as $key => $value) {
                if($where != ''){$where= 'AND ';}
                $where.= $key . '=:' . $key . ' ';
            }
            //Arma el array de campos a actualizar - Solo el filed indicado en la relacion
            //Lo armo en base al id del objeto one (reference)
            $keys= $objectOne->key();
            reset($keys);
            $valueKey= current($keys);
            $vars= array($field => $valueKey);
            //Armo y preparo la consulta
            $query= $this->prepareUpdate($objectMany->table, $vars, $where);
            //Uno los datos a guardar mas las claves, las claves del objeto contenedor
            $vars = array_merge($vars, $container->keys());
            //Ejecuto la consulta
            $query->execute($vars);
            $error= $query->errorInfo();
            if($error[0] != '00000'){
                $this->catchError($error);
                return FALSE;
            }else{
                return TRUE;
            }
        } catch (\PDOException $e) {
            throw $e;
        }
    }
    /**
     * En base a una tabla y un objeto elimina el mismo de la base de datos
     * @param type $table
     * @param \Enola\DB\Storable $object
     * @return boolean
     * @throws \PDOException
     */
    public function deleteObject($table, Storable $object){
        try{
            //Armo el Where
            $where= '';
            foreach ($object->keys() as $key => $value) {
                if($where != ''){$where= 'AND ';}
                $where.= $key . '=:' . $key . ' ';
            }
            //Armo y preparo la consulta
            $query= $this->prepareDelete($table, $where);
            //Ejecuto la consulta
            $query->execute($object->keys());
            $error= $query->errorInfo();
            if($error[0] != '00000'){
                $this->catchError($error);
                return FALSE;
            }else{
                return TRUE;
            }
        } catch (\PDOException $e) {
            throw $e;
        }
    }
}

/**
 * Esta interface establece los metodos que debe implementar un modelo que se desea guardar en la base de datos
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\DataBase
 */
interface Storable{
    /**
     * Retorna la tabla de la BD en la que se debe guardar
     */
    public function table();
    /** 
     * Retorna array asociativo con los campos que son clave
     * key=>nombre del campo(columna) / value=>su correspondiente valor
     * @return array
     */
    public function key();
    /**
     * Retorna un array con los nombres de los campos (columnas) incluidas las claves
     * key=>nombre del campo(columna)
     * @return array
     */
    public function fields();
    /** 
     * Retorna un array asociativo con los campos a guardar (incluidos los clave)
     * key=>nombre del campo(columna) / value=>su correspondiente valor
     * @return array
     */
    //public function saveObject();
    /**
     * Pasa los datos de la base de datos en base a las claves que indica saveObject()
     * @param array $values - key=>nombre del campo(columna) / value=>su correspondiente valor
     */
    public function loadObject($values);
    public function relationsOneToOne();
    public function relationsOneToMany();
    public function relationsManyToOne();
    public function relationsManyToMany();
}