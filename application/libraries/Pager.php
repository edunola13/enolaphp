<?php
namespace Enola\Lib;

/**
 * Libreria que ayuda a paginar un conjunto de resultados
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Lib
 * @version 1.0
 */
class Pager {
    /** Cantidad de elementos por pagina 
     * @var int */
    public $amountPerPage;
    /** Total de elementos 
     * @var int */
    public $totalAmount;
    /** Pagina actual
     * @var int */
    public $currentPage;
    /** Posicion de inicio del primer elemento. Suele ser 0
     * @var int */
    public $startPosition;
    /**
     * Constructor
     * @param int $amountPerPage
     * @param int $totalAmount
     * @param int $currentPage
     * @param int $startPosition
     */    
    public function __construct($amountPerPage, $totalAmount, $currentPage, $startPosition = 0) {
        $this->amountPerPage= $amountPerPage;
        $this->totalAmount= $totalAmount;
        $this->currentPage= $currentPage;
        $this->startPosition= $startPosition;
    }    
    /**
     * Retorna la cantidad de paginas
     * @return int
     */
    public function numberOfPages(){
        $cantidad= $this->totalAmount / $this->amountPerPage;
        if(is_int($cantidad)){
            return $cantidad;
        }else{
            $cantidad_int= intval($cantidad);
            if($cantidad_int > $cantidad){
                return $cantidad_int;
            }else{
                return $cantidad_int + 1;
            }
        }
    }    
    /**
     * Retorna la posicion del elemento de inicio de la pagina actual.
     * @return int
     */
    public function elementStartPosition(){
        return ($this->amountPerPage * $this->currentPage) - ($this->amountPerPage + $this->startPosition);
    }    
    /**
     * Retorna la posicion del elemento de fin de la pagina actual.
     * @return int
     */
    public function elementEndPosition(){
        if($this->numberOfPages() == $this->currentPage){
            return $this->totalAmount - (1 - $this->startPosition);
        }else{
            return $this->elementStartPosition() + $this->amountPerPage - 1;
        }
    }    
    /**
     * Retorna la pagina anterior o null en caso de que no haya anterior
     * @return int
     */
    public function previousPage(){
        if($this->currentPage > 1){
            return $this->currentPage - 1;
        }else{
            return NULL;
        }
    }    
    /**
     * Retorna la pagina siguiente o null en caso de que no haya siguiente
     * @return int
     */
    public function nextPage(){
        if($this->currentPage < $this->numberOfPages()){
            return $this->currentPage + 1;
        }else{
            return NULL;
        }
    }
    /**
     * Retorna si la pagina indicada es la actual
     * @param int $page
     * @return bool
     */
    public function isActualPage($page){
        return ($this->currentPage == $page);
    }
}