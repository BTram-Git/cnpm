<?php 
class PhongModel 
{ 
private $conn; 
private $table_name = "phong"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getPhongs() 
{ 
$query = "SELECT p.Maphong, p.Tenphong, p.Loaiphong, p.MatrangthaiP FROM " . $this->table_name . " p ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getPhongById($id) 
{ 
$query = "SELECT p.Maphong, p.Tenphong, p.Loaiphong, p.MatrangthaiP FROM " . $this->table_name . " p WHERE p.Maphong = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

public function addPhong($Tenphong, $Loaiphong, $MatrangthaiP)
{
    $query = "INSERT INTO " . $this->table_name . " (Tenphong, Loaiphong, MatrangthaiP) VALUES (:Tenphong, :Loaiphong, :MatrangthaiP)";
    $stmt = $this->conn->prepare($query);
    $Tenphong = htmlspecialchars(strip_tags($Tenphong));
    $Loaiphong = htmlspecialchars(strip_tags($Loaiphong));
    $MatrangthaiP = htmlspecialchars(strip_tags($MatrangthaiP));
    $stmt->bindParam(':Tenphong', $Tenphong);
    $stmt->bindParam(':Loaiphong', $Loaiphong);
    $stmt->bindParam(':MatrangthaiP', $MatrangthaiP);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function updatePhong($Maphong, $Tenphong, $Loaiphong, $MatrangthaiP)
{
    $query = "UPDATE " . $this->table_name . " SET Tenphong = :Tenphong, Loaiphong = :Loaiphong, MatrangthaiP = :MatrangthaiP WHERE Maphong = :Maphong";
    $stmt = $this->conn->prepare($query);
    $Maphong = htmlspecialchars(strip_tags($Maphong));
    $Tenphong = htmlspecialchars(strip_tags($Tenphong));
    $Loaiphong = htmlspecialchars(strip_tags($Loaiphong));
    $MatrangthaiP = htmlspecialchars(strip_tags($MatrangthaiP));
    $stmt->bindParam(':Maphong', $Maphong);
    $stmt->bindParam(':Tenphong', $Tenphong);
    $stmt->bindParam(':Loaiphong', $Loaiphong);
    $stmt->bindParam(':MatrangthaiP', $MatrangthaiP);
    $stmt->execute();
    return $stmt->rowCount();
}

public function deletePhong($Maphong)
{
    try {
        // Kiểm tra phòng có trong bảng đặt lịch không
        $queryCheckDatLich = "SELECT COUNT(*) as count FROM datlich WHERE Maphong = :Maphong";
        $stmtCheckDatLich = $this->conn->prepare($queryCheckDatLich);
        $stmtCheckDatLich->bindParam(':Maphong', $Maphong);
        $stmtCheckDatLich->execute();
        if ($stmtCheckDatLich->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            return ['error' => 'Không thể xóa: Phòng này đang có trong lịch đặt!'];
        }

        // Kiểm tra phòng có trong bảng hóa đơn không
        $queryCheckHoaDon = "SELECT COUNT(*) as count FROM hoadon_va_thanhtoan WHERE Maphong = :Maphong";
        $stmtCheckHoaDon = $this->conn->prepare($queryCheckHoaDon);
        $stmtCheckHoaDon->bindParam(':Maphong', $Maphong);
        $stmtCheckHoaDon->execute();
        if ($stmtCheckHoaDon->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            return ['error' => 'Không thể xóa: Phòng này đã có hóa đơn liên quan!'];
        }

        // Kiểm tra phòng có trong bảng trạng thái phòng không
        $queryCheckTrangThai = "SELECT COUNT(*) as count FROM trangthaiphong WHERE Maphong = :Maphong";
        $stmtCheckTrangThai = $this->conn->prepare($queryCheckTrangThai);
        $stmtCheckTrangThai->bindParam(':Maphong', $Maphong);
        $stmtCheckTrangThai->execute();
        if ($stmtCheckTrangThai->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            return ['error' => 'Không thể xóa: Phòng này đang có trạng thái được ghi nhận!'];
        }

        // Nếu không có liên kết thì xóa
        $queryDelete = "DELETE FROM " . $this->table_name . " WHERE Maphong = :Maphong";
        $stmtDelete = $this->conn->prepare($queryDelete);
        $stmtDelete->bindParam(':Maphong', $Maphong);

        if ($stmtDelete->execute()) {
            return true;
        }

        return ['error' => 'Không thể xóa phòng do lỗi không xác định.'];
    } catch (PDOException $e) {
        return ['error' => 'Lỗi PDO: ' . $e->getMessage()];
    }
}

} 