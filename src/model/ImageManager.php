<?php
namespace William\Model;

class ImageManager extends Model
{

    public function insert($images, $post_id)
    {
        $stmt = $this->db->prepare("INSERT INTO $this->images_table (image_key,image_name,post_id) VALUES (:key,:name,:post_id)");
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':post_id', $post_id);

        $post_id = $post_id;
        // insertion d'une ligne
        foreach ($images as $image) {
            $name = $image['name'];
            $key = $image['key'];   
            $stmt->execute();         
        }

    }

    public function update($images, $post_id)
    {
        $stmt = $this->db->prepare("SELECT image_id FROM $this->images_table WHERE post_id=?");
        $stmt->execute(array($post_id));

        if ($stmt->rowCount() > 0) {
            $stmt = $this->db->prepare("UPDATE $this->images_table SET image_name=:name WHERE image_key=:key AND post_id=:post_id");
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':post_id', $post_id);

            $post_id = $post_id;
            // insertion d'une ligne
            foreach ($images as $image) {
                $name = $image['name'];
                $key = $image['key'];
                $stmt->execute();

            }
        }else{
            $this->insert($images,$post_id);
        }
    }

    public function fetchImages($post_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->images_table WHERE post_id=?");
        if ($stmt->execute(array($post_id))) {
            $images = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $img = array();
            foreach ($images as $image) {
                $img[$image['image_key']] = $image['image_name'];
            }
            return $img;
        }
    }

    public function getImage($post_id, $size)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->images_table WHERE post_id=? AND image_key=?");
        if ($stmt->execute(array($post_id, $size))) {
            $image = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($image) {
                $url = ASSETS . 'img/uploaded_img/' . $image['image_name'];
                return $url;
            }

        }
    }

}
