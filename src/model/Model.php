<?php
namespace William\Model;

class Model
{

    public $db;
    private $username;
    private $dbname;
    private $password;
    private $servername;

    public function __construct()
    {

        $this->username = USERNAME;

        $this->servername = SERVERNAME;

        $this->password = PASSWORD;

        $this->dbname = DBNAME;
        try {
            $this->db = new \PDO("mysql:host=$this->servername;dbname=$this->dbname", $this->username, $this->password);
            // set the PDO error mode to exception
            $this->db->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            //echo "Connected successfully";

        } catch (PDOException $e) {
            echo "Connection failed: " . $e->getMessage();
        }
    }
}
