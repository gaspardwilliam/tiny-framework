<?php
namespace William\Model;

use William\Controller\ImageController;
use William\Model\ImageManager;

class PostManager extends Model
{

    public function create($post)
    {
        $stmt = $this->db->prepare("INSERT INTO $this->posts_table (post_title, post_content, post_slug, post_created_date, post_updated_date, cat_id, post_type) VALUES (:title, :content, :slug, :created,:updated, :cat, :type)");
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':content', $content);
        $stmt->bindParam(':slug', $slug);
        $stmt->bindParam(':created', $created);
        $stmt->bindParam(':updated', $updated);
        $stmt->bindParam(':cat', $cat);
        $stmt->bindParam(':type', $type);

        // insertion d'une ligne
        $title = $post->title();
        $content = $post->content();
        //$slug = slugify($post->title());
        $slug = generateSlug($post->title());
        $cat = $post->cat_id();
        $created = $post->created();
        $type = $post->type();
        $updated = $created;
        $stmt->execute();
        return  $this->db->lastInsertId();
        
    }

    public function fetchAll($type)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->posts_table LEFT JOIN $this->categories_table ON $this->posts_table.cat_id = $this->categories_table.cat_id WHERE $this->posts_table.post_type=? ORDER BY post_id DESC");
        if ($stmt->execute(array($type))) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
           

        }
    }

    public function fetchID($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->posts_table LEFT JOIN $this->categories_table ON $this->posts_table.cat_id = $this->categories_table.cat_id WHERE post_id = ?");
        if ($stmt->execute(array($id))) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

    }

    public function fetchSlug($type, $slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->posts_table LEFT JOIN $this->categories_table ON $this->posts_table.cat_id = $this->categories_table.cat_id  WHERE post_type=? AND post_slug = ? ");
        if ($stmt->execute(array($type, $slug))) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

    }

    public function update($post)
    {
        $stmt = $this->db->prepare("UPDATE $this->posts_table SET post_title=:title,post_content=:content ,post_updated_date=:updated, post_slug=:slug, cat_id=:cat_id, post_type=:type WHERE post_id=:id");
        $stmt->bindParam(":title", $title);
        $stmt->bindParam(":content", $content);
        $stmt->bindParam(":id", $id);
        $stmt->bindParam(":updated", $updated);
        $stmt->bindParam(":slug", $slug);
        $stmt->bindParam(":cat_id", $cat_id);
        $stmt->bindParam(":type", $type);

        $title = $post->title();
        $content = $post->content();
        $cat_id = $post->cat_id();
        $id = $post->id();
        $type = $post->type();
        $updated = $post->updated();
        $slug = slugify($post->title());
        $stmt->execute();
        /* $imgctrl = new ImageController;
        $imgctrl->image($id, 'update'); */
        

    }

    public function delete($id)
    {
        $sql = "DELETE FROM $this->posts_table WHERE post_id=$id";

        $imgmanager = new ImageManager;
        $imgmanager->delete_img_post_id($id);//supprime les fichiers images
        $sql2 = "DELETE FROM $this->images_table WHERE post_id=$id";
        $sql3 = "DELETE FROM $this->postmeta_table WHERE post_id=$id";

        // use exec() because no results are returned
        $this->db->exec($sql);

        $this->db->exec($sql2);
        $this->db->exec($sql3);

    }

}
