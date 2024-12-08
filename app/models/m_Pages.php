<?php
class m_Pages{
private $db;

public function __construct(){
$this->db=new Database();

}
public function getUsers(){
    $this->db->query('SELECT * FROM users0');
    
    return $this->db->resultSet();
    


}

}
?>