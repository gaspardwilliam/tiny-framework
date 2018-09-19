<?php
namespace William\Model;

class PostMetaManager extends Model
{

    public function insert($metas, $post_id)
    {
        $stmt = $this->db->prepare("INSERT INTO $this->postmeta_table (meta_key,meta_value,post_id) VALUES (:key,:value,:post_id)");
        $stmt->bindParam(':key', $key);
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':post_id', $post_id);

        $post_id = $post_id;
        // insertion d'une ligne
        foreach ($metas as $meta) {
            $value = $meta['value'];
            $key = $meta['key'];
            $stmt->execute();
        }

    }

    public function update($metas, $post_id)
    {
        $stmt = $this->db->prepare("SELECT meta_id FROM $this->postmeta_table WHERE post_id=?");
        $stmt->execute(array($post_id));

        if ($stmt->rowCount() > 0) {
            $stmt = $this->db->prepare("UPDATE $this->postmeta_table SET meta_value=:value WHERE meta_key=:key AND post_id=:post_id");
            $stmt->bindParam(':key', $key);
            $stmt->bindParam(':value', $value);
            $stmt->bindParam(':post_id', $post_id);

            $post_id = $post_id;
            // insertion d'une ligne
            foreach ($metas as $meta) {
                $value = $meta['value'];
                $key = $meta['key'];
                $stmt->execute();

            }
        } else {
            $this->insert($metas, $post_id);
        }
    }

    public function fetchMetas($ids)
    {
        // creates a string containing ?,?,?
        $clause = implode(',', array_fill(0, count($ids), '?'));

        $stmt = $this->db->prepare("SELECT * FROM $this->postmeta_table WHERE post_id IN ($clause)");

        //call_user_func_array(array($stmt, 'bindParam'), $ids);

        if ($stmt->execute($ids)) {
            $results = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $results[$row['post_id']][$row['meta_key']] = $row['meta_value'];
            }
            return $results;
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

    public function postImage($array, $post_id, $size)
    {
        $key = array_keys($array, array("post_id" => $post_id, "image_key" => $size));

        print_r($array);
    }

    public function delete_metas($id)
    {
        $stmt = $this->db->prepare("SELECT image_name FROM $this->images_table WHERE post_id = ?");
        if ($stmt->execute(array($id))) {
            $images = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            foreach ($images as $image) {
                unlink($image['image_name']);
            }return true;

        }
    }

}
