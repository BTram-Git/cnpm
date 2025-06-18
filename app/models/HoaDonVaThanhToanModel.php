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
    $query = "SELECT h.MaHD, h.NgayThanhToan, h.Tongtien,h.MaDL, h.Manguoidung, h.Maphong, h.MaPT, h.Matrangthai FROM " . $this->table_name . " h";
    $stmt = $this->conn->prepare($query); 
    $stmt->execute(); 
    $result = $stmt->fetchAll(PDO::FETCH_OBJ); 
    return $result; 
} 

public function getHoaDonVaThanhToanById($id) 
{ 
    $query = "SELECT h.MaHD, h.NgayThanhToan, h.Tongtien, h.MaD, h.Manguoidung, h.Maphong, h.MaPT, h.Matrangthai FROM " . $this->table_name . " h WHERE h.MaHD = :id";
    $stmt = $this->conn->prepare($query); 
    $stmt->bindParam(':id', $id); 
    $stmt->execute(); 
    $result = $stmt->fetch(PDO::FETCH_OBJ); 
    return $result; 
}

public function addHoaDonVaThanhToan($NgayThanhToan, $Tongtien,$MaDL, $Manguoidung, $Maphong, $MaPT, $Matrangthai)
{
    $query = "INSERT INTO " . $this->table_name . " (NgayThanhToan, Tongtien, MaDL,Manguoidung, Maphong, MaPT, Matrangthai)
              VALUES (:NgayThanhToan, :Tongtien, :MaDL,:Manguoidung,:Maphong, :MaPT, :Matrangthai)";
    $stmt = $this->conn->prepare($query);

    $NgayThanhToan = htmlspecialchars(strip_tags($NgayThanhToan));
    $Tongtien = htmlspecialchars(strip_tags($Tongtien));
    $MaDL = htmlspecialchars(strip_tags($MaDL));
    $Manguoidung = htmlspecialchars(strip_tags($Manguoidung));
    $Maphong = htmlspecialchars(strip_tags($Maphong));
    $MaPT = htmlspecialchars(strip_tags($MaPT));
    $Matrangthai = htmlspecialchars(strip_tags($Matrangthai));


    $stmt->bindParam(':NgayThanhToan', $NgayThanhToan);
    $stmt->bindParam(':Tongtien', $Tongtien);
    $stmt->bindParam(':MaDL', $MaDL);
    $stmt->bindParam(':Manguoidung', $Manguoidung);
    $stmt->bindParam(':Maphong', $Maphong);
    $stmt->bindParam(':MaPT', $MaPT);
    $stmt->bindParam(':Matrangthai', $Matrangthai);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function updateHoaDonVaThanhToan($MaHD, $NgayThanhToan, $Tongtien, $MaDL, $Manguoidung, $Maphong, $MaPT, $Matrangthai)
{
    $query = "UPDATE " . $this->table_name . " SET NgayThanhToan = :NgayThanhToan, Tongtien = :Tongtien, MaDL = :MaDL, Manguoidung = :Manguoidung, Maphong = :Maphong, MaPT = :MaPT, Matrangthai = :Matrangthai WHERE MaHD = :MaHD";
    $stmt = $this->conn->prepare($query);

    $NgayThanhToan = htmlspecialchars(strip_tags($NgayThanhToan));
    $Tongtien = htmlspecialchars(strip_tags($Tongtien));
    $MaDL = htmlspecialchars(strip_tags($MaDL));
    $Manguoidung = htmlspecialchars(strip_tags($Manguoidung));
    $Maphong = htmlspecialchars(strip_tags($Maphong));
    $MaPT = htmlspecialchars(strip_tags($MaPT));
    $Matrangthai = htmlspecialchars(strip_tags($Matrangthai));

    $stmt->bindParam(':NgayThanhToan', $NgayThanhToan);
    $stmt->bindParam(':Tongtien', $Tongtien);
    $stmt->bindParam(':MaDL', $MaDL);
    $stmt->bindParam(':Manguoidung', $Manguoidung);
    $stmt->bindParam(':Maphong', $Maphong);
    $stmt->bindParam(':MaPT', $MaPT);
    $stmt->bindParam(':Matrangthai', $Matrangthai);
    $stmt->bindParam(':MaHD', $MaHD);

    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function deleteHoaDonVaThanhToan($MaHD)
{
    $query = "DELETE FROM " . $this->table_name . " WHERE MaHD = :MaHD";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':MaHD', $MaHD);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

} 