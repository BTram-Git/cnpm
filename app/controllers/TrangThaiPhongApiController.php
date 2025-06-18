<?php
require_once('app/config/database.php');
require_once('app/models/TrangThaiPhongModel.php');

class TrangThaiPhongApiController
{
    private $trangThaiPhongModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->trangThaiPhongModel = new TrangThaiPhongModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        header('Content-Type: application/json');
        $trangThaiPhongs = $this->trangThaiPhongModel->getTrangThaiPhongs();
        echo json_encode($trangThaiPhongs);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $trangThaiPhong = $this->trangThaiPhongModel->getTrangThaiPhongById($id);
        
        if ($trangThaiPhong) {
            echo json_encode($trangThaiPhong);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Trạng thái phòng không tìm thấy']);
        }
    }

    // Thêm trạng thái phòng mới
    public function store()
    {
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
            echo json_encode(['error' => 'Vui lòng nhập tên trạng thái phòng']);
            return;
        }

        $result = $this->trangThaiPhongModel->addTrangThaiPhong($Tentrangthai);

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Thêm trạng thái phòng thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Thêm trạng thái phòng thất bại']);
        }
    }

    // Cập nhật trạng thái phòng theo ID
    public function update($id)
    {
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
            echo json_encode(['error' => 'Vui lòng nhập tên trạng thái phòng']);
            return;
        }

        $result = $this->trangThaiPhongModel->updateTrangThaiPhong($id, $Tentrangthai);

        if ($result === true) {
            echo json_encode(['message' => 'Cập nhật trạng thái phòng thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Cập nhật trạng thái phòng thất bại']);
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

        $result = $this->trangThaiPhongModel->deleteTrangThaiPhong($id);
        if ($result) {
            echo json_encode(['message' => 'Xóa trạng thái phòng thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Xóa trạng thái phòng thất bại']);
        }
    }
}