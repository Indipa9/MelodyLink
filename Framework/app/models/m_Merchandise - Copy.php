<?php
class m_Merchandise {  // Changed class name to match the filename
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    public function getAllMerchandise() {
        $this->db->query('SELECT merch_id, Name, Price, Description, image FROM Merchandise');
        return $this->db->resultSet();
    }

    public function getMerchandiseById($id) {
        $this->db->query('SELECT merch_id, Name, Price, Description, image FROM Merchandise WHERE merch_id = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }
}