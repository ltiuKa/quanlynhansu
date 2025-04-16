<?php
  // Đảm bảo ROOT_PATH đã được định nghĩa
  if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
  }
  
  // Kiểm tra xem biến $row_acc đã tồn tại chưa
  if (!isset($row_acc) || $row_acc === null) {
    // Nếu chưa tồn tại, thử tạo lại kết nối và lấy thông tin tài khoản
    if (isset($_SESSION['username'])) {
      $email = $_SESSION['username'];
      $acc = "SELECT * FROM tai_khoan WHERE email = '$email'";
      $result_acc = mysqli_query($conn, $acc);
      $row_acc = mysqli_fetch_array($result_acc);
    } else {
      // Nếu không có session, tạo một mảng trống để tránh lỗi
      $row_acc = array(
        'hinh_anh' => 'default.jpg',
        'ten' => 'Người dùng',
        'ho' => '',
        'quyen' => 0,
        'truy_cap' => 0
      );
    }
  }
?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <header class="main-header">
    <!-- Logo -->
    <a href="index.php?p=index&a=statistic" class="logo" style="display: flex; align-items: center; justify-content: center;">
      <!-- mini logo for sidebar mini 50x50 pixels -->
      <img class="logo-mini" src="../dist/images/logo_mini.png" alt="" width="45" style="margin: 0 auto;"/>
      <!-- <span class="logo-mini"><b></b></span> -->
      <img class="logo-lg" src="../dist/images/logoHRM_lg.png" alt="" width="150" style="margin: 0 auto;"/>
      <!-- logo for regular state and mobile devices -->
    </a>
    <!-- Header Navbar: style can be found in header.less -->
    <nav class="navbar navbar-static-top">
      <!-- Sidebar toggle button-->
      <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
        <span class="sr-only">Toggle navigation</span>
      </a>

      <div class="navbar-custom-menu">
        <ul class="nav navbar-nav">
          <!-- Messages: style can be found in dropdown.less-->
         
          <!-- Tasks: style can be found in dropdown.less -->
          <li class="dropdown tasks-menu">
            <!-- <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <i class="fa fa-flag-o"></i>
              <span class="label label-danger">9</span>
            </a> -->
            <ul class="dropdown-menu">
              <li class="header">You have 9 tasks</li>
              <li>
                <!-- inner menu: contains the actual data -->
                <ul class="menu">
                  <li><!-- Task item -->
                    <a href="#">
                      <h3>
                        Design some buttons
                        <small class="pull-right">20%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-aqua" style="width: 20%" role="progressbar"
                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">20% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                  <li><!-- Task item -->
                    <a href="#">
                      <h3>
                        Create a nice theme
                        <small class="pull-right">40%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-green" style="width: 40%" role="progressbar"
                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">40% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                  <li><!-- Task item -->
                    <a href="#">
                      <h3>
                        Some task I need to do
                        <small class="pull-right">60%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-red" style="width: 60%" role="progressbar"
                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">60% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                  <li><!-- Task item -->
                    <a href="#">
                      <h3>
                        Make beautiful transitions
                        <small class="pull-right">80%</small>
                      </h3>
                      <div class="progress xs">
                        <div class="progress-bar progress-bar-yellow" style="width: 80%" role="progressbar"
                             aria-valuenow="20" aria-valuemin="0" aria-valuemax="100">
                          <span class="sr-only">80% Complete</span>
                        </div>
                      </div>
                    </a>
                  </li>
                  <!-- end task item -->
                </ul>
              </li>
              <li class="footer">
                <a href="#">View all tasks</a>
              </li>
            </ul>
          </li>
          <!-- User Account: style can be found in dropdown.less -->
          <li class="dropdown user user-menu">
            <a href="#" class="dropdown-toggle" data-toggle="dropdown">
              <img src="../uploads/images/<?php echo $row_acc['hinh_anh']; ?>" class="user-image" alt="User Image">
              <span class="hidden-xs"><?php echo $row_acc['ten']; ?> <?php echo $row_acc['ho']; ?></span>
            </a>
            <ul class="dropdown-menu">
              <!-- User image -->
              <li class="user-header">
                <img src="../uploads/images/<?php echo $row_acc['hinh_anh']; ?>" class="img-circle" alt="User Image">

                <p>
                  <?php echo $row_acc['ten']; ?> <?php echo $row_acc['ho']; ?> - 
                  <?php 
                    if($row_acc['quyen'] == 1)
                    {
                      echo "Quản trị viên";
                    }
                    else
                    {
                      echo "Nhân viên";
                    }
                  ?>
                  <small>
                    <?php 
                      echo "Lượt truy cập: " . $row_acc['truy_cap']; 
                    ?>
                  </small>
                </p>
              </li>
              <!-- Menu Body -->
              <!-- Menu Footer-->
              <li class="user-footer">
                <div class="pull-left">
                  <a href="thong-tin-tai-khoan.php?p=account&a=profile" class="btn btn-default btn-flat">Thông tin</a>
                </div>
                <div class="pull-right">
                  <a href="dang-xuat.php" class="btn btn-default btn-flat">Đăng xuất</a>
                </div>
              </li>
            </ul>
          </li>
          <!-- Control Sidebar Toggle Button -->
          <li>
            <a href="../pages/index-scan-qr.php" target="_blank" title="Mở trang chấm công QR" class="btn-qr-scan"><i class="fa fa-qrcode"></i></a>
          </li>
        </ul>
      </div>
    </nav>
  </header>