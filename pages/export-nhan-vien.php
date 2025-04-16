<?php 
	// Định nghĩa ROOT_PATH nếu chưa có
	if (!defined('ROOT_PATH')) {
		define('ROOT_PATH', dirname(__DIR__));
	}
	
	// Require autoloader của Composer
	require_once ROOT_PATH . '/vendor/autoload.php';

	// Kết nối database
	require_once ROOT_PATH . '/config.php';

	// Sử dụng class ExportNhanVien
	require_once ROOT_PATH . '/app/exports/ExportNhanVien.php';

	// Khởi tạo đối tượng và xuất Excel
	$exporter = new App\Exports\ExportNhanVien($conn);
	$exporter->export();

?>