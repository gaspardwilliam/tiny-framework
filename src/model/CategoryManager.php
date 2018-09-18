<?php
namespace William\Model;

class CategoryManager extends Model
{

    public function create($cat)
    {
        $stmt = $this->db->prepare("INSERT INTO $this->categories_table (cat_name) VALUES (:name)");
        $stmt->bindParam(':name', $name);

        // insertion d'une ligne
        $name = $cat->name();
        $stmt->execute();
    }

    public function fetchAll()
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->categories_table ORDER BY cat_id DESC");
        if ($stmt->execute()) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
    }

    public function fetchID($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM $this->categories_table  where cat_id = ?");
        if ($stmt->execute(array($id))) {
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

    }

    public function update($cat)
    {
        $stmt = $this->db->prepare("UPDATE $this->categories_table SET cat_name=:name WHERE cat_id=:id");
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":id", $id);

        $name = $cat->name();
        $id = $cat->id();
        $stmt->execute();
    }

    public function delete($id)
    {
        $sql = "DELETE FROM $this->categories_table WHERE cat_id=$id";
        $this->db->exec($sql);
    }
}
