<!-- <?php
// Thiết lập múi giờ cho Việt Nam (UTC+7)
date_default_timezone_set('Asia/Ho_Chi_Minh');

// Thông tin kết nối cơ sở dữ liệu
// $servername = "localhost";
// $username = "root";
// $password = "";
// $dbname = "demo_scan_qr";

// Kết nối đến cơ sở dữ liệu
// $conn = mysqli_connect($servername, $username, $password, $dbname);

// Kiểm tra kết nối
// if (!$conn) {
//     die("Kết nối thất bại: " . mysqli_connect_error());
// }

// Đặt charset để hỗ trợ tiếng Việt
// mysqli_set_charset($conn, "utf8");

// Cấu hình thời gian làm việc
// Thời gian làm việc ngày thường (Thứ 2 - Thứ 6)
// $gioVaoThuong = "08:00:00"; // Giờ vào làm chuẩn ngày thường
// $gioRaThuong = "17:00:00";  // Giờ ra về chuẩn ngày thường

// // Thời gian làm việc ngày thứ 7 (chỉ buổi sáng)
// $gioVaoThu7 = "08:00:00";   // Giờ vào làm chuẩn thứ 7
// $gioRaThu7 = "12:00:00";    // Giờ ra về chuẩn thứ 7 (buổi trưa)

// // Cấu hình dung sai
// $mucTreChapNhan = 15; // Số phút trễ được chấp nhận
// $mucVeSomChapNhan = 15; // Số phút về sớm được chấp nhận

// // Biến tạm tương thích ngược 
// $gioVao = $gioVaoThuong;
// $gioRa = $gioRaThuong;

// Tạo các bảng dữ liệu nếu chưa tồn tại
// $sql = "
// -- Tạo bảng nhân viên
// CREATE TABLE IF NOT EXISTS nhanvien (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     ma_nv VARCHAR(50) NOT NULL UNIQUE,
//     ho_ten VARCHAR(100) NOT NULL,
//     chuc_vu VARCHAR(100),
//     phong_ban VARCHAR(100),
//     ngay_tao TIMESTAMP DEFAULT CURRENT_TIMESTAMP
// );

// -- Tạo bảng chấm công
// CREATE TABLE IF NOT EXISTS cham_cong (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     nhan_vien_id INT NOT NULL,
//     ngay DATE NOT NULL,
//     gio_vao TIME,
//     gio_ra TIME,
//     trang_thai_vao ENUM('dung_gio', 'tre', 'khong_du_lieu') DEFAULT 'khong_du_lieu',
//     trang_thai_ra ENUM('dung_gio', 'som', 'khong_du_lieu') DEFAULT 'khong_du_lieu',
//     hop_le BOOLEAN DEFAULT FALSE,
//     ghi_chu TEXT,
//     FOREIGN KEY (nhan_vien_id) REFERENCES nhanvien(id)
// );

// -- Tạo bảng tổng kết chuyên cần
// CREATE TABLE IF NOT EXISTS chuyen_can (
//     id INT AUTO_INCREMENT PRIMARY KEY,
//     nhan_vien_id INT NOT NULL,
//     thang INT NOT NULL,
//     nam INT NOT NULL,
//     so_buoi_hop_le INT DEFAULT 0,
//     FOREIGN KEY (nhan_vien_id) REFERENCES nhanvien(id),
//     UNIQUE KEY (nhan_vien_id, thang, nam)
// );
// ";

// // Thực thi câu lệnh SQL
// if (mysqli_multi_query($conn, $sql)) {
//     do {
//         // Xử lý từng kết quả truy vấn
//     } while (mysqli_next_result($conn));
// }
// ?>  -->