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
    public $amount_per_page;
    /** Total de elementos 
     * @var int */
    public $total_amount;
    /** Pagina actual
     * @var int */
    public $current_page;
    /** Posicion de inicio del primer elemento. Suele ser 0
     * @var int */
    public $start_position;
    /**
     * Constructor
     * @param int $amount_per_page
     * @param int $total_amount
     * @param int $current_page
     * @param int $start_position
     */    
    public function __construct($amount_per_page, $total_amount, $current_page, $start_position = 0) {
        $this->amount_per_page= $amount_per_page;
        $this->total_amount= $total_amount;
        $this->current_page= $current_page;
        $this->start_position= $start_position;
    }    
    /**
     * Retorna la cantidad de paginas
     * @return int
     */
    public function number_of_pages(){
        $cantidad= $this->total_amount / $this->amount_per_page;
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
    public function element_start_position(){
        return ($this->amount_per_page * $this->current_page) - ($this->amount_per_page + $this->start_position);
    }    
    /**
     * Retorna la posicion del elemento de fin de la pagina actual.
     * @return int
     */
    public function element_end_position(){
        if($this->number_of_pages() == $this->current_page){
            return $this->total_amount - (1 - $this->start_position);
        }else{
            return $this->element_start_position() + $this->amount_per_page - 1;
        }
    }    
    /**
     * Retorna la pagina anterior o null en caso de que no haya anterior
     * @return int
     */
    public function previous_page(){
        if($this->current_page > 1){
            return $this->current_page - 1;
        }else{
            return NULL;
        }
    }    
    /**
     * Retorna la pagina siguiente o null en caso de que no haya siguiente
     * @return int
     */
    public function next_page(){
        if($this->current_page < $this->number_of_pages()){
            return $this->current_page + 1;
        }else{
            return NULL;
        }
    }
    /**
     * Retorna si la pagina indicada es la actual
     * @param int $page
     * @return bool
     */
    public function is_actual_page($page){
        return ($this->current_page == $page);
    }
}