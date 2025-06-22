# Hướng dẫn sử dụng Postman cho API User

## Cài đặt ban đầu

1. **Import Collection**: 
   - Mở Postman
   - Click "Import" 
   - Chọn file `postman_user_auth_collection.json`

2. **Cấu hình Environment**:
   - Tạo Environment mới
   - Thêm variable `base_url` với giá trị: `http://localhost/webbanhang`
   - Hoặc thay đổi trực tiếp trong collection

## Các API Endpoints

### 1. Đăng ký tài khoản (User Register)
- **Method**: POST
- **URL**: `{{base_url}}/user/register`
- **Headers**: 
  ```
  Content-Type: application/json
  ```
- **Body** (JSON):
  ```json
  {
      "hoten": "Nguyễn Văn A",
      "sdt": "0123456789",
      "diachi": "123 Đường ABC, Quận 1, TP.HCM",
      "email": "nguyenvana@example.com",
      "ngaysinh": "1990-01-01",
      "gioitinh": "Nam",
      "matkhau": "password123"
  }
  ```

**Response thành công (201)**:
```json
{
    "message": "Đăng ký thành công"
}
```

**Response lỗi (400)**:
```json
{
    "error": "Vui lòng điền đầy đủ họ tên, email và mật khẩu."
}
```

### 2. Đăng nhập (User Login)
- **Method**: POST
- **URL**: `{{base_url}}/user/login`
- **Headers**: 
  ```
  Content-Type: application/json
  ```
- **Body** (JSON):
  ```json
  {
      "email": "nguyenvana@example.com",
      "matkhau": "password123"
  }
  ```

**Response thành công (200)**:
```json
{
    "message": "Đăng nhập thành công",
    "user": {
        "Manguoidung": 1,
        "Hoten": "Nguyễn Văn A",
        "Email": "nguyenvana@example.com",
        "Vaitro": "user"
    }
}
```

**Response lỗi (401)**:
```json
{
    "error": "Email hoặc mật khẩu không đúng."
}
```

### 3. Đăng xuất (User Logout)
- **Method**: POST
- **URL**: `{{base_url}}/user/logout`
- **Headers**: 
  ```
  Content-Type: application/json
  ```

**Response thành công (200)**:
```json
{
    "message": "Đăng xuất thành công"
}
```

### 4. Lấy thông tin user (Get User Info)
- **Method**: GET
- **URL**: `{{base_url}}/user/{id}`
- **Headers**: 
  ```
  Content-Type: application/json
  ```

**Response thành công (200)**:
```json
{
    "Manguoidung": 1,
    "Hoten": "Nguyễn Văn A",
    "Sdt": "0123456789",
    "Diachi": "123 Đường ABC, Quận 1, TP.HCM",
    "Email": "nguyenvana@example.com",
    "Ngaysinh": "1990-01-01",
    "Gioitinh": "Nam",
    "Vaitro": "user"
}
```

### 5. Cập nhật thông tin user (Update User Info)
- **Method**: PUT
- **URL**: `{{base_url}}/user/{id}`
- **Headers**: 
  ```
  Content-Type: application/json
  ```
- **Body** (JSON):
  ```json
  {
      "hoten": "Nguyễn Văn A Updated",
      "sdt": "0987654321",
      "diachi": "456 Đường XYZ, Quận 2, TP.HCM",
      "email": "nguyenvana@example.com",
      "ngaysinh": "1990-01-01",
      "gioitinh": "Nam",
      "matkhau": "newpassword123"
  }
  ```

### 6. Xóa user (Delete User)
- **Method**: DELETE
- **URL**: `{{base_url}}/user/{id}`
- **Headers**: 
  ```
  Content-Type: application/json
  ```

## Quy trình test

1. **Test đăng ký**: Tạo tài khoản mới
2. **Test đăng nhập**: Đăng nhập với tài khoản vừa tạo
3. **Test lấy thông tin**: Lấy thông tin user theo ID
4. **Test cập nhật**: Cập nhật thông tin user
5. **Test đăng xuất**: Đăng xuất khỏi hệ thống
6. **Test xóa** (nếu cần): Xóa user

## Lưu ý quan trọng

- **Validation**: API sẽ kiểm tra:
  - Email phải đúng định dạng
  - Số điện thoại phải có 10-11 chữ số
  - Các trường bắt buộc không được để trống
- **Session**: Hiện tại session chưa được kích hoạt trong login, cần bổ sung nếu cần
- **Bảo mật**: Mật khẩu được hash bằng password_hash() trong database
- **Quyền truy cập**: Một số API yêu cầu đăng nhập hoặc quyền admin

## Troubleshooting

- **404 Not Found**: Kiểm tra URL và routing
- **400 Bad Request**: Kiểm tra format JSON và dữ liệu gửi lên
- **401 Unauthorized**: Kiểm tra thông tin đăng nhập
- **403 Forbidden**: Kiểm tra quyền truy cập
- **500 Internal Server Error**: Kiểm tra log server và database connection 