<?php
require_once('app/config/database.php');

require_once('app/models/UserModel.php');

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

    // Thêm người dùng mới
    public function store()
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

        // Thêm người dùng mới
        $result = $this->userModel->addUser(
            $hoten,
            $sdt,
            $diachi,
            $email,
            $ngaysinh,
            $gioitinh
        );

        if ($result === true) {
            http_response_code(201);
            echo json_encode([
                'message' => 'Thêm người dùng thành công',
                'data' => [
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

    // Cập nhật thông tin người dùng
    public function update($id)
    {
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
            $gioitinh
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
    public function deleteUser($id)
    {
        header('Content-Type: application/json');
        
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID không hợp lệ']);
            return;
        }

        $result = $this->userModel->deleteUser($id);

        if ($result === true) {
            echo json_encode([
                'message' => 'Xóa người dùng thành công',
                'id' => $id
            ]);
        } else {
            http_response_code(400);
            echo json_encode($result);
        }
    }
}