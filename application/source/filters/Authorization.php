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
     * @param En_HttpRequest $request
     * @param En_HttpResponse $response
     */
    public function filter(En_HttpRequest $request, En_HttpResponse $response){
        $auth= \Enola\Support\Authorization::getInstance();
        //Tipo por defecto
        $userProfile= 'default';
        $actualProfile= $userProfile;
        if($request->session->exist('user_logged')){
            //Si existe le asigno el tipo correspondiente
            $userProfile= $request->session->get('user_logged');
        }
        $maps= FALSE;
        if(is_array($userProfile)){
            foreach ($userProfile as $profile) {
                $maps= $auth->profileHasAccessToUrl($profile, $request->uriApp, $request->requestMethod);
                $actualProfile= $profile;
                if($maps){break;}
            }
        }else{
            $actualProfile= $userProfile;
            $maps= $auth->profileHasAccessToUrl($userProfile, $request->uriApp, $request->requestMethod);
        }
        if(! $maps){
            //Si el perfil no existe elimina la sesion
            if($auth->getProfile($actualProfile) == NULL){
                $request->session->deleteSession();
            }
            //Si no tiene permiso es redireccionado a una url o manejado por un controlador
            if(isset($auth->getProfile($actualProfile)['error-redirect'])){
                $response->redirect($auth->getProfile($actualProfile)['error-redirect']);
            }else if(isset($auth->getProfile($actualProfile)['error-forward'])){
                $this->forward($auth->getProfile($actualProfile)['error-forward']);
            }else{
                echo 'No Permissions'; exit;
            } 
        }
    }
}