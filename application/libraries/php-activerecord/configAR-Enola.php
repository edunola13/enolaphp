<?php 
    require_once 'ActiveRecord.php';
    /**
     * Realiza la conexion a la BD para la implementacion del patron Active Record
     */
    function conect_db_ar($folder_models, $config_file){
        //Leo archivo de configuracion de BD
        $json_basededatos= file_get_contents($config_file);
        $config_bd= json_decode($json_basededatos, TRUE);
        //Consulta la bd actual
        $bd_actual= $config_bd['actual_db'];
        unset($config_bd['actual_db']);        
        try{
            $cfg = ActiveRecord\Config::instance();            
            $cfg->set_model_directory($folder_models);

            $conexiones= array();
            foreach ($config_bd as $key => $conexion) {
                $conexiones["$key"]= ''.$conexion['driverbd'].'://'.$conexion['user'].':'.$conexion['pass'].'@'.$conexion['hostname'].'/'.$conexion['database'].'?charset='.$conexion['charset'].'';
            }     
            $cfg->set_connections($conexiones);
            $cfg->set_default_connection($bd_actual);            
            
            ActiveRecord\DateTime::$DEFAULT_FORMAT = 'Y-m-d';
        }
        catch(Exception $e){
            throw new Exception ('Conexion Error' . ' $e->getMessage()');
        }        
    }
?>
