<?php
require_once('app/config/database.php');
require_once('app/models/TrangThaiModel.php');
require_once('app/helpers/SessionHelper.php');

class TrangThaiApiController
{
    private $trangThaiModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->trangThaiModel = new TrangThaiModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        header('Content-Type: application/json');
        $trangThais = $this->trangThaiModel->getTrangThais();
        echo json_encode($trangThais);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $trangThai = $this->trangThaiModel->getTrangThaiById($id);
        
        if ($trangThai) {
            echo json_encode($trangThai);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Trạng thái không tìm thấy']);
        }
    }

    // Thêm trạng thái mới
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

        $Tentrangthai = $data['Tentrangthai'] ?? '';

        if (empty($Tentrangthai)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập tên trạng thái']);
            return;
        }

        $result = $this->trangThaiModel->addTrangThai($Tentrangthai);

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Thêm trạng thái thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Thêm trạng thái thất bại']);
        }
    }

    // Cập nhật trạng thái theo ID
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

        $Tentrangthai = $data['Tentrangthai'] ?? '';

        if (empty($Tentrangthai)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập tên trạng thái']);
            return;
        }

        $result = $this->trangThaiModel->updateTrangThai($id, $Tentrangthai);

        if ($result === true) {
            echo json_encode(['message' => 'Cập nhật trạng thái thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Cập nhật trạng thái thất bại']);
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

        $result = $this->trangThaiModel->deleteTrangThai($id);
        if ($result === true) {
            echo json_encode(['message' => 'Xóa trạng thái thành công']);
        } elseif (is_array($result) && isset($result['error'])) {
            http_response_code(400); // Bad Request
            echo json_encode($result);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Xóa trạng thái thất bại do lỗi không xác định.']);
        }
    }
}