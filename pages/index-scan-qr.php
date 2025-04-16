<?php
// require_once "config.php";

// Định nghĩa ROOT_PATH để sử dụng đường dẫn tương đối
define('ROOT_PATH', dirname(__DIR__));

// Include file cấu hình kết nối database
require_once ROOT_PATH . "/config.php";

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../dist/images/logo_mini.png" type="image/x-icon"/>
    <title>Hệ thống chấm công QR - HRM</title>
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="../bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="../bower_components/font-awesome/css/font-awesome.min.css">
    <!-- AdminLTE style -->
    <link rel="stylesheet" href="../dist/css/AdminLTE.min.css">
    <style>
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
        }
        .main-container {
            display: flex;
            height: 100vh;
            padding: 0;
            margin: 0;
            background: #ecf0f5;
        }
        .side-notifications {
            width: 320px;
            background: #2c3e50;
            color: #fff;
            padding: 0;
            display: flex;
            flex-direction: column;
            height: 100vh;
            box-shadow: 3px 0 5px rgba(0,0,0,0.1);
        }
        .scanner-container {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-start;
            padding: 20px;
            position: relative;
            background: linear-gradient(135deg, #f5f7fa 0%, #ecf0f5 100%);
            overflow-y: auto;
        }
        .header {
            text-align: center;
            padding: 20px 0;
            background: #3c8dbc;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            width: 100%;
        }
        .header h2 {
            margin: 0;
            color: #fff;
            font-size: 24px;
            font-weight: 300;
        }
        .clock-container {
            margin: 20px 0;
            text-align: center;
            width: 100%;
        }
        .date-display {
            font-size: 16px;
            color: #7f8c8d;
            margin-bottom: 5px;
        }
        .time-display {
            font-size: 36px;
            font-weight: bold;
            color: #3498db;
        }
        #qr-reader {
            width: 400px;
            max-width: 100%;
            margin: 20px auto;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        }
        .notification-header {
            padding: 15px;
            background: #3498db;
            color: white;
            text-align: center;
            font-weight: bold;
            font-size: 18px;
        }
        #notification-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px;
        }
        .notification-item {
            margin-bottom: 15px;
            padding: 15px;
            border-radius: 5px;
            animation: fadeIn 0.5s ease-in-out;
            position: relative;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .notification-item .time {
            position: absolute;
            top: 8px;
            right: 10px;
            font-size: 12px;
            opacity: 0.8;
        }
        .notification-item .name {
            font-weight: bold;
            font-size: 16px;
            margin-bottom: 5px;
            color: #fff;
        }
        .notification-item .message {
            margin-bottom: 5px;
            color: rgba(255, 255, 255, 0.9);
        }
        .notification-item .status {
            font-style: italic;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.8);
        }
        .notification-success {
            background-color: #27ae60;
        }
        .notification-warning {
            background-color: #f39c12;
        }
        .notification-danger {
            background-color: #e74c3c;
        }
        .notification-info {
            background-color: #3498db;
        }
        .logo {
            max-width: 150px;
            margin: 10px auto;
            display: block;
        }
        .qr-status {
            margin-top: 20px;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
            background: rgba(255,255,255,0.8);
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
        }
        .status-circle {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 5px;
            vertical-align: middle;
        }
        .active {
            background-color: #2ecc71;
            animation: pulse 1.5s infinite;
        }
        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0.4); }
            70% { box-shadow: 0 0 0 10px rgba(46, 204, 113, 0); }
            100% { box-shadow: 0 0 0 0 rgba(46, 204, 113, 0); }
        }
        .inactive {
            background-color: #e74c3c;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: rgba(255,255,255,0.7);
        }
        .company-info {
            padding: 15px;
            text-align: center;
            background: rgba(0,0,0,0.2);
            font-size: 12px;
            color: rgba(255,255,255,0.7);
        }
        .btn-back {
            position: absolute;
            top: 20px;
            left: 20px;
            color: #3498db;
            text-decoration: none;
            font-size: 14px;
        }
        .btn-back:hover {
            color: #2980b9;
            text-decoration: none;
        }
        .scan-animation {
            height: 3px;
            width: 100%;
            background: linear-gradient(to right, transparent, #3498db, transparent);
            position: absolute;
            top: 0;
            left: 0;
            animation: scan 2s infinite;
        }
        @keyframes scan {
            0% { top: 0; }
            50% { top: 100%; }
            100% { top: 0; }
        }
        /* Responsive design */
        @media (max-width: 768px) {
            .main-container {
                flex-direction: column;
            }
            .side-notifications {
                width: 100%;
                height: 40vh;
            }
            .scanner-container {
                height: 60vh;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <!-- Sidebar với thông báo -->
        <div class="side-notifications">
            <div class="notification-header">
                <i class="fa fa-bell"></i> Lịch sử chấm công
            </div>
            <div id="notification-list">
                <div class="no-data">
                    <i class="fa fa-info-circle fa-2x"></i>
                    <p>Chưa có dữ liệu chấm công</p>
                    <p>Quét mã QR để bắt đầu</p>
                </div>
            </div>
            <div class="company-info">
                <p>HRM - Hệ thống quản lý nhân sự</p>
                <p>© <?php echo date('Y'); ?> Phòng nhân sự</p>
            </div>
        </div>
        
        <!-- Khu vực quét mã QR -->
        <div class="scanner-container">
            <a href="../pages/index.php" class="btn-back">
                <i class="fa fa-arrow-left"></i> Quay lại hệ thống
            </a>
            
            <img src="../dist/images/logoHRM_lg.png" alt="Logo HRM" class="logo">
            
            <div class="header">
                <h2><i class="fa fa-qrcode"></i> Hệ thống chấm công bằng QR</h2>
            </div>
            
            <div class="clock-container">
                <div class="date-display" id="currentDate">
                    <?php echo date('l, d/m/Y'); ?>
                </div>
                <div class="time-display" id="currentTime">
                    <?php echo date('H:i:s'); ?>
                </div>
            </div>
            
            <div id="qr-reader">
                <div class="scan-animation"></div>
            </div>
            
            <div class="qr-status">
                <div class="status-circle active" id="scanner-status"></div>
                <span id="status-text">Máy quét đang hoạt động. Đưa mã QR vào khung hình để chấm công.</span>
            </div>
        </div>
    </div>

    <!-- jQuery 3 -->
    <script src="../bower_components/jquery/dist/jquery.min.js"></script>
    <!-- Bootstrap 3.3.7 -->
    <script src="../bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/html5-qrcode@2.3.4/html5-qrcode.min.js"></script>
    <script>
        // Cập nhật thời gian thực
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            const seconds = String(now.getSeconds()).padStart(2, '0');
            document.getElementById('currentTime').textContent = `${hours}:${minutes}:${seconds}`;
            
            // Cập nhật ngày nếu đổi ngày
            const days = ['Chủ Nhật', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy'];
            const day = days[now.getDay()];
            const date = String(now.getDate()).padStart(2, '0');
            const month = String(now.getMonth() + 1).padStart(2, '0');
            const year = now.getFullYear();
            document.getElementById('currentDate').textContent = `${day}, ${date}/${month}/${year}`;
            
            setTimeout(updateClock, 3000);
        }

        function onScanSuccess(decodedText) {
            // Cập nhật trạng thái
            updateScannerStatus(false, "Đang xử lý...");
            
            // Dừng quét sau khi đã quét được
            html5QrCode.stop();
            
            // Hiển thị thông báo đang xử lý
            addNotification('info', 'Đang xử lý...', '', 'Vui lòng đợi');
            
            // Gửi dữ liệu đến máy chủ để xử lý
            fetch('../pages/scan.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'qrcode=' + encodeURIComponent(decodedText)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (e) {
                        throw new Error('Invalid JSON response: ' + text);
                    }
                });
            })
            .then(data => {
                if (data.success) {
                    // Thêm thông báo thành công
                    updateScannerStatus(true, "Quét mã thành công! Bắt đầu quét lại...");
                    addNotification(
                        'success', 
                        data.message, 
                        data.details.ho_ten, 
                        data.details.trang_thai
                    );
                    // Phát âm thanh báo thành công
                    playSound(true);
                } else {
                    // Thêm thông báo lỗi
                    updateScannerStatus(true, "Có lỗi! Bắt đầu quét lại...");
                    addNotification('danger', data.message, '', 'Lỗi');
                    // Phát âm thanh báo lỗi
                    playSound(false);
                }
                
                // Khởi động lại máy quét sau 3 giây
                setTimeout(() => {
                    startScanner();
                }, 3000);
            })
            .catch(error => {
                console.error('Lỗi:', error);
                // Thêm thông báo lỗi hệ thống
                updateScannerStatus(true, "Lỗi hệ thống! Bắt đầu quét lại...");
                addNotification('danger', 'Có lỗi xảy ra: ' + error, '', 'Lỗi hệ thống');
                
                // Khởi động lại máy quét sau 3 giây
                setTimeout(() => {
                    startScanner();
                }, 5000);
            });
        }

        function playSound(success) {
            // Tạo âm thanh thông báo
            const audio = new Audio();
            audio.src = success ? '../dist/sounds/success.mp3' : '../dist/sounds/error.mp3';
            audio.play().catch(e => console.log("Không thể phát âm thanh: " + e));
        }

        function updateScannerStatus(isActive, message) {
            const statusCircle = document.getElementById('scanner-status');
            const statusText = document.getElementById('status-text');
            
            if (isActive) {
                statusCircle.className = 'status-circle active';
            } else {
                statusCircle.className = 'status-circle inactive';
            }
            
            statusText.textContent = message;
        }

        function onScanFailure(error) {
            // Không cần làm gì khi không quét được
        }

        // Khởi tạo máy quét QR
        let html5QrCode;
        
        function startScanner() {
            updateScannerStatus(true, "Máy quét đang hoạt động. Đưa mã QR vào khung hình để chấm công.");
            html5QrCode = new Html5Qrcode("qr-reader");
            html5QrCode.start(
                { facingMode: "environment" }, // Sử dụng camera sau
                { fps: 10, qrbox: { width: 250, height: 250 } },
                onScanSuccess,
                onScanFailure
            ).catch(err => {
                console.error(`Không thể khởi động máy quét: ${err}`);
                updateScannerStatus(false, "Không thể khởi động máy quét. Vui lòng tải lại trang hoặc kiểm tra quyền camera.");
            });
        }
        
        // Bắt đầu quét khi trang được tải
        window.onload = function() {
            startScanner();
            updateClock(); // Khởi động đồng hồ
        };

        // Hàm thêm thông báo mới vào danh sách
        function addNotification(type, message, name, status) {
            const notificationList = document.getElementById('notification-list');
            
            // Xóa thông báo "Chưa có dữ liệu" nếu tồn tại
            const noData = notificationList.querySelector('.no-data');
            if (noData) {
                notificationList.innerHTML = '';
            }
            
            // Tạo phần tử thông báo mới
            const now = new Date();
            const timeString = now.getHours() + ':' + 
                              String(now.getMinutes()).padStart(2, '0') + ':' + 
                              String(now.getSeconds()).padStart(2, '0');
            
            const notificationItem = document.createElement('div');
            notificationItem.className = `notification-item notification-${type}`;
            
            let notificationContent = `
                <div class="time">${timeString}</div>
            `;
            
            if (name) {
                notificationContent += `<div class="name">${name}</div>`;
            }
            
            notificationContent += `<div class="message">${message}</div>`;
            
            if (status) {
                notificationContent += `<div class="status">${status}</div>`;
            }
            
            notificationItem.innerHTML = notificationContent;
            
            // Thêm thông báo vào đầu danh sách
            notificationList.insertBefore(notificationItem, notificationList.firstChild);
            
            // Giới hạn số lượng thông báo (giữ 30 thông báo gần nhất)
            const notifications = notificationList.querySelectorAll('.notification-item');
            if (notifications.length > 30) {
                notificationList.removeChild(notifications[notifications.length - 1]);
            }
            
            // Tự động xóa thông báo sau 30 giây (chỉ áp dụng cho thông báo đang xử lý)
            if (type === 'info' && message === 'Đang xử lý...') {
                setTimeout(() => {
                    if (notificationItem.parentNode) {
                        notificationItem.remove();
                    }
                }, 30000);
            }
        }
    </script>
</body>
</html> 