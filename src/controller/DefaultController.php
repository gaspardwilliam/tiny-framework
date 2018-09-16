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
        $key = array_search($slug, array_column($this->posts_type, 'slug'));
        if (!empty($key) | $key === 0) {
            $postmanager = new PostManager;
            $data = $postmanager->fetchAll($this->posts_type[$key]['singular_name']);
            if (file_exists('src/view/page/archive-' . $slug . '.html.twig')) {
                echo $this->twig->render('page/archive-' . $slug . '.html.twig', array('title' => $slug, 'posts' => $data));
            } else {
                echo $this->twig->render('page/archive.html.twig', array('title' => $slug, 'posts' => $data));
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

                if ($data['post_type'] == "page") {
                    if (file_exists('src/view/page/page-' . $data['post_slug'] . '.html.twig')) {
                        echo $this->twig->render('page/page-' . $data['post_slug'] . '.html.twig', array('title' => 'page', 'post' => $data));
                    } else {
                        echo $this->twig->render('page/page.html.twig', array('title' => 'page', 'post' => $datas));
                    }

                } elseif (file_exists('src/view/page/single-' . $data['post_type'] . '.html.twig')) {
                    echo $this->twig->render('page/single-' . $data['post_type'] . '.html.twig', array('title' => 'single-' . $type, 'post' => $data));
                } else {
                    echo $this->twig->render('page/single.html.twig', array('title' => 'single', 'post' => $data));
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

    public function deconnect() //se deconnecté

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
                    $post_id = $postmanager->create($post);

                    $this->image($post_id, 'insert');

                    $url = $this->router->generate('admin_type',array('type'=>$this->posts_type[$key]['slug']));
                    header("Location: $url");
                }
            }

            echo $this->twig->render('admin/admin_post_form.html.twig', array('title' => 'créer un/une '.$post_type, 'post' => $post, 'categories' => $categories, 'tips' => $this->posts_type));
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

                    $this->image($id, 'update');
                    $key = array_search($data['post_type'], array_column($this->posts_type, 'singular_name'));
                    $url = $this->router->generate('admin_type',array('type'=>$this->posts_type[$key]['slug']));
                    header("Location: $url");
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

    public function admin_category_delete($id) //supprime un post par son id

    {
        $catmanager = new CategoryManager;
        $data = $catmanager->delete($id);
        $url = $this->router->generate('admin_category');
        header("Location: $url");
    }

    private function image($post_id, $method)
    {
        $image_array = array();
        $handle = new \Upload($_FILES['image']);
        // then we check if the file has been uploaded properly
        // in its *temporary* location in the server (often, it is /tmp)
        if ($handle->uploaded) {
            // yes, the file is on the server
            // now, we start the upload 'process'. That is, to copy the uploaded file
            // from its temporary location to the wanted location
            // It could be something like $handle->Process('/home/www/my_uploads/');
            if ($method == "update") {
                $deleteimg=new Postmanager;
                $deleteimg->delete_img($post_id);
            }

            $handle->allowed = array('image/*');
            $handle->Process('public/img/uploaded_img/');
            array_push($image_array, array('key' => 'original',
                'name' => $handle->file_dst_name));

            $handle->image_resize = true;
            $handle->image_ratio_y = true;
            $handle->image_x = 1024;
            $handle->file_name_body_add = '_large';
            $handle->Process('public/img/uploaded_img/');
            array_push($image_array, array('key' => 'large',
                'name' => $handle->file_dst_name));

            $handle->image_resize = true;
            $handle->image_ratio_y = true;
            $handle->image_x = 300;
            $handle->file_name_body_add = '_medium';
            $handle->Process('public/img/uploaded_img/');
            array_push($image_array, array('key' => 'medium',
                'name' => $handle->file_dst_name));

            $handle->image_resize = true;
            $handle->image_ratio_crop = true;
            $handle->image_ratio_y = false;
            $handle->image_y = 150;
            $handle->image_x = 150;
            $handle->file_name_body_add = '_thumbnail';
            $handle->Process('public/img/uploaded_img/');
            array_push($image_array, array('key' => 'thumbnail',
                'name' => $handle->file_dst_name));
            // we check if everything went OK
            if ($handle->processed) {
                $imagemanager = new imageManager;
                $imagemanager->$method($image_array, $post_id);
            } else {

            }
            // we delete the temporary files
            $handle->Clean();

        }
    }

}
