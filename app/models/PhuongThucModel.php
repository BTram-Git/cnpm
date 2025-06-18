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
    $query = "DELETE FROM " . $this->table_name . " WHERE MaPT = :MaPT";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':MaPT', $MaPT);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

} 