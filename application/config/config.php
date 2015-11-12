<?php

$config['base_url']= 'http://localhost/enolaphp/';
$config['index_page']= '';
$config['environment']= 'development';
$config['calculate_performance']= true;
  
$config['controllers']= array(
    'index' => array(
        'class' => 'Index',
        'url' => '/'
    )
);
      
$config['filters']= array(
    'authorization' => array( 
        'class' => 'Authorization',
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
      
$config['libraries']= array(
    'validation' => array( 
        'path' => 'Validation'
    ),
    'paginator' => array(
        'path' => 'Paginator'
    )
);
      
$config['composer']= ''; 
      
$config['dependency_injection']= array(
    'dependencyInjection'
);