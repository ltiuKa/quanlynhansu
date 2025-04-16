<?php
// Đảm bảo không hiển thị lỗi PHP, chỉ trả về JSON
error_reporting(0);
ini_set('display_errors', 0);

// Định nghĩa ROOT_PATH để sử dụng đường dẫn tương đối
define('ROOT_PATH', dirname(__DIR__));

// Đặt header JSON ngay từ đầu
header('Content-Type: application/json');

try {
    // Include file cấu hình kết nối database
    require_once ROOT_PATH . "/config.php";

    // Kiểm tra nếu không phải là phương thức POST
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        echo json_encode(['success' => false, 'message' => 'Phương thức không hợp lệ']);
        exit;
    }

    // Lấy mã QR từ request
    $qrCode = isset($_POST['qrcode']) ? $_POST['qrcode'] : '';

    if (empty($qrCode)) {
        echo json_encode(['success' => false, 'message' => 'Không nhận được mã QR']);
        exit;
    }

    // Kiểm tra định dạng của mã QR
    // Mẫu: nhan-vien-qr-code-XYZ (XYZ là ID của nhân viên)
    if (!preg_match('/^nhan-vien-qr-code-(\d+)$/', $qrCode, $matches)) {
        echo json_encode(['success' => false, 'message' => 'Mã QR không hợp lệ']);
        exit;
    }

    $nhanVienId = $matches[1];

    // Kiểm tra nhân viên có tồn tại không
    $query = "SELECT id, ma_nv, ten_nv FROM nhanvien WHERE id = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "i", $nhanVienId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 0) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy thông tin nhân viên']);
        exit;
    }

    $nhanVien = mysqli_fetch_assoc($result);
    // Kiểm tra nếu không có cột ho_ten
    $hoTen = isset($nhanVien['ho_ten']) ? $nhanVien['ho_ten'] : $nhanVien['ten_nv'];

    // Lấy ngày hiện tại
    $ngayHienTai = date('Y-m-d');
    $gioHienTai = date('H:i:s');
    $thangHienTai = date('m');
    $namHienTai = date('Y');
    $thuHienTai = date('w'); // 0 = Chủ nhật, 1-6 = Thứ 2 - Thứ 7

    // Xác định thời gian làm việc theo ngày trong tuần
    if ($thuHienTai == 6) { // Thứ 7
        $gioVaoChuan = $gioVaoThu7;
        $gioRaChuan = $gioRaThu7;
    } else { // Ngày thường (Thứ 2 - Thứ 6)
        $gioVaoChuan = $gioVaoThuong;
        $gioRaChuan = $gioRaThuong;
    }

    // Kiểm tra xem nhân viên đã chấm công ngày hôm nay chưa
    $query = "SELECT * FROM cham_cong WHERE nhanvien_id = ? AND ngay = ?";
    $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, "is", $nhanVienId, $ngayHienTai);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Xác định hành động: check-in hoặc check-out
    if (mysqli_num_rows($result) == 0) {
        // Chưa có bản ghi cho ngày hôm nay, tạo mới và ghi nhận giờ vào
        $trangThaiVao = 'dung_gio';
        
        // Kiểm tra có trễ không
        if (strtotime($gioHienTai) > strtotime($gioVaoChuan) + $mucTreChapNhan * 60) {
            $trangThaiVao = 'tre';
        }
        
        $query = "INSERT INTO cham_cong (nhanvien_id, ngay, gio_vao, trang_thai_vao) VALUES (?, ?, ?, ?)";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "isss", $nhanVienId, $ngayHienTai, $gioHienTai, $trangThaiVao);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Đã ghi nhận giờ vào: " . $gioHienTai;
            $trangThai = ($trangThaiVao == 'dung_gio') ? 'Đúng giờ' : 'Đi trễ';
            
            echo json_encode([
                'success' => true, 
                'message' => $message,
                'details' => [
                    'ho_ten' => $hoTen,
                    'thoi_gian' => $gioHienTai,
                    'trang_thai' => $trangThai
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi ghi nhận giờ vào: ' . mysqli_error($conn)]);
        }
    } else {
        // Đã có bản ghi, cập nhật giờ ra
        $chamCong = mysqli_fetch_assoc($result);
        
        if ($chamCong['gio_ra'] != null) {
            echo json_encode(['success' => false, 'message' => 'Bạn đã chấm công đầy đủ cho ngày hôm nay']);
            exit;
        }
        
        $trangThaiRa = 'dung_gio';
        
        // Kiểm tra có về sớm không
        if (strtotime($gioHienTai) < strtotime($gioRaChuan) - $mucVeSomChapNhan * 60) {
            $trangThaiRa = 'som';
        }
        
        // Xác định xem có hợp lệ không (cả vào và ra đều đúng giờ)
        $hopLe = ($chamCong['trang_thai_vao'] == 'dung_gio' && $trangThaiRa == 'dung_gio') ? 1 : 0;
        
        $query = "UPDATE cham_cong SET gio_ra = ?, trang_thai_ra = ?, hop_le = ? WHERE id = ?";
        $stmt = mysqli_prepare($conn, $query);
        mysqli_stmt_bind_param($stmt, "ssii", $gioHienTai, $trangThaiRa, $hopLe, $chamCong['id']);
        
        if (mysqli_stmt_execute($stmt)) {
            $message = "Đã ghi nhận giờ ra: " . $gioHienTai;
            $trangThai = ($trangThaiRa == 'dung_gio') ? 'Đúng giờ' : 'Về sớm';
            
            // Nếu hợp lệ, cập nhật bảng chuyên cần
            if ($hopLe) {
                // Tính giá trị buổi làm việc (1 cho ngày thường, 0.5 cho thứ 7)
                $buoiLamViec = ($thuHienTai == 6) ? 0.5 : 1;
                
                // Kiểm tra đã có record cho tháng hiện tại chưa
                $query = "SELECT * FROM chuyen_can WHERE nhanvien_id = ? AND thang = ? AND nam = ?";
                $stmt = mysqli_prepare($conn, $query);
                mysqli_stmt_bind_param($stmt, "iii", $nhanVienId, $thangHienTai, $namHienTai);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) == 0) {
                    // Tạo mới bản ghi chuyên cần
                    $query = "INSERT INTO chuyen_can (nhanvien_id, thang, nam, so_buoi_hop_le) VALUES (?, ?, ?, ?)";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "iiid", $nhanVienId, $thangHienTai, $namHienTai, $buoiLamViec);
                    mysqli_stmt_execute($stmt);
                } else {
                    // Cập nhật bản ghi chuyên cần
                    $query = "UPDATE chuyen_can SET so_buoi_hop_le = so_buoi_hop_le + ? WHERE nhanvien_id = ? AND thang = ? AND nam = ?";
                    $stmt = mysqli_prepare($conn, $query);
                    mysqli_stmt_bind_param($stmt, "diii", $buoiLamViec, $nhanVienId, $thangHienTai, $namHienTai);
                    mysqli_stmt_execute($stmt);
                }
                
                $message .= " - Ngày làm việc hợp lệ!";
                if ($thuHienTai == 6) {
                    $message .= " (Thứ 7, tính 0.5 ngày)";
                }
            }
            
            echo json_encode([
                'success' => true, 
                'message' => $message,
                'details' => [
                    'ho_ten' => $hoTen,
                    'thoi_gian' => $gioHienTai,
                    'trang_thai' => $trangThai
                ]
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Lỗi khi ghi nhận giờ ra: ' . mysqli_error($conn)]);
        }
    }
} catch (Exception $e) {
    // Bắt bất kỳ lỗi nào và trả về JSON
    echo json_encode(['success' => false, 'message' => 'Lỗi hệ thống: ' . $e->getMessage()]);
    exit;
}
?> 