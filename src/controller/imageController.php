<?php
namespace William\Controller;

class ImageController
{
    private $id;
    private $name;
    private $key;

    // Liste des getters

    public function id()
    {
        return $this->id;
    }

    public function name()
    {
        return $this->name;
    }

    public function key()
    {
        return $this->key;
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

    public function setName($name)
    {
        // On vérifie qu'il s'agit bien d'une chaîne de caractères.
        if (is_string($name)) {
            $this->name = $name;
        }
    }

    public function setKey($key)
    {
        // On vérifie qu'il s'agit bien d'une chaîne de caractères.
        if (is_string($key)) {
            $this->name = $key;
        }
    }

    public function hydrate($donnees)
    {
        foreach ($donnees as $key => $value) {
            // On récupère le nom du setter correspondant à l'attribut.
            $method = 'set' . ucfirst($key);

            // Si le setter correspondant existe.
            if (method_exists($this, $method)) {
                // On appelle le setter.
                $this->$method($value);
            }
        }

    }

}
