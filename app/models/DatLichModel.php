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
public function getDatLichById($id) 
{ 
    $query = "SELECT dl.MaDL, dl.Manguoidung, dl.Thoigiandatlich, dl.Trangthai_ 
    FROM " . $this->table_name . " dl 
    WHERE dl.MaDL = 1";

$stmt = $this->conn->prepare($query); 
//$stmt->bindParam(':id', $id); 
$stmt->execute(); 
$result = $stmt->fetch(PDO::FETCH_OBJ);
return $result;

}
// Thêm mới danh mục
public function addDatLich($Manguoidung, $Thoigiandatlich,$Trangthai)
{
    $errors = [];

    if (empty($Manguoidung)) {
        $errors['Manguoidung'] = 'Ma nguoi không được để trống';
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
    $query = "INSERT INTO " . $this->table_name . " ( Manguoidung,Thoigiandatlich, Trangthai_) 
    VALUES (:Manguoidung, :Thoigiandatlich,:Trangthai)";
    $stmt = $this->conn->prepare($query);

    $Manguoidung = htmlspecialchars(strip_tags($Manguoidung));
    $Thoigiandatlich = (new DateTime())->format('Y-m-d H:i:s');
    $Trangthai = htmlspecialchars(strip_tags($Trangthai));
    
    $stmt->bindParam(':Manguoidung', $Manguoidung);
    $stmt->bindParam(':Thoigiandatlich', $Thoigiandatlich);
    $stmt->bindParam(':Trangthai', $Trangthai);

    if ($stmt->execute()) {
        return true;
    }

    return false;
}

public function updateDatLich($id, $Manguoidung, $Thoigiandatlich,$Trangthai)
{
    $query = "UPDATE " . $this->table_name . " SET Manguoidung = :Manguoidung, Thoigiandatlich = :Thoigiandatlich,Trangthai_ = :Trangthai  WHERE MaDL = :id";
    $stmt = $this->conn->prepare($query);


    $Manguoidung = htmlspecialchars(strip_tags($Manguoidung));
    $Thoigiandatlich = (new DateTime())->format('Y-m-d H:i:s');
    $Trangthai = htmlspecialchars(strip_tags($Trangthai));

    $stmt->bindParam(':id', $id);
    $stmt->bindParam(':Manguoidung', $Manguoidung);
    $stmt->bindParam(':Thoigiandatlich', $Thoigiandatlich);
    $stmt->bindParam(':Trangthai', $Trangthai);
    if ($stmt->execute()) {
        return true;
    }

    return false;
}

public function deleteDatLich($MaDL)
{
    $query = "DELETE FROM " . $this->table_name . " WHERE MaDL = :MaDL";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':MaDL', $MaDL);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

}