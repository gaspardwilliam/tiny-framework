<?php
namespace William\Model;


    
class UserManager extends Model
{
    private $user_table = 'alto_users';
    
    public function create($user)
  {
    $stmt = $this->db->prepare("INSERT INTO $this->user_table (user_email,user_password,user_role) VALUES (:email,:password,:role)");
    $stmt->bindParam(':email', $email);    
    $stmt->bindParam(':password', $password);    

    // insertion d'une ligne
    $email = $user->email();    
    $password = $user->password();    
    $role = $user->role();    
    $stmt->execute();
  }
  
    public function fetchAll(){
        $stmt = $this->db->prepare("SELECT * FROM $this->user_table ");
        if ($stmt->execute()) {
          return $stmt->fetchAll(\PDO::FETCH_ASSOC); 
        }
    }
    
    public function fetchID($id){
        $stmt = $this->db->prepare("SELECT * FROM $this->user_table  where user_id = ?");
        if ($stmt->execute(array($id))) {
          return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

    }
    
    public function fetchEmail($email){
        $stmt = $this->db->prepare("SELECT * FROM $this->user_table  where user_email = ?");
        if ($stmt->execute(array($email))) {
          return $stmt->fetch(\PDO::FETCH_ASSOC);
        }

    }
    
    public function update($user){
        $stmt = $this->db->prepare("UPDATE $this->user_table SET cat_name=:name WHERE cat_id=:id");
        $stmt->bindParam(":name",$name);        
        
        $name=$cat->name();       
        $stmt->execute();
    }
    
    public function delete($id){
        $sql = "DELETE FROM $this->user_table WHERE post_id=$id";

        // use exec() because no results are returned
        $this->db->exec($sql);
    }
}