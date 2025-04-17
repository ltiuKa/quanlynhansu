<?php 

// connect database
require_once('../config.php');

// Xử lý tính phụ cấp theo ngày công
if(isset($_POST["idNhanVien"]) && isset($_POST["soNgayCong"]))
{
  $idNhanVien = $_POST['idNhanVien'];
  $soNgayCong = $_POST['soNgayCong'];

  // lay chuc vu de kiem tra phu cap
  $phuCap = "SELECT ma_chuc_vu, ten_chuc_vu FROM nhanvien nv, chuc_vu cv WHERE nv.chuc_vu_id = cv.id AND nv.id = $idNhanVien";
  $resultPhuCap = mysqli_query($conn, $phuCap);
  $rowPhuCap = mysqli_fetch_array($resultPhuCap);
  $maChucVu = $rowPhuCap['ma_chuc_vu'];

  if($maChucVu == 'MCV1569203773') // giam doc
    $tongPhuCap = 1000000 + ($soNgayCong * 45000);
  else if($maChucVu == 'MCV1569203762') // pho giam doc
    $tongPhuCap = 800000 + ($soNgayCong * 45000);
  else if($maChucVu == 'MCV1569985216' || $maChucVu == 'MCV1569985261') // TP, PP
    $tongPhuCap = 500000 + ($soNgayCong * 45000);
  else if($maChucVu == 'MCV1569204007') // nhan vien
    // neu ngay cong lon hon 25 ngay 
    if($soNgayCong > 25)
      $tongPhuCap = 300000 + ($soNgayCong * 45000);
    else
      $tongPhuCap = 0;
  else
    $tongPhuCap = 0;

  echo $tongPhuCap;
}

// Xử lý cập nhật trạng thái chấm công
if(isset($_POST['action']) && isset($_POST['date']) && isset($_POST['nhanVienId'])) {
  // Lấy dữ liệu từ request
  $action = isset($_POST['action']) ? intval($_POST['action']) : null;
  $date = isset($_POST['date']) ? $_POST['date'] : null;
  $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
  $nhanVienId = isset($_POST['nhanVienId']) ? intval($_POST['nhanVienId']) : 0;
  $thang = isset($_POST['thang']) ? intval($_POST['thang']) : 0;
  $nam = isset($_POST['nam']) ? intval($_POST['nam']) : 0;

  // Kiểm tra dữ liệu đầu vào
  if ($action === null || empty($date) || empty($nhanVienId) || empty($thang) || empty($nam)) {
    echo json_encode(['success' => false, 'message' => 'Dữ liệu không hợp lệ']);
    exit;
  }

  // Kiểm tra xem bản ghi đã tồn tại chưa
  $sql = "SELECT * FROM cham_cong WHERE nhanvien_id = ? AND ngay = ?";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "is", $nhanVienId, $date);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if (mysqli_num_rows($result) > 0) {
    // Cập nhật bản ghi hiện có
    $row = mysqli_fetch_assoc($result);
    $recordId = $row['id'];
    
    $sql = "UPDATE cham_cong SET hop_le = ? WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ii", $action, $recordId);
    
    if (mysqli_stmt_execute($stmt)) {
      echo json_encode([
        'success' => true, 
        'message' => $action == 1 ? 'Đã cập nhật thành công: Có mặt' : 'Đã cập nhật thành công: Vắng mặt',
        'id' => $recordId
      ]);
    } else {
      echo json_encode(['success' => false, 'message' => 'Lỗi khi cập nhật: ' . mysqli_error($conn)]);
    }
  } else {
    // Tạo bản ghi mới nếu không tồn tại
    if ($action == 1) {
      // Nếu action là "Có mặt", tạo bản ghi với giờ vào/ra giả định
      $gioVao = '08:00:00';
      $gioRa = '17:00:00';
      $trangThaiVao = 'dung_gio';
      $trangThaiRa = 'dung_gio';
      
      $sql = "INSERT INTO cham_cong (nhanvien_id, ngay, gio_vao, gio_ra, trang_thai_vao, trang_thai_ra, hop_le) 
              VALUES (?, ?, ?, ?, ?, ?, ?)";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "isssssi", $nhanVienId, $date, $gioVao, $gioRa, $trangThaiVao, $trangThaiRa, $action);
      
      if (mysqli_stmt_execute($stmt)) {
        $newId = mysqli_insert_id($conn);
        echo json_encode([
          'success' => true, 
          'message' => 'Đã tạo điểm danh tự động: Có mặt',
          'id' => $newId
        ]);
      } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi tạo bản ghi mới: ' . mysqli_error($conn)]);
      }
    } else {
      // Nếu action là "Vắng mặt", chỉ tạo bản ghi vắng
      $sql = "INSERT INTO cham_cong (nhanvien_id, ngay, hop_le) VALUES (?, ?, ?)";
      $stmt = mysqli_prepare($conn, $sql);
      mysqli_stmt_bind_param($stmt, "isi", $nhanVienId, $date, $action);
      
      if (mysqli_stmt_execute($stmt)) {
        $newId = mysqli_insert_id($conn);
        echo json_encode([
          'success' => true, 
          'message' => 'Đã cập nhật thành công: Vắng mặt',
          'id' => $newId
        ]);
      } else {
        echo json_encode(['success' => false, 'message' => 'Lỗi khi tạo bản ghi mới: ' . mysqli_error($conn)]);
      }
    }
  }
  exit;
}
?>