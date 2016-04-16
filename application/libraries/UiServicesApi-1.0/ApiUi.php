<?php
namespace UiServices;
/**
 * Esta clase es la encargada de conectarse con el servidor y ejecutar los archivos.
 * Sabe como compilar las definiciones de los componentes y luego como ejecutarlos.
 * Es consultado por la clase Tags para poder imprimir los componentes.
 * Implementa el patron Singleton para realizar las cargas pesadas una unica vez.
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category \UiServices
 * @internal
 */
class ApiUi {
    private static $instance;
    private $api;
    private $componentsDefinition;
    
    public $project= 'bootstrap3';    
    public $components;
    public $pathTheme;
    public $pathJS;    
    public $pathComponent;
    public $serverUrl;
    public $serverDefinition;
    public $serverDefinitionFile;
    /**
     * Constructor: Guarda los datos de configuracion y setea el tipo de API a utilizar.
     */
    private function __construct() {
        $this->pathTheme= PATH_THEME;
        $this->pathJS= PATH_JAVASCRIPT;    
        $this->pathComponent= PATH_COMPONENT;
        $this->serverUrl= SERVER_URL;
        $this->serverDefinition= SERVER_DEFINITION;
        $this->serverDefinitionFile= SERVER_DEFINITION_FILE;
        if(UI_API_MODE == 'filephp'){
            $this->api= new ApiUiFilePhp($this);
        }else{
            $this->api= new ApiUiEval($this);
        }
    }
    /**
     * Retorna una instancia de la clase.
     * @return ApiUi
     */
    public static function getInstance(){
        if(!self::$instance instanceof self){
            self::$instance = new self;
        }
        return self::$instance;
    }
    /**
     * Ejecuta un theme, si no existe lo carga
     * @param string $name
     */
    public function theme($name){
        $nameComponent= $this->project . '_' . $name;
        if(! file_exists($this->pathTheme . $nameComponent . '.php')){
            try{
                $theme= $this->connectionTheme($name); 
                $file = fopen($this->pathTheme . $nameComponent . '.php', 'x');
                fwrite($file, $theme);
                fclose($file); 
            } catch(\Exception $e){
                echo 'Error loading component: ' . $name;
                return;
            }
        }
        include $this->pathTheme . $nameComponent . '.php';
    }
    /**
     * Ejecuta un javascript, si no existe lo carga
     * @param string $name
     */
    public function javaScript($name){
        $nameComponent= $this->project . '_' . $name;
        if(! file_exists($this->pathJS . $nameComponent . '.php')){
            try{
                $javascript= $this->connectionJavaScript($name);
                $file = fopen($this->pathJS . $nameComponent . '.php', 'x');
                fwrite($file, $javascript);
                fclose($file);
            } catch(\Exception $e){
                echo 'Error loading component: ' . $name;
                return;
            } 
        }
        include $this->pathJS . $nameComponent . '.php';
    }
    /**
     * Realiza la conexion con el servidor y carga el theme
     * @param string $name
     * @return string
     */
    private function connectionTheme($name){
        if(! $this->serverDefinition){
            $url= $this->serverUrl . 'uiprint/theme/' . $this->project . '/' . $name;            
            return $this->connectionGet($url);
        }else{
            $this->loadServerDefinition();
            $key= $this->project .'&theme&'. $name;
            return preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $this->componentsDefinition[$key]);
        }
    }
    /**
     * Realiza la conexion con el servidor y carga el javascript
     * @param string $name
     * @return string
     */
    private function connectionJavaScript($name){
        if(! $this->serverDefinition){
            $url= $this->serverUrl . 'uiprint/javascript/' . $this->project . '/' . $name;        
            return $this->connectionGet($url);
        }else{
            $this->loadServerDefinition();
            $clave= $this->project .'&javascript&'. $name;
            return preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $this->componentsDefinition[$clave]);
        }
    }
    /**
     * Ejecuta un componente, delega el trabajo a la sub API correspondiente
     * @param string $name
     * @param array $values
     */
    public function component($name, $values = null){        
        $this->api->component($name, $values);
    }
    /**
     * Si el componente no existe se conecta con el servidor y luego compila el codigo del componente listo para ejecutar.
     * @param string $name
     * @return string
     */
    public function createComponent($name){
        $component= $this->connectionComponent($name);                
        $code= "";
        $begin= 0;            
        $begin= strpos($component, "{{", $begin);
        $end= strpos($component, "}}", $begin);
        while($begin !== FALSE && $end !== FALSE){
            $code .= substr($component, 0, $begin);
            $begin += 2;
            $var= substr($component, $begin, $end - $begin);
            if($var != 'components'){
                $cod= '<?php echo $valores[' . '"'. $var . '"' . '];?>';
                $code .= $cod;
            }

            $component= substr($component, $end + 2);

            $begin= strpos($component, "{{", 0);
            $end= strpos($component, "}}", 0);
        }
        $code .= $component;
        $component= $code;
        $code= "";

        $begin= 0;            
        $begin= strpos($component, "{%", $begin);
        $end= strpos($component, "%}", $begin);
        $nivelesElseIf= array();
        while($begin !== FALSE && $end !== FALSE){
            $code .= substr($component, 0, $begin);
            //$code .= $this->codigoIf($component, $begin, $end);
            $tipoIf= $this->tipoIf($begin, $component);
            if($tipoIf == "if"){
                $code .= $this->buildIf($begin, $component);
                $nivelesElseIf[]= 0;                    
            }
            if($tipoIf == 'endif'){
                $cant= count($nivelesElseIf);
                $code .= '<?php }';
                for ($i = 0; $i < $nivelesElseIf[$cant - 1]; $i++) {
                    $code .= '}';
                }
                $code .= '?>';
                array_pop($nivelesElseIf);
            }
            if($tipoIf == 'else'){
                $code .= $this->buildIf($begin, $component);
            }
            if($tipoIf == 'elseif'){
                $code .= $this->buildIf($begin, $component);
                $nivelesElseIf[count($nivelesElseIf) - 1] += 1;
            }
            $component= substr($component, $end + 2);

            $begin= strpos($component, "{%", 0);
            $end= strpos($component, "%}", 0);
        }
        $code .= $component;
        return $code;
    }
    /**
     * Compila los If del componente
     * @param integer $begin
     * @param string $component
     * @return string
     */
    private function buildIf($begin, $component){
        $res= '<?php ';
        $tipo= "";
        $begin += 2;
        while($component[$begin] == " "){
            $begin++;
        }
        if(substr($component, $begin, 3) == 'if '){
            $tipo= 'if';
            $begin += 3;
            $res .= 'if(';
        }        
        if(substr($component, $begin, 7) == 'elseif '){
            $tipo= 'elseif';
            $begin += 7;
            $res .= '}else{if(';
        }        
        if((substr($component, $begin, 5) == 'else ') || (substr($component, $begin, 5) == 'else}')){
            $tipo= 'else';
        }        
        if($tipo == 'else'){
            $res .= '}else{?>';
            return $res;
        }        
        $op2= "";
	$continuar= TRUE;        
        while($continuar){
            //Busco la variable
            while($component[$begin] == " "){
                $begin++;
            }
            $posVar= $begin;
            while($component[$begin] != " "){
                $begin++;
            }
            $var= substr($component, $posVar, $begin - $posVar);
            
            //Busco el comparador
            while($component[$begin] == " "){
                $begin++;
            }
            $operacion= "";
            switch ($component[$begin]) {
                case '=':
                    $begin++;
                    if($component[$begin] == '='){
			$operacion= '==';
                    }
                    break;					
		case '!':
                    $begin++;
                    if($component[$begin] == '='){
			$operacion= '!=';
                    }
                    break;
                default:
                    break;
            }
            
            //Busco la variable 2
            $begin++;
            while($component[$begin] == " "){
                $begin++;
            }
            $posVar2= $begin;
            while($component[$begin] != " " && $component[$begin] != "}"){
                $begin++;
            }
            $var2= NULL;
            if($component[$posVar2] == '"' && $component[$begin - 1] == '"'){
                $var2= substr($component, $posVar2 + 1, $begin - $posVar2 - 2);
            }
            else{
                $var2= substr($component, $posVar2, $begin - $posVar2);
            }
            if($var2 == 'null' || $var2 == 'NULL'){
                $var2= 'NULL';
            }
            else{
                $var2= '"' . $var2 . '"';
            }
            
            $res .= ' ' . $op2 . ' ' . '$valores["' . $var . '"]' . ' ' . $operacion . ' ' . $var2;
            
            $continuar= false;
            $op2= "";
            if($component[$begin] != "}"){
                while($component[$begin] == " "){
                    $begin++;
                }
		if(substr($component, $begin, 3) == 'and'){
                    $op2= '&&';
                    $continuar= true;
                    $begin= $begin + 3;
                }
		if(substr($component, $begin, 2) == 'or'){
                    $op2= '||';
                    $continuar= true;
                    $begin= $begin + 2;
		}
            }
        }
        $res .= '){?>';
        
        return $res;
    }
    /**
     * Reconoce el tipo de If.
     * @param integer $begin
     * @param string $component
     * @return string
     */
    private function tipoIf($begin, $component){
        $begin += 2;
        while($component[$begin] == " "){
            $begin++;
        }
        if(substr($component, $begin, 3) == 'if '){
            return 'if';
        }        
        if(substr($component, $begin, 7) == 'elseif '){
            return 'elseif';
        }        
        if((substr($component, $begin, 5) == 'else ') || (substr($component, $begin, 5) == 'else}')){
            return 'else';
        }
        if((substr($component, $begin, 6) == 'endif ') || (substr($component, $begin, 6) == 'endif}')){
            return 'endif';
        }
	return 'error';
    }
    /**
     * Realiza la conexion con el servidor y carga el component
     * @param string $name
     * @return string
     */
    private function connectionComponent($name){
        if(! $this->serverDefinition){
            $url= $this->serverUrl . 'uidefinition/component/' . $this->project . '/' . $name;
            return $this->connectionGet($url);
        }else{
            $this->loadServerDefinition();
            $clave= $this->project .'&component&'. $name;
            return preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $this->componentsDefinition[$clave]);
        }
    }
    /**
     * Carga el archivo que representa al servidor.
     */
    private function loadServerDefinition(){
        if($this->componentsDefinition == NULL){
            $lineas= file($this->serverDefinitionFile);
            $this->componentsDefinition= $this->parse_properties($lineas);
        }
    }
    /**
     * Realiza conexion con el servidor. Hace una peticion de tipo GET.
     * @param string $url
     * @return string
     * @throws \Exception
     */
    private function connectionGet($url){
        //Configuracion general de conexion
        $options = array(
		CURLOPT_RETURNTRANSFER => true, // return web page
		//CURLOPT_FOLLOWLOCATION => true, // follow redirects
		CURLOPT_USERAGENT => 'clienteUIphp', // who am i
		CURLOPT_AUTOREFERER => true, // set referer on redirect
		CURLOPT_CONNECTTIMEOUT => 120, // timeout on connect
		CURLOPT_TIMEOUT => 120, // timeout on response
		CURLOPT_MAXREDIRS => 10, // stop after 10 redirects
	);        
        //Inicia conexion
        $curl_conexion= curl_init($url);  
        curl_setopt($curl_conexion, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl_conexion, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($curl_conexion, CURLOPT_VERBOSE, TRUE);                                                                
        curl_setopt_array( $curl_conexion, $options );        
        //Se ejecuta la consulta
        $result = curl_exec($curl_conexion);
        $header = curl_getinfo($curl_conexion);
        if($header['http_code'] != 200){
            throw new \Exception('The ' . $url . ' threw error ' . $header['http_code']);
        }
        //Cierra la conexion
        curl_close($curl_conexion);        
        return $result;
    }
}

/**
 * Esta clase representa la sub API que sabra como ejecutar un componente con EVAL()
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category \UiServices
 * @internal
 */
class ApiUiEval{
    /** @var ApiUi */
    protected $api;
    public function __construct($api) {
        $this->api= $api;
    }
    /**
     * Ejecuta un componente con eval()
     * Primero asegura que este exista, si no existe le pide a la API que lo traiga del servidor
     * @param string $name
     * @param array $valores
     */
    public function component($name, $valores){
        $nameComponent= $this->api->project . '_' . $name;
        try{
            if($this->api->components == NULL){
                if(! file_exists($this->api->pathComponent . 'components.txt')){
                    $arch = fopen($this->api->pathComponent . 'components.txt', 'x');
                    fclose($arch);
                }
                else{
                    $lineas= file($this->api->pathComponent . 'components.txt');
                    $this->api->components= $this->parseProperties($lineas);
                }
            }
            if(! isset($this->api->components[$nameComponent])){
                $code= $this->api->createComponent($name);
                    
                $file = fopen($this->api->pathComponent . 'components.txt', 'a+');                    
                fwrite($file, $nameComponent . '=?>'. $code . PHP_EOL);
                fclose($file);
                //Actualizo las lineas
                $this->api->components[$nameComponent] = '?>' . $code;         
            }
        } catch(\Exception $e){
            unset($this->api->components[$nameComponent]);
            echo $e->getMessage();
            echo 'Error loading component: ' . $name;
            return;
        }
        $rta= eval($this->api->components[$nameComponent]);
        //Si eval devuelve false quiere decir que fallo la ejecucion
        if($rta === FALSE){
            echo 'Error running the component: ' . $name;
        }
    }
    /**
     * Pasa las lineas donde se definen los componentes a un arreglo asociativo con el nombre del component
     * @param type $lines
     * @return array[asociativo]
     */
    private function parseProperties($lines) {
        $result= NULL;
        foreach($lines as $i=>$line) {
            if(empty($line) || !isset($line) || strpos($line,"#") === 0){
                continue;
            }
            $key = substr($line,0,strpos($line,'='));
            $value = substr($line,strpos($line,'=') + 1, strlen($line));
            $result[$key] = $value;
        }
        return $result;
   }
}
/**
 * Esta clase representa la sub API que sabra como ejecutar un componente incluyendo archivos .php
 * @author Eduardo Sebastian Nola <edunola13@gmail.com>
 * @category \UiServices
 * @internal
 */
class ApiUiFilePhp{
    /** @var ApiUi */
    protected $api;
    public function __construct($api) {
        $this->api= $api;
    }
    /**
     * Ejecuta un componente incluyendo el archivo .php
     * Primero asegura que este exista, si no existe le pide a la API que lo traiga del servidor
     * @param string $name
     * @param array $valores
     */
    public function component($name, $valores){
        $nameComponent= $this->api->project . '_' . $name;
        if(! file_exists($this->api->pathComponent . $nameComponent . '.php')){
            try{
                $code= $this->api->createComponent($name);
                
                $file = fopen($this->api->pathComponent . $nameComponent . '.php', 'x');
                fwrite($file, $code);
                fclose($file);
            } catch(\Exception $e){
                echo 'Error loading component: ' . $name;
                return;
            }            
        }
        //Lo incluyo y se ejecuta solo
        include $this->api->pathComponent . $nameComponent . '.php';
    }
}