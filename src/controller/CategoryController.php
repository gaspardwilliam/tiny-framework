<?php
namespace William\Controller;

class CategoryController
{
    /**
     * id de la catégorie
     *
     * @var int
     */
    private $id;
    /**
     * nom de la catégorie
     *
     * @var string
     */
    private $name;

    /**
     * id
     *
     * @return int
     */
    public function id()
    {
        return $this->id;
    }

    /**
     * name
     *
     * @return string
     */
    public function name()
    {
        return $this->name;
    }

    /**
     * setId
     *
     * @param  int $id
     *
     * @return void
     */
    public function setId($id)
    {
        $id = (int) $id;
        if ($id > 0) {
            $this->id = $id;
        }
    }

    /**
     * setName
     *
     * @param  string $name
     *
     * @return void
     */
    public function setName($name)
    {
        if (is_string($name)) {
            $this->name = $name;
        }
    }

    /**
     * hydrate
     *
     * @param  array $donnees
     *
     * @return void
     */
    public function hydrate($donnees)
    {
        foreach ($donnees as $key => $value) {
            $method = 'set' . ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

}
