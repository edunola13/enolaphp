<?php
/**
 * Libreria que realiza el manejo de la vista con Twig
 *
 * @author Enola
 */
class Twig {
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
                
        //Servicios UI
        if($this->uiservices == TRUE){
            require_once 'servicioui.php';
            $this->environment->addFunction('ui_theme', new Twig_Function_Function('ui_theme'));
            $this->environment->addFunction('ui_javaScript', new Twig_Function_Function('ui_javaScript'));

            $this->environment->addFunction('ui_column_parsed', new Twig_Function_Function('ui_column_parsed'));
            $this->environment->addFunction('ui_address', new Twig_Function_Function('ui_address'));
            $this->environment->addFunction('ui_alert_message', new Twig_Function_Function('ui_alert_message'));
            $this->environment->addFunction('ui_badge', new Twig_Function_Function('ui_badge'));
            $this->environment->addFunction('ui_blockquote', new Twig_Function_Function('ui_blockquote'));
            $this->environment->addFunction('ui_fixed_footer', new Twig_Function_Function('ui_fixed_footer'));
            $this->environment->addFunction('ui_end_fixed_footer', new Twig_Function_Function('ui_end_fixed_footer'));
            $this->environment->addFunction('ui_form_search', new Twig_Function_Function('ui_form_search'));
            $this->environment->addFunction('ui_iframe', new Twig_Function_Function('ui_iframe'));
            $this->environment->addFunction('ui_image', new Twig_Function_Function('ui_image'));
            $this->environment->addFunction('ui_jumbotron', new Twig_Function_Function('ui_jumbotron'));
            $this->environment->addFunction('ui_paginador_simple', new Twig_Function_Function('ui_paginador_simple'));
            $this->environment->addFunction('ui_progress_bar', new Twig_Function_Function('ui_progress_bar'));
            $this->environment->addFunction('ui_simple_footer', new Twig_Function_Function('ui_simple_footer'));
            $this->environment->addFunction('ui_end_simple_footer', new Twig_Function_Function('ui_end_simple_footer'));
            $this->environment->addFunction('ui_simple_header', new Twig_Function_Function('ui_simple_header'));
            $this->environment->addFunction('ui_thumbnail', new Twig_Function_Function('ui_thumbnail'));
            $this->environment->addFunction('ui_title', new Twig_Function_Function('ui_title'));
            $this->environment->addFunction('ui_well', new Twig_Function_Function('ui_well'));

            $this->environment->addFunction('ui_formulario', new Twig_Function_Function('ui_formulario'));
            $this->environment->addFunction('ui_end_formulario', new Twig_Function_Function('ui_end_formulario'));
            $this->environment->addFunction('ui_botonera', new Twig_Function_Function('ui_botonera'));
            $this->environment->addFunction('ui_end_botonera', new Twig_Function_Function('ui_end_botonera'));
            $this->environment->addFunction('ui_button', new Twig_Function_Function('ui_button'));
            $this->environment->addFunction('ui_boolean_checkbox', new Twig_Function_Function('ui_boolean_checkbox'));
            $this->environment->addFunction('ui_checkbox', new Twig_Function_Function('ui_checkbox'));
            $this->environment->addFunction('ui_end_checkbox', new Twig_Function_Function('ui_end_checkbox'));
            $this->environment->addFunction('ui_checkbox_option', new Twig_Function_Function('ui_checkbox_option'));
            $this->environment->addFunction('ui_file_button', new Twig_Function_Function('ui_file_button'));
            $this->environment->addFunction('ui_input', new Twig_Function_Function('ui_input'));
            $this->environment->addFunction('ui_login', new Twig_Function_Function('ui_login'));
            $this->environment->addFunction('ui_radio', new Twig_Function_Function('ui_radio'));
            $this->environment->addFunction('ui_end_radio', new Twig_Function_Function('ui_end_radio'));
            $this->environment->addFunction('ui_radio_option', new Twig_Function_Function('ui_radio_option'));
            $this->environment->addFunction('ui_select', new Twig_Function_Function('ui_select'));        
            $this->environment->addFunction('ui_end_select', new Twig_Function_Function('ui_end_select'));
            $this->environment->addFunction('ui_select_option', new Twig_Function_Function('ui_select_option'));
            $this->environment->addFunction('ui_textarea', new Twig_Function_Function('ui_textarea'));

            $this->environment->addFunction('ui_drop_down_menu', new Twig_Function_Function('ui_drop_down_menu'));
            $this->environment->addFunction('ui_end_drop_down_menu', new Twig_Function_Function('ui_end_drop_down_menu'));
            $this->environment->addFunction('ui_menu_item', new Twig_Function_Function('ui_menu_item'));
            $this->environment->addFunction('ui_nav_bar_form', new Twig_Function_Function('ui_nav_bar_form'));
            $this->environment->addFunction('ui_nav_bar_left', new Twig_Function_Function('ui_nav_bar_left'));
            $this->environment->addFunction('ui_end_nav_bar_left', new Twig_Function_Function('ui_end_nav_bar_left'));
            $this->environment->addFunction('ui_nav_bar_right', new Twig_Function_Function('ui_nav_bar_right'));
            $this->environment->addFunction('ui_end_nav_bar_right', new Twig_Function_Function('ui_end_nav_bar_right'));
            $this->environment->addFunction('ui_nav_item', new Twig_Function_Function('ui_nav_item'));
            $this->environment->addFunction('ui_nav_item_drop_down', new Twig_Function_Function('ui_nav_item_drop_down'));
            $this->environment->addFunction('ui_end_nav_item_drop_down', new Twig_Function_Function('ui_end_nav_item_drop_down'));
            $this->environment->addFunction('ui_nav_item_list', new Twig_Function_Function('ui_nav_item_list'));
            $this->environment->addFunction('ui_navigation_bar', new Twig_Function_Function('ui_navigation_bar'));
            $this->environment->addFunction('ui_end_navigation_bar', new Twig_Function_Function('ui_end_navigation_bar'));
            $this->environment->addFunction('ui_navigation_list', new Twig_Function_Function('ui_navigation_list'));
            $this->environment->addFunction('ui_end_navigation_list', new Twig_Function_Function('ui_end_navigation_list'));
            $this->environment->addFunction('ui_navigation_menu', new Twig_Function_Function('ui_navigation_menu'));
            $this->environment->addFunction('ui_end_navigation_menu', new Twig_Function_Function('ui_end_navigation_menu'));

            $this->environment->addFunction('ui_breadcrumb', new Twig_Function_Function('ui_breadcrumb'));
            $this->environment->addFunction('ui_end_breadcrumb', new Twig_Function_Function('ui_end_breadcrumb'));
            $this->environment->addFunction('ui_em', new Twig_Function_Function('ui_em'));
            $this->environment->addFunction('ui_li', new Twig_Function_Function('ui_li'));
            $this->environment->addFunction('ui_li_a', new Twig_Function_Function('ui_li_a'));
            $this->environment->addFunction('ui_media_object', new Twig_Function_Function('ui_media_object'));
            $this->environment->addFunction('ui_end_media_object', new Twig_Function_Function('ui_end_media_object'));
            $this->environment->addFunction('ui_page', new Twig_Function_Function('ui_page'));
            $this->environment->addFunction('ui_page_first', new Twig_Function_Function('ui_page_first'));
            $this->environment->addFunction('ui_page_last', new Twig_Function_Function('ui_page_last'));
            $this->environment->addFunction('ui_paginator', new Twig_Function_Function('ui_paginator'));
            $this->environment->addFunction('ui_end_paginator', new Twig_Function_Function('ui_end_paginator'));
            $this->environment->addFunction('ui_panel', new Twig_Function_Function('ui_panel'));
            $this->environment->addFunction('ui_paragraph', new Twig_Function_Function('ui_paragraph'));
            $this->environment->addFunction('ui_end_paragraph', new Twig_Function_Function('ui_end_paragraph'));
            $this->environment->addFunction('ui_small', new Twig_Function_Function('ui_small'));
            $this->environment->addFunction('ui_strong', new Twig_Function_Function('ui_strong'));
            $this->environment->addFunction('ui_text', new Twig_Function_Function('ui_text'));
            $this->environment->addFunction('ui_ul', new Twig_Function_Function('ui_ul'));
            $this->environment->addFunction('ui_end_ul', new Twig_Function_Function('ui_end_ul'));
            $this->environment->addFunction('ui_ul_a', new Twig_Function_Function('ui_ul_a'));
            $this->environment->addFunction('ui_end_ul_a', new Twig_Function_Function('ui_end_ul_a'));

            $this->environment->addFunction('ui_table', new Twig_Function_Function('ui_table'));
            $this->environment->addFunction('ui_end_table', new Twig_Function_Function('ui_end_table'));
            $this->environment->addFunction('ui_table_field', new Twig_Function_Function('ui_table_field'));
            $this->environment->addFunction('ui_end_table_field', new Twig_Function_Function('ui_end_table_field'));
            $this->environment->addFunction('ui_table_head', new Twig_Function_Function('ui_table_head'));
            $this->environment->addFunction('ui_end_table_head', new Twig_Function_Function('ui_end_table_head'));
            $this->environment->addFunction('ui_table_head_field', new Twig_Function_Function('ui_table_head_field'));
            $this->environment->addFunction('ui_end_table_head_field', new Twig_Function_Function('ui_end_table_head_field'));
            $this->environment->addFunction('ui_table_row', new Twig_Function_Function('ui_table_row'));
            $this->environment->addFunction('ui_end_table_row', new Twig_Function_Function('ui_end_table_row'));
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
?>