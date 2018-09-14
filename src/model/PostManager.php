<?php
namespace William\Model;

class PostManager extends Model
{
    private $posts_table = 'alto_posts';
    private $categories_table = 'alto_categories';

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
        $slug = slugify($post->title());
        $cat = $post->cat_id();
        $created = $post->created();
        $type = $post->type();
        $updated = $created;
        $stmt->execute();
    }

    public function fetchAll($type)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->posts_table LEFT JOIN $this->categories_table ON alto_posts.cat_id = alto_categories.cat_id WHERE $this->posts_table.post_type=? ORDER BY post_id DESC");
        if ($stmt->execute(array($type))) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function fetchID($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->posts_table LEFT JOIN $this->categories_table ON alto_posts.cat_id = alto_categories.cat_id where post_id = ?");
        if ($stmt->execute(array($id))) {
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
    }

    public function delete($id)
    {
        $sql = "DELETE FROM alto_posts WHERE post_id=$id";

        // use exec() because no results are returned
        $this->db->exec($sql);
    }
}
