<?php
// Bắt đầu session
session_start();

// Định nghĩa ROOT_PATH để sử dụng đường dẫn tương đối
define('ROOT_PATH', dirname(__DIR__));

// Include file cấu hình kết nối database
require_once ROOT_PATH . "/config.php";

// Kiểm tra đã đăng nhập chưa
if(isset($_SESSION['username']) && isset($_SESSION['level']))
{
	// Include các file layout
	include(ROOT_PATH . '/layouts/header.php');
	include(ROOT_PATH . '/layouts/topbar.php');
	include(ROOT_PATH . '/layouts/sidebar.php');

	// Xử lý tìm kiếm nhân viên
	$search = isset($_GET['search']) ? trim($_GET['search']) : '';

	// Lấy danh sách nhân viên
	$query = "SELECT nv.id, nv.ma_nv, nv.ten_nv as ho_ten, cv.ten_chuc_vu as chuc_vu, pb.ten_phong_ban as phong_ban 
              FROM nhanvien nv 
              LEFT JOIN chuc_vu cv ON nv.chuc_vu_id = cv.id 
              LEFT JOIN phong_ban pb ON nv.phong_ban_id = pb.id 
              ORDER BY nv.ten_nv";
	$result = mysqli_query($conn, $query);
	$danhSachNhanVien = [];

	while ($row = mysqli_fetch_assoc($result)) {
		$danhSachNhanVien[] = $row;
	}

	// Lọc nhân viên theo từ khóa tìm kiếm
	$nhanVien = [];
	foreach ($danhSachNhanVien as $nv) {
		if (empty($search) || 
			stripos($nv['ho_ten'], $search) !== false || 
			stripos($nv['ma_nv'], $search) !== false) {
			$nhanVien[] = $nv;
		}
	}

	// Xử lý download mã QR
	if (isset($_GET['download']) && !empty($_GET['download'])) {
		$id = $_GET['download'];
		$query = "SELECT id, ma_nv, ten_nv as ho_ten FROM nhanvien WHERE id = ?";
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, "i", $id);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		if (mysqli_num_rows($result) > 0) {
			$nv = mysqli_fetch_assoc($result);
			$qrCode = "nhan-vien-qr-code-" . $nv['id'];
			$qrFileName = "QR_" . $nv['ma_nv'] . ".png";
			
			// Tạo mã QR URL sử dụng QR Server API thay thế
			$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrCode);
			
			// Tải và lưu mã QR
			$imgData = file_get_contents($qrUrl);
			if ($imgData === false) {
				echo "Không thể tạo mã QR, vui lòng thử lại sau.";
				exit;
			}
			
			header('Content-Type: image/png');
			header('Content-Disposition: attachment; filename="' . $qrFileName . '"');
			echo $imgData;
			exit;
		}
	}

	// Xem chi tiết mã QR
	$viewMode = false;
	$viewData = null;
	if (isset($_GET['view']) && !empty($_GET['view'])) {
		$id = $_GET['view'];
		$query = "SELECT id, ma_nv, ten_nv as ho_ten FROM nhanvien WHERE id = ?";
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, "i", $id);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		if (mysqli_num_rows($result) > 0) {
			$viewData = mysqli_fetch_assoc($result);
			$viewData['qr_code'] = "nhan-vien-qr-code-" . $viewData['id'];
			$viewMode = true;
		}
	}
?>

	<!-- Content Wrapper. Contains page content -->
	<div class="content-wrapper">
		<!-- Content Header (Page header) -->
		<section class="content-header">
			<h1>
				Tạo mã QR chấm công
				<small>HRM - Quản lý nhân sự</small>
			</h1>
			<ol class="breadcrumb">
				<li><a href="<?= ROOT_PATH ?>/pages/index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Tổng quan</a></li>
				<li class="active">Mã QR chấm công</li>
			</ol>
		</section>

		<!-- Main content -->
		<section class="content">
			<?php if ($viewMode && $viewData): ?>
				<!-- Chi tiết mã QR -->
				<div class="row">
					<div class="col-md-8 col-md-offset-2">
						<div class="box box-info">
							<div class="box-header with-border">
								<h3 class="box-title">Chi tiết mã QR chấm công</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div>
							</div>
							<div class="box-body text-center">
								<h3><?= $viewData['ho_ten'] ?></h3>
								<p class="text-muted">Mã nhân viên: <?= $viewData['ma_nv'] ?></p>
								
								<div style="margin: 30px 0;">
									<img src="https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=<?= urlencode($viewData['qr_code']) ?>" 
										alt="Mã QR của <?= $viewData['ho_ten'] ?>" class="img-responsive center-block">
								</div>
								
								<div class="well well-sm">
									<p><strong>Chuỗi mã QR:</strong> <?= $viewData['qr_code'] ?></p>
									<p>Vui lòng in mã QR này và gắn vào thẻ nhân viên.</p>
								</div>
							</div>
							<div class="box-footer">
								<a href="tao-ma-qr-cham-cong.php?download=<?= $viewData['id'] ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" class="btn btn-primary">
									<i class="fa fa-download"></i> Tải xuống
								</a>
								<a href="tao-ma-qr-cham-cong.php<?= !empty($search) ? '?search='.urlencode($search) : '' ?>" class="btn btn-default">
									<i class="fa fa-arrow-left"></i> Quay lại danh sách
								</a>
							</div>
						</div>
					</div>
				</div>
			<?php else: ?>
				<!-- Danh sách mã QR -->
				<div class="row">
					<div class="col-md-12">
						<div class="box box-primary">
							<div class="box-header with-border">
								<h3 class="box-title">Tạo mã QR chấm công cho nhân viên</h3>
								<div class="box-tools pull-right">
									<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
								</div>
							</div>
							<div class="box-body">
								<div class="alert alert-info">
									<h4><i class="icon fa fa-info"></i> Lưu ý quan trọng:</h4>
									<p>Mã QR chấm công phải có định dạng <strong>"nhan-vien-qr-code-[ID]"</strong> để hệ thống có thể nhận diện.</p>
									<p>Ví dụ: nhan-vien-qr-code-1, nhan-vien-qr-code-2, ...</p>
								</div>
								
								<div class="form-group">
									<form method="get" class="row" id="searchForm">
										<div class="col-md-6">
											<div class="input-group">
												<input type="text" name="search" id="searchInput" class="form-control" placeholder="Tìm kiếm theo tên, mã nhân viên..." value="<?= htmlspecialchars($search) ?>">
												<span class="input-group-btn">
													<button type="submit" class="btn btn-primary">Tìm kiếm</button>
												</span>
											</div>
										</div>
										<div class="col-md-6 text-right">
											<?php if (!empty($search)): ?>
												<a href="tao-ma-qr-cham-cong.php" class="btn btn-default">Xóa bộ lọc</a>
											<?php endif; ?>
											<span class="text-muted">Hiển thị <?= count($nhanVien) ?> nhân viên<?= !empty($search) ? ' (đã lọc)' : '' ?></span>
										</div>
									</form>
								</div>
								
								<?php if (empty($nhanVien)): ?>
									<div class="alert alert-warning">
										<p>Không tìm thấy nhân viên nào phù hợp với từ khóa tìm kiếm "<?= htmlspecialchars($search) ?>"</p>
									</div>
								<?php else: ?>
									<div class="row">
										<?php foreach ($nhanVien as $nv): ?>
											<div class="col-md-4 col-sm-6">
												<div class="box box-widget">
													<div class="box-header with-border">
														<h3 class="box-title"><?= $nv['ho_ten'] ?></h3>
														<div class="box-tools pull-right">
															<span class="label label-primary">Mã NV: <?= $nv['ma_nv'] ?></span>
														</div>
													</div>
													<div class="box-body text-center">
														<?php 
														$qrCode = "nhan-vien-qr-code-" . $nv['id'];
														// Sử dụng QR Server API
														$qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=" . urlencode($qrCode);
														?>
														<img src="<?= $qrUrl ?>" alt="Mã QR của <?= $nv['ho_ten'] ?>" class="img-responsive center-block" style="margin: 0 auto;">
														<p class="text-muted" style="margin-top: 10px;">
															<strong>Phòng ban:</strong> <?= $nv['phong_ban'] ?><br>
															<strong>Chức vụ:</strong> <?= $nv['chuc_vu'] ?>
														</p>
													</div>
													<div class="box-footer text-center">
														<a href="tao-ma-qr-cham-cong.php?view=<?= $nv['id'] ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" class="btn btn-info btn-sm">
															<i class="fa fa-eye"></i> Xem chi tiết
														</a>
														<a href="tao-ma-qr-cham-cong.php?download=<?= $nv['id'] ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" class="btn btn-primary btn-sm">
															<i class="fa fa-download"></i> Tải xuống
														</a>
													</div>
												</div>
											</div>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
							<!-- <div class="box-footer">
								<a href="index.php" class="btn btn-default"><i class="fa fa-arrow-left"></i> Quay lại trang chủ</a>
							</div> -->
						</div>
					</div>
				</div>
			<?php endif; ?>
		</section>
		<!-- /.content -->
	</div>
	<!-- /.content-wrapper -->

<?php
	// include footer
	include(ROOT_PATH . '/layouts/footer.php');
}
else
{
	// go to pages login
	header('Location: ' . ROOT_PATH . '/pages/dang-nhap.php');
}
?> 