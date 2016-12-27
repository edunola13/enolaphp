<?php
namespace Enola\Lib;
require_once __DIR__ . '/Validation.php';
/**
 * Clase que realiza validacion de archivos de formulario
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Lib
 * @version 1.0
 */
class ValidationFiles extends Validation{
    public function __construct($locale = NULL) {
        parent::__construct($locale);
    }
    /**
     * Funcion de uso interno para las funciones de validar que analizan si se trata de un archivo multiple o no
     * @param mixed $value
     * @return boolean
     */
    protected function isMultiple($value){
        return (count($value['name']) > 1);
    }
    /**
     * Regla not_empty: analiza que el archivo no este vacio
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    public function not_empty($name, $value){
        if($value != NULL){
            return TRUE;
        }else{
            $this->add_message($name, 'empty_file');
            return FALSE;
        }
    }
    /**
     * Regla not_error: analiza que el/los archivos no se hayan cargado con errores
     * -Si no es cargado no se controla
     * @param string $name
     * @param mixed $value
     * @return boolean
     */
    public function not_error($name, $value){
        if(! $this->not_empty($name, $value)){
            return TRUE;
        }
        $error= 0;
        if($this->isMultiple($value)){
            foreach ($value['error'] as $errorType) {
                if($errorType != 0){
                    $error= $errorType;
                    break;
                }
            }
        }else{
            $error= $value['error'];
        }
        
        if($error == 0){
            return TRUE;
        }else{
            $this->add_message($name, 'error_file' . $error);
            return FALSE;
        }
    }
    /**
     * Regla max_files: analiza que la cantidad de archivos no supere max
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $max
     * @return boolean
     */
    public function max_files($name, $value, $max){
        if(! $this->not_empty($name, $value)){
            return TRUE;
        }
        if(count($value['name']) <= $max){
            return TRUE;
        }else{
            $this->add_message($name, 'max_files', array('max' => $max));
            return FALSE;
        }
    }
    /**
     * Regla min_files: analiza que cantidad de archivos no sea menor a min
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $min
     * @return boolean
     */
    public function minFiles($name, $value, $min){
        if(! $this->not_empty($name, $value)){
            return TRUE;
        }
        if(count($value['name']) >= $min){
            return TRUE;
        }else{
            $this->add_message($name, 'min_files', array('min' => $min));
            return FALSE;
        }
    }
    /**
     * Regla max_size_per_file: analiza que el tamano de cada archivo no supere max
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $max
     * @return boolean
     */
    public function max_size_per_file($name, $value, $max){
        if(! $this->not_empty($name, $value)){
            return TRUE;
        }
        $ok= TRUE;
        foreach ($value['size'] as $size) {
            if($size > $max){
                $ok= FALSE;
                break;
            }
        }
        
        if($ok){
            return TRUE;
        }else{
            $this->add_message($name, 'max_per_file', array('max' => $max));
            return FALSE;
        }
    }
    /**
     * Regla min_size_per_file: analiza que el tamano de cada archivo no sea menor a min
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $min
     * @return boolean
     */
    public function min_size_per_file($name, $value, $min){
        if(! $this->not_empty($name, $value)){
            return TRUE;
        }
        $ok= TRUE;
        foreach ($value['size'] as $size) {
            if($size < $min){
                $ok= FALSE;
                break;
            }
        }
        
        if($ok){
            return TRUE;
        }else{
            $this->add_message($name, 'min_per_file', array('min' => $min));
            return FALSE;
        }
    }
    /**
     * Regla max_size: analiza el tamano total no sea mayor a max
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $max
     * @return boolean
     */
    public function max_size($name, $value, $max){
        if(! $this->not_empty($name, $value)){
            return TRUE;
        }
        $total= 0;
        if($this->isMultiple($value)){
            foreach ($value['size'] as $size) {
                $total+= $size;
            }
        }else{
            $total= $value['size'];
        }
        
        if($total < $max){
            return TRUE;
        }else{
            $this->add_message($name, 'max_size', array('max' => $max));
            return FALSE;
        }
    }
    /**
     * Regla min_size: analiza que el tamano total no sea menor a min
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $min
     * @return boolean
     */
    public function min_size($name, $value, $min){
        if(! $this->not_empty($name, $value)){
            return TRUE;
        }
        $total= 0;
        if($this->isMultiple($value)){
            foreach ($value['size'] as $size) {
                $total+= $size;
            }
        }else{
            $total= $value['size'];
        }
        
        if($total > $min){
            return TRUE;
        }else{
            $this->add_message($name, 'min_size', array('min' => $min));
            return FALSE;
        }
    }
    /**
     * Regla is_type: analiza que el tipo de el/los archivos sea de los tipos indicados
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $types
     * @return boolean
     */
    public function is_type($name, $value, $types){
        if(! $this->not_empty($name, $value)){
            return TRUE;
        }
        $types= explode(',', $types);
        $ok= TRUE;
        if($this->isMultiple($value)){
            foreach ($value['type'] as $type) {
                if (in_array($type, $types)){
                    $ok= FALSE;
                    break;
                }
            }
        }else{
            $ok= in_array($value['type'], $types);
        }        
        
        if($ok){
            return TRUE;
        }else{
            $this->add_message($name, 'is_type', array('types' => $types));
            return FALSE;
        }
    }
    /**
     * Regla is_extension: analiza que la extension de el/los archivos sea de las extensiones indicadas
     * -Si no es cargada no se controla
     * @param string $name
     * @param mixed $value
     * @param int $extensions
     * @return boolean
     */
    public function is_extension($name, $value, $extensions){
        if(! $this->not_empty($name, $value)){
            return TRUE;
        }
        $extensions= explode(',', $extensions);
        $ok= TRUE;
        if($this->isMultiple($value)){
            foreach ($value['name'] as $name) {
                $ext = pathinfo($name, PATHINFO_EXTENSION);
                if (in_array($ext, $extensions)){
                    $ok= FALSE;
                    break;
                }
            }
        }else{
            $ext = pathinfo($value['name'], PATHINFO_EXTENSION);
            $ok= (in_array($ext, $extensions));
        }        
        
        if($ok){
            return TRUE;
        }else{
            $this->add_message($name, 'is_extension', array('extensions' => $extensions));
            return FALSE;
        }
    }
}