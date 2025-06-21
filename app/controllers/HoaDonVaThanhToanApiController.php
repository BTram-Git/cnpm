<?php
require_once('app/config/database.php');
require_once('app/models/HoaDonVaThanhToanModel.php');
require_once('app/helpers/SessionHelper.php');

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
        SessionHelper::start();
        header('Content-Type: application/json');

        if (SessionHelper::isAdmin()) {
            $hoaDons = $this->hoaDonVaThanhToanModel->getHoaDonVaThanhToans();
        } elseif (SessionHelper::isLoggedIn()) {
            $userId = SessionHelper::getUserId();
            $hoaDons = $this->hoaDonVaThanhToanModel->getHoaDonByUserId($userId);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Vui lòng đăng nhập để xem hóa đơn.']);
            return;
        }
        echo json_encode($hoaDons);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        SessionHelper::start();
        header('Content-Type: application/json');
        
        $hoaDon = $this->hoaDonVaThanhToanModel->getHoaDonVaThanhToanById($id);
        
        if ($hoaDon) {
            if (SessionHelper::isAdmin() || (SessionHelper::isLoggedIn() && SessionHelper::getUserId() == $hoaDon->Manguoidung)) {
                echo json_encode($hoaDon);
            } else {
                http_response_code(403);
                echo json_encode(['error' => 'Bạn không có quyền xem hóa đơn này.']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Hóa đơn không tìm thấy']);
        }
    }

    // Thêm mới hóa đơn và thanh toán
    public function store()
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền tạo hóa đơn.']);
            return;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }
        $NgayThanhToan = $data['NgayThanhToan'] ?? null;
        $Tongtien = $data['Tongtien'] ?? null;
        $MaDL = $data['MaDL'] ?? null;
        $Manguoidung = $data['Manguoidung'] ?? null;
        $Maphong = $data['Maphong'] ?? null;
        $MaPT = $data['MaPT'] ?? null;
        $Matrangthai = $data['Matrangthai'] ?? null;
        if (!$NgayThanhToan || !$Tongtien || !$Manguoidung || !$MaDL|| !$Maphong || !$MaPT || !$Matrangthai) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập đầy đủ thông tin']);
            return;
        }
        $result = $this->hoaDonVaThanhToanModel->addHoaDonVaThanhToan(
            $NgayThanhToan, 
            $Tongtien,
            $MaDL,
             $Manguoidung,
             $Maphong,
              $MaPT, 
             $Matrangthai)
             ;
             if (is_array($result)) {
                http_response_code(400);
                echo json_encode(['errors' => $result]);
            } elseif ($result === true) {
                http_response_code(201);
                echo json_encode(['message' => 'Hóa đơn được thêm thành công']);
            } else {
                http_response_code(500);
                echo json_encode(['error' => $result['error'] ?? 'Thêm hóa đơn thất bại']);
            }
    }

    // Cập nhật hóa đơn và thanh toán
    public function update($id)
    {
        SessionHelper::start();
        header('Content-Type: application/json');

        $hoaDon = $this->hoaDonVaThanhToanModel->getHoaDonVaThanhToanById($id);

        if (!$hoaDon) {
            http_response_code(404);
            echo json_encode(['message' => 'Hóa đơn không tìm thấy']);
            return;
        }

        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền cập nhật hóa đơn này.']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);
        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $NgayThanhToan = $data['NgayThanhToan'] ?? $hoaDon->NgayThanhToan;
        $Tongtien = $data['Tongtien'] ?? $hoaDon->Tongtien;
        $MaPT = $data['MaPT'] ?? $hoaDon->MaPT;
        $Matrangthai = $data['Matrangthai'] ?? $hoaDon->Matrangthai;

        $result = $this->hoaDonVaThanhToanModel->updateHoaDonVaThanhToan($id, $NgayThanhToan, $Tongtien, $MaPT, $Matrangthai);
        if ($result === true) {
            echo json_encode(['message' => 'Cập nhật hóa đơn thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Cập nhật hóa đơn thất bại']);
        }
    }

    public function destroy($id)
    {
        SessionHelper::start();
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền xóa hóa đơn này.']);
            return;
        }

        header('Content-Type: application/json');
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID không hợp lệ']);
            return;
        }
        $result = $this->hoaDonVaThanhToanModel->deleteHoaDonVaThanhToan($id);
        if ($result === true) {
            echo json_encode(['message' => 'Xóa hóa đơn thành công']);
        } elseif (is_array($result) && isset($result['error'])) {
            http_response_code(400);
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Xóa hóa đơn thất bại']);
        }
    }

}