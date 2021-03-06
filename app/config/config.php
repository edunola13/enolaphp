<?php

$config['url_app']= 'http://localhost';
$config['relative_url']= '/enolaphp';
$config['index_page']= '';
$config['environment']= 'development';
$config['calculate_performance']= true;
$config['authentication']= 'token';
$config['session_autostart']= false;
$config['authorization_file']= 'authorization';
  
$config['controllers']= array(
    'routes'
);
      
$config['filters']= array(
    'authorization' => array( 
        'class' => 'Authorization',
        'namespace' => 'YourApp\Filters',
        'filtered' => '/*'
    )
);

$config['filters_after_processing']= array();
  
$config['i18n']= array( 
    'default' => 'es',
    'locales' => 'en,fr,me'
);
    
$config['url-components']= 'enola-components';
$config['components']= array();
      
$config['libs']= array(
);
      
$config['dependency_injection']= array(
    'dependencyInjection'
);