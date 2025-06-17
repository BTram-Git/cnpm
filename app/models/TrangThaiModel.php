<?php 
class TrangThaiModel 
{ 
private $conn; 
private $table_name = "trangthai"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getTrangThais() 
{ 
$query = "SELECT t.Matrangthai, t.Tentrangthai FROM " . $this->table_name . " t ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getTrangThaiById($id) 
{ 
$query = "SELECT t.Matrangthai, t.Tentrangthai FROM " . $this->table_name . " t WHERE t.Matrangthai = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

} 