<?php 
class TrangThaiPhongModel 
{ 
private $conn; 
private $table_name = "trangthaiphong"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getTrangThaiPhongs() 
{ 
$query = "SELECT tp.MatrangthaiP, tp.Tentrangthai FROM " . $this->table_name . " tp ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getTrangThaiPhongById($id) 
{ 
$query = "SELECT tp.MatrangthaiP, tp.Tentrangthai FROM " . $this->table_name . " tp WHERE tp.MatrangthaiP = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

public function addTrangThaiPhong($Tentrangthai)
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

public function updateTrangThaiPhong($MatrangthaiP, $Tentrangthai)
{
    $query = "UPDATE " . $this->table_name . " SET Tentrangthai = :Tentrangthai WHERE MatrangthaiP = :MatrangthaiP";
    $stmt = $this->conn->prepare($query);
    $MatrangthaiP = htmlspecialchars(strip_tags($MatrangthaiP));
    $Tentrangthai = htmlspecialchars(strip_tags($Tentrangthai));
    $stmt->bindParam(':MatrangthaiP', $MatrangthaiP);
    $stmt->bindParam(':Tentrangthai', $Tentrangthai);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function deleteTrangThaiPhong($MatrangthaiP)
{
    try {
        // Kiểm tra xem trạng thái phòng có được sử dụng trong bảng 'phong' không
        $queryCheck = "SELECT COUNT(*) FROM phong WHERE MatrangthaiP = :MatrangthaiP";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(':MatrangthaiP', $MatrangthaiP);
        $stmtCheck->execute();
        if ($stmtCheck->fetchColumn() > 0) {
            return ['error' => 'Không thể xóa: Trạng thái này đang được sử dụng bởi một phòng!'];
        }

        // Nếu không có liên kết, tiến hành xóa
        $queryDelete = "DELETE FROM " . $this->table_name . " WHERE MatrangthaiP = :MatrangthaiP";
        $stmtDelete = $this->conn->prepare($queryDelete);
        $stmtDelete->bindParam(':MatrangthaiP', $MatrangthaiP);

        if ($stmtDelete->execute()) {
            if ($stmtDelete->rowCount() > 0) {
                return true;
            } else {
                return ['error' => 'Trạng thái phòng không tồn tại.'];
            }
        }
        
        return ['error' => 'Không thể xóa trạng thái phòng do lỗi không xác định.'];
    } catch (PDOException $e) {
        return ['error' => 'Lỗi PDO: ' . $e->getMessage()];
    }
}

} 