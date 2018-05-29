<?php

//配置文件

return [
    //websocket配置
    'ws' => [
        'host' => 'ws://yourdomin.com|ip',
        'port' => 80,
    ],
    
    'mysql' => [
        'database_type' => 'mysql',
        'database_name' => 'database_name',
        'server' => 'localhost',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8'
    ],
    
    'redis' => [
		'port' => 6379
    ],

    
];