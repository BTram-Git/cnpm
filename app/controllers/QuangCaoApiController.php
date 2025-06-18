<?php
require_once('app/config/database.php');
require_once('app/models/QuangCaoModel.php');

class QuangCaoApiController
{
    private $quangCaoModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->quangCaoModel = new QuangCaoModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        header('Content-Type: application/json');
        $quangCaos = $this->quangCaoModel->getQuangCaos();
        echo json_encode($quangCaos);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $quangCao = $this->quangCaoModel->getQuangCaoById($id);
        
        if ($quangCao) {
            echo json_encode($quangCao);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Quảng cáo không tìm thấy']);
        }
    }

    // Thêm quảng cáo mới
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $Tieude = $data['Tieude'] ?? '';
        $Noidung = $data['Noidung'] ?? '';
        $Loaiquangcao = $data['Loaiquangcao'] ?? '';
        $Image = $data['Image'] ?? '';
        $Ngaybatdau = $data['Ngaybatdau'] ?? '';
        $Ngayketthuc = $data['Ngayketthuc'] ?? '';
        $Manguoidung = $data['Manguoidung'] ?? '';

        if (empty($Tieude) || empty($Noidung) || empty($Loaiquangcao) || empty($Image) || empty($Ngaybatdau) || empty($Ngayketthuc) || empty($Manguoidung)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }

        $result = $this->quangCaoModel->addQuangCao($Tieude, $Noidung, $Loaiquangcao, $Image, $Ngaybatdau, $Ngayketthuc, $Manguoidung);

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Thêm quảng cáo thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Thêm quảng cáo thất bại']);
        }
    }

    // Cập nhật quảng cáo theo ID
    public function update($id)
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu hoặc ID không hợp lệ']);
            return;
        }

        $Tieude = $data['Tieude'] ?? '';
        $Noidung = $data['Noidung'] ?? '';
        $Loaiquangcao = $data['Loaiquangcao'] ?? '';
        $Image = $data['Image'] ?? '';
        $Ngaybatdau = $data['Ngaybatdau'] ?? '';
        $Ngayketthuc = $data['Ngayketthuc'] ?? '';
        $Manguoidung = $data['Manguoidung'] ?? '';

        if (empty($Tieude) || empty($Noidung) || empty($Loaiquangcao) || empty($Image) || empty($Ngaybatdau) || empty($Ngayketthuc) || empty($Manguoidung)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }

        $result = $this->quangCaoModel->updateQuangCao($id, $Tieude, $Noidung, $Loaiquangcao, $Image, $Ngaybatdau, $Ngayketthuc, $Manguoidung);

        if ($result === true) {
            echo json_encode(['message' => 'Cập nhật quảng cáo thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Cập nhật quảng cáo thất bại']);
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

        $result = $this->quangCaoModel->deleteQuangCao($id);
        if ($result) {
            echo json_encode(['message' => 'Xóa quảng cáo thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Xóa quảng cáo thất bại']);
        }
    }
}