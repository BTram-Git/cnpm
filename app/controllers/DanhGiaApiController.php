<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
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
            echo json_encode(['message' => 'Danh gia không tìm thấy']);
        }
    }

    // Thêm sản phẩm mới
    public function store()
    {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);

        if (!is_array($data)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu không hợp lệ']);
            return;
        }

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;

        // Kiểm tra dữ liệu cơ bản
        if (!is_string($name) || !is_string($description) || !is_numeric($price) || !is_numeric($category_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
            return;
        }

        $result = $this->productModel->addProduct(
            $name,
            $description,
            $price,
            $category_id,
            ""
        );

        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } elseif ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Product created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $result['error'] ?? 'Thêm sản phẩm thất bại']);
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

        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;

        if (!is_string($name) || !is_string($description) || !is_numeric($price) || !is_numeric($category_id)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
            return;
        }

        $result = $this->productModel->updateProduct(
            $id,
            $name,
            $description,
            $price,
            $category_id,
        ""
        );

        if ($result) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product update failed']);
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

        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed']);
        }
    }
}