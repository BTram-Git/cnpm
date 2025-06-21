<?php
require_once('app/config/database.php');

require_once('app/models/DanhGiaModel.php');
require_once('app/helpers/SessionHelper.php');

class DanhGiaApiController
{
    private $danhGiaModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->danhGiaModel = new DanhGiaModel($this->db);
    }

    // Lấy danh sách
    public function index()
    {
        header('Content-Type: application/json');
        $danhGias = $this->danhGiaModel->getDanhGias();
        echo json_encode($danhGias);
    }

    // Lấy thông tin sản phẩm theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        $danhGia = $this->danhGiaModel->getDanhGiaById($id);
        
        if ($danhGia) {
            echo json_encode($danhGia);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Đánh giá không tìm thấy']);
        }
    }

    // Thêm mới
   public function store()
   {
       SessionHelper::start();
       if (!SessionHelper::isLoggedIn()) {
           http_response_code(401);
           echo json_encode(['error' => 'Vui lòng đăng nhập để đánh giá.']);
           return;
       }

       header('Content-Type: application/json');
       $data = json_decode(file_get_contents("php://input"), true);

       if (!is_array($data)) {
           http_response_code(400);
           echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
           return;
       }
       
       $Danhgiasao = $data['Danhgiasao'] ?? '';
       $Nhanxet = $data['Nhanxet'] ?? '';
       $Ngaydanhgia = date('Y-m-d H:i:s');
       $Manguoidung = SessionHelper::getUserId(); // Lấy từ session
       $MaHD = $data['MaHD'] ?? '';
       
       // Sẽ tốt hơn nếu có kiểm tra xem người dùng này có thực sự sở hữu hóa đơn MaHD không
       
       $result = $this->danhGiaModel->addDanhGia(
           $Danhgiasao,
           $Nhanxet,
           $Ngaydanhgia,
           $Manguoidung,
           $MaHD
       );

       if ($result === true) {
           http_response_code(201);
           echo json_encode(['message' => 'Đánh giá được thêm thành công']);
       } else {
           http_response_code(400);
           echo json_encode(['errors' => $result]);
       }
   }

   // Cập nhật sản phẩm theo ID
   public function update($id)
   {
       SessionHelper::start();
       header('Content-Type: application/json');

       $danhGia = $this->danhGiaModel->getDanhGiaById($id);

       if (!$danhGia) {
           http_response_code(404);
           echo json_encode(['message' => 'Đánh giá không tìm thấy']);
           return;
       }

       if (!SessionHelper::isAdmin() && !(SessionHelper::isLoggedIn() && SessionHelper::getUserId() == $danhGia->Manguoidung)) {
           http_response_code(403);
           echo json_encode(['error' => 'Bạn không có quyền sửa đánh giá này.']);
           return;
       }
       
       $data = json_decode(file_get_contents("php://input"), true);

       if (!is_array($data) || !is_numeric($id)) {
           http_response_code(400);
           echo json_encode(['error' => 'Dữ liệu hoặc ID không hợp lệ']);
           return;
       }

       $Danhgiasao = $data['Danhgiasao'] ?? '';
       $Nhanxet = $data['Nhanxet'] ?? '';

       if (empty($Danhgiasao) || empty($Nhanxet)) {
           http_response_code(400);
           echo json_encode(['error' => 'Vui lòng điền đủ thông tin sao và nhận xét.']);
           return;
       }

       $result = $this->danhGiaModel->updateDanhGia(
           $id,
           $Danhgiasao,
           $Nhanxet
       );

       if ($result) {
           echo json_encode(['message' => 'Đánh giá được cập nhật thành công']);
       } else {
           http_response_code(400);
           echo json_encode(['message' => 'Đánh giá cập nhật thất bại']);
       }
   }

    // Xóa sản phẩm theo ID
    public function destroy($id)
    {
        SessionHelper::start();
        header('Content-Type: application/json');
        
        $danhGia = $this->danhGiaModel->getDanhGiaById($id);

        if (!$danhGia) {
            http_response_code(404);
            echo json_encode(['message' => 'Đánh giá không tìm thấy']);
            return;
        }

        if (!SessionHelper::isAdmin() && !(SessionHelper::isLoggedIn() && SessionHelper::getUserId() == $danhGia->Manguoidung)) {
            http_response_code(403);
            echo json_encode(['error' => 'Bạn không có quyền xóa đánh giá này.']);
            return;
        }

        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID không hợp lệ']);
            return;
        }

        $result = $this->danhGiaModel->deleteDanhGia($id);
        if ($result) {
            echo json_encode(['message' => 'Xóa đánh giá thành công']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Xóa đánh giá thất bại']);
        }
    }
}