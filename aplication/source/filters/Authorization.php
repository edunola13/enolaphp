<?php
/**
 * Filtro que analiza la autorizacion de los usuarios
 * @author Enola
 */
class Authorization extends En_Filter{
    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }    
    /**
     * Funcion que realiza el filtro correspondiente
     */
    public function filter(){
        //Leo el archivo de configuracion de seguridad
        $json_segurirad= file_get_contents(PATHAPP . CONFIGURATION . 'authorization.json');
        //Pasa el archivo json a un arreglo
        $seguridad= json_decode($json_segurirad, TRUE);
        if($this->request->session->exist('user_logged')){
            //Si existe le asigno el tipo correspondiente
            $user_logged= $this->request->session->get('user_logged');
        }
        else{
            //Si no existe le asigno el nombre Default
            $user_logged= 'default';
        }
        //Compruebo que exista la configuracion para el tipo de usuario logueado
        if(isset($seguridad[$user_logged])){
            //Seteo la configuracion del usuario correpondiente
            $config_seguridad= $seguridad[$user_logged];
            //Seteo los permisos del usuario
            $permisos= $config_seguridad['permit'];
            $mapea= FALSE;
            //Recorro sus permisos y veo si alguno coincice
            foreach ($permisos as $permiso) {
                if(maps_actual_url($permiso)){
                    //Cuando alguno coincide salgo del for
                    $mapea= TRUE;
                    break;
                }
            }
            if($mapea){
                //Si hubo mapeo, recorro las url denegadas para el usuario
                $denegados= $config_seguridad['deny'];
                foreach ($denegados as $denegado) {
                    if(maps_actual_url($denegado)){
                        //Si la url es denegada salgo del for
                        $mapea= FALSE;
                        break;
                    }
                }
            }
            if(! $mapea){
                //Si no tiene permiso es redireccionado
                redirect($config_seguridad['error']);
            }
        }
        else{
            //Si no existe la configuracion aviso del error
            echo "No existe definicion de seguridad para $user_logged";
            $sesion= new Sesion();
            $sesion->delete_session();
            exit();
        }
    }
}
?>