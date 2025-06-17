<?php 
class DanhGiaModel 
{ 
private $conn; 
private $table_name = "danhgia"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getDanhGias() 
{ 
$query = "SELECT dg.MaDG, dg.Danhgiasao, dg.Nhanxet, dg.Ngaydanhgia, dg.Manguoidung, dg.MaHD FROM " . $this->table_name . " dg ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getDanhGiaById($id) 
{ 
$query = "SELECT dg.MaDG, dg.Danhgiasao, dg.Nhanxet, dg.Ngaydanhgia, dg.Manguoidung, dg.MaHD FROM " . $this->table_name . " dg WHERE dg.MaDG = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

} 