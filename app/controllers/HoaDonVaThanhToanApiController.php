<?php
require_once('app/config/database.php');

require_once('app/models/HoaDonVaThanhToanModel.php');


class HoaDonVaThanhToanApiController
{
    private $hoaDonVaThanhToanModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->hoaDonVaThanhToanModel = new HoaDonVaThanhToanModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        header('Content-Type: application/json');
        $hoaDonVaThanhToans = $this->hoaDonVaThanhToanModel->getHoaDonVaThanhToans();
        echo json_encode($hoaDonVaThanhToans);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $hoaDonVaThanhToan = $this->hoaDonVaThanhToanModel->getHoaDonVaThanhToanById($id);
        
        if ($hoaDonVaThanhToan) {
            echo json_encode($hoaDonVaThanhToan);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Hóa đơn không tìm thấy']);
        }
    }

}