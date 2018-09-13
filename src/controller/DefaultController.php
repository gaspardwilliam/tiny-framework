<?php
namespace William\Controller;

use William\Controller\PostController as Post;
use William\Controller\CategoryController as Category;
use William\Controller\UserController as user;
use William\Model\PostManager;
use William\Model\CategoryManager;
use William\Model\userManager;


class DefaultController
{
    private $twig;
    private $router;
    private $posts_type;
    
    public function __construct(){
        global $twig;
        global $router;
        global $posts_type;
        $this->twig = $twig;
        $this->router = $router;
        $this->posts_type = $posts_type;
    }
    public function index()
    {   
        echo $this->twig->render('page/index.html.twig', array('title' => 'Accueil'));//appelle la vue twig
    }
    
    public function posts($slug)
    { 
    $key = array_search($slug, array_column($this->posts_type, 'slug'));
    if(!empty($key)|$key===0){
       $postmod=new PostManager;
       $data=$postmod->fetchAll($this->posts_type[$key]['singular_name']);
        echo $this->twig->render('page/posts.html.twig', array('title' => $slug,'posts'=>$data));
    }else{
        echo $this->twig->render('page/404.html.twig', array('title' => '404'));
    }
    }
    
    public function postid($id)
    {   
    $postmod=new PostManager;
    $data=$postmod->fetchID($id); 
    if($data){
        echo $this->twig->render('page/post.html.twig', array('title' => 'post','post'=>$data));
    }else{
        echo $this->twig->render('page/404.html.twig', array('title' => '404 - l\'article n\'existe pas'));
    }
    
     
    }
    
    public function admin()
    {        
    $postmod=new PostManager;
    $data=$postmod->fetchAll('post');
     echo $this->twig->render('page/admin.html.twig', array('title' => 'administration'));
    }
    
    public function admin_type($slug)
    { 
    $key = array_search($slug, array_column($this->posts_type, 'slug'));
    if(!empty($key)|$key===0){
       $postmod=new PostManager;
       $data=$postmod->fetchAll($this->posts_type[$key]['singular_name']);
       echo $this->twig->render('page/admin_type.html.twig', array('title' => 'administration '.$slug,'posts'=>$data));
    }else{
        echo $this->twig->render('page/404.html.twig', array('title' => '404'));
    } 
    }
    
    
    
    
    public function connect()
    {        
    $error=array();
    $user=new UserController;
     if(!empty($_POST['email'])&&!empty($_POST['password'])){
        
        $user->setEmail($_POST['email']);
        $user->setPassword($_POST['password']);
        $usermanager=new UserManager;
        $data=$usermanager->fetchEmail($user->email());
        if($data){
           if (password_verify($user->password(), $data['user_password'])) {
                $_SESSION['user']=$data;
                $url= $this->router->generate( 'admin');
                header("Location: $url");
               
            }else {
                $error['login']='Le mot de passe et/ou le pseudo sont/est incorrecte';
            }
        }else{$error['login']="Le mot de passe et/ou le pseudo sont/est incorrecte";}
     }
     echo $this->twig->render('page/connect.html.twig', array('title' => 'connexion','error'=>$error,'email'=>$user->email()));
    }
    
    public function deconnect()
    {        
        session_destroy();
        $url= $this->router->generate( 'home');
        header("Location: $url");
    }
    
    public function admin_create()
    {   
    $post=new PostController;    
    $catmanager=new CategoryManager;  
    $categories=$catmanager->fetchAll();      
    if($_POST){     
        $post->hydrate($_POST);
        $post->setCreated(date("Y-m-d H:i:s"));         
         //pre($_POST)  ;
         //pre($post)  ;
        if(!empty($_POST['title'])&&!empty($_POST['content'])){      
            
            $postmanager=new postManager;
            $postmanager->create($post);
            $url= $this->router->generate( 'admin');
            header("Location: $url");
        }
    }
        
    
     echo $this->twig->render('page/admin_create.html.twig', array('title' => 'créer un post','post'=>$post,'categories'=>$categories,'tips'=>$this->posts_type));
    }
    
    public function admin_update($id)
    {
    $post=new PostController;
    $postmanager=new PostManager;
    $catmanager=new CategoryManager;  
    $categories=$catmanager->fetchAll();  
    $data=$postmanager->fetchID($id);
    if($data){
        $post->setTitle($data['post_title']);
        $post->setID($data['post_id']);
        $post->setCat_id($data['cat_id']);
        $post->setContent($data['post_content']);
        $post->setType($data['post_type']);
        $post->setUpdated(date("Y-m-d H:i:s"));
        
        if($_POST){
            $post->hydrate($_POST);           
            if(!empty($_POST['title'])&&!empty($_POST['content'])){
                $postmanager=new postManager;
                $postmanager->update($post);
                $url= $this->router->generate( 'admin');
                header("Location: $url");
            }
        }   
         echo $this->twig->render('page/admin_create.html.twig', array('title' => 'modifier','post'=>$post,'categories'=>$categories,'tips'=>$this->posts_type)); 
        }else{
            echo $this->twig->render('page/404.html.twig', array('title' => 'user','post'=>$post)); 
        }
    
    }
    
    public function admin_delete($id){
        $postmanager=new PostManager;
        $data=$postmanager->delete($id);
        $url= $this->router->generate( 'admin');
        header("Location: $url");
    }
}