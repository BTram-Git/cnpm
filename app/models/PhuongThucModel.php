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

public function addPhuongThuc($TenPT, $Mota)
{
    $query = "INSERT INTO " . $this->table_name . " (TenPT, Mota) VALUES (:TenPT, :Mota)";
    $stmt = $this->conn->prepare($query);
    $TenPT = htmlspecialchars(strip_tags($TenPT));
    $Mota = htmlspecialchars(strip_tags($Mota));
    $stmt->bindParam(':TenPT', $TenPT);
    $stmt->bindParam(':Mota', $Mota);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function updatePhuongThuc($MaPT, $TenPT, $Mota)
{
    $query = "UPDATE " . $this->table_name . " SET TenPT = :TenPT, Mota = :Mota WHERE MaPT = :MaPT";
    $stmt = $this->conn->prepare($query);
    $MaPT = htmlspecialchars(strip_tags($MaPT));
    $TenPT = htmlspecialchars(strip_tags($TenPT));
    $Mota = htmlspecialchars(strip_tags($Mota));
    $stmt->bindParam(':MaPT', $MaPT);
    $stmt->bindParam(':TenPT', $TenPT);
    $stmt->bindParam(':Mota', $Mota);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function deletePhuongThuc($MaPT)
{
    try {
        // Kiểm tra xem phương thức thanh toán có được sử dụng trong hóa đơn không
        $queryCheck = "SELECT COUNT(*) as count FROM hoadon_va_thanhtoan WHERE MaPT = :MaPT";
        $stmtCheck = $this->conn->prepare($queryCheck);
        $stmtCheck->bindParam(':MaPT', $MaPT);
        $stmtCheck->execute();
        if ($stmtCheck->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            return ['error' => 'Không thể xóa: Phương thức thanh toán này đang được sử dụng trong hóa đơn!'];
        }

        // Nếu không có liên kết thì tiến hành xóa
        $queryDelete = "DELETE FROM " . $this->table_name . " WHERE MaPT = :MaPT";
        $stmtDelete = $this->conn->prepare($queryDelete);
        $stmtDelete->bindParam(':MaPT', $MaPT);

        if ($stmtDelete->execute()) {
            if ($stmtDelete->rowCount() > 0) {
                return true;
            } else {
                return ['error' => 'Phương thức thanh toán không tồn tại hoặc đã được xóa.'];
            }
        }
        
        return ['error' => 'Không thể xóa phương thức thanh toán do lỗi không xác định.'];
    } catch (PDOException $e) {
        return ['error' => 'Lỗi PDO: ' . $e->getMessage()];
    }
}

} 