<?php
namespace Enola\Lib;

/**
 * Libreria que realiza validacion de campos de formulario
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Lib
 * @version 1.0
 */
class Validation {
    /** Indica donde se encuentran los mensajes
     * @var string */
    public $dir_content= '../source/content/messages';
    /** Contiene toda la informacion sobre los datos/campos  
     * @var array */
    private $fieldsState;
    /** Contiene todos los mensajes posibles 
     * @var array */
    private $messages;
    /** Locale de los mensajes cargados 
     * @var string */
    private $messagesLocale= NULL;
    /** Locale a utilizar 
     * @var string */
    private $locale= NULL;
    /**
     * Constructor
     * @param string $locale
     */
    public function __construct($locale = NULL) {
        $this->locale= $locale;
    }
    /**
     * Cambia el locale. Si se desea no especificar locale llamar al metodo sin argumentos
     * @param type $locale 
     */
    public function change_locale($locale = NULL){
        $this->locale= $locale;
    }
    /**
     * Resetea el validador
     * Limpia la variable $fieldsState que contiene todas las definiciones y los resultados de ejecutar las reglas 
     */
    public function reset(){
        $this->fieldsState= array();
    }
    /**
     * Agregar una regla de validacion que luego sera validada
     * @param string $name
     * @param type $value
     * @param array[string] $rules
     */
    public function add_rule($name, $value, $rules){
        $this->fieldsState[$name] = array(
			'name'			=> $name,
			'value'			=> $value,
			'rules'			=> explode('|', $rules),
                        'valid'                 => TRUE,
                        'message'               => NULL
        );
    }    
    /**
     * Ejecuta todas las reglas de validacion que se hayan cargado.
     * Devuelve TRUE si todas las reglas pasan y FALSE en caso contrario
     * @return boolean
     */
    public function validate(){
        if(count($this->fieldsState) == 0){
            //Si no hay datos devuelve TRUE
            return TRUE;
        }
        else{
            //Si hay datos valida sus reglas            
            //El formulario empieza siendo valido
            $form_valid= TRUE;            
            //Recorro todos los datos y sus reglas
            foreach ($this->fieldsState as $fieldState) {
                $valid= TRUE;                
                //Recorro cada regla del dato
                foreach ($fieldState['rules'] as $rule) {
                    //Ve el tipo de regla, con o sin parametros
                    if (count(explode('[', $rule)) > 1){
                        //Si hay reglas con parametros, separa la regla y su parametro               
                        $vars= explode('[', $rule);
                        $rule= $vars[0];
                        $var= explode(']', $vars[1]);
                        $var= $var[0];
                        //Realiza el llamado a la funcion correspondiente
                        $valid= call_user_func_array(array($this, $rule), array($fieldState['name'], $fieldState['value'], $var));
                    }
                    else{
                        //Regla sin parametros
                        //Realiza el llamado a la funcion correspondiente
                        $valid= call_user_func_array(array($this, $rule), array($fieldState['name'], $fieldState['value']));
                    }
                    
                    //Si una regla ya no es valida, no reviso las demas
                    if(!$valid){
                        //Asigno al campo Valido del dato FALSE
                        $name= $fieldState['name'];
                        $this->fieldsState["$name"]['valid']= FALSE;
                        break;
                    }
                }                
                //Actualizo el valor de formulario_valido de manera que si ya es falso se mantiene en falso
                $form_valid= $form_valid && $valid;
            }            
            return $form_valid;
        }
    }
    /**
     * Devuelve los mensajes de error para cada campo que no haya pasado la validacion.
     * Es un array asociativo con el nombre del campo pasado
     * @return array[string]
     */
    public function error_messages(){
        $messages= array();
        foreach ($this->fieldsState as $fieldState) {
            //Si no es valido agrego el mensaje de error
            if(! $fieldState['valid']){
                $name= $fieldState['name'];
                $messages["$name"]= $fieldState['message'];
            }
        }
        return $messages;
    }    
    /**
     * Funcion utilizada internamente para agregar mensajes de error a los campos
     * @param string $name
     * @param string $message
     */
    private function add_message($name, $message, $parametros = array()){
        //Carga el archivo si es la primer llamada
        $this->load_messages();        
        //Consigue el mensaje
        $message= $this->messages[$message];        
        //Analiza si se pasaron parametros y si se pasaron cambia los valores correspondientes
        foreach ($parametros as $key => $valor) {
            $message= str_replace(":$key", $valor, $message);
        }        
        //Guarda el mensaje en el campo correspondiente
        $this->fieldsState["$name"]['message']= $message;
    }    
    /**
     * Carga el archivo de mensajes en la primer llamada
     */
    private function load_messages(){
        if($this->messages == NULL || $this->locale != $this->messagesLocale){
            if($this->locale != NULL){
                if(file_exists(realpath(dirname(__FILE__)). '/' . $this->dir_content . "_$this->locale" . '.txt')){
                    $this->messages= file(realpath(dirname(__FILE__)). '/' . $this->dir_content . "_$this->locale" . '.txt');
                    $this->messages= $this->parse_properties($this->messages);
                    $this->messagesLocale= $this->locale;
                }
            }
            if($this->messages == NULL){
                $this->messages= file(realpath(dirname(__FILE__)). '/' . $this->dir_content . '.txt');
                $this->messages= $this->parse_properties($this->messages);
                $this->messagesLocale= NULL;
            }
        }
    }    
    /**
     * Este proceso analiza de a una las lineas del archivo de mensajes usado. En este caso txt y me arma lo que seria
     * un array asociativo clave valor en base a la linea.
     * @param array $lines
     * @return array
     */
    private function parse_properties($lines) {
        $result= NULL;
        foreach($lines as $i=>$line) {
            if(empty($line) || !isset($line) || strpos($line,"#") === 0){
                continue;
            }
            $key = substr($line,0,strpos($line,'='));
            $value = substr($line,strpos($line,'=') + 1, strlen($line));
            $result[$key] = $value;
        }
        return $result;
    }
    /*
     * ACA EMPIEZAN LAS REGLAS
     */
    /**
     * Funcion de uso interno para las funciones de validar que validan aspectos solo cuando la variable esta completa-cargada
     * @param mixed $value
     * @return boolean
     */
    private function isComplete($value){
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
    private function required($name, $value){
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
    private function not_null($name, $value){
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
    private function not_empty($name, $value){
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
    private function max_length($name, $value, $max){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(is_string($value)){
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
    private function min_length($name, $value, $min){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        if(is_string($value)){
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
    private function length_between($name, $value, $param){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
        $params= explode('&', $param);
        $min= $params[0];
        $max= $params[1];
        if(is_string($value)){
            if(strlen($value) >= $min && strlen($value) <= $max){
                return TRUE;
            }else{
                $this->add_message($name, 'length_between', array('min' => $min, 'max' => $max));
            }
        }else{
            $this->add_message($name, 'is_string');
            return FALSE;
        }
    }
    /**
     * Regla es_integer: analiza que el campo sea un integer
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    private function is_integer($name, $value){
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
    private function max($name, $value, $max){
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
    private function min($name, $value, $min){
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
    private function num_between($name, $value, $param){
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
    private function equal($name, $value, $toCompare){
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
    private function user_name ($name, $value){
        //Si no se completo no se valida
        if(! $this->isComplete($value)){
            return TRUE;
        }
    	$expresion = '/^[a-zA-Záéíóúñ\d_]{5,20}$/i';
    	if(! preg_match($expresion, $value)){
            $this->add_message($name, 'user_name');
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
    private function letters ($name, $value){
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
    private function letters_numbers ($name, $value){
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
    private function telephone ($name, $value){
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
    private function email($name, $value){
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
    private function link($name, $value){
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
     * Regla date: analiza si un string cumple el formato de fecha segun un formato pasado pasado como parametro
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param string $format
     * @return boolean
     */
    private function date($name, $value, $format){
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
    private function date_is_greater($name, $value, $param){
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
    private function date_is_lower($name, $value, $param){
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