<?php
namespace Enola\Lib;
require_once __DIR__ . '/Validation.php';
/**
 * Clase que realiza validacion de campos de formulario
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Lib
 * @version 1.0
 */
class ValidationFields extends Validation{
    public function __construct($locale = NULL) {
        parent::__construct($locale);
    }
    
    /**
     * Funcion de uso interno para las funciones de validar que validan aspectos solo cuando la variable esta completa-cargada
     * @param mixed $value
     * @return boolean
     */
    protected function isComplete($value){
        if(!is_array($value)){
            return !($value === '' || $value === NULL);
        }else{
            return (count($value) > 0);
        }            
    }
    
    /**
     * Regla required: analiza que el campo no sea null ni vacio
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function required($name, $value){
        if(! $this->isComplete($value)){
            $this->add_message($name, 'required');
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Regla not_null: analiza que el campo no sea null
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function not_null($name, $value){
        if($value === NULL){
            $this->add_message($name, 'not_null');
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Regla not_empty: analiza que el campo no este vacio
     * -Si es null no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function not_empty($name, $value){
        if(! $value === NULL){
            return TRUE;
        }
        if($value === ''){            
           $this->add_message($name, 'not_empty');
           return FALSE;
        }
        return TRUE;
    }
    /**
     * Regla max_length: analiza que el string no contenga mas de $max caracteres
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $max
     * @return boolean
     */
    protected function max_length($name, $value, $max){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(is_string($value) || strval($value)){
            if(strlen($value) > $max){
                $this->add_message($name, 'max_length', array('max' => $max));
                return FALSE;
            }
        }else{
            $this->add_message($name, 'is_string');
            return FALSE;
        }
        return TRUE;
    }    
    /**
     * Regla min_lenght: analiza que el string no contenga menos de $min caracteres
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $min
     * @return boolean
     */
    protected function min_length($name, $value, $min){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(is_string($value) || strval($value)){
            if(strlen($value) < $min){
                $this->add_message($name, 'min_length', array('min' => $min));
                return FALSE;
            }
        }else{
            $this->add_message($name, 'is_string');
            return FALSE;
        }
        return TRUE;
    }    
    /**
     * Regla length_between: analiza que el string este entre un minimo y un maximo
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param string $param El minimo y el maximo separado por &
     * @return boolean 
     */
    protected function length_between($name, $value, $param){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        $params= explode('&', $param);
        $min= $params[0];
        $max= $params[1];
        if(is_string($value) || strval($value)){
            if(strlen($value) >= $min && strlen($value) <= $max){
                return TRUE;
            }else{
                $this->add_message($name, 'length_between', array('min' => $min, 'max' => $max));
                return FALSE;
            }
        }else{
            $this->add_message($name, 'is_string');
            return FALSE;
        }
    }
    /**
     * Regla length_between: analiza que el string este entre un minimo y un maximo
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param string $param El minimo y el maximo separado por &
     * @return boolean 
     */
    protected function options($name, $value, $param){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        $params= explode(',', $param);
        $options= "";
        foreach ($params as $option) {
            if($value == $option){
                return TRUE;
            }
            $options.= '"'. $option .'" ';
        }
        $this->add_message($name, 'options', array('options' => $options));
        return FALSE;
    }
    /**
     * Regla es_integer: analiza que el campo sea un integer
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function is_boolean($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(! is_bool($value)){
            if($value != "0" && $value != "1"){
                $this->add_message($name, 'is_boolean');
                return FALSE;
            }
        }
        return TRUE;
    } 
    /**
     * Regla es_string: analiza que el campo sea un string
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function is_string($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(!is_string($value)){
            $this->add_message($name, 'is_string');
            return FALSE;
        }
        return TRUE;
    } 
    /**
     * Regla es_integer: analiza que el campo sea un integer
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function is_integer($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(! is_numeric($value)){
            $this->add_message($name, 'is_integer');
            return FALSE;
        }
        return TRUE;
    }    
    /**
     * Regla max: analiza que el numero no se mayor a $max
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $max
     * @return boolean
     */
    protected function max($name, $value, $max){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(is_numeric($value)){
            $value= (float)$value;
            if($value > $max){
                $this->add_message($name, 'max', array('max' => $max));
                return FALSE;
            }
        }else{
            $this->add_message($name, 'is_integer');
            return FALSE;
        }
        return TRUE;
    }    
    /**
     * Regla min: analiza que el numero no sea menor a $min
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $min
     * @return boolean
     */
    protected function min($name, $value, $min){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(is_numeric($value)){
            $value= (float)$value;
            if($value < $min){
                $this->add_message($name, 'min', array('min' => $min));
                return FALSE;
            }
        }else{
            $this->add_message($name, 'is_integer');
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Regla num_between: analiza que el numero este entre un minimo y un maximo
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param string $param El minimo y el maximo separado por &
     * @return boolean 
     */
    protected function num_between($name, $value, $param){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        $params= explode('&', $param);
        $min= $params[0];
        $max= $params[1];
        if(is_numeric($value)){
            if($value >= $min && $value <= $max){
                return TRUE;
            }else{
                $this->add_message($name, 'num_between', array('min' => $min, 'max' => $max));
                return FALSE;
            }
        }else{
            $this->add_message($name, 'is_integer');
            return FALSE;
        }
    }
    /**
     * Regla igual: analiza si 2 datos son iguales
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param mixed $toCompare
     * @return boolean
     */
    protected function equal($name, $value, $toCompare){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if($value != $this->fieldsState["$toCompare"]['value']){
            $this->add_message($name, 'igual', array('tocompare' => $toCompare));
            return FALSE;
        }
        return TRUE;
    } 
    /**
     * Regla username: analiza si un string cumple con un mínimo de 5 caracteres y un máximo de 20, y que se usen sólo letras, números y guión bajo
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function user_name ($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
    	$expresion = '/^[a-zA-Záéíóúñ\d_\d-]{5,50}$/i';
    	if(! preg_match($expresion, $value)){
            $this->add_message($name, 'user_name');
            return FALSE;
    	}
        return TRUE;
    }
    /**
     * Regla username_or_email: Debe cumplir una de las 2 reglas
     * Regla username: analiza si un string cumple con un mínimo de 5 caracteres y un máximo de 20, y que se usen sólo letras, números y guión bajo
     * Regla email: analiza si el string cumple el formato de mail
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function user_name_or_email ($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
    	$expresion = '/^[a-zA-Záéíóúñ\d_]{5,50}$/i';
    	if(! preg_match($expresion, $value) && ! filter_var($value, FILTER_VALIDATE_EMAIL)){
            $this->add_message($name, 'user_name_or_email');
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Regla letras: analiza si un string contiene sólo letras y vocales con acento
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function letters ($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
    	$expresion = '/^[a-zA-Záéíóúñ\s]*$/';
    	if(! preg_match($expresion, $value)){
            $this->add_message($name, 'letters');
            return FALSE;
    	}
        return TRUE;
    }
    /**
     * Regla letras y nums: analiza si un string contiene sólo letras y/o números
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function letters_numbers ($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
    	$expresion = '/^[a-zA-Záéíóúñ0-9]*$/';
    	if(! preg_match($expresion, $value)){
            $this->add_message($name, 'letters_numbers');
            return FALSE;
    	}
        return TRUE;
    }
    /**
     * Regla telefono: analiza si un número de teléfono es correcto
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function telephone ($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
    	$expresion = '/^\+?\d{0,3}?[- .]?\(?(?:\d{0,3})\)?[- .]?\d{2,4}?[- .]?\d\d\d\d$/';
    	if(! preg_match($expresion, $value)){
            $this->add_message($name, 'telephone');
            return FALSE;
    	}
        return TRUE;
    }
    /**
     * Regla email: analiza si el string cumple el formato de mail
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function email($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(! filter_var($value, FILTER_VALIDATE_EMAIL)){
            $this->add_message($name, 'email');
            return FALSE;
        }
        return TRUE;
    }
    /**
     * Regla link: analiza si el string cumple el formato de URL
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function link($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(! filter_var($value, FILTER_VALIDATE_URL)){
            $this->add_message($name, 'link');
            return FALSE;
        }
        return TRUE;
    } 
    /**
     * Regla ip: analiza si el string cumple el formato de IP
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    protected function ip($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(! filter_var($value, FILTER_VALIDATE_IP)){
            $this->add_message($name, 'ip');
            return FALSE;
        }
        return TRUE;
    } 
    /**
     * Regla date: analiza si un string cumple el formato de fecha segun un formato pasado pasado como parametro
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param string $format
     * @return boolean
     */
    protected function date($name, $value, $format){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        $separator_type= array(
            "/",
            "-",
            "."
        );        
        $separator_used= NULL;
        foreach ($separator_type as $separator) {
            $find= stripos($value, $separator);
            if($find <> FALSE){
                $separator_used= $separator;
            }
        }        
        if($separator_used != NULL){      
            $dato_array= explode($separator_used, $value);
            $valid= FALSE;
            switch ($format){
                case 'Y-m-d':
                    if($separator_used == '-'){
                        $valid= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;
                case 'd-m-Y':
                    if($separator_used == '-'){
                        $valid= checkdate($dato_array[1], $dato_array[0], $dato_array[2]);
                    }
                    break;
                case 'd/m/Y':
                    if($separator_used == '/'){
                        $valid= checkdate($dato_array[1], $dato_array[0], $dato_array[2]);
                    }
                    break;
                case 'Y/m/d':
                    if($separator_used == '/'){
                        $valid= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;
                case 'Y.m.d':
                    if($separator_used == '.'){
                        $valid= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;
                case 'd.m.Y':
                    if($separator_used == '.'){
                        $valid= checkdate($dato_array[1], $dato_array[0], $dato_array[2]);
                    }
                    break;
                default :
                    if($separator_used == '-'){
                        $valid= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;          
            }
        }else{
            $valid= false;
        }
        
        if($valid){
            return TRUE;
        }else{
            $this->add_message($name, 'date', array('format' => $format));
            return FALSE;
        }        
    }
    /**
     * Regla date_is_greater: ve si el dato fecha es mayor que la fecha pasada
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param string $param tiene el formato y la fecha separada por &
     * @return boolean 
     */
    protected function date_is_greater($name, $value, $param){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        $params= explode('&', $param);
        $format= $params[0];
        $date= $params[1];
    	$date1  = \DateTime::createFromFormat($format, "$value");
    	$date2  = \DateTime::createFromFormat($format, "$date");    	 
    	if(($date1 > $date2)){
            return TRUE;
    	}else{
            $this->add_message($name, 'date_is_greater', array('date' => $date));
            return FALSE;
    	}
    }
    /**
     * Regla date_is_lower: ve si el dato fecha es menor que la fecha pasada
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param string $param tiene el formato y la fecha separada por &
     * @return boolean 
     */
    protected function date_is_lower($name, $value, $param){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        $params= explode('&', $param);
        $format= $params[0];
        $date= $params[1];
    	$date1  = \DateTime::createFromFormat($format, "$value");
    	$date2  = \DateTime::createFromFormat($format, "$date");    	 
    	if(($date1 < $date2)){
            return TRUE;
    	}else{
            $this->add_message($name, 'date_is_lover', array('date' => $date));
            return FALSE;
    	}
    }
}
