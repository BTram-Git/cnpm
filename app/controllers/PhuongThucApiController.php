<?php
require_once('app/config/database.php');

require_once('app/models/PhuongThucModel.php');

class PhuongThucApiController
{
    private $phuongThucModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->phuongThucModel = new PhuongThucModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        header('Content-Type: application/json');
        $phuongThucs = $this->phuongThucModel->getPhuongThucs();
        echo json_encode($phuongThucs);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $phuongThuc = $this->phuongThucModel->getPhuongThucById($id);
        
        if ($phuongThuc) {
            echo json_encode($phuongThuc);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Phương thức thanh toán không tìm thấy']);
        }
    }

    // Thêm phương thức mới
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $TenPT = $data['TenPT'] ?? '';
        $Mota = $data['Mota'] ?? '';

        if (empty($TenPT) || empty($Mota)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }

        $result = $this->phuongThucModel->addPhuongThuc($TenPT, $Mota);

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Thêm phương thức thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Thêm phương thức thất bại']);
        }
    }

    // Cập nhật phương thức theo ID
    public function update($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu hoặc ID không hợp lệ']);
            return;
        }

        $TenPT = $data['TenPT'] ?? '';
        $Mota = $data['Mota'] ?? '';

        if (empty($TenPT) || empty($Mota)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }

        $result = $this->phuongThucModel->updatePhuongThuc($id, $TenPT, $Mota);

        if ($result === true) {
            echo json_encode(['message' => 'Cập nhật phương thức thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Cập nhật phương thức thất bại']);
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

        $result = $this->phuongThucModel->deletePhuongThuc($id);
        if ($result) {
            echo json_encode(['message' => 'Xóa phương thức thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Xóa phương thức thất bại']);
        }
    }
}