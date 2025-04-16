<?php
require_once "config.php";

// Tạo dữ liệu mẫu cho nhân viên
$sqlInsertNhanVien = "
INSERT INTO nhanvien (ma_nv, ho_ten, chuc_vu, phong_ban) VALUES
('NV001', 'Nguyễn Văn A', 'Nhân viên', 'Phòng Kỹ thuật'),
('NV002', 'Trần Thị B', 'Trưởng phòng', 'Phòng Nhân sự'),
('NV003', 'Lê Văn C', 'Nhân viên', 'Phòng Kinh doanh'),
('NV004', 'Phạm Thị D', 'Nhân viên', 'Phòng Kế toán'),
('NV005', 'Hoàng Văn E', 'Giám đốc', 'Ban Giám đốc')
";

// Kiểm tra xem đã có dữ liệu chưa
$checkData = mysqli_query($conn, "SELECT COUNT(*) as count FROM nhanvien");
$row = mysqli_fetch_assoc($checkData);

if ($row['count'] == 0) {
    // Thực hiện thêm dữ liệu mẫu
    if (mysqli_query($conn, $sqlInsertNhanVien)) {
        echo "<h3>Đã thêm dữ liệu mẫu vào bảng nhân viên thành công!</h3>";
        
        echo "<h4>Các mã QR hợp lệ:</h4>";
        $result = mysqli_query($conn, "SELECT id, ma_nv, ho_ten FROM nhanvien");
        echo "<ul>";
        while ($nv = mysqli_fetch_assoc($result)) {
            $qrCode = "nhan-vien-qr-code-" . $nv['id'];
            echo "<li>ID: " . $nv['id'] . " - Mã NV: " . $nv['ma_nv'] . " - Họ tên: " . $nv['ho_ten'] . " - <strong>Mã QR: " . $qrCode . "</strong></li>";
        }
        echo "</ul>";
        
        echo "<p>Bạn có thể sử dụng mã QR này để test chức năng chấm công.</p>";
        echo "<p><a href='index.php' class='btn btn-primary'>Quay lại trang chủ</a></p>";
    } else {
        echo "Lỗi: " . mysqli_error($conn);
    }
} else {
    echo "<h3>Dữ liệu mẫu đã tồn tại!</h3>";
    
    echo "<h4>Các mã QR hợp lệ:</h4>";
    $result = mysqli_query($conn, "SELECT id, ma_nv, ho_ten FROM nhanvien");
    echo "<ul>";
    while ($nv = mysqli_fetch_assoc($result)) {
        $qrCode = "nhan-vien-qr-code-" . $nv['id'];
        echo "<li>ID: " . $nv['id'] . " - Mã NV: " . $nv['ma_nv'] . " - Họ tên: " . $nv['ho_ten'] . " - <strong>Mã QR: " . $qrCode . "</strong></li>";
    }
    echo "</ul>";
    
    echo "<p>Bạn có thể sử dụng mã QR này để test chức năng chấm công.</p>";
    echo "<p><a href='index.php' class='btn btn-primary'>Quay lại trang chủ</a></p>";
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cài đặt hệ thống</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        ul {
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h2>Cài đặt hệ thống chấm công</h2>
                    </div>
                    <div class="card-body">
                        <!-- Kết quả sẽ được hiển thị ở đây bởi PHP -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 