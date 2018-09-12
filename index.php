<?php
require 'vendor/autoload.php';
require 'vendor/altorouter/altorouter/AltoRouter.php';
require 'src/config.php';
require 'src/functions.php';

use William\Controller\DefaultController;
session_start();
//TWIG
$loader = new Twig_Loader_Filesystem('src/view/');
$twig = new Twig_Environment($loader, array(
    'cache' => 'src/cache/twig',
    'debug' => true
));
$twig->addGlobal('home', SITE_URL);
$twig->addGlobal('assets', ASSETS);



//ALTOROUTER
$router = new AltoRouter();
$router->setBasePath('/altorouter');

// map homepage
$router->map('GET', '/', function() {
	$ctrl=new DefaultController;//appelle Defaultcontroller dans src/controller
    $ctrl->index();//appelle la methode index() de la class DefaultController
    
},'home');

//liste des posts
$router->map('GET', '/posts', function() {
	$ctrl=new DefaultController;
    $ctrl->posts('post');
},'posts');

//liste des posts
$router->map('GET', '/astuces', function() {
	$ctrl=new DefaultController;
    $ctrl->posts('astuce');
},'astuces');


/*//liste des posts
$router->map('GET', '/[a:type]', function($type) { 
	$ctrl=new DefaultController;
    $ctrl->posts($type);
},'archive');*/




//affiche un post
$router->map('GET', '/post/[i:id]', function($id) {    
	$ctrl=new DefaultController;
    $ctrl->postid($id);
},'post');


//page de connexion
$router->map('GET|POST', '/connect', function() {
	$ctrl=new DefaultController;
    $ctrl->connect();
},'connect');

//page de déconnexion
$router->map('GET|POST', '/deco', function() {
	$ctrl=new DefaultController;
    $ctrl->deconnect();
},'deconnect');

//page admin
$router->map('GET', '/admin', function() {
	$ctrl=new DefaultController;
    $ctrl->admin();
},'admin');



//page admin création d'un post
$router->map('GET|POST', '/admin/create', function() {
	$ctrl=new DefaultController;
    $ctrl->admin_create();
},'admin_create');

//page admin
$router->map('GET', '/admin/[a:type]', function($type) {
	$ctrl=new DefaultController;
    $ctrl->admin_type($type);
},'admin_type');


//page admin editer un post
$router->map('GET|POST', '/admin/[:id]/edit', function($id) {
	$ctrl=new DefaultController;
    $ctrl->admin_update($id);
},'admin_update');

//supprime un post
$router->map('GET|POST', '/admin/[:id]/delete', function($id) {
	$ctrl=new DefaultController;
    $ctrl->admin_delete($id);
},'admin_delete');



$match = $router->match();




// call closure or throw 404 status
if( $match && is_callable( $match['target'] ) ) {
    //route matched and call the function
    if(preg_match('/^admin/', $match['name'])&&(empty($_SESSION['user'])||$_SESSION['user']['user_role']!='admin')){
        $url= $router->generate( 'connect');
        header("Location: $url");
    }else{
      call_user_func_array( $match['target'], $match['params'] );
    }
	
} else {
	// no route was matched
	header( $_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo $twig->render('page/404.html.twig', array('title' => '404'));
}
