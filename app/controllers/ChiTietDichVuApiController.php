<?php
require_once('app/config/database.php');

require_once('app/models/ChiTietDichVuModel.php');
require_once('app/helpers/SessionHelper.php');

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
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền thực hiện hành động này.']);
            return;
        }
        header('Content-Type: application/json');
        $chiTietDichVus = $this->chiTietDichVuModel->getChiTietDichVus();
        echo json_encode($chiTietDichVus);
    }

    // Lấy thông tin chi tiết dịch vụ theo MaDL
    public function show($id)
    {
        header('Content-Type: application/json');
        $chiTietDichVu = $this->chiTietDichVuModel->getChiTietDichVuByMaDL($id);
        
        if ($chiTietDichVu) {
            echo json_encode($chiTietDichVu);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Chi tiết dịch vụ không tìm thấy']);
        }
    }

    // Thêm chi tiết dịch vụ
    public function store()
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền thực hiện hành động này.']);
            return;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data || !isset($data['MaDL']) || !isset($data['MaDV'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ. Vui lòng cung cấp MaDL và MaDV.']);
            return;
        }
        
        $result = $this->chiTietDichVuModel->addChiTietDichVu($data['MaDL'], $data['MaDV']);

        if ($result === true) {
            http_response_code(201); // Created
            echo json_encode(['message' => 'Chi tiết dịch vụ đã được thêm thành công.']);
        } elseif (is_array($result) && isset($result['error'])) {
            http_response_code(400); // Bad Request (e.g., foreign key violation)
            echo json_encode($result);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Lỗi máy chủ khi thêm chi tiết dịch vụ.']);
        }
    }

    // Xóa sản phẩm theo ID, với MaDL từ URL và MaDV từ Body
    public function destroy($MaDL)
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền thực hiện hành động này.']);
            return;
        }

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