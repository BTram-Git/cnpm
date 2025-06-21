<?php 
class TrangThaiModel 
{ 
private $conn; 
private $table_name = "trangthai"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getTrangThais() 
{ 
$query = "SELECT t.Matrangthai, t.Tentrangthai FROM " . $this->table_name . " t ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getTrangThaiById($id) 
{ 
$query = "SELECT t.Matrangthai, t.Tentrangthai FROM " . $this->table_name . " t WHERE t.Matrangthai = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

public function addTrangThai($Tentrangthai)
{
    $query = "INSERT INTO " . $this->table_name . " (Tentrangthai) VALUES (:Tentrangthai)";
    $stmt = $this->conn->prepare($query);
    $Tentrangthai = htmlspecialchars(strip_tags($Tentrangthai));
    $stmt->bindParam(':Tentrangthai', $Tentrangthai);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function updateTrangThai($Matrangthai, $Tentrangthai)
{
    $query = "UPDATE " . $this->table_name . " SET Tentrangthai = :Tentrangthai WHERE Matrangthai = :Matrangthai";
    $stmt = $this->conn->prepare($query);
    $Matrangthai = htmlspecialchars(strip_tags($Matrangthai));
    $Tentrangthai = htmlspecialchars(strip_tags($Tentrangthai));
    $stmt->bindParam(':Matrangthai', $Matrangthai);
    $stmt->bindParam(':Tentrangthai', $Tentrangthai);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function deleteTrangThai($Matrangthai)
{
    try {
        // Kiểm tra xem trạng thái có được sử dụng trong hóa đơn không
        $queryCheckHoaDon = "SELECT COUNT(*) FROM hoadon_va_thanhtoan WHERE Matrangthai = :Matrangthai";
        $stmtCheckHoaDon = $this->conn->prepare($queryCheckHoaDon);
        $stmtCheckHoaDon->bindParam(':Matrangthai', $Matrangthai);
        $stmtCheckHoaDon->execute();
        if ($stmtCheckHoaDon->fetchColumn() > 0) {
            return ['error' => 'Không thể xóa: Trạng thái này đang được sử dụng trong hóa đơn!'];
        }

        // Kiểm tra xem trạng thái có được sử dụng trong trạng thái phòng không
        // Giả sử tên cột trong bảng trangthaiphong cũng là Matrangthai
        $queryCheckTrangThaiPhong = "SELECT COUNT(*) FROM trangthaiphong WHERE Matrangthai = :Matrangthai";
        $stmtCheckTrangThaiPhong = $this->conn->prepare($queryCheckTrangThaiPhong);
        $stmtCheckTrangThaiPhong->bindParam(':Matrangthai', $Matrangthai);
        $stmtCheckTrangThaiPhong->execute();
        if ($stmtCheckTrangThaiPhong->fetchColumn() > 0) {
            return ['error' => 'Không thể xóa: Trạng thái này đang được sử dụng trong trạng thái phòng!'];
        }

        // Nếu không có liên kết, tiến hành xóa
        $queryDelete = "DELETE FROM " . $this->table_name . " WHERE Matrangthai = :Matrangthai";
        $stmtDelete = $this->conn->prepare($queryDelete);
        $stmtDelete->bindParam(':Matrangthai', $Matrangthai);
        
        if ($stmtDelete->execute()) {
            if ($stmtDelete->rowCount() > 0) {
                return true;
            } else {
                return ['error' => 'Trạng thái không tồn tại.'];
            }
        }
        
        return ['error' => 'Không thể xóa trạng thái do lỗi không xác định.'];

    } catch (PDOException $e) {
        return ['error' => 'Lỗi PDO: ' . $e->getMessage()];
    }
}

} 