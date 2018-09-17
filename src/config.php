<?php
//DB variables
define('SERVERNAME', 'localhost');
define('DBNAME', 'alto');
define('USERNAME', 'root');
define('PASSWORD', 'root');

define('SITE_URL', '/altorouter/');
define('SITE_NAME', 'mon site');
define('ASSETS', SITE_URL.'public/');


//post type
$posts_type=array(
    array(
        'slug'=>'pages',
        'singular_name'=>'page',
        'plural_name'=>'pages'
    ),
    array(
        'slug'=>'posts',
        'singular_name'=>'post',
        'plural_name'=>'posts'
    )
     

);