<?php
namespace William\Controller;

use Intervention\Image\ImageManager as InterventionImage;
use William\Model\ImageManager;

class ImageController
{
    /**
     * id de l'image
     *
     * @var int
     */
    private $id;
    /**
     * nom d l'image
     *
     * @var string
     */
    private $name;
    /**
     * format de l'image
     *
     * @var string
     */
    private $key;


    /**
     * id
     *
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * key
     *
     * @return string
     */
    public function key()
    {
        return $this->key;
    }


    /**
     * setId
     *
     * @param  int $id
     *
     * @return void
     */
    public function setId($id)
    {

        $id = (int) $id;

        if ($id > 0) {
            $this->id = $id;
        }
    }

    /**
     * setName
     *
     * @param  string $name
     *
     * @return void
     */
    public function setName($name)
    {
        if (is_string($name)) {
            $this->name = $name;
        }
    }

    /**
     * setKey
     *
     * @param  string $key
     *
     * @return void
     */
    public function setKey($key)
    {
        if (is_string($key)) {
            $this->name = $key;
        }
    }

    /**
     * hydrate l'objet image 
     *
     * @param  array $donnees
     *
     * @return void
     */
    public function hydrate($donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }

    }

    /**
     * image
     * sauvegarde l'image dans plusieurs format et les ajoute à la db
     *
     * @param  int $post_id
     * @param  string $method insert/update
     *
     * @return void
     */
    public function image($post_id, $method) 
    {
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
                ->interlace()
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
