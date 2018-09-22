<?php
namespace William\Controller;

use Aptoma\Twig\Extension\MarkdownEngine;
use Aptoma\Twig\Extension\MarkdownExtension;
use William\Model\CategoryManager;
use William\Model\ImageManager;
use William\Model\PostManager;
use William\Model\PostMetaManager;
use William\Model\UserManager;

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
            $data = $postmanager->fetchAll($this->posts_type[$key]['singular_name']); //récupérer les posts du type

            if ($data) {
                foreach ($data as $post) {
                    $ids[] = $post['post_id']; //récupère les ids pour récupérer les images et postmeta correspondantes
                }

                $imagemanager = new ImageManager;
                $images = $imagemanager->fetchImages($ids); //récupère un tableau avec images des posts
                $metamanager = new PostMetaManager;
                $metas = $metamanager->fetchMetas($ids); //récupère les metas

                if (file_exists('src/view/page/archive-' . $slug . '.html.twig')) {
                    $name = '-' . $slug;
                } else {
                    $name = '';
                }
                echo $this->twig->render('page/archive' . $name . '.html.twig', array('title' => $slug, 'posts' => $data, 'images' => $images, 'metas' => $metas));
            } else {
                echo $this->twig->render('page/404.html.twig', array('title' => 'Aucun articles dans cette section pour le moment'));
            }
        } else {
            $this->postslug('page', $slug);
        }
    }

    public function postslug($type, $slug) //affiche un post par son slug avec son post_type

    {
        $key = array_search($type, array_column($this->posts_type, 'singular_name'));
        if (!empty($key) | $key === 0) {
            $postmanager = new PostManager;
            $data = $postmanager->fetchSlug($type, $slug);
            if ($data) {
                $imagemanager = new ImageManager;
                $image = $imagemanager->fetchImages(array($data['post_id']));
                $metamanager = new PostMetaManager;
                $metas = $metamanager->fetchMetas(array($data['post_id']));

                if ($data['post_type'] == "page") {
                    if (file_exists('src/view/page/page-' . $data['post_slug'] . '.html.twig')) {
                        $name = 'page-' . $data['post_slug'];
                    } else {
                        $name = 'page';
                    }
                } elseif (file_exists('src/view/page/single-' . $this->posts_type[$key]['slug'] . '.html.twig')) {
                    $name = 'single-' . $this->posts_type[$key]['slug'];
                } else {
                    $name = "single";
                }
                echo $this->twig->render('page/' . $name . '.html.twig', array('title' => $type, 'post' => $data, 'images' => $image, 'metas' => $metas));
            } else {
                echo $this->twig->render('page/404.html.twig', array('title' => '404 - l\'article n\'existe pas'));
            }
        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => '404 - cette section n\'existe pas !'));
        }
    }

    public function connect() //affiche le formulaire de connection admin

    {
        $user = new UserController;
        $error = array();
       
        if (!empty($_POST['email']) && !empty($_POST['password'])) {
           
            $user->setEmail($_POST['email']);
            $user->setPassword($_POST['password']);
            $usermanager = new UserManager;
            $data = $usermanager->fetchEmail($user->email());
            if ($data) { //si l'email existe
                if (password_verify($user->password(), $data['user_password'])) { //si le mot de passe correspond
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
            echo $this->twig->render('admin/admin-type.html.twig', array('title' => 'administration ' . $slug, 'posts' => $data, 'post_type' => $this->posts_type[$key]['singular_name']));
        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => '404 - ce post type n\'existe pas'));
        }
    }

    public function admin_post_create($post_type) //affiche le formulaire de création d'un post

    {
        $key = array_search($post_type, array_column($this->posts_type, 'singular_name'));
        if ($key | $key === 0) {
            $postctrl = new PostController;
            $catmanager = new CategoryManager;
            $categories = $catmanager->fetchAll();
            $error = "";
            if ($_POST) {

                if (!empty($_POST['title']) && !empty($_POST['content'])) {                    
                    $postctrl->setType($post_type);
                    if (empty($_POST['cat_id'])) {
                        $postctrl->setCat_id(0);
                    }

                    $error = $postctrl->create($_POST);/////////////////////////////////////////
                    if (empty($error)) {
                        $url = $this->router->generate('admin_type', array('type' => $this->posts_type[$key]['slug']));
                        header("Location: $url");
                    }

                } else {
                    $postctrl->setTitle($_POST['title']);
                    $postctrl->setContent($_POST['content']);
                }
            }
            if (file_exists('src/view/admin/admin-' . $post_type . '-form.html.twig')) {
                $pt = $post_type;
            } else {
                $pt = 'post';
            }
            echo $this->twig->render('admin/admin-' . $pt . '-form.html.twig', array('title' => 'créer un/une ' . $post_type, 'post' => $postctrl, 'categories' => $categories, 'error' => $error));
        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => '404 - ce post type n\'existe pas'));
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

            $imagemanager = new ImageManager;
            $images = $imagemanager->fetchImages(array($data['post_id']));
            $metamanager = new PostMetaManager;
            $metas = $metamanager->fetchMetas(array($data['post_id']));
            $error = "";
            if ($_POST) {
                /* $post->hydrate($_POST); */
                if (!empty($_POST['title']) && !empty($_POST['content'])) {
                    $error = $post->update($_POST);///////////////

                    if (empty($error)) {
                        $key = array_search($data['post_type'], array_column($this->posts_type, 'singular_name'));
                        $url = $this->router->generate('admin_type', array('type' => $this->posts_type[$key]['slug']));
                        header("Location: $url");
                    }
                }
            }
            echo $this->twig->render('admin/admin-post-form.html.twig', array('title' => 'modifier', 'post' => $post, 'metas' => $metas, 'categories' => $categories, 'images' => $images, 'error' => $error));
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
        echo $this->twig->render('admin/admin-categories.html.twig', array('title' => 'Catégories', 'categories' => $data));
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
        echo $this->twig->render('admin/admin-category-form.html.twig', array('title' => 'créer une catégorie'));
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
            echo $this->twig->render('admin/admin-category-form.html.twig', array('title' => 'modifier', 'cat' => $cat));
        } else {
            echo $this->twig->render('page/404.html.twig', array('title' => '404'));
        }
    }
    public function admin_category_delete($id) //supprime un post par son id

    {
        $catmanager = new CategoryManager;
        $data = $catmanager->delete($id);
        $url = $this->router->generate('admin_category');
        header("Location: $url");
    }

}
