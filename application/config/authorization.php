<?php
$config['session-profile'] = 'user_logged';
$config['actual-authorization'] = 'Local';

$config['Local']= array(
    'authorization-type' => 'file',

    'modules' => array(
        'front' => array(
            array('url' => '/*', 'method' => '*')
        )
    ),

    'profiles' => array(
        'default' => array(
            'permit' => array(
                'front'
            ),
            'deny' => array(),
            'error' => 'login'
        )
    )
);

$config['Servidor']= array(
    'authorization-type' => 'database',
    'connection' => 'Authorization',
    
    'tables' => array(
        'user' => 'user',
        'user-profile' => 'user_profile',
        'profile' => 'profile',
        'profile-permit' => 'profile_permit',
        'profile-deny' => 'profile_deny',
        'module' => 'module',
        'key' => 'modulekey'
    )
);