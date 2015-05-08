<?php
//Version 1.0
/**
 * Libreria que realiza validacion de campos de formulario
 *
 * @author Enola
 */
class Validation {
    //Configuro donde se encuentran los mensajes
    public $dir_content= '../source/content/messages';
    //Variable con toda la informacion sobre los datos/campos
    private $campos_datos;
    private $messages;
    private $messagesLocale= NULL;
    private $locale= NULL;
    
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
     * Limpia la variable campos_datos que contiene todas las definiciones y los resultados de ejecutar las reglas 
     */
    public function reset(){
        $this->campos_datos= array();
    }
    /**
     * Agregar una regla de validacion que luego sera validada
     * @param string $nombre
     * @param DATO $dato
     * @param array[string] $reglas
     */
    public function add_rule($nombre, $dato, $reglas){
        $this->campos_datos[$nombre] = array(
			'nombre'			=> $nombre,
			'dato'				=> $dato,
			'reglas'			=> explode('|', $reglas),
                        'valido'                        => TRUE,
                        'mensaje'                       => NULL
        );
    }    
    /**
     * Ejecuta todas las reglas de validacion que se hayan cargado.
     * Devuelve TRUE si todas las reglas pasan y FALSE en caso contrario
     * @return boolean
     */
    public function validate(){
        if(count($this->campos_datos) == 0){
            //Si no hay datos devuelve TRUE
            return TRUE;
        }
        else{
            //Si hay datos valida sus reglas            
            //El formulario empieza siendo valido
            $formulario_valido= TRUE;            
            //Recorro todos los datos y sus reglas
            foreach ($this->campos_datos as $campos_dato) {
                $valido= TRUE;                
                //Recorro cada regla del dato
                foreach ($campos_dato['reglas'] as $regla) {
                    //Ve el tipo de regla, con o sin parametros
                    if (count(explode('[', $regla)) > 1){
                        //Si hay reglas con parametros, separa la regla y su parametro               
                        $vars= explode('[', $regla);
                        $regla= $vars[0];
                        $var= explode(']', $vars[1]);
                        $var= $var[0];
                        //Realiza el llamado a la funcion correspondiente
                        $valido= call_user_func_array(array($this, $regla), array($campos_dato['nombre'], $campos_dato['dato'], $var));
                    }
                    else{
                        //Regla sin parametros
                        //Realiza el llamado a la funcion correspondiente
                        $valido= call_user_func_array(array($this, $regla), array($campos_dato['nombre'], $campos_dato['dato']));
                    }
                    
                    //Si una regla ya no es valida, no reviso las demas
                    if(!$valido){
                        //Asigno al campo Valido del dato FALSE
                        $nombre= $campos_dato['nombre'];
                        $this->campos_datos["$nombre"]['valido']= FALSE;
                        break;
                    }
                }                
                //Actualizo el valor de formulario_valido de manera que si ya es falso se mantiene en falso
                $formulario_valido= $formulario_valido && $valido;
            }            
            return $formulario_valido;
        }
    }
    /**
     * Devuelve los mensajes de error para cada campo que no haya pasado la validacion.
     * Es un array asociativo con el nombre del campo pasado
     * @return array[string]
     */
    public function error_messages(){
        $mensajes= array();
        foreach ($this->campos_datos as $campos_dato) {
            //Si no es valido agrego el mensaje de error
            if(! $campos_dato['valido']){
                $nombre= $campos_dato['nombre'];
                $mensajes["$nombre"]= $campos_dato['mensaje'];
            }
        }
        return $mensajes;
    }    
    /**
     * Funcion utilizada internamente para agregar mensajes de error a los campos
     * @param string $nombre
     * @param string $mensaje
     */
    private function add_message($nombre, $mensaje, $parametros = array()){
        //Carga el archivo si es la primer llamada
        $this->load_messages();        
        //Consigue el mensaje
        $mensaje= $this->messages[$mensaje];        
        //Analiza si se pasaron parametros y si se pasaron cambia los valores correspondientes
        foreach ($parametros as $key => $valor) {
            $mensaje= str_replace(":$key", $valor, $mensaje);
        }        
        //Guarda el mensaje en el campo correspondiente
        $this->campos_datos["$nombre"]['mensaje']= $mensaje;
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
     * Este proceso analiza de a una las lineas del archivo de mensajes usado. En este caso ini file y me arma lo que seria
     * un array asociativo clave valor en base a la linea.
     * @param type $lineas
     * @return type
     */
    private function parse_properties($lineas) {
        $result= NULL;
        foreach($lineas as $i=>$linea) {
            if(empty($linea) || !isset($linea) || strpos($linea,"#") === 0){
                continue;
            }
            $key = substr($linea,0,strpos($linea,'='));
            $value = substr($linea,strpos($linea,'=') + 1, strlen($linea));
            $result[$key] = $value;
        }
        return $result;
    }
    /*
     * ACA EMPIEZAN LAS REGLAS
     */
    
    /**
     * Regla requerido: analiza si el campo fue completado 
     * @param string $nombre
     * @param DATO $dato
     * @return boolean
     */
    private function required($nombre, $dato){
        if(! is_array($dato)){
            if($dato == ''){
                $this->add_message($nombre, 'requerido');
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            if(count($dato) > 1){
                return TRUE;
            }
            else{
                return FALSE;
            }
        }
    }    
    /**
     * Regla max_length: analiza que el string no contenga mas de $max caracteres
     * @param string $nombre
     * @param DATO $dato
     * @param int $max
     * @return boolean
     */
    private function max_length($nombre, $dato, $max){
        if(is_string($dato)){
            if(strlen($dato) > $max){
                $this->add_message($nombre, 'max_length', array('max' => $max));
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            $this->add_message($nombre, 'es_string');
            return FALSE;
        }
    }    
    /**
     * Regla min_lenght: analiza que el string no contenga menos de $min caracteres
     * @param string $nombre
     * @param DATO $dato
     * @param int $min
     * @return boolean
     */
    private function min_length($nombre, $dato, $min){
        if(is_string($dato)){
            if(strlen($dato) < $min){
                $this->add_message($nombre, 'min_length', array('min' => $min));
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            $this->add_message($nombre, 'es_string');
            return FALSE;
        }
    }    
    /**
     * Regla length_between: analiza que el string este entre un minimo y un maximo
     * @param type $nombre
     * @param type $dato
     * @param type $param El minimo y el maximo separado por &
     * @return boolean 
     */
    private function length_between($nombre, $dato, $param){
        $params= explode('&', $param);
        $min= $params[0];
        $max= $params[1];
        if(is_string($dato)){
            if(strlen($dato) >= $min && strlen($dato) <= $max){
                return TRUE;
            }
            else{
                $this->add_message($nombre, 'length_between', array('min' => $min, 'max' => $max));
            }
        }
        else{
            $this->add_message($nombre, 'es_string');
            return FALSE;
        }
    }
    /**
     * Regla es_integer: analiza que el campo sea un integer
     * @param string $nombre
     * @param DATO $dato
     * @return boolean
     */
    private function is_integer($nombre, $dato){
        if(is_numeric($dato)){
            return TRUE;
        }
        else{
            $this->add_message($nombre, 'es_integer');
            return FALSE;
        }
    }    
    /**
     * Regla max: analiza que el numero no se mayor a $max
     * @param string $nombre
     * @param DATO $dato
     * @param int $max
     * @return boolean
     */
    private function max($nombre, $dato, $max){
        if($dato == ''){
            return TRUE;
        }
        if(is_numeric($dato)){
            $dato= (float)$dato;
            if($dato > $max){
                $this->add_message($nombre, 'max', array('max' => $max));
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            $this->add_message($nombre, 'es_integer');
            return FALSE;
        }
    }    
    /**
     * Regla min: analiza que el numero no sea menor a $min
     * @param string $nombre
     * @param DATO $dato
     * @param int $min
     * @return boolean
     */
    private function min($nombre, $dato, $min){
        if(is_numeric($dato)){
            $dato= (float)$dato;
            if($dato < $min){
                $this->add_message($nombre, 'min', array('min' => $min));
                return FALSE;
            }
            else{
                return TRUE;
            }
        }
        else{
            $this->add_message($nombre, 'es_integer');
            return FALSE;
        }
    }
    /**
     * Regla num_between: analiza que el numero este entre un minimo y un maximo
     * @param type $nombre
     * @param type $dato
     * @param type $param El minimo y el maximo separado por &
     * @return boolean 
     */
    private function num_between($nombre, $dato, $param){
        $params= explode('&', $param);
        $min= $params[0];
        $max= $params[1];
        if(is_numeric($dato)){
            if($dato >= $min && $dato <= $max){
                return TRUE;
            }
            else{
                $this->add_message($nombre, 'num_between', array('min' => $min, 'max' => $max));
            }
        }
        else{
            $this->add_message($nombre, 'es_integer');
            return FALSE;
        }
    }
    /**
     * Regla igual: analiza si 2 datos son iguales
     * @param string $nombre
     * @param DATO $dato
     * @param DATO $acomparar
     * @return boolean
     */
    private function equal($nombre, $dato, $acomparar){
        if($dato == $this->campos_datos["$acomparar"]['dato']){
            return TRUE;
        }
        else{
            $this->add_message($nombre, 'igual', array('acomparar' => $acomparar));
            return FALSE;
        }
    } 
    /**
     * Regla username: analiza si un string cumple con un mínimo de 6 caracteres y un máximo de 20, y que se usen sólo letras, números y guión bajo
     * @param string $nombre
     * @param string $dato
     * @return boolean
     */
    private function user_name ($nombre, $dato){
    	$expresion = '/^[a-zA-Záéíóúñ\d_]{5,20}$/i';
    	if(preg_match($expresion, $dato)){
            return TRUE;
    	}
    	else{
            $this->add_message($nombre, 'user_name');
            return FALSE;
    	}
    }
    /**
     * Regla letras: analiza si un string contiene sólo letras y vocales con acento
     * @param string $nombre
     * @param string $dato
     * @return boolean
     */
    private function letters ($nombre, $dato){
    	$expresion = '/^[a-zA-Záéíóúñ\s]*$/';
    	if(preg_match($expresion, $dato)){
            return TRUE;
    	}
    	else{
            $this->add_message($nombre, 'letters');
            return FALSE;
    	}
    }
    /**
     * Regla letras y nums: analiza si un string contiene sólo letras y/o números
     * @param string $nombre
     * @param string $dato
     * @return boolean
     */
    private function letters_numbers ($nombre, $dato){
    	$expresion = '/^[a-zA-Záéíóúñ0-9]*$/';
    	if(preg_match($expresion, $dato)){
            return TRUE;
    	}
    	else{
            $this->add_message($nombre, 'letters_numbers');
            return FALSE;
    	}
    }
    /**
     * Regla telefono: analiza si un número de teléfono es correcto
     * @param string $nombre
     * @param string $dato
     * @return boolean
     */
    private function telephone ($nombre, $dato){
    	$expresion = '/^\+?\d{0,3}?[- .]?\(?(?:\d{0,3})\)?[- .]?\d{2,4}?[- .]?\d\d\d\d$/';
    	if(preg_match($expresion, $dato)){
            return TRUE;
    	}
    	else{
            $this->add_message($nombre, 'telephone');
            return FALSE;
    	}
    }
    /**
     * Regla email: analiza si el string cumple el formato de mail
     * @param string $nombre
     * @param string $dato
     * @return boolean
     */
    private function email($nombre, $dato){
        if(filter_var($dato, FILTER_VALIDATE_EMAIL)){
            return TRUE; 
        }
        else{
            $this->add_message($nombre, 'email');
            return FALSE;
        }
    }    
    //Regla url: ve si el dato es un link correcto
    /**
     * Regla url: analiza si el string cumple el formato de URL
     * @param string $nombre
     * @param string $dato
     * @return boolean
     */
    private function link($nombre, $dato){
        if(filter_var($dato, FILTER_VALIDATE_URL)){
            return TRUE; 
        }
        else{
            $this->add_message($nombre, 'link');
            return FALSE;
        }
    }    
    /**
     * Regla fecha: analiza si un string cumple el formato de fecha segun un formato pasado pasado como parametro
     * @param string $nombre
     * @param string $dato
     * @param string $formato (no obligatorio)
     * @return boolean
     */
    private function date($nombre, $dato, $formato){
        $tipo_separador= array(
            "/",
            "-",
            "."
        );        
        $separador_usado= NULL;
        foreach ($tipo_separador as $separador) {
            $find= stripos($dato, $separador);
            if($find <> FALSE){
                $separador_usado= $separador;
            }
        }        
        if($separador_usado != NULL){      
            $dato_array= explode($separador_usado, $dato);
            $valido= FALSE;
            switch ($formato){
                case 'Y-m-d':
                    if($separador_usado == '-'){
                        $valido= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;
                case 'd-m-Y':
                    if($separador_usado == '-'){
                        $valido= checkdate($dato_array[1], $dato_array[0], $dato_array[2]);
                    }
                    break;
                case 'd/m/Y':
                    if($separador_usado == '/'){
                        $valido= checkdate($dato_array[1], $dato_array[0], $dato_array[2]);
                    }
                    break;
                case 'Y/m/d':
                    if($separador_usado == '/'){
                        $valido= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;
                case 'Y.m.d':
                    if($separador_usado == '.'){
                        $valido= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;
                case 'd.m.Y':
                    if($separador_usado == '.'){
                        $valido= checkdate($dato_array[1], $dato_array[0], $dato_array[2]);
                    }
                    break;
                default :
                    if($separador_usado == '-'){
                        $valido= checkdate($dato_array[1], $dato_array[2], $dato_array[0]);
                    }
                    break;          
            }
        }
        else{
            $valido= false;
        }
        
        if($valido){
            return TRUE;
        }
        else{
            $this->add_message($nombre, 'date', array('formato' => $formato));
            return FALSE;
        }        
    }
    /**
     * Ve si el dato fecha es mayor que la fecha pasada
     * @param type $nombre
     * @param type $dato
     * @param type $param tiene el formato y la fecha separada por &
     * @return boolean 
     */
    private function date_is_greater($nombre, $dato, $param){
        $params= explode('&', $param);
        $formato= $params[0];
        $fecha= $params[1];
    	$date1  = DateTime::createFromFormat($formato, "$dato");
    	$date2  = DateTime::createFromFormat($formato, "$fecha");    	 
    	if(($date1 > $date2)){
            return TRUE;
    	}
    	else{
            $this->add_message($nombre, 'date_is_greater', array('fecha' => $fecha));
            return FALSE;
    	}
    }
    /**
     * Ve si el dato fecha es menor que la fecha pasada
     * @param type $nombre
     * @param type $dato
     * @param type $param tiene el formato y la fecha separada por &
     * @return boolean 
     */
    private function date_is_lover($nombre, $dato, $param){
        $params= explode('&', $param);
        $formato= $params[0];
        $fecha= $params[1];
    	$date1  = DateTime::createFromFormat($formato, "$dato");
    	$date2  = DateTime::createFromFormat($formato, "$fecha");    	 
    	if(($date1 < $date2)){
            return TRUE;
    	}
    	else{
            $this->add_message($nombre, 'date_is_lover', array('fecha' => $fecha));
            return FALSE;
    	}
    }
}