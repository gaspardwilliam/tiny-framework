<?php
//DB variables
define('SERVERNAME', 'localhost');
define('DBNAME', 'alto');
define('USERNAME', 'root');
define('PASSWORD', 'root');

define('SITE_URL', '/altorouter/');
define('ASSETS', SITE_URL.'public/');


//post type
$posts_type=array(
    array(
        'slug'=>'posts',
        'name'=>'post'
    ),
    array(
        'slug'=>'astuces',
        'name'=>'astuce'
    ),

);