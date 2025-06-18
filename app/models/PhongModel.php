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
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

public function deletePhong($Maphong)
{
    $query = "DELETE FROM " . $this->table_name . " WHERE Maphong = :Maphong";
    $stmt = $this->conn->prepare($query);
    $stmt->bindParam(':Maphong', $Maphong);
    if ($stmt->execute()) {
        return true;
    }
    return false;
}

} 