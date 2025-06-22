<?php
require_once('app/config/database.php');
require_once('app/models/PhongModel.php');
require_once('app/helpers/SessionHelper.php');

class PhongApiController
{
    private $phongModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->phongModel = new PhongModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'Bạn không có quyền truy cập chức năng này.']);
            return;
        }
        header('Content-Type: application/json');
        $phongs = $this->phongModel->getPhongs();
        echo json_encode($phongs);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $phong = $this->phongModel->getPhongById($id);
        
        if ($phong) {
            echo json_encode($phong);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Phòng không tìm thấy']);
        }
    }

    // Thêm phòng mới
    public function store()
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403); // Forbidden
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

        $Tenphong = $data['Tenphong'] ?? '';
        $Loaiphong = $data['Loaiphong'] ?? '';
        $MatrangthaiP = $data['MatrangthaiP'] ?? '';

        if (empty($Tenphong) || empty($Loaiphong) || empty($MatrangthaiP)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }

        $result = $this->phongModel->addPhong($Tenphong, $Loaiphong, $MatrangthaiP);

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Thêm phòng thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Thêm phòng thất bại']);
        }
    }

    // Cập nhật phòng theo ID
    public function update($id)
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403); // Forbidden
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

        $Tenphong = $data['Tenphong'] ?? '';
        $Loaiphong = $data['Loaiphong'] ?? '';
        $MatrangthaiP = $data['MatrangthaiP'] ?? '';

        if (empty($Tenphong) || empty($Loaiphong) || empty($MatrangthaiP)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }

        $result = $this->phongModel->updatePhong($id, $Tenphong, $Loaiphong, $MatrangthaiP);

        if ($result === true) {
            echo json_encode(['message' => 'Cập nhật phòng thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Cập nhật phòng thất bại']);
        }
    }

    // Xóa sản phẩm theo ID
    public function destroy($id)
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'Bạn không có quyền thực hiện hành động này.']);
            return;
        }

        header('Content-Type: application/json');
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID không hợp lệ']);
            return;
        }
        $result = $this->phongModel->deletePhong($id);
        if ($result === true) {
            echo json_encode(['message' => 'Xóa phòng thành công']);
        } elseif (is_array($result) && isset($result['error'])) {
            http_response_code(400);
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Xóa phòng thất bại']);
        }
    }
}