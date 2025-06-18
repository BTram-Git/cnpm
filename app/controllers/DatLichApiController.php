<?php
require_once('app/config/database.php');
require_once('app/models/DatLichModel.php');

class DatLichApiController
{
    private $datLichModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->datLichModel = new DatLichModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        header('Content-Type: application/json');
        $datLichs = $this->datLichModel->getDatLich();
       
        echo json_encode($datLichs);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $datLich = $this->datLichModel->getDatLichById($id);
        
        if ($datLich) {
            echo json_encode($datLich);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Lịch không tìm thấy']);
        }
    }

    // Thêm sản phẩm mới
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }
        //$now = new DateTime();
        $Manguoidung = $data['Manguoidung'] ?? '';
        $Thoigiandatlich = $data['Thoigiandatlich'];
        $Trangthai = $data['Trangthai'] ?? '';
    
        // Kiểm tra dữ liệu cơ bản
        if (!is_string($Manguoidung) || !is_string($Trangthai) ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
            return;
        }

        $result = $this->datLichModel->addDatLich(
            $Manguoidung,
            $Thoigiandatlich,
            $Trangthai
        );

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } elseif ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Dat lich created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $result['error'] ?? 'Thêm Dat lich thất bại']);
        }
    }

    // Cập nhật sản phẩm theo ID
    public function update($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu hoặc ID không hợp lệ']);
            return;
        }

        $Manguoidung = $data['Manguoidung'] ?? '';
        $Thoigiandatlich = $data['Thoigiandatlich'];
        $Trangthai = $data['Trangthai'] ?? '';

        if (!is_string($Manguoidung) || !is_string($Trangthai) ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
            return;
        }


        $result = $this->datLichModel->updateDatLich(
            $id,
            $Manguoidung,
            $Thoigiandatlich,
            $Trangthai
        );

        if ($result) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product update failed']);
        }
    }

    // Xóa sản phẩm theo ID
    public function destroy($id)
    {
        header('Content-Type: application/json');
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID không hợp lệ']);
            return;
        }

        $result = $this->datLichModel->deleteDatLich($id);
        if ($result) {
            echo json_encode(['message' => 'Xóa đặt lịch thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Xóa đặt lịch thất bại']);
        }
    }
}