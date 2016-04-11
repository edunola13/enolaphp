<?php
namespace Enola\Support;
/**
 * Esta clase realiza los controles de autorizacion para los diferentes modulos de su aplicacion.
 * Este servira para controlar los accesos a diferentes funcionalidades de la aplicacion como puede ser un controlador,
 * componentes, etc.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category Enola\Support
 */
class Authorization {
    protected static $instance;
    protected $context;
    protected $modules;
    protected $profiles;
    
    protected function __construct() {
        $this->context= \EnolaContext::getInstance();
        $config= $this->context->readConfigurationFile($this->context->getAuthorizationFile());
        if(!isset($config['modules']) || !isset($config['profiles'])){
            \Enola\Error::general_error('Configuration Error', 'The configuration file ' . $this->context->getAuthorizationFile() . ' is not available or is misspelled');
        }
        $this->modules= $config['modules'];
        $this->profiles= $config['profiles'];
    }
    /** 
     * @return Authorization
     */
    public static function getInstance(){
        if(Authorization::$instance == NULL){
            Authorization::$instance= new Authorization();
        }
        return Authorization::$instance;
    }
    /**
     * Retorna todos los modulos de la aplicacion
     * @return array
     */
    public function getModules(){
        return $this->modules;
    }
    /**
     * Retorna un determinado modulo o NULL si no existe
     * @param string $name
     * @return array
     */
    public function getModule($name){
        if(isset($this->modules[$name])){
            return $this->modules[$name];
        }
        return NULL;
    }
    /**
     * Retorna todos los profiles de la aplicacion
     * @return array
     */
    public function getProfiles(){
        return $this->profiles;
    }
    /**
     * Retorna un determinado profile o NULL si no existe
     * @param string $name
     * @return array
     */
    public function getProfile($name){
        if(isset($this->profiles[$name])){
            return $this->profiles[$name];
        }
        return NULL;
    }
    
    /*
     * AUTORIZACION DE CONTROLADORES / MODULOS-PERFILES
     */
    /**
     * Indica si el usuario logueado tiene acceso a un modulo
     * @param Request $request
     * @param string $moduleName
     * @return boolean
     */
    public function userHasAccess($request, $moduleName){
        //Tipo por defecto
        $userProfile= 'default';
        if($request->session->exist('user_logged')){
            //Si existe le asigno el tipo correspondiente
            $userProfile= $request->session->get('user_logged');
        }
        $maps= FALSE;
        if(is_array($userProfile)){
            $maps= $this->profilesHasAccess($userProfile, $moduleName);
        }else{
            $maps= $this->profileHasAccess($userProfile, $moduleName);
        }
        return $maps;
    }
    /**
     * Indica si un conjunto de perfiles tienen acceso a un modulo
     * Si un perfil tiene acceso, el conjunto lo tiene
     * @param string[] $profilesName
     * @param string $moduleName
     * @return boolean
     */
    public function profilesHasAccess($profilesName, $moduleName){
        $maps= FALSE;
        foreach ($profilesName as $profile) {
            $maps= $this->profileHasAccess($profile, $moduleName);
        }
        return $maps;
    }
    /**
     * Indica si un perfil tiene acceso a un modulo
     * @param string $profileName
     * @param string $moduleName
     * @return boolean
     */
    public function profileHasAccess($profileName, $moduleName){
        $maps= FALSE;
        //Compruebo que exista la configuracion para el tipo de usuario logueado
        if($this->getProfile($profileName) != NULL){            
            //Seteo la configuracion del usuario correpondiente
            $config_seguridad= $this->getProfile($profileName);
            //Seteo los permisos del usuario
            $permisos= $config_seguridad['permit'];
            //Veo si el profile contiene el modulo en su seccion de permitidos
            $maps= in_array($moduleName, $permisos);            
            if($maps){
                //Si hubo mapeo, recorro las url denegadas para el usuario
                $denegados= $config_seguridad['deny'];
                $maps= ! in_array($moduleName, $denegados);
            }            
        }        
        return $maps;
    }
    /**
     * Indica si el usuario logueado tiene acceso a un url y request method
     * @param Request $request
     * @param string $url
     * @param string $method
     * @return boolean
     */    
    public function userHasAccessToUrl($request, $url, $method){
        //Tipo por defecto
        $userProfile= 'default';
        if($request->session->exist('user_logged')){
            //Si existe le asigno el tipo correspondiente
            $userProfile= $request->session->get('user_logged');
        }
        $maps= FALSE;
        if(is_array($userProfile)){
            $maps= $this->profilesHasAccessToUrl($userProfile, $url, $method);
        }else{
            $maps= $this->profileHasAccessToUrl($userProfile, $url, $method);
        }
        return $maps;
    }
    /**
     * Indica si un conjunto de perfiles tienen acceso a una url y request method
     * Si un perfil tiene acceso, el conjunto lo tiene
     * @param string[] $profilesName
     * @param string $url
     * @param string $method
     * @return boolean
     */
    public function profilesHasAccessToUrl($profilesName, $url, $method){
        $maps= FALSE;
        foreach ($profilesName as $profile) {
            $maps= $this->profileHasAccessToUrl($profile, $url, $method);
        }
        return $maps;
    }
    /**
     * Indica si un perfil tiene acceso a una url y request method
     * @param string $profileName
     * @param string $url
     * @param string $method
     * @return boolean
     */
    public function profileHasAccessToUrl($profileName, $url, $method){
        $maps= FALSE;
        //Compruebo que exista la configuracion para el tipo de usuario logueado
        if($this->getProfile($profileName) != NULL){            
            //Seteo la configuracion del usuario correpondiente
            $config_seguridad= $this->getProfile($profileName);
            //Seteo los permisos del usuario
            $permisos= $config_seguridad['permit'];            
            //Recorro sus permisos y veo si alguno coincice
            foreach ($permisos as $permiso) {
                if($this->mapsModule($permiso, $url, $method)){
                    //Cuando alguno coincide salgo del for
                    $maps= TRUE;
                    break;
                }
            }
            if($maps){
                //Si hubo mapeo, recorro las url denegadas para el usuario
                $denegados= $config_seguridad['deny'];
                foreach ($denegados as $denegado) {
                    if($this->mapsModule($denegado, $url, $method)){
                        //Si la url es denegada salgo del for
                        $maps= FALSE;
                        break;
                    }
                }
            }  
        }
        return $maps;
    }
    /**
     * Indica si para un modulo dado la url y el request method mapean con el
     * @param string $moduleName
     * @param string $url
     * @param string $method
     * @return boolean
     */
    protected function mapsModule($moduleName, $url, $method){
        return (\Enola\Http\UrlUri::mapsActualUrl($this->getModule($moduleName)['url'], $url) && \Enola\Http\UrlUri::mapsActualMethod($this->getModule($moduleName)['method'], $method));
    }
    
    /*
     * AUTORIZACION DE COMPONENTES
     */
    /**
     * Indica si el usuario logueado tiene acceso a la definicion de un componente
     * @param Request $request
     * @param array $component
     * @return boolean
     */
    public function userHasAccessToComponentDefinition($request, $component){
        if(isset($component['authorization-profiles']) && $component['authorization-profiles'] != ""){
            $profiles= str_replace(' ', '', $component['authorization-profiles']);
            $profiles= explode(',', $component['authorization-profiles']);
            $userProfile= 'default';
            $session= $request->session;
            if($session->exist($this->context->getSessionProfile())){
                $userProfile= $session->get($this->context->getSessionProfile());
            }
            //Comprueba si el usuario logueado tiene o no multiples perfiles y en base a eso comprueba
            if(is_array($userProfile)){
                return (count(array_intersect($userProfile, $profiles)) > 0);
            }else{
                return in_array($userProfile, $profiles);
            }            
        }else{
            //Si no esta seteado o es vacio el componente es publico
            return TRUE;
        }
    }
    /**
     * Indica si el usuario logueado tiene acceso a un componente
     * @param Request $request
     * @param string $componentName
     * @return boolean
     */
    public function userHasAccessToComponent($request, $componentName){
        $component= $this->context->getComponentsDefinition()[$componentName];
        return $this->userHasAccessToComponentDefinition($request, $component);
    }    
    /**
     * Indica si un conjunto de perfiles tienen acceso a un componente
     * Si un perfil tiene acceso, el conjunto lo tiene
     * @param string $profilesName
     * @param string $componentName
     * @return boolean
     */
    public function profilesHasAccessToComponent($profilesName, $componentName){
        $component= $this->context->getComponentsDefinition()[$componentName];
        if(isset($component['authorization-profiles']) && $component['authorization-profiles'] != ""){
            $profiles= str_replace(' ', '', $component['authorization-profiles']);
            $profiles= explode(',', $component['authorization-profiles']);
            return (count(array_intersect($profilesName, $profiles)) > 0);
        }else{
            return TRUE;
        }
    }
    /**
     * Indica si un perfil tiene acceso a un componente
     * @param string $profileName
     * @param string $componentName
     * @return boolean
     */
    public function profileHasAccessToComponent($profileName, $componentName){
        $component= $this->context->getComponentsDefinition()[$componentName];
        if(isset($component['authorization-profiles']) && $component['authorization-profiles'] != ""){
            $profiles= str_replace(' ', '', $component['authorization-profiles']);
            $profiles= explode(',', $component['authorization-profiles']);
            return in_array($profileName, $profiles);
        }else{
            return TRUE;
        }
    }
}