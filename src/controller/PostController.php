<?php
namespace William\Controller;

class PostController
{
    private $id;
    private $title;
    private $content;
    private $created;
    private $updated;
    private $slug;
    private $cat_id;
    private $type;

    // Liste des getters

    public function id()
    {
        return $this->id;
    }

    public function title()
    {
        return $this->title;
    }

    public function type()
    {
        return $this->type;
    }

    public function content()
    {
        return $this->content;
    }

    public function created()
    {
        return $this->created;
    }

    public function updated()
    {
        return $this->updated;
    }

    public function slug()
    {
        return $this->slug;
    }

    public function cat_id()
    {
        return $this->cat_id;
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

    public function setCat_id($id)
    {

        $id = (int) $id;

        // Si c'est le cas, c'est tout bon, on assigne la valeur à l'attribut correspondant.
        $this->cat_id = $id;

    }

    public function setTitle($title)
    {
        // On vérifie qu'il s'agit bien d'une chaîne de caractères.
        if (is_string($title)) {
            $this->title = $title;
        }
    }

    public function setType($type)
    {
        // On vérifie qu'il s'agit bien d'une chaîne de caractères.
        if (is_string($type)) {
            $this->type = $type;
        }
    }

    public function setContent($content)
    {
        if (is_string($content)) {
            $this->content = $content;
        }
    }

    public function setCreated($created)
    {

        $this->created = $created;

    }

    public function setUpdated($updated)
    {

        $this->updated = $updated;

    }

    public function setSlug($slug)
    {

        $this->slug = $slug;

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
