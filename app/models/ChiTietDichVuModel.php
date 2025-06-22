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

public function addChiTietDichVu($MaDL, $MaDV)
{
    $query = "INSERT INTO " . $this->table_name . " (MaDL, MaDV) VALUES (:MaDL, :MaDV)";
    try {
        $stmt = $this->conn->prepare($query);
        $MaDL = htmlspecialchars(strip_tags($MaDL));
        $MaDV = htmlspecialchars(strip_tags($MaDV));
        $stmt->bindParam(':MaDL', $MaDL);
        $stmt->bindParam(':MaDV', $MaDV);
        if ($stmt->execute()) {
            return true;
        }
        return false;
    } catch (PDOException $e) {
        // Bắt lỗi ràng buộc khóa ngoại (mã lỗi 23000)
        if ($e->getCode() == '23000') {
            return ['error' => 'Không thể thêm: MaDL hoặc MaDV không tồn tại trong bảng gốc.'];
        } else {
            return ['error' => 'Lỗi PDO: ' . $e->getMessage()];
        }
    }
}

public function updateChiTietDichVu($MaDL, $MaDV)
{
    $query = "UPDATE " . $this->table_name . " SET MaDV = :MaDV WHERE MaDL = :MaDL";
    try {
        $stmt = $this->conn->prepare($query);
        $MaDL = htmlspecialchars(strip_tags($MaDL));
        $MaDV = htmlspecialchars(strip_tags($MaDV));
        $stmt->bindParam(':MaDL', $MaDL);
        $stmt->bindParam(':MaDV', $MaDV);
        $stmt->execute();
        // Trả về số dòng bị ảnh hưởng
        return $stmt->rowCount();
    } catch (PDOException $e) {
        if ($e->getCode() == '23000') {
            return ['error' => 'Không thể cập nhật: MaDV không tồn tại trong bảng dịch vụ.'];
        } else {
            return ['error' => 'Lỗi PDO: ' . $e->getMessage()];
        }
    }
}

public function deleteChiTietDichVu($MaDL, $MaDV)
{
    $query = "DELETE FROM " . $this->table_name . " WHERE MaDL = :MaDL AND MaDV = :MaDV";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':MaDL', $MaDL);
    $stmt->bindParam(':MaDV', $MaDV);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}
}
