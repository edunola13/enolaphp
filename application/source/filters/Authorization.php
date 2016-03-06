<?php
use Enola\Http;
use Enola\Http\En_HttpRequest,Enola\Http\En_HttpResponse;

/**
 * Filtro que analiza la autorizacion de los usuarios
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 */
class Authorization extends Http\En_Filter{
    /** @var string */
    public $configFile= 'authorization';
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
        //Leo el archivo de configuracion de seguridad
        $seguridad= $this->context->readConfigurationFile($this->configFile);
        //Tipo por defecto
        $user_logged= 'default';
        if($request->session->exist('user_logged')){
            //Si existe le asigno el tipo correspondiente
            $user_logged= $request->session->get('user_logged');
        }
        $maps= FALSE;
        if(is_array($user_logged)){
            foreach ($user_logged as $profile) {
                $maps= $this->checkAuthorization($seguridad, $profile, $request, $response);
                if($maps){break;}
            }
        }else{            
            $maps= $this->checkAuthorization($seguridad, $user_logged, $request, $response);
        }
        if(! $maps){
            //Si no tiene permiso es redireccionado
            $response->redirect($seguridad[$user_logged]['error']);
        }
    }
    /**
     * Comprueba si un tipo de usuario-profile tiene permisos
     * @param type $seguridad
     * @param type $profile
     * @param type $response
     */
    private function checkAuthorization($seguridad, $profile, $request, $response){
        $maps= FALSE;
        //Compruebo que exista la configuracion para el tipo de usuario logueado
        if(isset($seguridad[$profile])){
            //Seteo la configuracion del usuario correpondiente
            $config_seguridad= $seguridad[$profile];
            //Seteo los permisos del usuario
            $permisos= $config_seguridad['permit'];            
            //Recorro sus permisos y veo si alguno coincice
            foreach ($permisos as $permiso) {
                if(Http\UrlUri::mapsActualUrl($permiso)){
                    //Cuando alguno coincide salgo del for
                    $maps= TRUE;
                    break;
                }
            }
            if($maps){
                //Si hubo mapeo, recorro las url denegadas para el usuario
                $denegados= $config_seguridad['deny'];
                foreach ($denegados as $denegado) {
                    if(Http\UrlUri::mapsActualUrl($denegado)){
                        //Si la url es denegada salgo del for
                        $maps= FALSE;
                        break;
                    }
                }
            }            
        }
        else{
            //Si no existe la configuracion aviso del error
            echo "No existe definicion de seguridad para $profile";
            $request->session->deleteSession();
            exit();
        }
        return $maps;
    }
    
    /** @return string */
    public function getConfigFile(){
        return $this->configFile;
    }
    /** @param string $configFile */
    public function setConfigFile($configFile){
        $this->configFile= $configFile;
    }
}