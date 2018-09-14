<?php
namespace William\Model;

class CategoryManager extends Model
{
    private $category_table = 'alto_categories';

    public function create($cat)
    {
        $stmt = $this->db->prepare("INSERT INTO $this->category_table (cat_name) VALUES (:name)");
        $stmt->bindParam(':name', $name);

        // insertion d'une ligne
        $name = $cat->name();
        $stmt->execute();
    }

    public function fetchAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->category_table ");
        if ($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

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
        $sql = "DELETE FROM $this->category_table WHERE post_id=$id";

        // use exec() because no results are returned
        $this->db->exec($sql);
    }
}
