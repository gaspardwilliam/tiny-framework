<?php
namespace William\Model;

class ImageManager extends Model
{
    private $images_table = 'alto_images';

    public function insert($images,$post_id)
    {
        $stmt = $this->db->prepare("INSERT INTO $this->images_table (image_key,image_name,post_id) VALUES (:key,:name,:post_id)");
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':post_id', $post_id);

        $post_id = $post_id;
        // insertion d'une ligne
        foreach($images as $image ){
            $name = $image['name'];
            $key = $image['key'];
            

            $stmt->execute();
        }
        
        
    }

     public function fetchImages($post_id)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->images_table WHERE post_id=?");
        if ($stmt->execute(array($post_id))) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }
/*
    public function fetchID($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->category_table  where cat_id = ?");
        if ($stmt->execute(array($id))) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

    }

    public function update($cat)
    {
        $stmt = $this->db->prepare("UPDATE $this->category_table SET cat_name=:name WHERE cat_id=:id");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":id", $id);

        $name = $cat->name();
        $id = $cat->id();
        $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM $this->category_table WHERE cat_id=$id";
        $this->db->exec($sql);
    } */
}