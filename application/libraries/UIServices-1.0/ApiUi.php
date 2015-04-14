<?php
/**
 * Description of ApiUi
 *
 * @author Usuario_2
 */

class ApiUi {
    private static $instancia;
    private $serverDefinition;
    
    public static $proyecto= 'bootstrap3';    
    public $componentes;
    
    private function __construct() {
    }    
    public static function getInstance(){
        if(!self::$instancia instanceof self){
            self::$instancia = new self;
        }
        return self::$instancia;
    }    
    public function theme($nombre){
        $nom_componente= self::$proyecto . '_' . $nombre;
        if(! file_exists(PATH_THEME . $nom_componente . '.php')){
            try{
                $theme= $this->conexionTheme($nombre); 
                $arch = fopen(PATH_THEME . $nom_componente . '.php', 'x');
                fwrite($arch, $theme);
                fclose($arch); 
            } catch(Exception $e){
                echo 'Error en carga del componente: ' . $nombre;
                return;
            }
        }
        include PATH_THEME . $nom_componente . '.php';
    }
    public function javaScript($nombre){
        $nom_componente= self::$proyecto . '_' . $nombre;
        if(! file_exists(PATH_JAVASCRIPT . $nom_componente . '.php')){
            try{
                $javascript= $this->conexionJavaScript($nombre);
                $arch = fopen(PATH_JAVASCRIPT . $nom_componente . '.php', 'x');
                fwrite($arch, $javascript);
                fclose($arch);
            } catch(Exception $e){
                echo 'Error en carga del componente: ' . $nombre;
                return;
            } 
        }
        include PATH_JAVASCRIPT . $nom_componente . '.php';
    }  
    private function conexionTheme($nombre){
        if(! SERVER_DEFINITION){
            $url = 'http://www.edunola.com.ar/serviciosui/theme?nombre=' . $nombre . '&proyecto=' . self::$proyecto;
            //$url= 'http://localhost/uiservices/theme?nombre=' . $nombre . '&proyecto=' . self::$proyecto;              
            return $this->conexionGet($url);
        }else{
            $this->load_server_definition();
            $clave= self::$proyecto .'&theme&'. $nombre;
            return preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $this->serverDefinition[$clave]);
        }
    }     
    private function conexionJavaScript($nombre){
        if(! SERVER_DEFINITION){
            $url = 'http://www.edunola.com.ar/serviciosui/javascript?nombre=' . $nombre . '&proyecto=' . self::$proyecto;
            //$url= 'http://localhost/uiservices/javascript?nombre=' . $nombre . '&proyecto=' . self::$proyecto;        
            return $this->conexionGet($url);
        }else{
            $this->load_server_definition();
            $clave= self::$proyecto .'&javascript&'. $nombre;
            return preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $this->serverDefinition[$clave]);
        }
    }  
    public function componente($nombre, $valores = null){        
        $nom_componente= self::$proyecto . '_' . $nombre;
        if(! file_exists(PATH_COMPONENT . $nom_componente . '.php')){
            try{
                $componente= $this->conexionComponente($nombre);                
                $codigo= "";
                $inicio= 0;            
                $inicio= strpos($componente, "{{", $inicio);
                $fin= strpos($componente, "}}", $inicio);
                while($inicio !== FALSE && $fin !== FALSE){
                    $codigo .= substr($componente, 0, $inicio);
                    $inicio += 2;
                    $var= substr($componente, $inicio, $fin - $inicio);
                    if($var != 'components'){
                        $cod= '<?php echo $valores[' . '"'. $var . '"' . '];?>';
                        $codigo .= $cod;
                    }

                    $componente= substr($componente, $fin + 2);

                    $inicio= strpos($componente, "{{", 0);
                    $fin= strpos($componente, "}}", 0);
                }
                $codigo .= $componente;
                $componente= $codigo;
                $codigo= "";

                $inicio= 0;            
                $inicio= strpos($componente, "{%", $inicio);
                $fin= strpos($componente, "%}", $inicio);
                $nivelesElseIf= array();
                while($inicio !== FALSE && $fin !== FALSE){
                    $codigo .= substr($componente, 0, $inicio);

                    //$codigo .= $this->codigoIf($componente, $inicio, $fin);
                    $tipoIf= $this->tipoIf($inicio, $componente);
                    if($tipoIf == "if"){
                        $codigo .= $this->armarIf($inicio, $componente);
                        $nivelesElseIf[]= 0;                    
                    }
                    if($tipoIf == 'endif'){
                        $cant= count($nivelesElseIf);
                        $codigo .= '<?php }';
                        for ($i = 0; $i < $nivelesElseIf[$cant - 1]; $i++) {
                            $codigo .= '}';
                        }
                        $codigo .= '?>';
                        array_pop($nivelesElseIf);
                    }
                    if($tipoIf == 'else'){
                        $codigo .= $this->armarIf($inicio, $componente);
                    }
                    if($tipoIf == 'elseif'){
                        $codigo .= $this->armarIf($inicio, $componente);
                        $nivelesElseIf[count($nivelesElseIf) - 1] += 1;
                    }

                    $componente= substr($componente, $fin + 2);

                    $inicio= strpos($componente, "{%", 0);
                    $fin= strpos($componente, "%}", 0);
                }
                $codigo .= $componente;
                $arch = fopen(PATH_COMPONENT . $nom_componente . '.php', 'x');
                fwrite($arch, $codigo);
                fclose($arch);
            } catch(Exception $e){
                echo 'Error en carga del componente: ' . $nombre;
                return;
            }            
        }
        //Lo incluyo y se ejecuta solo
        include PATH_COMPONENT . $nom_componente . '.php';
    }  
    private function armarIf($inicio, $componente){
        $res= '<?php ';
        $tipo= "";
        $inicio += 2;
        while($componente[$inicio] == " "){
            $inicio++;
        }
        if(substr($componente, $inicio, 3) == 'if '){
            $tipo= 'if';
            $inicio += 3;
            $res .= 'if(';
        }        
        if(substr($componente, $inicio, 7) == 'elseif '){
            $tipo= 'elseif';
            $inicio += 7;
            $res .= '}else{if(';
        }        
        if((substr($componente, $inicio, 5) == 'else ') || (substr($componente, $inicio, 5) == 'else}')){
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
            while($componente[$inicio] == " "){
                $inicio++;
            }
            $posVar= $inicio;
            while($componente[$inicio] != " "){
                $inicio++;
            }
            $var= substr($componente, $posVar, $inicio - $posVar);
            
            //Busco el comparador
            while($componente[$inicio] == " "){
                $inicio++;
            }
            $operacion= "";
            switch ($componente[$inicio]) {
                case '=':
                    $inicio++;
                    if($componente[$inicio] == '='){
			$operacion= '==';
                    }
                    break;					
		case '!':
                    $inicio++;
                    if($componente[$inicio] == '='){
			$operacion= '!=';
                    }
                    break;
                default:
                    break;
            }
            
            //Busco la variable 2
            $inicio++;
            while($componente[$inicio] == " "){
                $inicio++;
            }
            $posVar2= $inicio;
            while($componente[$inicio] != " " && $componente[$inicio] != "}"){
                $inicio++;
            }
            $var2= NULL;
            if($componente[$posVar2] == '"' && $componente[$inicio - 1] == '"'){
                $var2= substr($componente, $posVar2 + 1, $inicio - $posVar2 - 2);
            }
            else{
                $var2= substr($componente, $posVar2, $inicio - $posVar2);
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
            if($componente[$inicio] != "}"){
                while($componente[$inicio] == " "){
                    $inicio++;
                }
		if(substr($componente, $inicio, 3) == 'and'){
                    $op2= '&&';
                    $continuar= true;
                    $inicio= $inicio + 3;
                }
		if(substr($componente, $inicio, 2) == 'or'){
                    $op2= '||';
                    $continuar= true;
                    $inicio= $inicio + 2;
		}
            }
        }
        $res .= '){?>';
        
        return $res;
    }   
    private function tipoIf($inicio, $componente){
        $inicio += 2;
        while($componente[$inicio] == " "){
            $inicio++;
        }
        if(substr($componente, $inicio, 3) == 'if '){
            return 'if';
        }        
        if(substr($componente, $inicio, 7) == 'elseif '){
            return 'elseif';
        }        
        if((substr($componente, $inicio, 5) == 'else ') || (substr($componente, $inicio, 5) == 'else}')){
            return 'else';
        }
        if((substr($componente, $inicio, 6) == 'endif ') || (substr($componente, $inicio, 6) == 'endif}')){
            return 'endif';
        }
	return 'error';
    }
    private function conexionComponente($nombre){
        if(! SERVER_DEFINITION){
            //$url = 'http://www.edunola.com.ar/serviciosui/componenteDefinition?nombre=' . $nombre . '&proyecto=' . self::$proyecto;
            $url= 'http://localhost/uiservices/componenteDefinition?nombre=' . $nombre . '&proyecto=' . self::$proyecto;
            return $this->conexionGet($url);
        }else{
            $this->load_server_definition();
            $clave= self::$proyecto .'&component&'. $nombre;
            return preg_replace("/\r\n+|\r+|\n+|\t+/i", " ", $this->serverDefinition[$clave]);
        }
    }
    private function load_server_definition(){
        if($this->serverDefinition == NULL){
            $lineas= file(SERVER_DEFINITION_FILE);
            $this->serverDefinition= $this->parse_properties($lineas);
        }
    }    
    private function conexionGet($url){
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
            throw new Exception();
        }
        //Cierra la conexion
        curl_close($curl_conexion);        
        return $result;
    }
    /**
     * Pasa las lineas donde se definen los componentes a un arreglo asociativo con el nombre del component
     * @param type $lineas
     * @return array[asociativo]
     */
    private function parse_properties($lineas) {
        $result= NULL;
        foreach($lineas as $i=>$linea) {
            if(empty($linea) || !isset($linea) || strpos($linea,"#") === 0){
                continue;
            }
            $key = substr($linea,0,strpos($linea,'='));
            $value = substr($linea,strpos($linea,'=') + 1, strlen($linea));
            $result[$key] = $value;
        }
        return $result;
   }
}
?>