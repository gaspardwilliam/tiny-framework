<?php
require 'vendor/autoload.php';
require 'vendor/altorouter/altorouter/AltoRouter.php';
require 'src/config.php';
require 'src/functions.php';

use William\Model\ImageManager;

use William\Controller\DefaultController;
session_start();
//TWIG
$loader = new Twig_Loader_Filesystem('src/view/');
$twig = new Twig_Environment($loader, array(
    'cache' => 'src/cache/twig',
    'debug' => true,
));
$twig->addGlobal('home', SITE_URL);
$twig->addGlobal('site_name', SITE_NAME);
$twig->addGlobal('assets', ASSETS);
$twig->addGlobal('posts_type', $posts_type);
//$twig->addGlobal('image', new ImageManager());

$filter = new Twig_Filter('pre', function ($string) {
    return pre($string);
});

$twig->addFilter($filter);



//ALTOROUTER
$router = new AltoRouter();
$router->setBasePath('/tiny_framework');

//  homepage
$router->map('GET', '/', function () {
    $ctrl = new DefaultController; //appelle Defaultcontroller dans src/controller
    $ctrl->index(); //appelle la methode index() de la class DefaultController

}, 'home');

//page de connexion
$router->map('GET|POST', '/connect', function () {
    $ctrl = new DefaultController;
    $ctrl->connect();
}, 'connect');

//page de déconnexion
$router->map('GET|POST', '/deco', function () {
    $ctrl = new DefaultController;
    $ctrl->deconnect();
}, 'deconnect');

//page admin
$router->map('GET', '/admin', function () {
    $ctrl = new DefaultController;
    $ctrl->admin();
}, 'admin');

//liste des posts d'un type de contenu
$router->map('GET', '/[a:type]', function ($type) {
    $ctrl = new DefaultController;
    $ctrl->posts($type);
}, 'posts');

//affiche un post
/* $router->map('GET', '/post/[i:id]', function ($id) {
    $ctrl = new DefaultController;
    $ctrl->postid($id);
}, 'post'); */



//page admin création d'une categorie
$router->map('GET|POST', '/admin/category/create', function () {
    $ctrl = new DefaultController;
    $ctrl->admin_category_create();
}, 'admin_category_create');

//page admin update d'un post
$router->map('GET|POST', '/admin/category/[i:id]', function ($id) {
    $ctrl = new DefaultController;
    $ctrl->admin_category_update($id);
}, 'admin_category_update');

//page admin création d'un post
$router->map('GET|POST', '/admin/[a:type]/create', function ($type) {
    $ctrl = new DefaultController;
    $ctrl->admin_post_create($type);
}, 'admin_create');

//page admin voir les catégories
$router->map('GET', '/admin/categories', function () {
    $ctrl = new DefaultController;
    $ctrl->admin_categories();
}, 'admin_category');

//page admin
$router->map('GET', '/admin/[a:type]', function ($type) {
    $ctrl = new DefaultController;
    $ctrl->admin_type($type);
}, 'admin_type');

//page admin editer un post
$router->map('GET|POST', '/admin/[:id]/edit', function ($id) {
    $ctrl = new DefaultController;
    $ctrl->admin_post_update($id);
}, 'admin_update');

//supprime un post
$router->map('GET|POST', '/admin/post/[:id]/delete', function ($id) {
    $ctrl = new DefaultController;
    $ctrl->admin_post_delete($id);
}, 'admin_delete');

//supprime une categorie
$router->map('GET|POST', '/admin/category/[:id]/delete', function ($id) {
    $ctrl = new DefaultController;
    $ctrl->admin_category_delete($id);
}, 'admin_cat_delete');

$router->map('GET', '/[a:type]/[:slug]', function ($type,$slug) {
    $ctrl = new DefaultController;
    $ctrl->postslug($type,$slug);
}, 'post');

$router->map('GET', '/[:slug]', function ($slug) {
    $ctrl = new DefaultController;
    $ctrl->postslug('page',$slug);
}, 'page');

$twig->addGlobal('router', $router);
$match = $router->match();

// call closure or throw 404 status
if ($match && is_callable($match['target'])) {
    //route matched and call the function
    if (preg_match('/^admin/', $match['name']) && (empty($_SESSION['user']) || $_SESSION['user']['user_role'] != 'admin')) {
        $url = $router->generate('connect');
        header("Location: $url");
    } else {
        call_user_func_array($match['target'], $match['params']);
    }

} else {
    // no route was matched
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo $twig->render('page/404.html.twig', array('title' => '404-no route match'));
}
