<?php
// Đường dẫn đến autoload.php của Composer
require __DIR__ . '/../vendor/autoload.php';

// Sử dụng các lớp từ PhpSpreadsheet
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// Tạo một tài liệu Spreadsheet mới
$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();

// Đặt tiêu đề cho sheet
$sheet->setTitle("Danh sách nhân viên");

// Thêm dữ liệu vào các ô
$sheet->setCellValue('A1', 'Mã NV');
$sheet->setCellValue('B1', 'Họ và tên');
$sheet->setCellValue('C1', 'Chức vụ');
$sheet->setCellValue('D1', 'Phòng ban');

// Thêm một số dữ liệu mẫu
$sheet->setCellValue('A2', 'NV001');
$sheet->setCellValue('B2', 'Nguyễn Văn A');
$sheet->setCellValue('C2', 'Giám đốc');
$sheet->setCellValue('D2', 'Ban giám đốc');

$sheet->setCellValue('A3', 'NV002');
$sheet->setCellValue('B3', 'Trần Thị B');
$sheet->setCellValue('C3', 'Kế toán trưởng');
$sheet->setCellValue('D3', 'Kế toán');

// Định dạng tiêu đề đậm
$sheet->getStyle('A1:D1')->getFont()->setBold(true);

// Tự động điều chỉnh độ rộng cột
foreach(range('A','D') as $columnID) {
    $sheet->getColumnDimension($columnID)->setAutoSize(true);
}

// Tạo writer để xuất file Excel
$writer = new Xlsx($spreadsheet);

// Đặt header để tải xuống file
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="danh_sach_nhan_vien.xlsx"');
header('Cache-Control: max-age=0');

// Xuất file Excel trực tiếp đến output
$writer->save('php://output');
exit; 