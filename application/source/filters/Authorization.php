<?php
use Enola\Http;
use Enola\Http\En_HttpRequest,Enola\Http\En_HttpResponse;

/**
 * Filtro que analiza la autorizacion de los usuarios
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 */
class Authorization extends Http\En_Filter{    
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct();
    }    
    /**
     * Funcion que realiza el filtro correspondiente
     */
    public function filter(En_HttpRequest $request, En_HttpResponse $response){
        //Leo el archivo de configuracion de seguridad
        $seguridad= $this->context->readConfigurationFile('authorization');
        //Tipo por defecto
        $user_logged= 'default';
        if($request->session->exist('user_logged')){
            //Si existe le asigno el tipo correspondiente
            $user_logged= $request->session->get('user_logged');
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
                if(Http\UrlUri::mapsActualUrl($permiso)){
                    //Cuando alguno coincide salgo del for
                    $mapea= TRUE;
                    break;
                }
            }
            if($mapea){
                //Si hubo mapeo, recorro las url denegadas para el usuario
                $denegados= $config_seguridad['deny'];
                foreach ($denegados as $denegado) {
                    if(Http\UrlUri::mapsActualUrl($denegado)){
                        //Si la url es denegada salgo del for
                        $mapea= FALSE;
                        break;
                    }
                }
            }
            if(! $mapea){
                //Si no tiene permiso es redireccionado
                $response->redirect($config_seguridad['error']);
            }
        }
        else{
            //Si no existe la configuracion aviso del error
            echo "No existe definicion de seguridad para $user_logged";
            $request->session->deleteSession();
            exit();
        }
    }
}