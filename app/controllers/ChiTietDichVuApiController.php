<?php
require_once('app/config/database.php');

require_once('app/models/ChiTietDichVuModel.php');

class ChiTietDichVuApiController
{
    private $chiTietDichVuModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->chiTietDichVuModel = new ChiTietDichVuModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        header('Content-Type: application/json');
        $chiTietDichVus = $this->chiTietDichVuModel->getChiTietDichVus();
        echo json_encode($chiTietDichVus);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $chiTietDichVu = $this->chiTietDichVuModel->getChiTietDichVuById($id);
        
        if ($chiTietDichVu) {
            echo json_encode($chiTietDichVu);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Chi tiết dịch vụ không tìm thấy']);
        }
    }

    // Thêm chi tiết dịch vụ mới
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $MaDL = $data['MaDL'] ?? '';
        $MaDV = $data['MaDV'] ?? '';

        if (empty($MaDL) || empty($MaDV)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }

        $result = $this->chiTietDichVuModel->addChiTietDichVu($MaDL, $MaDV);

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Thêm chi tiết dịch vụ thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Thêm chi tiết dịch vụ thất bại']);
        }
    }

    // Cập nhật chi tiết dịch vụ theo MaDL
    public function update($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || empty($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu hoặc ID không hợp lệ']);
            return;
        }

        $MaDV = $data['MaDV'] ?? '';

        if (empty($MaDV)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }

        $result = $this->chiTietDichVuModel->updateChiTietDichVu($id, $MaDV);

        if ($result === true) {
            echo json_encode(['message' => 'Cập nhật chi tiết dịch vụ thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Cập nhật chi tiết dịch vụ thất bại']);
        }
    }

    // Xóa sản phẩm theo ID, với MaDL từ URL và MaDV từ Body
    public function destroy($MaDL)
    {
        header('Content-Type: application/json');

        // Lấy MaDV từ body
        $data = json_decode(file_get_contents("php://input"), true);
        $MaDV = $data['MaDV'] ?? null;

        // Kiểm tra xem đã có đủ cả 2 key chưa
        if (empty($MaDL) || empty($MaDV)) {
            http_response_code(400);
            echo json_encode(['error' => 'Thiếu MaDL trong URL hoặc MaDV trong body của request.']);
            return;
        }

        $result = $this->chiTietDichVuModel->deleteChiTietDichVu($MaDL, $MaDV);
        
        if ($result === true) {
            echo json_encode(['message' => 'Xóa chi tiết dịch vụ thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Xóa chi tiết dịch vụ thất bại. Có thể do bản ghi không tồn tại.']);
        }
    }
}