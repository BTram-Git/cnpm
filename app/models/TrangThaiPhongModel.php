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
    $query = "DELETE FROM " . $this->table_name . " WHERE MatrangthaiP = :MatrangthaiP";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':MatrangthaiP', $MatrangthaiP);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

} 