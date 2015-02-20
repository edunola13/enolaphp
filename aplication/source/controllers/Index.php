<?php

/**
 * Description of Index
 *
 * @author Enola
 */

class Index extends En_Controller{
    public function __construct() {
        parent::__construct();
    }
    
    public function doGet(){
        import_aplication_file('source/controllers/Prueba');
        import_aplication_file('source/controllers/Usuario');
        $prueba= new Prueba();
        
        //Prueba 1 - Una simple con get_from_where
        $res=$prueba->get_from_where('usuario', 'usuario=:user', array('user' => 'eduardo_n'), 'usuario', '10', '0');
        print_r($res->fetchAll());
        
        //Prueba 2 - Una consulta armada de a partes
        $prueba->select('usuario,nombre');
        $prueba->from('usuario');
        $prueba->where('habilitado=:hab', array('hab'=>TRUE));
        $prueba->order('habilitado desc, nombre');
        $prueba->limit('2','1');
        $res2=$prueba->get();
        print_r($res2->fetchAll());
        
        //Prueba 3 - Una consulta armada de a partes y con inner join
        //Paso a objeto, el objeto tiene un campo q no es de base y en la consulta traigo un dato que no esta en el objeto
        $prueba->select('titulo,usuario,nombre,autor');
        $prueba->from('usuario');
        $prueba->join('post', 'usuario.id=post.autor');
        $prueba->where('usuario=:user', array('user'=>'anadg'));
        $prueba->order('titulo');
        $prueba->limit('10');
        $res3=$prueba->get();
        print_r($prueba->results_in_objects($res3, 'Usuario'));
        //print_r($res3->fetchAll());
        
        //Prueba 4 - group by y having
        //Lo paso a un objeto
        $prueba->select('nombre,autor');
        $prueba->from('usuario');
        $prueba->join('post', 'usuario.id=post.autor');
        $prueba->where('usuario.habilitado=:hab', array('hab'=>TRUE));
        $prueba->group('autor, nombre');
        $prueba->having('count(nombre) > :cant', array('cant' => 1));
        $prueba->order('titulo');
        $prueba->limit('10');
        $res4=$prueba->get();
        print_r($prueba->results_in_objects($res4, 'Usuario'));
        //print_r($res3->fetchAll());
        
        $this->load_view("index");
    }

}
?>