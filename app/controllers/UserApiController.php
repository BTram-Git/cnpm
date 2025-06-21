<?php
require_once('app/config/database.php');

require_once('app/models/UserModel.php');
require_once('app/helpers/SessionHelper.php');

class UserApiController
{
    private $userModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->userModel = new UserModel($this->db);
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
        $users = $this->userModel->getUsers();
        echo json_encode($users);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $user = $this->userModel->getUserById($id);
        
        if ($user) {
            echo json_encode($user);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Người dùng không tìm thấy']);
        }
    }

    // Đăng ký người dùng mới
    public function register()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        // Lấy dữ liệu từ request
        $hoten = $data['hoten'] ?? '';
        $sdt = $data['sdt'] ?? '';
        $diachi = $data['diachi'] ?? '';
        $email = $data['email'] ?? '';
        $ngaysinh = $data['ngaysinh'] ?? '';
        $gioitinh = $data['gioitinh'] ?? '';
        $matkhau = $data['matkhau'] ?? '';

        // Kiểm tra dữ liệu bắt buộc
        if (empty($hoten) || empty($email) || empty($matkhau)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng điền đầy đủ họ tên, email và mật khẩu.']);
            return;
        }

        // Kiểm tra định dạng email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email không hợp lệ']);
            return;
        }

        // Kiểm tra định dạng số điện thoại (nếu có)
        if (!empty($sdt) && !preg_match('/^[0-9]{10,11}$/', $sdt)) {
            http_response_code(400);
            echo json_encode(['error' => 'Số điện thoại không hợp lệ']);
            return;
        }

        // Thêm người dùng mới
        $result = $this->userModel->registerUser(
            $hoten,
            $sdt,
            $diachi,
            $email,
            $ngaysinh,
            $gioitinh,
            $matkhau
        );

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Đăng ký thành công']);
        } else {
            http_response_code(400);
            echo json_encode($result);
        }
    }

    // Đăng nhập
    public function login()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $email = $data['email'] ?? '';
        $matkhau = $data['matkhau'] ?? '';

        if (empty($email) || empty($matkhau)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng nhập email và mật khẩu.']);
            return;
        }

        $user = $this->userModel->loginUser($email, $matkhau);

        if ($user && !isset($user['error'])) {
            // SessionHelper::start();
            // $_SESSION['user_id'] = $user['Manguoidung'];
            // $_SESSION['username'] = $user['Hoten'];
            // $_SESSION['user_email'] = $user['Email'];
            // $_SESSION['role'] = $user['Vaitro']; // Lấy vai trò từ DB

            echo json_encode([
                'message' => 'Đăng nhập thành công',
                'user' => $user
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Email hoặc mật khẩu không đúng.']);
        }
    }
    
    // Đăng xuất
    public function logout() {
        header('Content-Type: application/json');
        SessionHelper::start();
        session_destroy();
        echo json_encode(['message' => 'Đăng xuất thành công']);
    }

    // Cập nhật thông tin người dùng
    public function update($id)
    {
        SessionHelper::start();
        // 1. Kiểm tra đăng nhập
        if (!SessionHelper::isLoggedIn()) {
            http_response_code(401); // Unauthorized
            echo json_encode(['error' => 'Vui lòng đăng nhập.']);
            return;
        }

        // 2. Kiểm tra quyền
        $loggedInUserId = SessionHelper::getUserId();
        if (!SessionHelper::isAdmin() && $loggedInUserId != $id) {
            http_response_code(403); // Forbidden
            echo json_encode(['error' => 'Bạn không có quyền cập nhật thông tin của người dùng này.']);
            return;
        }

        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data) || !is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu hoặc ID không hợp lệ']);
            return;
        }

        // Lấy dữ liệu từ request
        $hoten = $data['hoten'] ?? '';
        $sdt = $data['sdt'] ?? '';
        $diachi = $data['diachi'] ?? '';
        $email = $data['email'] ?? '';
        $ngaysinh = $data['ngaysinh'] ?? '';
        $gioitinh = $data['gioitinh'] ?? '';
        $matkhau = $data['matkhau'] ?? null; // Mật khẩu có thể không được cung cấp

        // Kiểm tra dữ liệu bắt buộc
        if (empty($hoten) || empty($sdt) || empty($email)) {
            http_response_code(400);
            echo json_encode(['error' => 'Vui lòng điền đầy đủ thông tin bắt buộc']);
            return;
        }

        // Kiểm tra định dạng email
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            http_response_code(400);
            echo json_encode(['error' => 'Email không hợp lệ']);
            return;
        }

        // Kiểm tra định dạng số điện thoại
        if (!preg_match('/^[0-9]{10,11}$/', $sdt)) {
            http_response_code(400);
            echo json_encode(['error' => 'Số điện thoại không hợp lệ']);
            return;
        }

        // Cập nhật thông tin người dùng
        $result = $this->userModel->updateUser(
            $id,
            $hoten,
            $sdt,
            $diachi,
            $email,
            $ngaysinh,
            $gioitinh,
            $matkhau
        );

        if ($result === true) {
            echo json_encode([
                'message' => 'Cập nhật thông tin thành công',
                'data' => [
                    'id' => $id,
                    'hoten' => $hoten,
                    'sdt' => $sdt,
                    'email' => $email
                ]
            ]);
        } else {
            http_response_code(400);
            echo json_encode($result);
        }
    }

    // Xóa người dùng
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

        $result = $this->userModel->deleteUser($id);

        if ($result === true) {
            http_response_code(200);
            echo json_encode(['message' => 'Xóa người dùng thành công']);
        } elseif (is_array($result) && isset($result['error'])) {
            http_response_code(400); // Bad Request
            echo json_encode($result);
        } else {
            http_response_code(500); // Internal Server Error
            echo json_encode(['error' => 'Xóa người dùng thất bại do lỗi không xác định.']);
        }
    }
}