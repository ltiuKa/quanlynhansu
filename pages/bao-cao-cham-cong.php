<?php
// Bắt đầu session
session_start();

// Định nghĩa ROOT_PATH để sử dụng đường dẫn tương đối
define('ROOT_PATH', dirname(__DIR__));

// Include file config
require ROOT_PATH . '/config.php';

// Kiểm tra đăng nhập
if(isset($_SESSION['username']) && isset($_SESSION['level']))
{
	// Include các file layout
	include(ROOT_PATH . '/layouts/header.php');
    include(ROOT_PATH . '/layouts/topbar.php');
    include(ROOT_PATH . '/layouts/sidebar.php');
	// Lấy tháng và năm hiện tại nếu không có tham số
	$thang = isset($_GET['thang']) ? intval($_GET['thang']) : date('m');
	$nam = isset($_GET['nam']) ? intval($_GET['nam']) : date('Y');
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

	// Tổng số ngày làm việc trong tháng (tính cả thứ 7 buổi sáng, không tính CN)
	function tinhNgayLamViecTrongThang($thang, $nam) {
		$soNgay = date('t', strtotime("$nam-$thang-01"));
		$soNgayLamViec = 0;
		
		for ($i = 1; $i <= $soNgay; $i++) {
			$ngay = sprintf("%04d-%02d-%02d", $nam, $thang, $i);
			$thu = date('w', strtotime($ngay));
			if ($thu > 0 && $thu < 6) { // Thứ 2 đến thứ 6
				$soNgayLamViec++;
			} elseif ($thu == 6) { // Thứ 7 (chỉ tính nửa ngày)
				$soNgayLamViec += 0.5;
			}
		}
		
		return $soNgayLamViec;
	}

	$soNgayLamViec = tinhNgayLamViecTrongThang($thang, $nam);

	// Lấy chi tiết chấm công theo nhân viên nếu có tham số
	$nhanVienId = isset($_GET['nhan_vien_id']) ? intval($_GET['nhan_vien_id']) : 0;
	$chiTietChamCong = [];
	$nhanVienInfo = null;

	// Thông tin báo cáo cho tất cả nhân viên
	$baoCaoChuyenCan = [];
	foreach ($danhSachNhanVien as $nhanVien) {
		// Lấy số buổi hợp lệ từ bảng chấm công
		$query = "SELECT COUNT(*) as so_buoi_hop_le FROM cham_cong 
				WHERE nhanvien_id = ? AND MONTH(ngay) = ? AND YEAR(ngay) = ? AND hop_le = 1";
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, "iii", $nhanVien['id'], $thang, $nam);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$count = mysqli_fetch_assoc($result);
		
		$nhanVien['so_buoi_hop_le'] = $count['so_buoi_hop_le'];
		
		// Lọc theo từ khóa tìm kiếm nếu có
		if (empty($search) || 
			stripos($nhanVien['ho_ten'], $search) !== false || 
			stripos($nhanVien['ma_nv'], $search) !== false ||
			stripos($nhanVien['phong_ban'], $search) !== false) {
			$baoCaoChuyenCan[] = $nhanVien;
		}
	}

	// Lấy chi tiết chấm công của một nhân viên nếu có yêu cầu
	if ($nhanVienId > 0) {
		// Lấy thông tin nhân viên
		$query = "SELECT nv.id, nv.ma_nv, nv.ten_nv as ho_ten, cv.ten_chuc_vu as chuc_vu, pb.ten_phong_ban as phong_ban 
                  FROM nhanvien nv 
                  LEFT JOIN chuc_vu cv ON nv.chuc_vu_id = cv.id 
                  LEFT JOIN phong_ban pb ON nv.phong_ban_id = pb.id 
                  WHERE nv.id = ?";
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, "i", $nhanVienId);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		$nhanVienInfo = mysqli_fetch_assoc($result);
		
		// Tạo mảng cho tất cả các ngày trong tháng
		$ngayDauThang = "$nam-$thang-01";
		$ngayCuoiThang = date('Y-m-t', strtotime($ngayDauThang));
		$soNgay = date('t', strtotime($ngayDauThang));
		
		// Khởi tạo mảng chứa dữ liệu tất cả các ngày trong tháng
		$duLieuTatCaNgay = [];
		for ($i = 1; $i <= $soNgay; $i++) {
			$ngay = sprintf("%04d-%02d-%02d", $nam, $thang, $i);
			$duLieuTatCaNgay[$ngay] = [
				'ngay' => $ngay,
				'gio_vao' => null,
				'gio_ra' => null,
				'trang_thai_vao' => 'khong_du_lieu',
				'trang_thai_ra' => 'khong_du_lieu',
				'hop_le' => 0,
				'thu' => date('w', strtotime($ngay)) // 0: CN, 1-6: T2-T7
			];
		}
		
		// Lấy dữ liệu chấm công từ DB
		$query = "SELECT * FROM cham_cong 
				WHERE nhanvien_id = ? AND ngay BETWEEN ? AND ?
				ORDER BY ngay";
		$stmt = mysqli_prepare($conn, $query);
		mysqli_stmt_bind_param($stmt, "iss", $nhanVienId, $ngayDauThang, $ngayCuoiThang);
		mysqli_stmt_execute($stmt);
		$result = mysqli_stmt_get_result($stmt);
		
		while ($row = mysqli_fetch_assoc($result)) {
			$duLieuTatCaNgay[$row['ngay']] = array_merge($duLieuTatCaNgay[$row['ngay']], $row);
		}
		
		// Chuyển mảng kết hợp thành mảng tuần tự cho dễ xử lý
		foreach ($duLieuTatCaNgay as $ngay => $duLieu) {
			$chiTietChamCong[] = $duLieu;
		}
	}
?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
	<!-- Content Header (Page header) -->
	<section class="content-header">
		<h1>
			Báo cáo chấm công
			<small>Tháng <?= $thang ?>/<?= $nam ?></small>
		</h1>
		<ol class="breadcrumb">
			<li><a href="<?= ROOT_PATH ?>/pages/index.php?p=index&a=statistic"><i class="fa fa-dashboard"></i> Trang chủ</a></li>
			<li><a href="index.php?p=attendance&a=scan"><i class="fa fa-calendar"></i> Chấm công</a></li>
			<li class="active">Báo cáo chấm công</li>
		</ol>
	</section>

	<!-- Main content -->
	<section class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="box box-primary">
					<div class="box-header with-border">
						<h3 class="box-title">Chọn thời gian báo cáo</h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">
						<form method="get" class="form-horizontal">
							<div class="form-group">
								<label class="col-sm-2 control-label">Chọn tháng/năm:</label>
								<div class="col-sm-10">
									<div class="input-group">
										<select name="thang" class="form-control">
											<?php for ($i = 1; $i <= 12; $i++): ?>
												<option value="<?= $i ?>" <?= ($i == $thang) ? 'selected' : '' ?>>
													Tháng <?= $i ?>
												</option>
											<?php endfor; ?>
										</select>
										<span class="input-group-addon">-</span>
										<select name="nam" class="form-control">
											<?php for ($i = date('Y') - 2; $i <= date('Y') + 1; $i++): ?>
												<option value="<?= $i ?>" <?= ($i == $nam) ? 'selected' : '' ?>>
													<?= $i ?>
												</option>
											<?php endfor; ?>
										</select>
										<span class="input-group-btn">
											<input type="hidden" name="p" value="attendance">
											<input type="hidden" name="a" value="report">
											<?php if (isset($_GET['search'])): ?>
												<input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
											<?php endif; ?>
											<?php if (isset($_GET['nhan_vien_id'])): ?>
												<input type="hidden" name="nhan_vien_id" value="<?= $nhanVienId ?>">
											<?php endif; ?>
											<button type="submit" class="btn btn-primary">Xem</button>
										</span>
									</div>
								</div>
							</div>
						</form>
					</div>
				</div>
			</div>
		</div>

		<div class="row">
			<div class="col-md-12">
				<div class="box box-success">
					<div class="box-header with-border">
						<h3 class="box-title">Báo cáo chuyên cần tháng <?= $thang ?>/<?= $nam ?></h3>
						<div class="box-tools pull-right">
							<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
						</div>
					</div>
					<div class="box-body">
						<div class="alert alert-info">
							<p><i class="icon fa fa-info"></i> Tổng số ngày làm việc trong tháng: <strong><?= $soNgayLamViec ?> ngày</strong> (thứ 2 - thứ 6 cả ngày, thứ 7 buổi sáng, không tính chủ nhật)</p>
						</div>
						
						<div class="row">
							<div class="col-md-12">
								<form method="get" class="form-horizontal">
									<div class="form-group">
										<div class="col-sm-6">
											<div class="input-group">
												<input type="text" name="search" class="form-control" placeholder="Tìm kiếm theo tên, mã NV, phòng ban..." value="<?= htmlspecialchars($search) ?>">
												<span class="input-group-btn">
													<input type="hidden" name="thang" value="<?= $thang ?>">
													<input type="hidden" name="nam" value="<?= $nam ?>">
													<input type="hidden" name="p" value="attendance">
													<input type="hidden" name="a" value="report">
													<?php if (isset($_GET['nhan_vien_id'])): ?>
														<input type="hidden" name="nhan_vien_id" value="<?= $nhanVienId ?>">
													<?php endif; ?>
													<button type="submit" class="btn btn-default">Tìm kiếm</button>
												</span>
											</div>
										</div>
										<div class="col-sm-6 text-right">
											<?php if (!empty($search)): ?>
												<a href="?p=attendance&a=report&thang=<?= $thang ?>&nam=<?= $nam ?><?= $nhanVienId ? '&nhan_vien_id='.$nhanVienId : '' ?>" class="btn btn-default">
													<i class="fa fa-times"></i> Xóa bộ lọc
												</a>
											<?php endif; ?>
											<span class="text-muted">Hiển thị <?= count($baoCaoChuyenCan) ?> nhân viên<?= !empty($search) ? ' (đã lọc)' : '' ?></span>
										</div>
									</div>
								</form>
							</div>
						</div>
						
						<div class="table-responsive">
							<table id="example1" class="table table-bordered table-striped">
								<thead>
									<tr>
										<th>STT</th>
										<th>Mã NV</th>
										<th>Họ tên</th>
										<th>Phòng ban</th>
										<th>Số buổi hợp lệ</th>
										<th>Tỷ lệ</th>
										<th>Chi tiết</th>
									</tr>
								</thead>
								<tbody>
									<?php if (empty($baoCaoChuyenCan)): ?>
										<tr>
											<td colspan="7" class="text-center">Không tìm thấy nhân viên nào<?= !empty($search) ? ' phù hợp với từ khóa tìm kiếm' : '' ?></td>
										</tr>
									<?php else: ?>
										<?php foreach ($baoCaoChuyenCan as $index => $item): ?>
											<tr>
												<td><?= $index + 1 ?></td>
												<td><?= $item['ma_nv'] ?></td>
												<td><?= $item['ho_ten'] ?></td>
												<td><?= $item['phong_ban'] ?></td>
												<td><?= $item['so_buoi_hop_le'] ?></td>
												<td>
													<?php 
													$tyLe = $soNgayLamViec > 0 ? round(($item['so_buoi_hop_le'] / $soNgayLamViec) * 100, 1) : 0;
													$badgeClass = 'label-danger';
													if ($tyLe >= 90) $badgeClass = 'label-success';
													else if ($tyLe >= 75) $badgeClass = 'label-info';
													else if ($tyLe >= 50) $badgeClass = 'label-warning';
													?>
													<span class="label <?= $badgeClass ?>"><?= $tyLe ?>%</span>
												</td>
												<td>
													<a href="?p=attendance&a=report&thang=<?= $thang ?>&nam=<?= $nam ?>&nhan_vien_id=<?= $item['id'] ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" 
													   class="btn btn-xs btn-info">
														<i class="fa fa-eye"></i> Xem chi tiết
													</a>
												</td>
											</tr>
										<?php endforeach; ?>
									<?php endif; ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
		
		<?php if ($nhanVienInfo): ?>
			<div class="row">
				<div class="col-md-12">
					<div class="box box-info">
						<div class="box-header with-border">
							<h3 class="box-title">Chi tiết chấm công - <?= $nhanVienInfo['ho_ten'] ?> (<?= $nhanVienInfo['ma_nv'] ?>)</h3>
							<div class="box-tools pull-right">
								<button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
							</div>
						</div>
						<div class="box-body">
							<div class="row">
								<div class="col-md-4">
									<div class="box box-solid">
										<div class="box-header with-border">
											<h3 class="box-title">Thông tin nhân viên</h3>
										</div>
										<div class="box-body">
											<p><strong>Họ tên:</strong> <?= $nhanVienInfo['ho_ten'] ?></p>
											<p><strong>Mã NV:</strong> <?= $nhanVienInfo['ma_nv'] ?></p>
											<p><strong>Chức vụ:</strong> <?= $nhanVienInfo['chuc_vu'] ?></p>
											<p><strong>Phòng ban:</strong> <?= $nhanVienInfo['phong_ban'] ?></p>
										</div>
									</div>
								</div>
								<div class="col-md-8">
									<div class="box box-solid">
										<div class="box-header with-border">
											<h3 class="box-title">Tổng kết tháng <?= $thang ?>/<?= $nam ?></h3>
										</div>
										<div class="box-body">
											<?php
											// Thống kê
											$tongNgayLamViec = 0;
											$ngayDiDungGio = 0;
											$ngayDiTre = 0;
											$ngayVeSom = 0;
											$ngayHopLe = 0;
											
											foreach ($chiTietChamCong as $item) {
												if ($item['thu'] > 0 && $item['thu'] < 6) { // Thứ 2-6
													$tongNgayLamViec++;
													if ($item['gio_vao'] !== null) {
														if ($item['trang_thai_vao'] == 'dung_gio') $ngayDiDungGio++;
														if ($item['trang_thai_vao'] == 'tre') $ngayDiTre++;
													}
													if ($item['gio_ra'] !== null && $item['trang_thai_ra'] == 'som') {
														$ngayVeSom++;
													}
													if ($item['hop_le']) $ngayHopLe++;
												}
											}
											
											// Tính tỷ lệ chuyên cần
											$tyLeChuyenCan = $tongNgayLamViec > 0 ? round(($ngayHopLe / $tongNgayLamViec) * 100, 1) : 0;
											?>
											<div class="row">
												<div class="col-md-6">
													<p><strong>Số ngày đi làm:</strong> <?= $tongNgayLamViec ?> ngày</p>
													<p><strong>Đi làm đúng giờ:</strong> <?= $ngayDiDungGio ?> ngày</p>
													<p><strong>Đi làm trễ giờ:</strong> <?= $ngayDiTre ?> ngày</p>
													<p><strong>Về sớm:</strong> <?= $ngayVeSom ?> ngày</p>
												</div>
												<div class="col-md-6">
													<p><strong>Số ngày làm hợp lệ:</strong> <?= $ngayHopLe ?> ngày</p>
													<p><strong>Tỷ lệ chuyên cần:</strong> 
														<span class="label 
															<?= $tyLeChuyenCan >= 90 ? 'label-success' : 
															   ($tyLeChuyenCan >= 75 ? 'label-info' : 
															   ($tyLeChuyenCan >= 50 ? 'label-warning' : 'label-danger')) ?>">
															<?= $tyLeChuyenCan ?>%
														</span>
													</p>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							
							<div class="table-responsive">
								<table class="table table-bordered table-striped">
									<thead>
										<tr>
											<th>Ngày</th>
											<th>Thứ</th>
											<th>Giờ vào</th>
											<th>Trạng thái vào</th>
											<th>Giờ ra</th>
											<th>Trạng thái ra</th>
											<th>Hợp lệ</th>
										</tr>
									</thead>
									<tbody>
										<?php 
										$days = ['Chủ nhật', 'Thứ hai', 'Thứ ba', 'Thứ tư', 'Thứ năm', 'Thứ sáu', 'Thứ bảy'];
										$today = date('Y-m-d');
										
										foreach ($chiTietChamCong as $item): 
											$isWeekend = ($item['thu'] == 0 || $item['thu'] == 6); 
											$isToday = ($item['ngay'] == $today);
											$rowClass = $isWeekend ? 'active' : ($isToday ? 'warning' : '');
										?>
											<tr class="<?= $rowClass ?>">
												<td><?= date('d/m/Y', strtotime($item['ngay'])) ?></td>
												<td><?= $days[$item['thu']] ?></td>
												<td><?= $item['gio_vao'] ? $item['gio_vao'] : '---' ?></td>
												<td>
													<?php if ($item['trang_thai_vao'] == 'dung_gio'): ?>
														<span class="label label-success">Đúng giờ</span>
													<?php elseif ($item['trang_thai_vao'] == 'tre'): ?>
														<span class="label label-warning">Trễ</span>
													<?php else: ?>
														<span class="label label-default">Không có dữ liệu</span>
													<?php endif; ?>
												</td>
												<td><?= $item['gio_ra'] ? $item['gio_ra'] : '---' ?></td>
												<td>
													<?php if ($item['trang_thai_ra'] == 'dung_gio'): ?>
														<span class="label label-success">Đúng giờ</span>
													<?php elseif ($item['trang_thai_ra'] == 'som'): ?>
														<span class="label label-warning">Về sớm</span>
													<?php else: ?>
														<span class="label label-default">Không có dữ liệu</span>
													<?php endif; ?>
												</td>
												<td>
													<?php if ($item['hop_le']): ?>
														<span class="label label-success">Hợp lệ</span>
													<?php elseif ($isWeekend): ?>
														<span class="label label-info">Ngày nghỉ</span>
													<?php elseif ($isToday && $item['gio_vao'] === null): ?>
														<span class="label label-primary">Hôm nay</span>
													<?php elseif ($item['gio_vao'] === null): ?>
														<span class="label label-danger">Vắng mặt</span>
													<?php else: ?>
														<span class="label label-danger">Không hợp lệ</span>
													<?php endif; ?>
												</td>
											</tr>
										<?php endforeach; ?>
									</tbody>
								</table>
							</div>
							
							<div class="box-footer">
								<a href="?p=attendance&a=report&thang=<?= $thang ?>&nam=<?= $nam ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>" class="btn btn-default">
									<i class="fa fa-arrow-left"></i> Quay lại danh sách
								</a>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php endif; ?>
	</section>
	<!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php
	// Include footer
	include(ROOT_PATH . '/layouts/footer.php');
}
else
{
	// Redirect to login page
	header('Location: ' . ROOT_PATH . '/pages/dang-nhap.php');
}
?> 