<?php 
class UserModel 
{ 
private $conn; 
private $table_name = "users"; //
public function __construct($db) 
{ 
$this->conn = $db; 
} 
public function getUsers() 
{ 
$query = "SELECT u.Manguoidung, u.Hoten, u.SDT, u.DiaChi, u.Email, u.Ngaysinh, u.Gioitinh FROM  " . $this->table_name . " u ";
$stmt = $this->conn->prepare($query); 
$stmt->execute(); 
$result = $stmt->fetchAll(PDO::FETCH_OBJ); 
return $result; 
} 
public function getUserById($id) 
{ 
$query = "SELECT u.Manguoidung, u.Hoten, u.SDT, u.DiaChi, u.Email, u.Ngaysinh, u.Gioitinh FROM " . $this->table_name . " u WHERE u.Manguoidung = :id";

$stmt = $this->conn->prepare($query); 
$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ); 
return $result; 
}

public function addUser($hoten, $sdt, $diachi, $email, $ngaysinh, $gioitinh) 
{
    try {
        // Kiểm tra email đã tồn tại chưa
        $checkQuery = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE Email = :email";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':email', $email);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] > 0) {
            return ['error' => 'Email đã tồn tại'];
        }

        // Thêm người dùng mới
        $query = "INSERT INTO " . $this->table_name . " (Hoten, SDT, DiaChi, Email, Ngaysinh, Gioitinh) 
                 VALUES (:hoten, :sdt, :diachi, :email, :ngaysinh, :gioitinh)";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind các tham số
        $stmt->bindParam(':hoten', $hoten);
        $stmt->bindParam(':sdt', $sdt);
        $stmt->bindParam(':diachi', $diachi);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':ngaysinh', $ngaysinh);
        $stmt->bindParam(':gioitinh', $gioitinh);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return ['error' => 'Không thể thêm người dùng'];
        }
    } catch (PDOException $e) {
        return ['error' => 'Lỗi: ' . $e->getMessage()];
    }
}

public function updateUser($id, $hoten, $sdt, $diachi, $email, $ngaysinh, $gioitinh) 
{
    try {
        // Kiểm tra người dùng tồn tại
        $checkQuery = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE Manguoidung = :id";
        $checkStmt = $this->conn->prepare($checkQuery);
        $checkStmt->bindParam(':id', $id);
        $checkStmt->execute();
        $result = $checkStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result['count'] == 0) {
            return ['error' => 'Người dùng không tồn tại'];
        }

        // Kiểm tra email đã tồn tại chưa (trừ email của người dùng hiện tại)
        $checkEmailQuery = "SELECT COUNT(*) as count FROM " . $this->table_name . " WHERE Email = :email AND Manguoidung != :id";
        $checkEmailStmt = $this->conn->prepare($checkEmailQuery);
        $checkEmailStmt->bindParam(':email', $email);
        $checkEmailStmt->bindParam(':id', $id);
        $checkEmailStmt->execute();
        $emailResult = $checkEmailStmt->fetch(PDO::FETCH_ASSOC);
        
        if ($emailResult['count'] > 0) {
            return ['error' => 'Email đã tồn tại'];
        }

        // Cập nhật thông tin người dùng
        $query = "UPDATE " . $this->table_name . " 
                 SET Hoten = :hoten, 
                     SDT = :sdt, 
                     DiaChi = :diachi, 
                     Email = :email, 
                     Ngaysinh = :ngaysinh, 
                     Gioitinh = :gioitinh 
                 WHERE Manguoidung = :id";
        
        $stmt = $this->conn->prepare($query);
        
        // Bind các tham số
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':hoten', $hoten);
        $stmt->bindParam(':sdt', $sdt);
        $stmt->bindParam(':diachi', $diachi);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':ngaysinh', $ngaysinh);
        $stmt->bindParam(':gioitinh', $gioitinh);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return ['error' => 'Không thể cập nhật thông tin người dùng'];
        }
    } catch (PDOException $e) {
        return ['error' => 'Lỗi: ' . $e->getMessage()];
    }
}

public function deleteUser($id) 
{
    try {
        // 1. Kiểm tra người dùng có trong bảng `datlich` không
        $queryDatLich = "SELECT COUNT(*) FROM datlich WHERE Manguoidung = :id";
        $stmtDatLich = $this->conn->prepare($queryDatLich);
        $stmtDatLich->bindParam(':id', $id);
        $stmtDatLich->execute();
        if ($stmtDatLich->fetchColumn() > 0) {
            return ['error' => 'Không thể xóa: Người dùng này đã có lịch đặt!'];
        }

        // 2. Kiểm tra người dùng có trong bảng `hoadon_va_thanhtoan` không
        $queryHoaDon = "SELECT COUNT(*) FROM hoadon_va_thanhtoan WHERE Manguoidung = :id";
        $stmtHoaDon = $this->conn->prepare($queryHoaDon);
        $stmtHoaDon->bindParam(':id', $id);
        $stmtHoaDon->execute();
        if ($stmtHoaDon->fetchColumn() > 0) {
            return ['error' => 'Không thể xóa: Người dùng này đã có hóa đơn!'];
        }

        // 3. Kiểm tra người dùng có trong bảng `danhgia` không
        $queryDanhGia = "SELECT COUNT(*) FROM danhgia WHERE Manguoidung = :id";
        $stmtDanhGia = $this->conn->prepare($queryDanhGia);
        $stmtDanhGia->bindParam(':id', $id);
        $stmtDanhGia->execute();
        if ($stmtDanhGia->fetchColumn() > 0) {
            return ['error' => 'Không thể xóa: Người dùng này đã để lại đánh giá!'];
        }
        
        // Nếu không có liên kết nào, tiến hành xóa người dùng
        $queryDelete = "DELETE FROM " . $this->table_name . " WHERE Manguoidung = :id";
        $stmtDelete = $this->conn->prepare($queryDelete);
        $stmtDelete->bindParam(':id', $id);
        
        if ($stmtDelete->execute()) {
            // Kiểm tra xem có hàng nào thực sự bị xóa không
            if ($stmtDelete->rowCount() > 0) {
                return true;
            } else {
                return ['error' => 'Người dùng không tồn tại hoặc đã bị xóa.'];
            }
        } else {
            return ['error' => 'Không thể xóa người dùng do lỗi không xác định.'];
        }
    } catch (PDOException $e) {
        return ['error' => 'Lỗi PDO: ' . $e->getMessage()];
    }
}

} 