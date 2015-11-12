<?php

$config['defaultStore']= 'File';
$config['prefix']= 'enolaphp';
  
$config['stores']= array(
    'File' => array( 
        'driver' => 'file',
        'folder' => 'cache'
    ),
    'DataBase' => array(
        'driver' => 'database',
        'connection' => 'Local',
        'table' => 'cache'
    ),
    'Apc' => array(
        'driver' => 'apc'
    ),
    'Memcached' => array( 
        'driver' => 'memcached',
        'servers' => array(
            'server1' => array(
                'host' => 'localhost',
                'port' => '11211',
                'weight' => 0
            )
        )
    ),
    'Redis' => array(
        'driver' => 'redis',
        'schema' => 'tcp',
        'host' => 'localhost',
        'port' => '6379'
    )
);