<?php
require_once('app/config/database.php');
require_once('app/models/CategoryModel.php');

class CategoryController
{
    private $categoryModel;
    private $db;

    public function __construct()
    {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // Hiển thị tất cả các danh mục
    public function index()
    {
        $categories = $this->categoryModel->getCategories();
        include 'app/views/category/list.php';
    }

    // Thêm danh mục mới
    public function add()
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';

            $result = $this->categoryModel->addCategory($name, $description);

            if (is_array($result)) {
                // Nếu có lỗi, quay lại trang thêm
                $errors = $result;
                include 'app/views/category/add.php';
            } else {
                header('Location: /webbanhang/Category');
            }
        } else {
            include 'app/views/category/add.php';
        }
    }

    // Chỉnh sửa danh mục
    public function edit($id)
    {
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            include 'app/views/category/edit.php';
        } else {
            echo "Không tìm thấy danh mục.";
        }
    }

    // Cập nhật danh mục
    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];

            $update = $this->categoryModel->updateCategory($id, $name, $description);

            if ($update) {
                header('Location: /webbanhang/Category');
            } else {
                echo "Đã xảy ra lỗi khi lưu danh mục.";
            }
        }
    }

    // Xóa danh mục
    public function delete($id)
    {
        if ($this->categoryModel->deleteCategory($id)) {
            header('Location: /webbanhang/Category');
        } else {
            echo "Đã xảy ra lỗi khi xóa danh mục.";
        }
    }
}
