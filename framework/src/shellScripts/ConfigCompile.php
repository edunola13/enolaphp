<?php
use Enola\Cron;
use Enola\Cron\En_CronRequest, Enola\Support\Response;

class ConfigCompile extends Cron\En_CronController{
    
    public function compileAllConfig(En_CronRequest $request, Response $response){        
        $files= E_fn\get_files_from_folder($this->context->getConfigurationFolder(), true, array('yml', 'json'));
        foreach ($files as $file) {
            $this->context->compileConfigurationFile($file, true);
        }
    }
}