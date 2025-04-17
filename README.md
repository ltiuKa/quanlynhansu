# HỆ THỐNG QUẢN LÝ NHÂN SỰ HRM

## Giới thiệu
Hệ thống Quản lý Nhân sự và Chấm công là một ứng dụng web được phát triển để giúp doanh nghiệp quản lý thông tin nhân viên và theo dõi chấm công một cách hiệu quả.

## Yêu cầu hệ thống
- PHP >= 8.X
- MySQL >= 5.7
- Apache/Nginx
- XAMPP (recommended)
- Trình duyệt web hiện đại (Chrome, Firefox, Edge)

## Cài đặt

1. **Chuẩn bị môi trường**
   ```bash
   # Clone repository về máy local
   git clone https://github.com/your-username/quanlynhansu_v2.git

   # Di chuyển vào thư mục dự án
   cd quanlynhansu_v2
   ```

2. **Cấu hình cơ sở dữ liệu**
   - Tạo database mới trong MySQL
   - Import file SQL từ thư mục `database/quanlynhansu.sql`
   - Cập nhật thông tin kết nối trong file `config.php`:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'your_database');
     ```

3. **Cấu hình web server**
   - Đặt thư mục dự án trong `htdocs` (XAMPP)
   - Cấu hình virtual host (nếu cần)

## Cấu trúc thư mục
```
quanlynhansu_v2/
├── app/
│   └── models/
├── config.php
├── dist/
│   ├── css/
│   ├── js/
│   └── img/
├── layouts/
├── pages/
└── README.md
```

## Tài khoản mặc định
- Admin 1:
  - Email: `adminka@gmail.com`
  - Password: `123`
- Nhân viên:
  - Email: `nhanvien@gmail.com`
  - Password: `123`

## Các chức năng chính

### 1. Quản lý tài khoản
- Đăng nhập/đăng xuất hệ thống (dang-nhap.php, dang-xuat.php)
- Phân quyền người dùng
- Đổi mật khẩu (doi-mat-khau.php)
- Tạo tài khoản (tao-tai-khoan.php)
- Sửa tài khoản (sua-tai-khoan.php)
- Danh sách tài khoản (ds-tai-khoan.php)
- Thông tin tài khoản (thong-tin-tai-khoan.php)

### 2. Quản lý nhân viên
- Thêm nhân viên mới (them-nhan-vien.php)
- Sửa thông tin nhân viên (sua-nhan-vien.php)
- Xem danh sách nhân viên (danh-sach-nhan-vien.php)
- Xem chi tiết nhân viên (thong-tin-nhan-vien.php)
- Export danh sách nhân viên (export-nhan-vien.php)
- Tìm kiếm nhân viên theo tên, mã NV, phòng ban

### 3. Quản lý phòng ban và chức vụ
- Quản lý phòng ban (phong-ban.php, sua-phong-ban.php)
- Quản lý chức vụ (chuc-vu.php, sua-chuc-vu.php)
- Quản lý loại nhân viên (loai-nhan-vien.php, sua-loai-nhan-vien.php)

### 4. Quản lý trình độ và bằng cấp
- Quản lý trình độ (trinh-do.php, sua-trinh-do.php)
- Quản lý chuyên môn (chuyen-mon.php, sua-chuyen-mon.php)
- Quản lý bằng cấp (bang-cap.php, sua-bang-cap.php)

### 5. Quản lý công tác
- Quản lý công tác (cong-tac.php, sua-cong-tac.php)
- Danh sách công tác (danh-sach-cong-tac.php)

### 6. Quản lý khen thưởng và kỷ luật
- Quản lý khen thưởng (khen-thuong.php, sua-khen-thuong.php)
- Quản lý kỷ luật (ky-luat.php, sua-ky-luat.php)
- Quản lý loại khen thưởng (sua-loai-khen-thuong.php)
- Quản lý loại kỷ luật (sua-loai-ky-luat.php)

### 7. Quản lý lương
- Bảng lương (bang-luong.php)
- Chi tiết lương (chi-tiet-luong.php)
- Tính lương (tinh-luong.php)
- Export bảng lương (export-bang-luong.php)

### 8. Quản lý nhóm
- Tạo nhóm (tao-nhom.php)
- Danh sách nhóm (danh-sach-nhom.php)
- Chi tiết nhóm (chi-tiet-nhom.php)

### 9. Chấm công
- Ghi nhận giờ vào/ra
- Theo dõi trạng thái (đúng giờ, trễ, về sớm)
- Điều chỉnh trạng thái chấm công
- Báo cáo chấm công theo tháng
- Thống kê tỷ lệ chuyên cần

### 10. Tính năng bổ sung
- Các lớp xử lý (thư mục Classes)
- Hệ thống layout (thư mục layout)

## Giao diện
- Sử dụng các template UI AdminLTE
- Có hệ thống form thống nhất
- Có hệ thống bảng dữ liệu


## Bảo mật
- Có hệ thống đăng nhập/đăng xuất
- Có chức năng đổi mật khẩu
- Có phân quyền tài khoản

## Xuất báo cáo
- Xuất bảng lương ra Excel (export-bang-luong.php)
- Xuất danh sách nhân viên ra Excel (export-nhan-vien.php)

## Hỗ trợ và phát triển
- Email: anhbao88thang@gmail.com - Thắng

---
© 2025 Hệ thống Quản lý Nhân sự HRM


