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
    $query = "DELETE FROM " . $this->table_name . " WHERE Matrangthai = :Matrangthai";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':Matrangthai', $Matrangthai);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

} 