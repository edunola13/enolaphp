<?php
    /*
     * Realiza la configuracion completa del sistema y empieza a delegar el requerimiento del cliente a los modulos del
     * framework y a los del cliente
     */
    /*
     * Lee archivo configuracion.json donde se encuentra toda la configuracion de variables, filtros, controladores, 
     * librerias, helpers, etc.
     */
    $json_configuration= file_get_contents($path_aplication . 'configuration.json');
    $config= json_decode($json_configuration, true);    
    if(! is_array($config)){
        //Arma una respuesta de error de configuracion.
        //No realiza el llamado a funcions de error porque todavia no se cargo el modulo de errores
        $head= 'Configuration Error';
        $message= 'The file configuration.json is not available or is misspelled';
        require $path_aplication . 'errors/general_error.php';
        //Cierra la aplicacion
        exit;
    }    
    //Define si muestra o no los errores y en que nivel de detalle dependiendo en que fase se encuentre la aplicacion
    switch ($config['environment']){
        case 'development':
            error_reporting(E_ALL);
            define('ERROR', 'todos');
            break;	
        case 'production':
            error_reporting(0);
            define('ERROR', 'ninguno');
            break;
        default:
            //No realiza el llamado a funcion de error porque todavia no se cargo el modulo de errores
            $head= 'Configuration Erron';
            $message= 'The environment is not defined in configuration.json';
            require $path_aplication . 'errors/general_error.php';
            exit;
    }    
    //Carga la clase Rendimiento
    require $path_framework . 'classes/Performance.php';
    //Analiza si calcula el tiempo que tarda la aplicacion en ejecutarse
    $performance= NULL;
    if($config['calculate_performance'] == 'TRUE' || $config['calculate_performance'] == 'true'){
        //Incluye la clase Rendimiento 
        $performance= new Performance();
        $performance->start();
    }	
    //Seteo la codificacion de caracteres, casi siempre es o debe ser UTF-8
    ini_set('default_charset', $config['charset']);   

    // Define las constantes del sistema
    // BASE_URL: Base url de la aplicacion - definida por el usuario en el archivo de configuracion    
    $pos= strlen($config['base_url']) - 1;
    if($config['base_url'][$pos] != '/'){
        $config['base_url'] .= '/';
    }
    define('BASEURL', $config['base_url']);    
	//ENVIRONMENT: Indica el ambiente de la aplicacion
	define('ENVIRONMENT', $config['environment']);
    //CONFIGURATION: carpeta base de configuracion - definida por el usuario en el archivo de configuracion
    define('CONFIGURATION', $config['configuration']);    
    //JSON_CONFIG_BD: archivo de configuracion para la base de datos
    //Si el usuario definio que va a tener bd, en el archivo de configuracion guarda el archivo de configuracion de la BD
    if(isset($config['database']['configuration'])){
        define('JSON_CONFIG_BD', $config['database']['configuration']);
    }    
    //URL_COMPONENT: URL con la cual se deben mapear los controladores
    define('URL_COMPONENT', $config['url-components']);    
    // PATHFRA: direccion de la carpeta de la aplicacion - definida en index.php
    define('PATHFRA', $path_framework);    
    // PATHAPP: direccion de la carpeta de la aplicacion - definida en index.php
    define('PATHAPP', $path_aplication);         
    /*
     * Creacion de variables globales
     */    
    //Creo variable global con la configuracion de Internacionalizacion
    if(isset($config['i18n'])){
        $GLOBALS['i18n']= $config['i18n'];
    }    
    //Creo la variable global con la configuracion de librerias
    $GLOBALS['libraries_file']= $config['libraries'];    
    /*
     * Carga de modulos obligatorios para que el framework trabaje correctamente
     */    
    //Carga del modulo errores
    require PATHFRA . 'modules/errors.php';
    //Define un manejador de excepciones - definido en el modulo errores
    set_error_handler('_error_handler');
    //Define un manejador de fin de cierre - definido en el modulo de errores
    register_shutdown_function('_shutdown');         
    //Carga de modulo URL-URI
    require PATHFRA . 'modules/url_uri.php';
    //Define la uri de la aplicacion y la setea como una variable estatica
    define_application_uri();        
    //Carga de modulo para carga de archivos
    require PATHFRA . 'modules/load_files.php';
    //Carga de modulo con funciones para la vista
    require PATHFRA . 'modules/view.php';
    //Carga de modulo de seguridad
    require PATHFRA . 'modules/security.php';
    //Carga Clase Base Enola
    require PATHFRA . 'classes/Enola.php';  
    //Carga Clase En_DataBase
    require PATHFRA . 'classes/En_DataBase.php';    
    /*
     * Analiza el paso de un error HTTP
     */
    catch_server_error();
    /*
     * Fin
     */    
    /*
     * Cargo todas las librerias particulares de la aplicacion que se cargaran automaticamente indicadas en el archivo de configuracion
     */
    //Leo las librerias de la variable config
    $archivos_librerias_a= $config['libraries'];
    //Recorro de a una las librerias y las importo
    foreach ($archivos_librerias_a as $libreria) {
        //$libreria['class'] tiene la direccion completa desde LIBRARIE, no solo el nombre
        $dir= $libreria['class'];
        import_librarie($dir);
    }
    
    /**
     * Configuracion Inicial: Despues de la carga inicial y las libreria permite que el usuario realice su propia configuracion
     * Antes de atender el requerimiento HTTP 
     */
    require PATHAPP . 'load_user_config.php';    
    
    /*
     * Cargo el modulo HTTP 
     */
    require PATHFRA . 'modules/http.php';
    
    /*
     * Almacena la definicion de componentes en una variable global y analiza si carga el modulo componente
     */    
    //Leo las componentes de la variable config y analizo todo lo respectivo a ellas
    $componentes= $config['components'];
    if(count($componentes) > 0){
        //La guarda como global para que luego pueda ser utilizada
        $GLOBALS['components']= $componentes;
        //Cargo el modulo componente
        require PATHFRA . 'modules/component.php';
		//Analiza si se ejecuta un componente via URL
		if(maps_components()){
			execute_url_component();
			//Termina la ejecucion
			exit;
		}
    }
    
    /*
     * Lee los controladores de la variable config. En caso de que no haya controladores avisa del error
     * Me quedo con el controlador que mapea
     */
    $controllers= $config['controllers'];
    $actual_controller= NULL;
    if(count($controllers) > 0){
        $actual_controller= mapping_controller($controllers);
    }
    else{
        general_error('Controller Error', 'There isent define any controller');
    }
    //Creo el HTTP REQUEST correspondiente en base a la URL que mapeo
    create_request($actual_controller['url']);
    /*
     * Lee los filtros que se deben ejecutar antes del procesamiento de la variable config y delega trabajo a archivo filtros.php
     * En caso de que no haya filtros asignados no delega ningun trabajo
     */
    $filtros= $config['filters'];
    if(count($filtros) > 0){
        execute_filters($filtros);
    }        
    /**
     *Ejecuto el controlador correspondiente 
     */
    execute_controller($actual_controller);
    /**
     * Lee los filtros que se deben ejecutar despues del procesamiento de la variable config y delega trabajo a archivo filtros.php
     * En caso de que no haya filtros asignados no delega ningun trabajo
     */
    $filtros_despues= $config['filters_after_processing'];
    if(count($filtros_despues) > 0){
        execute_filters($filtros_despues);
    }     
    /*
     * Si se esta calculando el tiempo, realiza el calculo y envia la respuesta
     */
    if($performance != NULL){
        $performance->terminate();
        $mensaje= 'The execution time of the APP is: ' . $performance->elapsed();
        $titulo= 'Performance';
        //Muestra la informacion al usuario
        display_information($titulo, $mensaje);
    }
?>