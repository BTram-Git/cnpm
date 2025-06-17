<?php
require_once('app/config/database.php');
require_once('app/models/AccountModel.php');

class AccountApiController
{
    private $accountModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->accountModel = new AccountModel($this->db);
        session_start();
    }

    // API: Đăng ký tài khoản
    public function register()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $username = $data['username'] ?? '';
        $fullName = $data['fullname'] ?? '';
        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirmpassword'] ?? '';
        $role = in_array($data['role'] ?? 'user', ['admin', 'user']) ? $data['role'] : 'user';

        $errors = [];
        if (empty($username)) $errors['username'] = "Vui lòng nhập username!";
        if (empty($fullName)) $errors['fullname'] = "Vui lòng nhập fullname!";
        if (empty($password)) $errors['password'] = "Vui lòng nhập password!";
        if ($password !== $confirmPassword) $errors['confirmpassword'] = "Mật khẩu không khớp!";
        if ($this->accountModel->getAccountByUsername($username)) {
            $errors['account'] = "Tài khoản này đã được đăng ký!";
        }

        if (!empty($errors)) {
            http_response_code(400);
            echo json_encode(['errors' => $errors]);
            return;
        }

        $result = $this->accountModel->save($username, $fullName, $password, $role);
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => 'Tạo tài khoản thành công']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Lỗi máy chủ khi tạo tài khoản']);
        }
    }

    // API: Đăng nhập
    public function login()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $username = $data['username'] ?? '';
        $password = $data['password'] ?? '';

        $account = $this->accountModel->getAccountByUsername($username);

        if ($account && password_verify($password, $account->password)) {
            $_SESSION['username'] = $account->username;
            $_SESSION['role'] = $account->role;
            echo json_encode([
                'message' => 'Đăng nhập thành công',
                'user' => [
                    'username' => $account->username,
                    'fullname' => $account->fullname,
                    'role' => $account->role
                ]
            ]);
        } else {
            http_response_code(401);
            echo json_encode(['error' => 'Tên đăng nhập hoặc mật khẩu không đúng']);
        }
    }

    // API: Đăng xuất
    public function logout()
    {
        header('Content-Type: application/json');
        session_destroy();
        echo json_encode(['message' => 'Đăng xuất thành công']);
    }

    // API: Lấy thông tin tài khoản đang đăng nhập
    public function profile()
    {
        header('Content-Type: application/json');
        if (isset($_SESSION['username'])) {
            $account = $this->accountModel->getAccountByUsername($_SESSION['username']);
            if ($account) {
                echo json_encode([
                    'username' => $account->username,
                    'fullname' => $account->fullname,
                    'role' => $account->role
                ]);
                return;
            }
        }
        http_response_code(401);
        echo json_encode(['error' => 'Chưa đăng nhập']);
    }
}
