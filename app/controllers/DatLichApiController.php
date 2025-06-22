<?php
require_once('app/config/database.php');
require_once('app/models/DatLichModel.php');
require_once('app/helpers/SessionHelper.php');

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
        SessionHelper::start();
        header('Content-Type: application/json');

        if (SessionHelper::isAdmin()) {
            // Admin thấy tất cả
            $datLichs = $this->datLichModel->getDatLich();
        } elseif (SessionHelper::isLoggedIn()) {
            // User thấy của mình
            $userId = SessionHelper::getUserId();
            $datLichs = $this->datLichModel->getDatLichByUserId($userId);
        } else {
            // Chưa đăng nhập
            http_response_code(401);
            echo json_encode(['error' => 'Vui lòng đăng nhập để xem lịch đặt.']);
            return;
        }
       
        echo json_encode($datLichs);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        SessionHelper::start();
        header('Content-Type: application/json');
        
        $datLich = $this->datLichModel->getDatLichById($id);
        
        if ($datLich) {
            // Kiểm tra quyền
            if (SessionHelper::isAdmin() || (SessionHelper::isLoggedIn() && SessionHelper::getUserId() == $datLich->Manguoidung)) {
                echo json_encode($datLich);
            } else {
                http_response_code(403);
                echo json_encode(['error' => 'Bạn không có quyền xem lịch đặt này.']);
            }
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Lịch không tìm thấy']);
        }
    }

    // Thêm sản phẩm mới
    public function store()
    {
        SessionHelper::start();
        if (!SessionHelper::isLoggedIn()) {
            http_response_code(401);
            echo json_encode(['error' => 'Vui lòng đăng nhập để đặt lịch.']);
            return;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        // Dòng code debug: Dừng và in ra nội dung của $data
        // die(json_encode($data));

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $Manguoidung = SessionHelper::getUserId(); // Lấy ID người dùng từ session
        $Thoigiandatlich = $data['Thoigiandatlich'] ?? date('Y-m-d H:i:s');
        $Trangthai = $data['Trangthai_'] ?? 'Chờ xác nhận';
    
        $result = $this->datLichModel->addDatLich(
            $Manguoidung,
            $Thoigiandatlich,
            $Trangthai
        );

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Đặt lịch thành công']);
        } elseif (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Đặt lịch thất bại do lỗi máy chủ.']);
        }
    }

    // Cập nhật sản phẩm theo ID
    public function update($id)
    {
        SessionHelper::start();
        header('Content-Type: application/json');

        $datLich = $this->datLichModel->getDatLichById($id);

        if (!$datLich) {
            http_response_code(404);
            echo json_encode(['message' => 'Lịch không tìm thấy']);
            return;
        }

        // Kiểm tra quyền
        if (!SessionHelper::isAdmin() && !(SessionHelper::isLoggedIn() && SessionHelper::getUserId() == $datLich->Manguoidung)) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền sửa lịch đặt này.']);
            return;
        }

        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu hoặc ID không hợp lệ']);
            return;
        }

        // Admin có thể cập nhật cả trạng thái và người dùng, user thì không
        $Manguoidung = $datLich->Manguoidung; // Giữ nguyên người dùng cũ
        if (SessionHelper::isAdmin() && isset($data['Manguoidung'])) {
            $Manguoidung = $data['Manguoidung']; // Admin có thể đổi người dùng
        }
        $Thoigiandatlich = $data['Thoigiandatlich'] ?? $datLich->Thoigiandatlich;
        $Trangthai = $datLich->Trangthai_; // Giữ nguyên trạng thái cũ
        if (SessionHelper::isAdmin() && isset($data['Trangthai_'])) {
             $Trangthai = $data['Trangthai_']; // Admin có thể đổi trạng thái
        }

        $result = $this->datLichModel->updateDatLich(
            $id,
            $Manguoidung,
            $Thoigiandatlich,
            $Trangthai
        );

        if ($result > 0) {
            echo json_encode(['message' => 'Cập nhật đặt lịch thành công.']);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy lịch đặt để cập nhật hoặc dữ liệu không thay đổi.']);
        }
    }

    // Xóa sản phẩm theo ID
    public function destroy($id)
    {
        SessionHelper::start();
        header('Content-Type: application/json');
        
        $datLich = $this->datLichModel->getDatLichById($id);

        if (!$datLich) {
            http_response_code(404);
            echo json_encode(['message' => 'Lịch không tìm thấy']);
            return;
        }

        // Kiểm tra quyền
        if (!SessionHelper::isAdmin() && !(SessionHelper::isLoggedIn() && SessionHelper::getUserId() == $datLich->Manguoidung)) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền xóa lịch đặt này.']);
            return;
        }
        
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID không hợp lệ']);
            return;
        }

        $result = $this->datLichModel->deleteDatLich($id);
        if ($result === true) {
            echo json_encode(['message' => 'Xóa đặt lịch thành công']);
        } elseif (is_array($result) && isset($result['error'])) {
            http_response_code(400); // Bad Request
            echo json_encode($result);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Xóa đặt lịch thất bại do lỗi không xác định.']);
        }
    }
}