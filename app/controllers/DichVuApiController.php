<?php
require_once('app/config/database.php');
require_once('app/models/DichVuModel.php');
require_once('app/helpers/SessionHelper.php');

class DichVuApiController
{
    private $dichVuModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->dichVuModel = new DichVuModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        header('Content-Type: application/json');
        $dichVus = $this->dichVuModel->getDichVus();
        echo json_encode($dichVus);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $dichVu = $this->dichVuModel->getDichVuById($id);
        
        if ($dichVu) {
            echo json_encode($dichVu);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Dịch vụ không tìm thấy']);
        }
    }

    // Thêm sản phẩm mới
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

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }
        //$now = new DateTime();
        $Tendichvu = $data['Tendichvu'] ?? '';
        $Gia = $data['Gia'];
        $MoTa = $data['MoTa'] ?? '';
    
        // Kiểm tra dữ liệu cơ bản
        if (!is_string($Tendichvu) || !is_string($MoTa) ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
            return;
        }

        $result = $this->dichVuModel->addDichVu(
            $Tendichvu,
            $Gia,
            $MoTa
        );

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } elseif ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Dich vu created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $result['error'] ?? 'Thêm Dich vu thất bại']);
        }
    }

    // Cập nhật sản phẩm theo ID
    public function update($id)
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền thực hiện hành động này.']);
            return;
        }
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu hoặc ID không hợp lệ']);
            return;
        }

        $Tendichvu = $data['Tendichvu'] ?? '';
        $Gia = $data['Gia'];
        $MoTa = $data['MoTa'] ?? '';

        if (!is_string($Tendichvu) || !is_string($MoTa) ) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
            return;
        }


        $result = $this->dichVuModel->updateDichVu(
            $id,
            $Tendichvu,
            $Gia,
            $MoTa
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
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền thực hiện hành động này.']);
            return;
        }
        header('Content-Type: application/json');
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID không hợp lệ']);
            return;
        }

        $result = $this->dichVuModel->deleteDichVu($id);
        if ($result === true) {
            echo json_encode(['message' => 'Xóa dịch vụ thành công']);
        } elseif (is_array($result) && isset($result['error'])) {
            http_response_code(400); // Bad Request
            echo json_encode($result);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Xóa dịch vụ thất bại do lỗi không xác định.']);
        }
    }
}