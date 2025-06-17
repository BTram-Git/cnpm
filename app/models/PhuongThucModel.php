<?php 
class PhuongThucModel 
{ 
private $conn; 
private $table_name = "phuongthuc"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getPhuongThucs() 
{ 
$query = "SELECT pt.MaPT, pt.TenPT, pt.Mota FROM " . $this->table_name . " pt ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getPhuongThucById($id) 
{ 
$query = "SELECT pt.MaPT, pt.TenPT, pt.Mota FROM " . $this->table_name . " pt WHERE pt.MaPT = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

} 