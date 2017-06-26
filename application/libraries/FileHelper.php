<?php
namespace Enola\Lib;
/**
 * Libreria de ayuda para la manipulacion de archivos
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Lib
 * @version 1.0
 */
class FileHelper {
    /**
     * Path base desde donde se va a manipular los archivos
     * @var string
     */
    protected $pathBase= PATHROOT;
    
    public function __construct($pathBase = null) {        
        if($pathBase != null){
            $this->pathBase= $pathBase;
        }
    }
    /**
     * Retorna el path base desde el cual se esta trabajando
     * @return string
     */
    public function getPathBase(){
        return $this->getPathBase();
    }
    /**
     * Setea el path base sobre el cual trabajar
     * @param string $pathBase
     */
    public function setPathBase($pathBase){
        $this->pathBase= $pathBase;
    }
    /**
     * Retorna si existe el archivo indicado
     * @param boolean
     */
    public function existeArchivo($fileName){
        file_exists($this->getPathBase() . $fileName);
    }
    /**
     * Mueve un determinado archivo cargado a una nueva ubicacion
     * @param string $fileName
     * @param string $destinationName
     * @return boolean
     */
    public function moveUploadFile($fileName, $destinationName){
        return move_uploaded_file($fileName, $this->getPathBase() . $destinationName);
    }
    /**
     * Mueve un archivo a otra ubicacion
     * @param string $fileName
     * @param string $destinationName
     * @return boolean
     */
    public function moveFile($fileName, $destinationName){
        return move_uploaded_file($this->getPathBase() . $fileName, $this->getPathBase() . $destinationName);
    }
    /**
     * Elimina un archivo
     * @param string $fileName
     * @return boolean
     */
    public function deleteFile($fileName){
        return unlink($this->getPathBase() . $fileName);
    }
    /**
     * Lee un archivo
     * @param string $fileName
     * @return int
     */
    public function readFile($fileName){
        return readfile($this->getPathBase() . $fileName);
    }  
}