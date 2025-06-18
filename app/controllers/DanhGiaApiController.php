<?php
require_once('app/config/database.php');

require_once('app/models/DanhGiaModel.php');

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
       header('Content-Type: application/json');
       $data = json_decode(file_get_contents("php://input"), true);

       if (!is_array($data)) {
           http_response_code(400);
           echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
           return;
       }
       //$now = new DateTime();
       $Danhgiasao = $data['Danhgiasao'] ?? '';
       $Nhanxet = $data['Nhanxet'];
       $Ngaydanhgia = $data['Ngaydanhgia'] ?? '';
       $Manguoidung = $data['Manguoidung'];
       $MaHD = $data['MaHD'] ?? '';
       // Kiểm tra dữ liệu cơ bản
       if (!is_string($Danhgiasao) || !is_string($Nhanxet) || !is_string($Manguoidung) || !is_string($MaHD) ) {
           http_response_code(400);
           echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
           return;
       }

       $result = $this->danhGiaModel->addDanhGia(
        $Danhgiasao,
        $Nhanxet,
        $Ngaydanhgia,
        $Manguoidung,
        $MaHD
       );

       if (is_array($result)) {
           http_response_code(400);
           echo json_encode(['errors' => $result]);
       } elseif ($result === true) {
           http_response_code(201);
           echo json_encode(['message' => 'Đánh giá được thêm thành công']);
       } else {
           http_response_code(500);
           echo json_encode(['error' => $result['error'] ?? 'Thêm đánh giá thất bại']);
       }
   }

   // Cập nhật sản phẩm theo ID
   public function update($id)
   {
       header('Content-Type: application/json');
       $data = json_decode(file_get_contents("php://input"), true);

       if (!is_array($data) || !is_numeric($id)) {
           http_response_code(400);
           echo json_encode(['error' => 'Dữ liệu hoặc ID không hợp lệ']);
           return;
       }

       $Danhgiasao = $data['Danhgiasao'] ?? '';
       $Nhanxet = $data['Nhanxet'];
       $Ngaydanhgia = $data['Ngaydanhgia'] ?? '';
       $Manguoidung = $data['Manguoidung'];
       $MaHD = $data['MaHD'] ?? '';

       if (!is_string($Danhgiasao) || !is_string($Nhanxet) || !is_string($Manguoidung) || !is_string($MaHD) ) {
        http_response_code(400);
        echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
        return;
       }


       $result = $this->danhGiaModel->updateDanhGia(
        $id,
        $Danhgiasao,
        $Nhanxet,
        $Ngaydanhgia,
        $Manguoidung,
        $MaHD
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
        header('Content-Type: application/json');
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