<?php 
class HoaDonVaThanhToanModel 
{ 
private $conn; 
private $table_name = "hoadon_va_thanhtoan"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getHoaDonVaThanhToans() 
{ 
    $query = "SELECT h.MaHD, h.NgayThanhToan, h.Tongtien, h.Manguoidung, h.Maphong, h.MaPT, h.Matrangthai FROM " . $this->table_name . " h";
    $stmt = $this->conn->prepare($query); 
    $stmt->execute(); 
    $result = $stmt->fetchAll(PDO::FETCH_OBJ); 
    return $result; 
} 

public function getHoaDonVaThanhToanById($id) 
{ 
    $query = "SELECT h.MaHD, h.NgayThanhToan, h.Tongtien, h.Manguoidung, h.Maphong, h.MaPT, h.Matrangthai FROM " . $this->table_name . " h WHERE h.MaHD = :id";
    $stmt = $this->conn->prepare($query); 
    $stmt->bindParam(':id', $id); 
    $stmt->execute(); 
    $result = $stmt->fetch(PDO::FETCH_OBJ); 
    return $result; 
}


} 