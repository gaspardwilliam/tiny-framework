<?php
namespace William\Controller;

use Aptoma\Twig\Extension\MarkdownEngine;
use Aptoma\Twig\Extension\MarkdownExtension;
use William\Model\CategoryManager;
use William\Model\ImageManager;
use William\Model\PostManager;
use William\Model\userManager;

require_once 'vendor/verot/class.upload.php/src/class.upload.php';

class DefaultController
{
    private $twig;
    private $router;
    private $posts_type;

    public function __construct()
    {
        global $twig;
        global $router;
        global $posts_type;
        $this->twig = $twig;
        $this->router = $router;
        $this->posts_type = $posts_type;

        $engine = new MarkdownEngine\ParsedownEngine();

        $this->twig->addExtension(new MarkdownExtension($engine));
    }
    public function index() //affiche la page d'accueil

    {
        echo $this->twig->render('page/index.html.twig', array('title' => 'Accueil'));
    }

    public function posts($slug) //affiche la page des posts d'un type de post

    {
        $key = array_search($slug, array_column($this->posts_type, 'slug')); //récupere l'index du type de post si il existe
        if (!empty($key) | $key === 0) { //si le type de post existe dans le tableau
            $postmanager = new PostManager;
            $data = $postmanager->fetchAll($this->posts_type[$key]['singular_name']);

            foreach ($data as $post) {
                $ids[] = $post['post_id'];
            }

            $imagemanager = new ImageManager;
            $images = $imagemanager->fetchImages($ids);

            if (file_exists('src/view/page/archive-' . $slug . '.html.twig')) {
                echo $this->twig->render('page/archive-' . $slug . '.html.twig', array('title' => $slug, 'posts' => $data, 'images' => $images));
            } else {
                echo $this->twig->render('page/archive.html.twig', array('title' => $slug, 'posts' => $data, 'images' => $images));
            }

        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => '404'));
        }
    }

    /*  public function postid($id)

    {
    $postmanager = new PostManager;
    $data = $postmanager->fetchID($id);
    if ($data) {
    echo $this->twig->render('page/single.html.twig', array('title' => 'post', 'post' => $data));
    } else {
    echo $this->twig->render('page/404.html.twig', array('title' => '404 - l\'article n\'existe pas'));
    }

    } */

    public function postslug($type, $slug) //affiche un post par son slug avec son post_type

    {
        $key = array_search($type, array_column($this->posts_type, 'singular_name'));
        if (!empty($key) | $key === 0) {
            $postmanager = new PostManager;
            $data = $postmanager->fetchSlug($type, $slug);
            if ($data) {
                $imagemanager = new ImageManager;
                $image = $imagemanager->fetchImages(array($data['post_id']));
                if ($data['post_type'] == "page") {
                    if (file_exists('src/view/page/page-' . $data['post_slug'] . '.html.twig')) {
                        echo $this->twig->render('page/page-' . $data['post_slug'] . '.html.twig', array('title' => 'page', 'post' => $data, 'images' => $image));
                    } else {
                        echo $this->twig->render('page/page.html.twig', array('title' => 'page', 'post' => $datas, 'images' => $image));
                    }
                } elseif (file_exists('src/view/page/single-' . $data['post_type'] . '.html.twig')) {
                    echo $this->twig->render('page/single-' . $data['post_type'] . '.html.twig', array('title' => 'single-' . $type, 'post' => $data, 'images' => $image));
                } else {
                    echo $this->twig->render('page/single.html.twig', array('title' => 'single', 'post' => $data, 'images' => $image));
                }

            } else {
                echo $this->twig->render('page/404.html.twig', array('title' => '404 - l\'article n\'existe pas'));
            }
        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => '404'));
        }
    }

    public function connect() //affiche le formulaire de connection admin

    {
        $error = array();
        $user = new UserController;
        if (!empty($_POST['email']) && !empty($_POST['password'])) {

            $user->setEmail($_POST['email']);
            $user->setPassword($_POST['password']);
            $usermanager = new UserManager;
            $data = $usermanager->fetchEmail($user->email());
            if ($data) {
                if (password_verify($user->password(), $data['user_password'])) {
                    $_SESSION['user'] = $data;
                    $url = $this->router->generate('admin');
                    header("Location: $url");

                } else {
                    $error['login'] = 'Le mot de passe et/ou le pseudo sont/est incorrecte';
                }
            } else { $error['login'] = "Le mot de passe et/ou le pseudo sont/est incorrecte";}
        }
        echo $this->twig->render('page/connect.html.twig', array('title' => 'connexion', 'error' => $error, 'email' => $user->email()));
    }

    public function deconnect() //se deconnecter

    {
        session_destroy();
        $url = $this->router->generate('home');
        header("Location: $url");
    }

    public function admin() //affiche l'accueil du panneau d'administration

    {

        echo $this->twig->render('admin/admin.html.twig', array('title' => 'administration'));
    }

    public function admin_type($slug) //affiche les post_types dans l'admin

    {
        $key = array_search($slug, array_column($this->posts_type, 'slug'));

        if (!empty($key) | $key === 0) {
            $postmanager = new PostManager;
            $data = $postmanager->fetchAll($this->posts_type[$key]['singular_name']);
            echo $this->twig->render('admin/admin_type.html.twig', array('title' => 'administration ' . $slug, 'posts' => $data, 'post_type' => $this->posts_type[$key]['singular_name']));
        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => '404'));
        }
    }

    public function admin_post_create($post_type) //affiche le formulaire de création d'un post

    {

        $key = array_search($post_type, array_column($this->posts_type, 'singular_name'));
        if ($key | $key === 0) {
            $post = new PostController;
            $catmanager = new CategoryManager;
            $categories = $catmanager->fetchAll();
            if ($_POST) {
                $post->hydrate($_POST);
                $post->setCreated(date("Y-m-d H:i:s"));
                $post->setType($post_type);
                //pre($_POST)  ;
                //pre($post)  ;
                if (!empty($_POST['title']) && !empty($_POST['content'])) {

                    $postmanager = new postManager;
                    $postmanager->create($post);

                    $url = $this->router->generate('admin_type', array('type' => $this->posts_type[$key]['slug']));
                    header("Location: $url");
                }
            }

            echo $this->twig->render('admin/admin_post_form.html.twig', array('title' => 'créer un/une ' . $post_type, 'post' => $post, 'categories' => $categories, 'tips' => $this->posts_type));
        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => '404'));
        }

    }

    public function admin_post_update($id) //affiche le formulaire de mise à jour d'un post

    {
        $post = new PostController;
        $postmanager = new PostManager;
        $catmanager = new CategoryManager;
        $categories = $catmanager->fetchAll();
        $data = $postmanager->fetchID($id);
        if ($data) {
            $post->setTitle($data['post_title']);
            $post->setID($data['post_id']);
            $post->setCat_id($data['cat_id']);
            $post->setContent($data['post_content']);
            $post->setType($data['post_type']);
            $post->setUpdated(date("Y-m-d H:i:s"));

            if ($_POST) {
                $post->hydrate($_POST);
                if (!empty($_POST['title']) && !empty($_POST['content'])) {
                    $postmanager = new postManager;
                    $postmanager->update($post);

                    
                    $key = array_search($data['post_type'], array_column($this->posts_type, 'singular_name'));
                    $url = $this->router->generate('admin_type', array('type' => $this->posts_type[$key]['slug']));
                    //header("Location: $url");
                }
            }
            echo $this->twig->render('admin/admin_post_form.html.twig', array('title' => 'modifier', 'post' => $post, 'categories' => $categories, 'tips' => $this->posts_type));
        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => 'user', 'post' => $post));
        }

    }

    public function admin_post_delete($id) //supprime un post par son id

    {
        $postmanager = new PostManager;
        $data = $postmanager->delete($id);

        $url = $this->router->generate('admin');
        header("Location: $url");
    }

    public function admin_categories() //affiche les catégories dans l'admin

    {
        $catmanager = new CategoryManager;
        $data = $catmanager->fetchAll();
        echo $this->twig->render('admin/admin_categories.html.twig', array('title' => 'Catégories', 'categories' => $data));
    }

    public function admin_category_create() //affiche le formulaire de création d'une catégorie

    {

        $category = new CategoryController;
        if ($_POST) {
            $category->hydrate($_POST);
            if (!empty($_POST['name'])) {
                $catmanager = new CategoryManager;
                $catmanager->create($category);
                $url = $this->router->generate('admin_category');
                header("Location: $url");
            }
        }

        echo $this->twig->render('admin/admin_category_form.html.twig', array('title' => 'créer une catégorie'));

    }

    public function admin_category_update($id) //affiche le formulaire de mise à jour d'une catégorie

    {
        $cat = new CategoryController;
        $catmanager = new CategoryManager;
        $data = $catmanager->fetchID($id);
        if ($data) {
            $cat->setName($data['cat_name']);
            $cat->setID($data['cat_id']);

            if ($_POST) {
                $cat->hydrate($_POST);
                if (!empty($_POST['name'])) {
                    $catmanager->update($cat);
                    $url = $this->router->generate('admin_category');
                    header("Location: $url");
                }
            }
            echo $this->twig->render('admin/admin_category_form.html.twig', array('title' => 'modifier', 'cat' => $cat));
        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => '404'));
        }

    }

}
