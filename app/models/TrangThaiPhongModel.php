<?php 
class TrangThaiPhongModel 
{ 
private $conn; 
private $table_name = "trangthaiphong"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getTrangThaiPhongs() 
{ 
$query = "SELECT tp.MatrangthaiP, tp.Tentrangthai FROM " . $this->table_name . " tp ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getTrangThaiPhongById($id) 
{ 
$query = "SELECT tp.MatrangthaiP, tp.Tentrangthai FROM " . $this->table_name . " tp WHERE tp.MatrangthaiP = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

} 