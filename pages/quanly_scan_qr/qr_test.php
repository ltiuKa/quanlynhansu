<?php
require_once "config.php";

$message = '';
$status = '';
$qrData = '';
$nhanVien = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['qr_code'])) {
    $qrCode = trim($_POST['qr_code']);
    $qrData = $qrCode;
    
    if (empty($qrCode)) {
        $message = 'Vui lòng nhập mã QR để kiểm tra';
        $status = 'warning';
    } else {
        // Kiểm tra định dạng mã QR
        if (preg_match('/^nhan-vien-qr-code-(\d+)$/', $qrCode, $matches)) {
            $nhanVienId = $matches[1];
            
            // Kiểm tra nhân viên có tồn tại không
            $query = "SELECT * FROM nhanvien WHERE id = ?";
            $stmt = mysqli_prepare($conn, $query);
            mysqli_stmt_bind_param($stmt, "i", $nhanVienId);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if (mysqli_num_rows($result) > 0) {
                $nhanVien = mysqli_fetch_assoc($result);
                $message = 'Mã QR hợp lệ! Đã tìm thấy thông tin nhân viên.';
                $status = 'success';
            } else {
                $message = 'Mã QR đúng định dạng nhưng không tìm thấy nhân viên có ID: ' . $nhanVienId;
                $status = 'danger';
            }
        } else {
            $message = 'Mã QR không đúng định dạng. Định dạng chuẩn: nhan-vien-qr-code-[ID]';
            $status = 'danger';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kiểm tra mã QR</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }
        .result-container {
            margin-top: 20px;
        }
        .nv-info {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h2>Kiểm tra mã QR chấm công</h2>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <h5>Hướng dẫn:</h5>
                            <p>Công cụ này giúp bạn kiểm tra xem mã QR có đúng định dạng và có khớp với thông tin nhân viên trong hệ thống không.</p>
                            <p>Định dạng mã QR hợp lệ: <strong>nhan-vien-qr-code-[ID]</strong></p>
                        </div>
                        
                        <form method="post" action="">
                            <div class="mb-3">
                                <label for="qr_code" class="form-label">Nhập mã QR cần kiểm tra:</label>
                                <input type="text" class="form-control" id="qr_code" name="qr_code" 
                                       placeholder="Ví dụ: nhan-vien-qr-code-1" value="<?php echo htmlspecialchars($qrData); ?>">
                            </div>
                            <button type="submit" class="btn btn-primary">Kiểm tra</button>
                        </form>
                        
                        <?php if (!empty($message)): ?>
                            <div class="result-container">
                                <div class="alert alert-<?php echo $status; ?>">
                                    <?php echo $message; ?>
                                </div>
                                
                                <?php if ($nhanVien): ?>
                                    <div class="nv-info">
                                        <h5>Thông tin nhân viên:</h5>
                                        <p><strong>ID:</strong> <?php echo $nhanVien['id']; ?></p>
                                        <p><strong>Mã nhân viên:</strong> <?php echo $nhanVien['ma_nv']; ?></p>
                                        <p><strong>Họ tên:</strong> <?php echo $nhanVien['ho_ten']; ?></p>
                                        <p><strong>Chức vụ:</strong> <?php echo $nhanVien['chuc_vu']; ?></p>
                                        <p><strong>Phòng ban:</strong> <?php echo $nhanVien['phong_ban']; ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <a href="index.php" class="btn btn-secondary">Quay lại trang chủ</a>
                        <a href="generate_qr.php" class="btn btn-success">Tạo mã QR</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 