-- Script tích hợp hệ thống chấm công vào cơ sở dữ liệu quản lý nhân sự
-- Ngày tạo: 2025-04-12
-- Author: Claude AI

-- Bổ sung cấu hình vào bảng cấu hình
-- Lưu ý: Nếu bạn đã có bảng cấu hình, bổ sung các trường sau.
-- Nếu chưa có, hãy tạo bảng mới.

-- Tạo bảng cấu hình nếu chưa có
-- CREATE TABLE IF NOT EXISTS `cau_hinh` (
--   `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
--   `ten_cau_hinh` varchar(255) NOT NULL,
--   `gia_tri` varchar(255) NOT NULL,
--   `mo_ta` text,
--   `nguoi_tao` varchar(50),
--   `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
--   `nguoi_sua` varchar(50),
--   `ngay_sua` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- -- Bổ sung các thông số cấu hình thời gian làm việc
-- INSERT INTO `cau_hinh` (`ten_cau_hinh`, `gia_tri`, `mo_ta`, `nguoi_tao`, `ngay_tao`) VALUES
-- ('gio_vao_thuong', '08:00:00', 'Giờ vào làm chuẩn ngày thường (Thứ 2 - Thứ 6)', 'Admin', NOW()),
-- ('gio_ra_thuong', '17:00:00', 'Giờ ra về chuẩn ngày thường (Thứ 2 - Thứ 6)', 'Admin', NOW()),
-- ('gio_vao_thu7', '08:00:00', 'Giờ vào làm chuẩn thứ 7', 'Admin', NOW()),
-- ('gio_ra_thu7', '12:00:00', 'Giờ ra về chuẩn thứ 7 (buổi trưa)', 'Admin', NOW()),
-- ('muc_tre_chap_nhan', '15', 'Số phút trễ được chấp nhận', 'Admin', NOW()),
-- ('muc_ve_som_chap_nhan', '15', 'Số phút về sớm được chấp nhận', 'Admin', NOW());

-- Tạo bảng chấm công
CREATE TABLE IF NOT EXISTS `cham_cong` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nhanvien_id` int(11) NOT NULL,
  `ngay` date NOT NULL,
  `gio_vao` time DEFAULT NULL,
  `gio_ra` time DEFAULT NULL,
  `trang_thai_vao` enum('dung_gio','tre','khong_du_lieu') DEFAULT 'khong_du_lieu',
  `trang_thai_ra` enum('dung_gio','som','khong_du_lieu') DEFAULT 'khong_du_lieu',
  `hop_le` tinyint(1) DEFAULT 0,
  `ghi_chu` text,
  `nguoi_tao` varchar(50),
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `nguoi_sua` varchar(50),
  `ngay_sua` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo index để tối ưu truy vấn
CREATE INDEX `idx_nhanvien_ngay` ON `cham_cong` (`nhanvien_id`, `ngay`);
CREATE INDEX `idx_ngay` ON `cham_cong` (`ngay`);

-- Tạo bảng tổng kết chuyên cần
CREATE TABLE IF NOT EXISTS `chuyen_can` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nhanvien_id` int(11) NOT NULL,
  `thang` int(11) NOT NULL,
  `nam` int(11) NOT NULL,
  `so_buoi_hop_le` decimal(5,1) DEFAULT 0,
  `so_ngay_nghi` decimal(5,1) DEFAULT 0,
  `so_lan_di_tre` int(11) DEFAULT 0,
  `so_lan_ve_som` int(11) DEFAULT 0,
  `ty_le_chuyen_can` decimal(5,2) DEFAULT 0,
  `nguoi_tao` varchar(50),
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `nguoi_sua` varchar(50),
  `ngay_sua` datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_nhanvien_thang_nam` (`nhanvien_id`, `thang`, `nam`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tạo index để tối ưu truy vấn
CREATE INDEX `idx_thang_nam` ON `chuyen_can` (`thang`, `nam`);

-- Tạo bảng lưu trữ QR Code nhân viên (tùy chọn)
CREATE TABLE IF NOT EXISTS `qr_code_nhanvien` (
  `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `nhanvien_id` int(11) NOT NULL,
  `ma_qr` varchar(255) NOT NULL,
  `ngay_tao` datetime DEFAULT CURRENT_TIMESTAMP,
  `trang_thai` tinyint(1) DEFAULT 1,
  FOREIGN KEY (`nhanvien_id`) REFERENCES `nhanvien`(`id`) ON DELETE CASCADE,
  UNIQUE KEY `unique_nhanvien` (`nhanvien_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- Tự động tạo mã QR cho tất cả nhân viên hiện có
INSERT INTO `qr_code_nhanvien` (`nhanvien_id`, `ma_qr`)
SELECT `id`, CONCAT('nhan-vien-qr-code-', `id`) FROM `nhanvien` 
WHERE `id` NOT IN (SELECT `nhanvien_id` FROM `qr_code_nhanvien`);

-- Bổ sung thủ tục tạo báo cáo chấm công tự động
DELIMITER //
CREATE PROCEDURE IF NOT EXISTS `sp_TinhTongKetChuyenCan`(
  IN p_thang INT, 
  IN p_nam INT
)
BEGIN
  -- Tính số ngày làm việc trong tháng (không tính CN, T7 tính nửa ngày)
  DECLARE so_ngay_lam_viec DECIMAL(5,1) DEFAULT 0;
  DECLARE ngay_bat_dau DATE;
  DECLARE ngay_cuoi_thang DATE;
  DECLARE ngay_hien_tai DATE;
  DECLARE thu_trong_tuan INT;
  
  SET ngay_bat_dau = CONCAT(p_nam, '-', p_thang, '-01');
  SET ngay_cuoi_thang = LAST_DAY(ngay_bat_dau);
  SET ngay_hien_tai = ngay_bat_dau;
  
  WHILE ngay_hien_tai <= ngay_cuoi_thang DO
    SET thu_trong_tuan = DAYOFWEEK(ngay_hien_tai);
    IF thu_trong_tuan = 1 THEN -- Chủ nhật
      SET so_ngay_lam_viec = so_ngay_lam_viec + 0;
    ELSEIF thu_trong_tuan = 7 THEN -- Thứ 7
      SET so_ngay_lam_viec = so_ngay_lam_viec + 0.5;
    ELSE -- Thứ 2 đến thứ 6
      SET so_ngay_lam_viec = so_ngay_lam_viec + 1;
    END IF;
    SET ngay_hien_tai = DATE_ADD(ngay_hien_tai, INTERVAL 1 DAY);
  END WHILE;
  
  -- Xóa dữ liệu cũ nếu đã tồng kết tháng này 
  DELETE FROM `chuyen_can` WHERE `thang` = p_thang AND `nam` = p_nam;
  
  -- Thống kê chuyên cần cho từng nhân viên
  INSERT INTO `chuyen_can` 
    (`nhanvien_id`, `thang`, `nam`, `so_buoi_hop_le`, `so_ngay_nghi`, 
     `so_lan_di_tre`, `so_lan_ve_som`, `ty_le_chuyen_can`, `nguoi_tao`)
  SELECT 
    nv.id, 
    p_thang,
    p_nam,
    IFNULL(SUM(IF(cc.hop_le = 1, 
                 IF(DAYOFWEEK(cc.ngay) = 7, 0.5, 1), -- nếu là thứ 7 thì tính 0.5, ngược lại tính 1
                 0)), 0) AS so_buoi_hop_le,
    so_ngay_lam_viec - IFNULL(COUNT(DISTINCT cc.ngay), 0) AS so_ngay_nghi,
    IFNULL(SUM(IF(cc.trang_thai_vao = 'tre', 1, 0)), 0) AS so_lan_di_tre,
    IFNULL(SUM(IF(cc.trang_thai_ra = 'som', 1, 0)), 0) AS so_lan_ve_som,
    IFNULL(SUM(IF(cc.hop_le = 1, 
                 IF(DAYOFWEEK(cc.ngay) = 7, 0.5, 1),
                 0)) / so_ngay_lam_viec * 100, 0) AS ty_le_chuyen_can,
    'System'
  FROM 
    `nhanvien` nv
  LEFT JOIN 
    `cham_cong` cc ON nv.id = cc.nhanvien_id 
                  AND MONTH(cc.ngay) = p_thang 
                  AND YEAR(cc.ngay) = p_nam
  GROUP BY 
    nv.id;
    
END //
DELIMITER ;

-- Tạo Event để tự động chạy thủ tục vào cuối mỗi tháng (tùy chọn)
-- Lưu ý: Cần bật event scheduler: SET GLOBAL event_scheduler = ON;
DELIMITER //
CREATE EVENT IF NOT EXISTS `event_TinhTongKetChuyenCanHangThang`
ON SCHEDULE EVERY 1 MONTH
STARTS CONCAT(DATE_FORMAT(LAST_DAY(NOW()), '%Y-%m-%d'), ' 23:00:00')
DO
BEGIN
  CALL sp_TinhTongKetChuyenCan(MONTH(NOW()), YEAR(NOW()));
END //
DELIMITER ;

-- Đối với việc di chuyển dữ liệu từ DB cũ sang, bạn có thể chạy câu lệnh sau khi đã có dữ liệu chấm công
-- CALL sp_TinhTongKetChuyenCan(MONTH(NOW()), YEAR(NOW())); 