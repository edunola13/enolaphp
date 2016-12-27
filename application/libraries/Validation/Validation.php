<?php
namespace Enola\Lib;
/**
 * Clase que contiene el funcionamiento base para la validacion de datos de entrada
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Lib
 * @version 1.0
 */
class Validation {
    /** Indica donde se encuentran los mensajes
     * @var string */
    public $dir_content= '../../source/content/messages';
    /** Contiene toda la informacion sobre los datos/campos  
     * @var array */
    protected $fieldsState;
    /** Contiene todos los mensajes posibles 
     * @var array */
    protected $messages;
    /** Locale de los mensajes cargados 
     * @var string */
    protected $messagesLocale= NULL;
    /** Locale a utilizar 
     * @var string */
    protected $locale= NULL;
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
    protected function add_message($name, $message, $parametros = array()){
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
    protected function load_messages(){
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
    protected function parse_properties($lines) {
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
}