<?php 

// create session
session_start();

// connect database
include('../config.php');

if(isset($_SESSION['username']) && isset($_SESSION['level']))
{
	header("Location: index.php");
}
else
{

	if(isset($_POST['login']))
	{
		// array error
		$error = array();
		// array success
		$success = array();
		// showMess
		$showMess = false;

		// validate form 
		if(empty($_POST['email']))
		{
			$error['email'] = 'Bạn chưa nhập <b> email </b>';
		}

		if(empty($_POST['password']))
		{
			$error['password'] = 'Bạn chưa nhập <b> mật khẩu </b>';
		}

		if(!$error)
		{	
			
			$email = $_POST['email'];
			$password = md5($_POST['password']);

			// check user
			$check = "SELECT email, mat_khau, quyen, truy_cap, trang_thai FROM tai_khoan WHERE email = '$email'";
			$result = mysqli_query($conn, $check);
			$row = mysqli_fetch_array($result);

			if(mysqli_num_rows($result) == 1)
			{
				// Kiểm tra mật khẩu
				if($row['mat_khau'] == $password)
				{
					// Kiểm tra trạng thái tài khoản
					if($row['trang_thai'] == 1)
				{
					$showMess = true;
						$level = $row['quyen'];
						
					// create var session username
					$_SESSION['username'] = $email;
					// create var session level
					$_SESSION['level'] = $level;

          // set access
          $access = $row['truy_cap'] + 1;
          $update = "UPDATE tai_khoan SET truy_cap = $access WHERE email = '$email'";
          mysqli_query($conn, $update); 

					$success['mess'] = 'Đăng nhập thành công';
					header("Refresh: 1; index.php?p=index&a=statistic");
					}
					else
					{
						$error['status'] = 'Tài khoản của bạn đã bị <b>vô hiệu hóa</b>. Vui lòng liên hệ quản trị viên!';
					}
				}
				else
				{
					$error['check'] = 'Nhập sai <b> mật khẩu </b>. Vui lòng thử lại';
				}
			}
			else
			{
				$error['check'] = 'Nhập sai <b> email </b>. Vui lòng thử lại';
			}
		}
	}

?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="../dist/images/logo_mini.png" type="image/x-icon"/>
    <title>Đăng nhập - Quản lý nhân sự HRM</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        @keyframes gradient {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        body {
            background: linear-gradient(-45deg, #4a65ff, #3498db, #5e84ff, #6c5ce7);
            background-size: 400% 400%;
            animation: gradient 15s ease infinite;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .blur-circle {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            z-index: 0;
        }
        
        .circle-1 {
            width: 500px;
            height: 500px;
            background: rgba(97, 255, 189, 0.4);
            top: -200px;
            left: -100px;
            animation: float 10s ease-in-out infinite;
        }
        
        .circle-2 {
            width: 600px;
            height: 600px;
            background: rgba(255, 165, 97, 0.3);
            bottom: -250px;
            right: -150px;
            animation: float 15s ease-in-out infinite reverse;
        }
        
        @keyframes float {
            0% {
                transform: translate(0px, 0px);
            }
            50% {
                transform: translate(30px, 20px);
            }
            100% {
                transform: translate(0px, 0px);
            }
        }
        
        .login-container {
            width: 420px;
            background: rgba(255, 255, 255, 0.18);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 40px;
            box-shadow: 0 8px 32px rgba(31, 38, 135, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.25);
            z-index: 10;
            position: relative;
        }
        
        .logo {
            text-align: center;
            margin-bottom: 20px;
            position: relative;
        }
        
        .logo img {
            width: 250px;
            height: auto;
            filter: drop-shadow(0 4px 6px rgba(0, 0, 0, 0.1));
        }
        
        .logo h1 {
            color: white;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: 1px;
            margin-top: 10px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }
        
        .login-container h2 {
            text-align: center;
            color: white;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .login-container p {
            text-align: center;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            color: white;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 400;
        }
        
        .form-group input {
            width: 100%;
            padding: 14px 20px;
            border: none;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 12px;
            color: white;
            font-size: 14px;
            outline: none;
            transition: all 0.3s;
            backdrop-filter: blur(5px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .form-group input:focus {
            background: rgba(255, 255, 255, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.4);
            box-shadow: 0 4px 16px rgba(31, 38, 135, 0.15);
        }
        
        .form-group input::placeholder {
            color: rgba(255, 255, 255, 0.7);
        }
        
        .password-field {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: rgba(255, 255, 255, 0.7);
            cursor: pointer;
            font-size: 14px;
        }
        
        .remember-forgot {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .remember {
            display: flex;
            align-items: center;
        }
        
        .remember input[type="checkbox"] {
            appearance: none;
            width: 18px;
            height: 18px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 5px;
            margin-right: 8px;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            outline: none;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        .remember input[type="checkbox"]:checked::before {
            content: '✓';
            font-size: 12px;
            color: white;
        }
        
        .remember label {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            cursor: pointer;
        }
        
        .forgot a {
            color: rgba(255, 255, 255, 0.9);
            font-size: 13px;
            text-decoration: none;
            transition: color 0.3s;
        }
        
        .forgot a:hover {
            color: white;
            text-decoration: underline;
        }
        
        .btn-login {
            width: 100%;
            padding: 16px;
            background: linear-gradient(45deg, #4a65ff, #6a88ff);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 8px 15px rgba(74, 101, 255, 0.3);
            position: relative;
            overflow: hidden;
        }
        
        .btn-login:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(74, 101, 255, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(-1px);
        }
        
        .btn-login::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: 0.5s;
        }
        
        .btn-login:hover::before {
            left: 100%;
        }
        
        .toast-container {
            position: fixed;
            bottom: 20px;
            left: 20px;
            z-index: 999;
        }
        
        .alert {
            padding: 16px 20px;
            margin-bottom: 10px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            min-width: 300px;
            max-width: 450px;
            animation: slideInLeft 0.3s ease-out forwards;
        }
        
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .alert-danger {
            background: rgba(239, 68, 68, 0.85);
            border-left: 4px solid #ef4444;
            color: white;
        }
        
        .alert-success {
            background: rgba(34, 197, 94, 0.85);
            border-left: 4px solid #22c55e;
            color: white;
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.85);
            border-left: 4px solid #f59e0b;
            color: white;
        }
        
        .alert i {
            margin-right: 10px;
            font-size: 18px;
        }
        
        /* Modal Dialog Box Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: opacity 0.3s ease, visibility 0.3s ease;
        }
        
        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }
        
        .modal-container {
            width: 90%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            transform: scale(0.9);
            transition: transform 0.3s ease;
            animation: modalAppear 0.3s forwards;
        }
        
        @keyframes modalAppear {
            from {
                transform: scale(0.9);
                opacity: 0;
            }
            to {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .modal-header {
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #f87171;
            color: white;
        }
        
        .modal-header h3 {
            margin: 0;
            font-size: 20px;
            font-weight: 600;
            display: flex;
            align-items: center;
        }
        
        .modal-header h3 i {
            margin-right: 10px;
            font-size: 24px;
        }
        
        .modal-close {
            background: none;
            border: none;
            color: white;
            font-size: 20px;
            cursor: pointer;
            width: 30px;
            height: 30px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: transform 0.2s;
        }
        
        .modal-close:hover {
            transform: rotate(90deg);
        }
        
        .modal-content {
            padding: 20px;
        }
        
        .modal-content p {
            margin: 0 0 15px;
            font-size: 16px;
            line-height: 1.6;
            color: #4b5563;
            text-align: left;
        }
        
        .modal-content p strong {
            color: #111827;
        }
        
        .modal-actions {
            padding: 15px 20px 20px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        
        .modal-btn {
            padding: 10px 20px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
            border: none;
        }
        
        .modal-btn.primary {
            background-color: #f87171;
            color: white;
            box-shadow: 0 2px 6px rgba(248, 113, 113, 0.3);
        }
        
        .modal-btn.primary:hover {
            background-color: #ef4444;
            box-shadow: 0 4px 10px rgba(248, 113, 113, 0.4);
        }
        
        .modal-btn.secondary {
            background-color: #e5e7eb;
            color: #4b5563;
        }
        
        .modal-btn.secondary:hover {
            background-color: #d1d5db;
        }
        
        @media (max-width: 480px) {
            .login-container {
                width: 90%;
                padding: 30px 20px;
            }
            
            .toast-container {
                left: 10px;
                right: 10px;
            }
            
            .alert {
                min-width: unset;
                max-width: unset;
                width: calc(100% - 20px);
            }
            
            .modal-container {
                width: 95%;
            }
        }
    </style>
</head>
<body>
    <div class="blur-circle circle-1"></div>
    <div class="blur-circle circle-2"></div>
    
    <div class="login-container">
        <div class="logo">
            <img src="../dist/images/logoHRM_lg.png" alt="Logo HRM">
            <!-- <h1>QUẢN LÝ NHÂN SỰ</h1> -->
        </div>
        
        <h2>Chào mừng trở lại!</h2>
        <p>Vui lòng đăng nhập để tiếp tục sử dụng hệ thống</p>
        
        <form method="POST">
            <div class="form-group">
                <label for="email">Tên đăng nhập</label>
                <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Mật khẩu</label>
                <div class="password-field">
                    <input type="password" id="password" name="password" placeholder="Nhập mật khẩu" required>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fa-regular fa-eye" id="password-icon"></i>
                    </span>
                </div>
            </div>
            
            <div class="remember-forgot">
                <div class="remember">
                    <input type="checkbox" id="remember-me">
                    <label for="remember-me">Ghi nhớ đăng nhập</label>
                </div>
                <div class="forgot">
                    <!-- <a href="#">Quên mật khẩu?</a> -->
                </div>
  </div>
            
            <button type="submit" class="btn-login" name="login">Đăng nhập</button>
        </form>
    </div>
    
    <div class="toast-container">
      <?php
        // show error
        if(isset($error) && $showMess == false)
        {
            foreach ($error as $key => $err)
            {
                // Bỏ qua lỗi trạng thái vì sẽ hiển thị trong modal
                if($key == 'status') {
                    continue;
                }
                
                $class = "alert-danger";
                $icon = "fa-exclamation-circle";
                
                echo "<div class='alert $class'>";
                echo "<i class='fas $icon'></i> " . $err;
            echo "</div>";
          }
        }

        // show success
        if(isset($success) && $showMess == true)
        {
            foreach ($success as $suc)
            {
                echo "<div class='alert alert-success'>";
                echo "<i class='fas fa-check-circle'></i> " . $suc;
                echo "</div>";
            }
        }
      ?>
    </div>
    
    <!-- Modal Dialog for Disabled Account -->
    <div class="modal-overlay <?php echo (isset($error['status'])) ? 'active' : ''; ?>" id="accountDisabledModal">
        <div class="modal-container">
            <div class="modal-header">
                <h3><i class="fas fa-ban"></i> Tài khoản bị vô hiệu hóa</h3>
                <button class="modal-close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-content">
                <p>
                    <?php if(isset($error['status'])): ?>
                    <?php echo $error['status']; ?>
                    <?php else: ?>
                    Tài khoản của bạn hiện đã bị <strong>vô hiệu hóa</strong> và không thể truy cập vào hệ thống.
                    <?php endif; ?>
                </p>
                <p>
                    Nếu bạn cho rằng đây là sự nhầm lẫn, vui lòng liên hệ với <strong>quản trị viên</strong> để được hỗ trợ.
                </p>
            </div>
            <div class="modal-actions">
                <button class="modal-btn primary" onclick="closeModal()">Đã hiểu</button>
        </div>
        </div>
    </div>
    
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        }
        
        function closeModal() {
            document.getElementById('accountDisabledModal').classList.remove('active');
        }
        
        // Tự động ẩn thông báo sau 5 giây
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.opacity = '0';
                alert.style.transform = 'translateX(-100%)';
                alert.style.transition = 'all 0.5s ease';
                setTimeout(() => {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>
</body>
</html>

<?php 
}
// end check session
?>