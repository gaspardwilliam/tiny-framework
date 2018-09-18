<?php
namespace William\Controller;

class PostMetaController
{
    private $id;
    private $key;
    private $value;
    

    // Liste des getters

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

    public function setKey($key)
    {
        // On vérifie qu'il s'agit bien d'une chaîne de caractères.
        if (is_string($key)) {
            $this->key = $key;
        }
    }

    public function setValue($value)
    {
        // On vérifie qu'il s'agit bien d'une chaîne de caractères.
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
