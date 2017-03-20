<?php
namespace Enola\Lib;
/**
 * Es un Trait que va a permitir que objetos mapeados con doctrine se serialicen a json.
 * Los objetos no inicializados no son cargados, solo su Id.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
trait JsonHelperDoctrine{
    public function jsonSerialize() {
        $vars = get_object_vars($this);
        unset($vars['__initializer__'], $vars['__isInitialized__'], $vars['__cloner__']);
        foreach ($vars as $key => $var) {
            if(is_object($var) && ($var instanceof \Doctrine\Common\Persistence\Proxy) && !$var->__isInitialized()){
                $vars[$key]= $var->getId();
            }else if($var instanceof  \Doctrine\ORM\PersistentCollection && $var->isInitialized()){
                $list= array();
                foreach ($var as $objeto) {
                    $list[]= $objeto;
                }
                $vars[$key]= $list;                
            }else if($var instanceof  \Doctrine\ORM\PersistentCollection && !$var->isInitialized()){
                $vars[$key]= array();
            }
        }
        return $vars;
    }
}
/**
 * Esta clase maneja diferentes formas de transformar objetos a a json y a array
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class JsonHelper{
    public function __construct() {        
    }
    /**
     * Transforma un objeto en un array en base al parametro $fields que indica que atributos del objeto mapear
     * @param Object $object
     * @param mixed $fields
     * @return mixed
     */   
    public function object_to_array($object, array $fields){
        $array= array();
        if(is_array($object) || $object instanceof \Traversable){
            $array= array();
            foreach ($object as $var) {
                $array[]= $this->object_to_array_internal($var, $fields);
            }
        }else if(! is_null($object)){        
            $array= $this->object_to_array_internal($object, $fields);
        }else{
            $array= NULL;
        }
        return $array;        
    }
    /**
     * Este es un metodo interno que transforma un objeto en un array en base al parametro $fields que indica 
     * que atributos del objeto mapear. Utilizado por object_to_array
     * @param Object $object
     * @param mixed $fields
     * @return mixed
     */ 
    protected function object_to_array_internal($object, $fields){
        $var= array();
        $reflection= new \Enola\Support\Reflection($object);
        foreach ($fields as $key => $value) {
            if(is_array($value)){
                $var[$key]= $this->object_to_array($reflection->getProperty($key), $fields[$key]);
            }else{
                if(is_string($key) && is_string($value)){
                    $var[$key]= $reflection->getProperty($value);
                }else{
                    $var[$value]= $reflection->getProperty($value);
                }
            }
        }
        return $var;
    }
    /**
     * Este realiza el pasaje de un objeto a json asegurando que el mismo no entre en loop.
     * Para esto primero pasa el objeto a array en base a los campos que quiere mapear y despues lo pasa a json
     * @param Object $object
     * @param mixed $fields
     * @param int $options
     * @return string
     */ 
    public function json_encode_data($object, array $fields, $options = 0){
        $array= $this->object_to_array($object, $fields);
        return json_encode($array, $options);
    }
    /**
     * Este metodo realiza el pasaje de un objeto Doctrine a json.
     * Para esto indicamos el nivel de profundidad que se va a ingresar en el objeto y los objetos no cargados hasta
     * el momento solo se mapearan su id.
     * @param Object $object
     * @param int $recursionLevel
     * @param int $options
     * @return strong
     */
    public function json_encode_recursion($object, $recursionLevel = 1, $options = 0){
        $objects= array();
        $ocurrencias= array();
        $array= array();
        if(is_array($object)){
            $array= $object;
            foreach ($array as &$var) {
                $var= $this->json_encode_recursion_internal($var, $recursionLevel, $options, $objects, $ocurrencias);
            }
        }else{        
            $array= $this->json_encode_recursion_internal($object, $recursionLevel, $options, $objects, $ocurrencias);
        }
        return json_encode($array, $options);
    }
    /**
     * Este metodo es privado y realiza el pasaje de un objeto Doctrine a json.
     * Para esto indicamos el nivel de profundidad que se va a ingresar en el objeto y los objetos no cargados hasta
     * el momento solo se mapearan su id.
     * @param Object $object
     * @param int $recursionLevel
     * @param int $options
     * @param mixed $objects
     * @param int $ocurrencias
     * @return mixed
     */
    protected function json_encode_recursion_internal($object, $recursionLevel = 1, $options = 0, $objects, $ocurrencias){
        $pos= array_search($object, $objects);
        $repeticiones= 1;
        if($pos !== FALSE){
            $ocurrencias[$pos]++;
            $repeticiones= $ocurrencias[$pos];
        }else{
            $objects[]= $object;
            $ocurrencias[]= 1;
        }
        if($repeticiones > $recursionLevel){            
            return FALSE;
        }else{
            if(method_exists($object, 'jsonSerialize')){                
                $vars= $object->jsonSerialize();
                foreach ($vars as $key => &$var) {
                    if(is_object($var)){
                        $rta= $this->json_encode_recursion_internal($var, $recursionLevel, $options, $objects, $ocurrencias);
                        if($rta !== FALSE){
                            $var= $rta;
                        }else{
                            unset($var);
                            unset($vars[$key]);
                        }
                    }else if(is_array($var) || $var instanceof Traversable){
                        foreach ($var as &$item) {
                            $item= $this->json_encode_recursion_internal($item, $recursionLevel, $options, $objects, $ocurrencias);
                        }
                        //$var= json_encode_recursion_internal($var, $recursionLevel, $options, $objects, $ocurrencias);
                    }
                }
                return $vars;
            }else{
                return $object;
            }
        }    
    }
}
/**
 * Transforma un objeto en un array en base al parametro $fields que indica que atributos del objeto mapear
 * @param Object $object
 * @param mixed $fields
 * @return mixed
 */ 
function object_to_array($object, array $fields){
    $jsonHelper= new JsonHelper();
    return $jsonHelper->object_to_array($object, $fields);
}
/**
 * Este realiza el pasaje de un objeto a json asegurando que el mismo no entre en loop.
 * Para esto primero pasa el objeto a array en base a los campos que quiere mapear y despues lo pasa a json
 * @param Object $object
 * @param mixed $fields
 * @param int $options
 * @return string
 */ 
function json_encode_data($object, array $fields, $options = 0){
    $jsonHelper= new JsonHelper();
    return $jsonHelper->json_encode_data($object, $fields, $options);
}
/**
 * Este metodo realiza el pasaje de un objeto Doctrine a json.
 * @param Object $object
 * @param int $recursionLevel
 * @param int $options
 * @return strong
 */
function json_encode_recursion($object, $recursionLevel = 1, $options = 0){
    $jsonHelper= new JsonHelper();
    return $jsonHelper->json_encode_recursion($object, $recursionLevel, $options);
}