<?php

$config['cache']= array(
    'class' => 'Cache',
    'namespace' => 'Enola\Cache',
    'load_in' => 'controller',
    'constructor' => array( 
        'store' => array(
            'value' => 'File',
            'type' => 'string'
        )
    ),
    'properties' => array(
        'prefix' => array(
            'value' => 'File', 
            'type' => 'string',
        )
    )
);
     
$config['validation']= array(
    'class' => 'Validation',
    'namespace' => 'Enola\Lib',
    'load_in' => 'controller',
    'constructor' => array( 
        'locale' => array(
            'value' => 'es',
            'type' => 'string'
        )
    ),
    'properties' => array(
        'locale' => array(
            'value' => 'es', 
            'type' => 'string',
        )
    )
);

$config['paginator']= array(
    'class' => 'Paginator',
    'namespace' => 'Enola\Lib',
    'load_in' => 'controller',
    'constructor' => array( 
        'amount' => array(
            'value' => 5,
            'type' => 'integer'
        ),
        'total' => array(
            'value' => 15,
            'type' => 'integer'
        ),
        'current' => array(
            'ref' => 'validation'
        )
    )
);