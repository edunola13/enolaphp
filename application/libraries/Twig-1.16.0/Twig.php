<?php
/**
 * Libreria que realiza el manejo de la vista con Twig
 *
 * @author Enola
 */
class Twig {
    private static $instance;       //Instance
    
    private $loader; // Instance of Twig_Loader_Filesystem
    private $environment; // Instance of Twig_Environment
    
    public $con_environment= "production";
    //public $template_dir= "../../source/view";
    public $template_dir= "source/view";
    //public $cache_dir= "../../cache";
    public $cache_dir= "cache";
    public $uiservices= FALSE;
    
    /**
     * Realiza toda la configuracion con Twig para que la libreria quede lista para ser usada por el usuario
     */
    public function __construct(){
        //Configuracion Twig
//        $json_twig= file_get_contents(PATHAPP . CONFIGURATION . 'twig.json');
//        $config_twig= json_decode($json_twig, TRUE);
        
        require_once 'lib/Twig/Autoloader.php';
        // Twig's autoloader will take care of loading required classes
        Twig_Autoloader::register();
        
        //$config_twig esta definida en twig.php
        $this->loader = new Twig_Loader_Filesystem(PATHAPP . $this->template_dir);
        //$this->loader = new Twig_Loader_Filesystem(realpath(dirname(__FILE__)) . '/' . $this->template_dir);
        
	$this->con_environment= ENVIRONMENT;
		
        if($this->con_environment == 'production'){
            //Para produccion
            $this->environment = new Twig_Environment($this->loader, array('cache' => PATHAPP . $this->cache_dir));
            //$this->environment = new Twig_Environment($this->loader, array('cache' => realpath(dirname(__FILE__)) . '/' . $this->cache_dir));
        }
        else{
            //Para Desarrollo, no usa cache
            $this->environment = new Twig_Environment($this->loader);
        }     

        /*
         * Solo para Enola PHP
         * Cargamos funciones de la vista del framework a Twig
         */
        $this->environment->addFunction('base', new Twig_Function_Function('base'));
        $this->environment->addFunction('base_locale', new Twig_Function_Function('base_locale'));
        $this->environment->addFunction('locale', new Twig_Function_Function('locale'));
        $this->environment->addFunction('locale_uri', new Twig_Function_Function('locale_uri'));
        $this->environment->addFunction('replace', new Twig_Function_Function('replace'));
        $this->environment->addFunction('replaces_space', new Twig_Function_Function('replaces_space'));
        $this->environment->addFunction("component", new Twig_Function_Function('component'));
        $this->environment->addFunction('i18n', new Twig_Function_Function('i18n'));
        $this->environment->addFunction('i18n_change_locale', new Twig_Function_Function('i18n_change_locale'));
        $this->environment->addFunction("i18n_value", new Twig_Function_Function('i18n_value'));
        $this->environment->addFunction("i18n_locale", new Twig_Function_Function('i18n_locale'));
    }
    
    /**
     * Crea una unica instancia
     */
    public static function getInstance(){
        if(Twig::$instance == NULL){
            Twig::$instance= new Twig();
        }
        return Twig::$instance;
    }
    
    /**
     * Carga una vista correspondiente con sus correspondientes datos
     * @param string $templateFile
     * @param array $variables
     * @return VISTA
     */
    public function render($templateFile, array $variables = NULL){
        if($variables == NULL){
            return $this->environment->render($templateFile);
        }
        else{
            return $this->environment->render($templateFile, $variables);
        }
    }  
    /**
     * Carga un template correspondiente
     * @param string $templateFile
     * @return TEMPLATE
     */
    public function loadTemplate($templateFile){
        return $this->environment->loadTemplate($templateFile);
    }
}