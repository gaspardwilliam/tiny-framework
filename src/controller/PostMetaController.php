<?php
namespace William\Controller;

class PostMetaController
{
    private $id;
    private $key;
    private $value;
    


    public function id()
    {
        return $this->id;
    }

    public function key()
    {
        return $this->key;
    }

    public function value()
    {
        return $this->value;
    }


    public function setId($id)
    {
        $id = (int) $id;
        if ($id > 0) {
            $this->id = $id;
        }
    }

    public function setKey($key)
    {
        if (is_string($key)) {
            $this->key = $key;
        }
    }

    public function setValue($value)
    {
        if (is_string($value)||empty($value)) {
            $this->value = $value;
        }
    }

    public function hydrate($donnees)
    {
        foreach ($donnees as $key => $value) {
            array_push($metas_array, array('key' => 'original',
                'name' => 'public/img/uploaded_img/' . $handle->file_dst_name));
        }
    }

}
