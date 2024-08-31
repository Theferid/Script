<?php
    
    define('PATH', realpath('.'));
    define('SUBFOLDER', false);
    define('URL', 'https://shop.socialclub.tech');
    define('DINAMICLISANCE', 'GLYCON-THAZV-KGEYP-RMYOL');
    
    ini_set('display_errors', 0);
    date_default_timezone_set('Europe/Istanbul');
    
    return [
      'db' => [
        'name'    =>  'socialcl_importdb',
        'host'    =>  'localhost',
        'user'    =>  'socialcl_importdb',
        'pass'    =>  'socialcl_importdb',
        'charset' =>  'utf8mb4' 
      ]
    ];
    
    