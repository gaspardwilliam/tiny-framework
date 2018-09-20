<?php
namespace William\Controller;

use Intervention\Image\ImageManager as InterventionImage;
use William\Model\ImageManager;

class ImageController
{
    private $id;
    private $name;
    private $key;

    // Liste des getters

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return $this->name;
    }

    public function key()
    {
        return $this->key;
    }

    // Liste des setters

    public function setId($id)
    {

        $id = (int) $id;

        // On vérifie ensuite si ce nombre est bien strictement positif.
        if ($id > 0) {
            // Si c'est le cas, c'est tout bon, on assigne la valeur à l'attribut correspondant.
            $this->id = $id;
        }
    }

    public function setName($name)
    {
        // On vérifie qu'il s'agit bien d'une chaîne de caractères.
        if (is_string($name)) {
            $this->name = $name;
        }
    }

    public function setKey($key)
    {
        // On vérifie qu'il s'agit bien d'une chaîne de caractères.
        if (is_string($key)) {
            $this->name = $key;
        }
    }

    public function hydrate($donnees)
    {
        foreach ($donnees as $key => $value) {
            // On récupère le nom du setter correspondant à l'attribut.
            $method = 'set' . ucfirst($key);

            // Si le setter correspondant existe.
            if (method_exists($this, $method)) {
                // On appelle le setter.
                $this->$method($value);
            }
        }

    }

    public function image($post_id, $method) //ajoute les images sur le serveur et dans la db

    {
        /*  $image_array = array();
        $handle = new \Upload($_FILES['image']);
        // then we check if the file has been uploaded properly
        // in its *temporary* location in the server (often, it is /tmp)
        if ($handle->uploaded) {
        // yes, the file is on the server
        // now, we start the upload 'process'. That is, to copy the uploaded file
        // from its temporary location to the wanted location
        // It could be something like $handle->Process('/home/www/my_uploads/');
        $imgmanager = new ImageManager;
        if ($method == "update") {

        if (empty($imgmanager->check_img($post_id))) {
        $method = "insert";
        }
        }

        $handle->allowed = array('image/*');
        $handle->Process('public/img/uploaded_img/');
        $error="";
        if ($handle->processed) {
        array_push($image_array, array('key' => 'original',
        'name' => 'public/img/uploaded_img/' . $handle->file_dst_name));

        $handle->image_resize = true;
        $handle->image_ratio_y = true;
        $handle->image_x = 1024;
        $handle->file_name_body_add = '_large';
        $handle->Process('public/img/uploaded_img/');
        array_push($image_array, array('key' => 'large',
        'name' => 'public/img/uploaded_img/' . $handle->file_dst_name));

        $handle->image_resize = true;
        $handle->image_ratio_y = true;
        $handle->image_x = 300;
        $handle->file_name_body_add = '_medium';
        $handle->Process('public/img/uploaded_img/');
        array_push($image_array, array('key' => 'medium',
        'name' => 'public/img/uploaded_img/' . $handle->file_dst_name));

        $handle->image_resize = true;
        $handle->image_ratio_crop = true;
        $handle->image_ratio_y = false;
        $handle->image_y = 150;
        $handle->image_x = 150;
        $handle->file_name_body_add = '_thumbnail';
        $handle->Process('public/img/uploaded_img/');
        array_push($image_array, array('key' => 'thumbnail',
        'name' => 'public/img/uploaded_img/' . $handle->file_dst_name));
        // we check if everything went OK

        $imgmanager->$method($image_array, $post_id);
        $imgmanager->delete_img($post_id);

        }else{
        return 'l\'image n\'a pas pu etre envoyée sur le serveur';

        //echo'test p';
        }$handle->Clean();

        }  */
        $name = pathinfo($_FILES['image']['name'], PATHINFO_FILENAME) . '_' . $post_id . '_';
        $intervention = new InterventionImage(array('driver' => 'gd'));
        $images_sizes = [
            array('key' => 'large', 'value' => 1024, 'name' => $name . 'large.jpg'),
            array('key' => 'medium', 'value' => 600, 'name' => $name . 'medium.jpg'),
            array('key' => 'thumbnail', 'value' => 200, 'name' => $name . 'thumbnail.jpg'),
        ];
        $imgmanager = new ImageManager;

        $actual_images = $imgmanager->check_img($post_id);
        

        foreach ($images_sizes as $image) {

            $intervention->make($_FILES['image']['tmp_name'])

                ->resize($image['value'], null, function ($constraint) {$constraint->aspectRatio();})
                ->save('public/img/uploaded_img/' . $image['name'], 65);

        }
        if (empty($actual_images)) {
            $method = "insert";

        } else {
            $imgmanager->delete_img($actual_images);
        }
        $imgmanager->$method($images_sizes, $post_id);

    }

}
