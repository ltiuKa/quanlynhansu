<?php

	$conn = mysqli_connect("localhost", "root", "", "quanly_nhansu");
	//$conn = mysqli_connect("am1shyeyqbxzy8gc.cbetxkdyhwsb.us-east-1.rds.amazonaws.com", "itsiajvhn4a184io", "qeh0dyxjt8lrj1gs", "qzjiq6mzfi02utjx");

	// Kết nối đến cơ sở dữ liệu
	// $conn = mysqli_connect($servername, $username, $password, $dbname);


	if (!$conn) {
	    echo "Error: Unable to connect to MySQL." . PHP_EOL;
	    echo "Debugging errno: " . mysqli_connect_errno() . PHP_EOL;
	    echo "Debugging error: " . mysqli_connect_error() . PHP_EOL;
	    exit;
	}

	// Thiết lập múi giờ cho Việt Nam (UTC+7)
	date_default_timezone_set('Asia/Ho_Chi_Minh');


	if (!$conn) {
		die("Kết nối thất bại: " . mysqli_connect_error());
	}

	mysqli_set_charset($conn, "utf8");


	

	// Cấu hình thời gian làm việc
	// Thời gian làm việc ngày thường (Thứ 2 - Thứ 6)
	$gioVaoThuong = "08:00:00"; 
	$gioRaThuong = "17:00:00"; 

	// Thời gian làm việc ngày thứ 7 (chỉ buổi sáng)
	$gioVaoThu7 = "08:00:00";   
	$gioRaThu7 = "12:00:00"; 

	// Thời gian chấp nhận muộn
	$mucTreChapNhan = 15; 
	$mucVeSomChapNhan = 15;

	// Biến tạm tương thích ngược 
	$gioVao = $gioVaoThuong;
	$gioRa = $gioRaThuong;




?>