<?php 
	// Định nghĩa ROOT_PATH nếu chưa có
	if (!defined('ROOT_PATH')) {
		define('ROOT_PATH', dirname(__DIR__));
	}
	
	// Require autoloader của Composer
	require_once ROOT_PATH . '/vendor/autoload.php';

	// Kết nối database
	require_once ROOT_PATH . '/config.php';

	// Sử dụng class ExportBangLuong
	require_once ROOT_PATH . '/app/exports/ExportBangLuong.php';

	// Khởi tạo đối tượng và xuất Excel
	$exporter = new App\Exports\ExportBangLuong($conn);
	$exporter->export();

?>