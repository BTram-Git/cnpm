<?php 
class ChiTietDichVuModel 
{   
private $conn; 
private $table_name = "chitietdichvu"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getChiTietDichVus() 
{ 
$query = "SELECT ct.MaDL, ct.MaDV FROM " . $this->table_name . " ct ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getChiTietDichVuById($id) 
{ 
$query = "SELECT ct.MaDL, ct.MaDV FROM " . $this->table_name . " ct WHERE ct.MaDL = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}
}
