<?php
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');

class CategoryApiController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // Lấy danh sách danh mục
    public function index()
    {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getCategories();
        echo json_encode($categories);
    }

    // Lấy thông tin danh mục theo ID
    public function show($id)
    {
        header('Content-Type: application/json');
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID không hợp lệ']);
            return;
        }

        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            echo json_encode($category);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Category not found']);
        }
    }

    // Thêm danh mục mới
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

        if (!is_string($name) || !is_string($description)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
            return;
        }

        $result = $this->categoryModel->addCategory($name, $description);

        if ($result === true) {
            http_response_code(201);
            echo json_encode(['message' => 'Category created successfully']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => $result['error'] ?? 'Thêm danh mục thất bại']);
        }
    }

    // Cập nhật danh mục theo ID
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

        if (!is_string($name) || !is_string($description)) {
            http_response_code(400);
            echo json_encode(['error' => 'Dữ liệu đầu vào không hợp lệ']);
            return;
        }

        $result = $this->categoryModel->updateCategory($id, $name, $description);

        if ($result) {
            echo json_encode(['message' => 'Category updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Category update failed']);
        }
    }

    // Xóa danh mục theo ID
    public function destroy($id)
    {
        header('Content-Type: application/json');
        if (!is_numeric($id)) {
            http_response_code(400);
            echo json_encode(['error' => 'ID không hợp lệ']);
            return;
        }

        $result = $this->categoryModel->deleteCategory($id);
        if ($result) {
            echo json_encode(['message' => 'Category deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Category deletion failed']);
        }
    }
}
