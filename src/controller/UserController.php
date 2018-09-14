<?php
namespace William\Controller;

class UserController
{
    private $id;
    private $email;
    private $role;
    private $password;

    // Liste des getters

    public function id()
    {
        return $this->id;
    }

    public function email()
    {
        return $this->email;
    }

    public function role()
    {
        return $this->role;
    }

    public function password()
    {
        return $this->password;
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

    public function setEmail($email)
    {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->email = $email;
        }

    }

    public function setRole($role)
    {
        $this->role = $role;

    }

    public function setPassword($password)
    {
        $this->password = $password;

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
