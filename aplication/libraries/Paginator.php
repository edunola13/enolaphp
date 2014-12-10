<?php
//Version 1.0
/**
 * Description of Paginador
 *
 * @author Enola
 */
class Paginator {
    public $amount_per_page;
    public $total_amount;
    public $current_page;
    
    public function __construct($amount_per_page, $total_amount, $current_page) {
        $this->amount_per_page= $amount_per_page;
        $this->total_amount= $total_amount;
        $this->current_page= $current_page;
    }    
    /**
     * Retorna la cantidad de paginas que hay
     * @return int
     */
    public function number_of_pages(){
        $cantidad= $this->total_amount / $this->amount_per_page;
        if(is_int($cantidad)){
            return $cantidad;
        }
        else{
            $cantidad_int= intval($cantidad);
            if($cantidad_int > $cantidad){
                return $cantidad_int;
            }
            else{
                return $cantidad_int + 1;
            }
        }
    }    
    /**
     * Retorna la posicion del elemento de inicio de la pagina actual.
     * Empieza de 0.
     * @return int
     */
    public function element_start_position(){
        return ($this->amount_per_page * $this->current_page) - $this->amount_per_page;
    }    
    /**
     * Retorna la posicion del elemento de fin de la pagina actual.
     * Empieza de 0.
     * @return int
     */
    public function element_end_position(){
        if($this->cantidad_de_paginas() == $this->current_page){
            return $this->total_amount - 1;
        }
        else{
            return $this->posicion_elemento_inicio() + $this->amount_per_page - 1;
        }
    }    
    /**
     * Retorna la pagina anterior o null en caso de que no haya anterior
     * @return int
     */
    public function previous_page(){
        if($this->current_page > 1){
            return $this->current_page - 1;
        }
        else{
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
        }
        else{
            return NULL;
        }
    }
}
?>