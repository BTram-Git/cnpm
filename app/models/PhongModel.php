<?php 
class PhongModel 
{ 
private $conn; 
private $table_name = "phong"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getPhongs() 
{ 
$query = "SELECT p.Maphong, p.Tenphong, p.Loaiphong, p.MatrangthaiP FROM " . $this->table_name . " p ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getPhongById($id) 
{ 
$query = "SELECT p.Maphong, p.Tenphong, p.Loaiphong, p.MatrangthaiP FROM " . $this->table_name . " p WHERE p.Maphong = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

} 