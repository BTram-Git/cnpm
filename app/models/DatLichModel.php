<?php 
class DatLichModel 
{ 
private $conn; 
private $table_name = "datlich"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getDatLich() 
{ 
$query = "SELECT dl.MaDL, dl.Manguoidung, dl.Thoigiandatlich, dl.Trangthai_ FROM " . $this->table_name . " dl ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getDatLichByUserId($userId) 
{ 
    $query = "SELECT dl.MaDL, dl.Manguoidung, dl.Thoigiandatlich, dl.Trangthai_ 
    FROM " . $this->table_name . " dl 
    WHERE dl.Manguoidung = :userId";

    $stmt = $this->conn->prepare($query); 
    $stmt->bindParam(':userId', $userId); 
    $stmt->execute(); 
    $result = $stmt->fetchAll(PDO::FETCH_OBJ);
    return $result;
}
public function getDatLichById($id) 
{ 
    $query = "SELECT dl.MaDL, dl.Manguoidung, dl.Thoigiandatlich, dl.Trangthai_ 
    FROM " . $this->table_name . " dl 
    WHERE dl.MaDL = :id";

    $stmt = $this->conn->prepare($query); 
    $stmt->bindParam(':id', $id); 
    $stmt->execute(); 
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    return $result;
}
// Thêm mới danh mục
public function addDatLich($Manguoidung, $Thoigiandatlich, $Trangthai)
{
    $errors = [];

    if (empty($Manguoidung)) {
        $errors['Manguoidung'] = 'Mã người dùng không hợp lệ';
    }
    if (empty($Thoigiandatlich)) {
        $errors['Thoigiandatlich'] = 'Thoigiandatlich không được để trống';
    }
    if (empty($Trangthai)) {
        $errors['Trangthai'] = 'Trangthai không được để trống';
    }

    if (count($errors) > 0) {
        return $errors;
    }
//INSERT INTO datlich ( Manguoidung,Thoigiandatlich, Trangthai_) VALUE (1,NOW(),"123")
    $query = "INSERT INTO " . $this->table_name . " (Manguoidung, Thoigiandatlich, Trangthai_) 
    VALUES (:Manguoidung, :Thoigiandatlich, :Trangthai)";
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':Manguoidung', $Manguoidung);
    $stmt->bindParam(':Thoigiandatlich', $Thoigiandatlich);
    $stmt->bindParam(':Trangthai', $Trangthai);

    if ($stmt->execute()) {
        return true;
    }

    return false;
}

public function updateDatLich($id, $Thoigiandatlich, $Trangthai)
{
    $query = "UPDATE " . $this->table_name . " SET Thoigiandatlich = :Thoigiandatlich, Trangthai_ = :Trangthai WHERE MaDL = :id";
    $stmt = $this->conn->prepare($query);

    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':Thoigiandatlich', $Thoigiandatlich);
    $stmt->bindParam(':Trangthai', $Trangthai);

    if ($stmt->execute()) {
        return true;
    }

    return false;
}

public function deleteDatLich($MaDL)
{
    try {
        // Kiểm tra xem lịch đặt có trong bảng hóa đơn không
        $queryCheckHoaDon = "SELECT COUNT(*) as count FROM hoadon_va_thanhtoan WHERE MaDL = :MaDL";
        $stmtCheckHoaDon = $this->conn->prepare($queryCheckHoaDon);
        $stmtCheckHoaDon->bindParam(':MaDL', $MaDL);
        $stmtCheckHoaDon->execute();
        if ($stmtCheckHoaDon->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            return ['error' => 'Không thể xóa: Lịch đặt này đã có hóa đơn!'];
        }

        // Kiểm tra xem lịch đặt có trong chi tiết dịch vụ không
        $queryCheckChiTiet = "SELECT COUNT(*) as count FROM chitietdichvu WHERE MaDL = :MaDL";
        $stmtCheckChiTiet = $this->conn->prepare($queryCheckChiTiet);
        $stmtCheckChiTiet->bindParam(':MaDL', $MaDL);
        $stmtCheckChiTiet->execute();
        if ($stmtCheckChiTiet->fetch(PDO::FETCH_ASSOC)['count'] > 0) {
            return ['error' => 'Không thể xóa: Lịch đặt này đã có chi tiết dịch vụ!'];
        }

        // Nếu không, tiến hành xóa
        $queryDelete = "DELETE FROM " . $this->table_name . " WHERE MaDL = :MaDL";
        $stmtDelete = $this->conn->prepare($queryDelete);
        $stmtDelete->bindParam(':MaDL', $MaDL);
        if ($stmtDelete->execute()) {
            return true;
        }
        return ['error' => 'Không thể xóa lịch đặt do lỗi không xác định.'];
    } catch (PDOException $e) {
        return ['error' => 'Lỗi PDO: ' . $e->getMessage()];
    }
}

}