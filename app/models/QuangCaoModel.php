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

public function addQuangCao($Tieude, $Noidung, $Loaiquangcao, $Image, $Ngaybatdau, $Ngayketthuc, $Manguoidung)
{
    $query = "INSERT INTO " . $this->table_name . " (Tieude, Noidung, Loaiquangcao, Image, Ngaybatdau, Ngayketthuc, Manguoidung) VALUES (:Tieude, :Noidung, :Loaiquangcao, :Image, :Ngaybatdau, :Ngayketthuc, :Manguoidung)";
    $stmt = $this->conn->prepare($query);
    $Tieude = htmlspecialchars(strip_tags($Tieude));
    $Noidung = htmlspecialchars(strip_tags($Noidung));
    $Loaiquangcao = htmlspecialchars(strip_tags($Loaiquangcao));
    $Image = htmlspecialchars(strip_tags($Image));
    $Ngaybatdau = htmlspecialchars(strip_tags($Ngaybatdau));
    $Ngayketthuc = htmlspecialchars(strip_tags($Ngayketthuc));
    $Manguoidung = htmlspecialchars(strip_tags($Manguoidung));
    $stmt->bindParam(':Tieude', $Tieude);
    $stmt->bindParam(':Noidung', $Noidung);
    $stmt->bindParam(':Loaiquangcao', $Loaiquangcao);
    $stmt->bindParam(':Image', $Image);
    $stmt->bindParam(':Ngaybatdau', $Ngaybatdau);
    $stmt->bindParam(':Ngayketthuc', $Ngayketthuc);
    $stmt->bindParam(':Manguoidung', $Manguoidung);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function updateQuangCao($MaQC, $Tieude, $Noidung, $Loaiquangcao, $Image, $Ngaybatdau, $Ngayketthuc, $Manguoidung)
{
    $query = "UPDATE " . $this->table_name . " SET Tieude = :Tieude, Noidung = :Noidung, Loaiquangcao = :Loaiquangcao, Image = :Image, Ngaybatdau = :Ngaybatdau, Ngayketthuc = :Ngayketthuc, Manguoidung = :Manguoidung WHERE MaQC = :MaQC";
    $stmt = $this->conn->prepare($query);
    $MaQC = htmlspecialchars(strip_tags($MaQC));
    $Tieude = htmlspecialchars(strip_tags($Tieude));
    $Noidung = htmlspecialchars(strip_tags($Noidung));
    $Loaiquangcao = htmlspecialchars(strip_tags($Loaiquangcao));
    $Image = htmlspecialchars(strip_tags($Image));
    $Ngaybatdau = htmlspecialchars(strip_tags($Ngaybatdau));
    $Ngayketthuc = htmlspecialchars(strip_tags($Ngayketthuc));
    $Manguoidung = htmlspecialchars(strip_tags($Manguoidung));
    $stmt->bindParam(':MaQC', $MaQC);
    $stmt->bindParam(':Tieude', $Tieude);
    $stmt->bindParam(':Noidung', $Noidung);
    $stmt->bindParam(':Loaiquangcao', $Loaiquangcao);
    $stmt->bindParam(':Image', $Image);
    $stmt->bindParam(':Ngaybatdau', $Ngaybatdau);
    $stmt->bindParam(':Ngayketthuc', $Ngayketthuc);
    $stmt->bindParam(':Manguoidung', $Manguoidung);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function deleteQuangCao($MaQC)
{
    $query = "DELETE FROM " . $this->table_name . " WHERE MaQC = :MaQC";
    $stmt = $this->conn->prepare($query);
    $MaQC = htmlspecialchars(strip_tags($MaQC));
    $stmt->bindParam(':MaQC', $MaQC);
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return false; // Hoặc có thể trả về một thông báo lỗi cụ thể
        }
    }
    return false;
}

} 