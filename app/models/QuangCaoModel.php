<?php 
class QuangCaoModel 
{ 
private $conn; 
private $table_name = "quangcao"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getQuangCaos() 
{ 
$query = "SELECT q.MaQC, q.Tieude, q.Noidung, q.Loaiquangcao, q.Image, q.Ngaybatdau, q.Ngayketthuc, q.Manguoidung  FROM " . $this->table_name . " q ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getQuangCaoById($id) 
{ 
$query = "SELECT q.MaQC, q.Tieude, q.Noidung, q.Loaiquangcao, q.Image, q.Ngaybatdau, q.Ngayketthuc, q.Manguoidung  FROM " . $this->table_name . " q WHERE q.MaQC = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

} 