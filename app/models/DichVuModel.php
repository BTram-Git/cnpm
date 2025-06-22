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
// Thêm mới danh mục
public function addDichVu($Tendichvu,$Gia,$MoTa)
{
    $errors = [];

    if (empty($Tendichvu)) {
        $errors['Tendichvu'] = 'Ten dich vu không được để trống';
    }
    if (empty($Gia)) {
        $errors['Gia'] = 'Gia không được để trống';
    }
    if (empty($MoTa)) {
        $errors['MoTa'] = 'Mo ta không được để trống';
    }

    if (count($errors) > 0) {
        return $errors;
    }
//INSERT INTO dichvu (Tendichvu,Gia,MoTa) VALUE ("tam trang spa",450,"123")
    $query = "INSERT INTO " . $this->table_name . " (Tendichvu,Gia,MoTa) 
    VALUES (:Tendichvu, :Gia,:MoTa)";
    $stmt = $this->conn->prepare($query);

    $Tendichvu = htmlspecialchars(strip_tags($Tendichvu));
    $Gia = htmlspecialchars(strip_tags($Gia));
    $MoTa = htmlspecialchars(strip_tags($MoTa));
    
    $stmt->bindParam(':Tendichvu', $Tendichvu);
    $stmt->bindParam(':Gia', $Gia);
    $stmt->bindParam(':MoTa', $MoTa);

    if ($stmt->execute()) {
        return true;
    }

    return false;
}
public function updateDichVu($id, $Tendichvu, $Gia,$MoTa )
{
    $query = "UPDATE " . $this->table_name . " SET Tendichvu = :Tendichvu, Gia = :Gia,MoTa = :MoTa  WHERE MaDV = :id";
    $stmt = $this->conn->prepare($query);


    $Tendichvu = htmlspecialchars(strip_tags($Tendichvu));
    $Gia = htmlspecialchars(strip_tags($Gia));
    $MoTa = htmlspecialchars(strip_tags($MoTa));

    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':Tendichvu', $Tendichvu);
    $stmt->bindParam(':Gia', $Gia);
    $stmt->bindParam(':MoTa', $MoTa);
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            return true;
        }
    }
    return false;
}
// Kiểm tra dịch vụ có liên kết với chitietdichvu không
private function hasRelatedChiTietDichVu($MaDV) {
    $query = "SELECT COUNT(*) as count FROM chitietdichvu WHERE MaDV = :MaDV";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':MaDV', $MaDV);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return ($result['count'] > 0);
}
public function deleteDichVu($MaDV)
{
    // Kiểm tra liên kết với chitietdichvu
    if ($this->hasRelatedChiTietDichVu($MaDV)) {
        return ['error' => 'Không thể xóa: Dịch vụ này đang được sử dụng trong chi tiết dịch vụ!'];
    }
    $query = "DELETE FROM " . $this->table_name . " WHERE MaDV = :MaDV";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':MaDV', $MaDV);
    if ($stmt->execute()) {
        if ($stmt->rowCount() > 0) {
            return true;
        } else {
            return ['error' => 'Dịch vụ không tồn tại hoặc đã được xóa.'];
        }
    }
    return ['error' => 'Không thể xóa dịch vụ do lỗi không xác định.'];
}
}                                                                                                                                                                                                                                                                                                                                                                                                                        