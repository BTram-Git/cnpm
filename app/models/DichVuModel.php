<?php 
class DichVuModel 
{ 
private $conn; 
private $table_name = "dichvu"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getDichVus() 
{ 
$query = "SELECT d.MaDV, d.Tendichvu,d.Gia,d.MoTa FROM " . $this->table_name . " d ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getDichVuById($id) 
{ 
$query = "SELECT d.MaDV, d.Tendichvu,d.Gia,d.MoTa FROM " . $this->table_name . " d WHERE d.MaDV = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

} 