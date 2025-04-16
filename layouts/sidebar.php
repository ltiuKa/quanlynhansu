<?php 
  // Đảm bảo ROOT_PATH đã được định nghĩa
  if (!defined('ROOT_PATH')) {
    define('ROOT_PATH', dirname(__DIR__));
  }

    // get active sidebar
    if(isset($_GET['p']) && isset($_GET['a']))
    {
        $p = $_GET['p'];
        $a = $_GET['a'];
    }
?>

<!-- Left side column. contains the logo and sidebar -->
  <aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">
      <!-- Sidebar user panel -->
      <div class="user-panel">
        <div class="pull-left image">
          <img src="../uploads/images/<?php echo $row_acc['hinh_anh']; ?>" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
          <p>
            <?php echo $row_acc['ten']; ?> <?php echo $row_acc['ho']; ?>
          </p>
          <a href="#"><i class="fa fa-circle text-success"></i>
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
          </a>
        </div>
      </div>
      <!-- search form -->
      <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
          <input type="text" name="q" class="form-control" placeholder="Tìm kiếm...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
      </form>
      <!-- /.search form -->
      <!-- sidebar menu: : style can be found in sidebar.less -->
      <ul class="sidebar-menu" data-widget="tree">
        <li class="header">CƠ SỞ DỮ LIỆU</li>
        <li class="<?php if($p == 'index') echo 'active'; ?> treeview">
          <a href="#">
            <i class="fa fa-dashboard"></i> <span>Tổng quan</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if($a == 'statistic') echo 'active'; ?>"><a href="index.php?p=index&a=statistic"><i class="fa fa-circle-o"></i> Thống kê</a></li>
            <li class="<?php if($a == 'nhanvien') echo 'active'; ?>"><a a href="ds-nhanvien.php?p=index&a=nhanvien"><i class="fa fa-circle-o"></i> Danh sách nhân viên</a></li>
            <li class="<?php if(($p == 'index') && ($a == 'taikhoan')) echo 'active'; ?>"><a href="index_taikhoan.php?p=index&a=taikhoan"><i class="fa fa-circle-o"></i> Danh sách tài khoản</a></li>
          </ul>
        </li>
        <li class="<?php if($p == 'staff') echo 'active'; ?> treeview">
          <a href="#">
            <i class="fa fa-users"></i>
            <span>Quản lý nhân viên</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if(($p == 'staff') && ($a == 'room')) echo 'active'; ?>"><a href="phong-ban.php?p=staff&a=room"><i class="fa fa-circle-o"></i> Phòng ban</a></li>
            <li class="<?php if(($p == 'staff') && ($a == 'position')) echo 'active'; ?>"><a href="chuc-vu.php?p=staff&a=position"><i class="fa fa-circle-o"></i> Chức vụ</a></li>
            <li class="<?php if(($p == 'staff') && ($a == 'level')) echo 'active'; ?>"><a href="trinh-do.php?p=staff&a=level"><i class="fa fa-circle-o"></i> Trình độ</a></li>
            <li class="<?php if(($p == 'staff') && ($a == 'specialize')) echo 'active'; ?>"><a href="chuyen-mon.php?p=staff&a=specialize"><i class="fa fa-circle-o"></i> Chuyên môn</a></li>
            <li class="<?php if(($p == 'staff') && ($a == 'certificate')) echo 'active'; ?>"><a href="bang-cap.php?p=staff&a=certificate"><i class="fa fa-circle-o"></i> Bằng cấp</a></li>
            <li class="<?php if(($p == 'staff') && ($a == 'employee-type')) echo 'active'; ?>"><a href="loai-nhan-vien.php?p=staff&a=employee-type"><i class="fa fa-circle-o"></i> Loại nhân viên</a></li>
            <li class="<?php if(($p == 'staff') && ($a == 'add-staff')) echo 'active'; ?>"><a href="them-nhan-vien.php?p=staff&a=add-staff"><i class="fa fa-circle-o"></i> Thêm mới nhân viên</a></li>
            <li class="<?php if(($p == 'staff') && ($a =='list-staff')) echo 'active'; ?>"><a href="danh-sach-nhan-vien.php?p=staff&a=list-staff"><i class="fa fa-circle-o"></i> Danh sách nhân viên</a></li>
          </ul>
        </li>

        <li class="<?php if($p == 'attendance') echo 'active'; ?> treeview">
          <a href="#">
            <i class="fa fa-calendar"></i>
            <span>Quản lý chấm công</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if(($p == 'attendance') && ($a == 'report')) echo 'active'; ?>">
              <a href="bao-cao-cham-cong.php?p=attendance&a=report"><i class="fa fa-circle-o"></i> Báo cáo chấm công</a></li>
            <li class="<?php if(($p == 'attendance') && ($a == 'create-code')) echo 'active'; ?>">
              <a href="tao-ma-qr-cham-cong.php?p=attendance&a=create-code"><i class="fa fa-circle-o"></i> Tạo mã chấm công</a></li>
          </ul>
        </li>




        <li class="<?php if($p == 'salary') echo 'active'; ?> treeview">
          <a href="#">
            <i class="fa fa-money"></i>
            <span>Quản lý lương</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if(($p == 'salary') && ($a =='salary')) echo 'active'; ?>"><a href="bang-luong.php?p=salary&a=x"><i class="fa fa-circle-o"></i> Bảng tính lương</a></li>
            <li class="<?php if(($p == 'salary') && ($a =='calculator')) echo 'active'; ?>"><a href="tinh-luong.php?p=salary&a=calculator"><i class="fa fa-circle-o"></i> Tính lương</a></li>
          </ul>
        </li>



        <li class="<?php if($p == 'collaborate') echo 'active'; ?> treeview">
          <a href="#">
            <i class="fa fa-files-o"></i>
            <span>Quản lý công tác</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if(($p == 'collaborate') && ($a =='add-collaborate')) echo 'active'; ?>"><a href="cong-tac.php?p=collaborate&a=add-collaborate"><i class="fa fa-circle-o"></i> Tạo công tác</a></li>
            <li class="<?php if(($p == 'collaborate') && ($a =='list-collaborate')) echo 'active'; ?>"><a href="danh-sach-cong-tac.php?p=collaborate&a=list-collaborate"><i class="fa fa-circle-o"></i> Danh sách công tác</a></li>
          </ul>
        </li>
    
       
        <li class="<?php if($p == 'group') echo 'active'; ?> treeview">
          <a href="#">
          <i class="fa fa-folder"></i>
                       <span>Nhóm nhân viên</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if(($p == 'group') && ($a =='add-group')) echo 'active'; ?>"><a href="tao-nhom.php?p=group&a=add-group"><i class="fa fa-circle-o"></i> Tạo nhóm</a></li>
            <li class="<?php if(($p == 'group') && ($a =='list-group')) echo 'active'; ?>"><a href="danh-sach-nhom.php?p=group&a=list-group"><i class="fa fa-circle-o"></i> Danh sách nhóm</a></li>
          </ul>
        </li>
        <li class="<?php if($p == 'bonus-discipline') echo 'active'; ?> treeview">
          <a href="#">
            <i class="fa fa-star"></i> <span>Khen thưởng - Kỷ luật</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if(($p == 'bonus-discipline') && ($a =='bonus')) echo 'active'; ?>"><a href="khen-thuong.php?p=bonus-discipline&a=bonus"><i class="fa fa-circle-o"></i>Khen thưởng</a></li>
            <li class="<?php if(($p == 'bonus-discipline') && ($a =='discipline')) echo 'active'; ?>"><a href="ky-luat.php?p=bonus-discipline&a=discipline"><i class="fa fa-circle-o"></i> Kỷ luật</a></li>
          </ul>
        </li>
        <!--
        
        -->

        <li class="<?php if($p == 'account') echo 'active'; ?> treeview">
          <a href="#">
            <i class="fa fa-user"></i> <span>Tài khoản</span>
            <span class="pull-right-container">
              <i class="fa fa-angle-left pull-right"></i>
            </span>
          </a>
          <ul class="treeview-menu">
            <li class="<?php if($a == 'profile') echo 'active'; ?>"><a href="thong-tin-tai-khoan.php?p=account&a=profile"><i class="fa fa-circle-o"></i> Thông tin tài khoản</a></li>
            <li class="<?php if($a == 'add-account') echo 'active'; ?>"><a href="tao-tai-khoan.php?p=account&a=add-account"><i class="fa fa-circle-o"></i> Tạo tài khoản</a></li>
            <li class="<?php if(($p == 'account') && ($a == 'list-account')) echo 'active'; ?>"><a href="ds-tai-khoan.php?p=account&a=list-account"><i class="fa fa-circle-o"></i> Danh sách tài khoản</a></li>
            <li class="<?php if($a == 'changepass') echo 'active'; ?>"><a href="doi-mat-khau.php?p=account&a=changepass"><i class="fa fa-circle-o"></i> Đổi mật khẩu</a></li>
            <li><a href="dang-xuat.php"><i class="fa fa-circle-o"></i> Đăng xuất</a></li>
          </ul>
        </li>

        
      </ul>
    </section>
    <!-- /.sidebar -->
  </aside>