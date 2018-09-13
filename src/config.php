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
        'singular_name'=>'post',
        'plural_name'=>'posts'
    ),
    array(
        'slug'=>'astuces',
        'singular_name'=>'astuce',
        'plural_name'=>'astuces'
    ),
     array(
        'slug'=>'recettes',
        'singular_name'=>'recette',
        'plural_name'=>'recettes'
    ),    
     array(
        'slug'=>'jeux',
        'singular_name'=>'jeu',
        'plural_name'=>'jeux'
    ),

);