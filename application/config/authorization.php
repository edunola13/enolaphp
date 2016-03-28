<?php

$config['modules'] = array(
    'front' => array('url' => '/*', 'method' => '*')
);

$config['profiles'] = array(
    'default' => array(
        'permit' => array(
            'front' => '/*'
        ),
        'deny' => array(),
        'error' => 'login'
    )
);