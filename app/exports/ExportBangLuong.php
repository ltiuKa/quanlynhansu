<?php

namespace App\Exports;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExportBangLuong
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function export()
    {
        // Tạo một tài liệu Spreadsheet mới
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Bảng lương');

        // Định dạng tự động chiều rộng cột
        for ($i = 'A'; $i <= 'H'; $i++) {
            $sheet->getColumnDimension($i)->setAutoSize(true);
        }

        // Tô màu hàng tiêu đề
        $sheet->getStyle('A1:H1')->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setARGB('FFFFFF00');

        // Căn giữa hàng tiêu đề
        $sheet->getStyle('A1:H1')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER);

        // Số dòng
        $rowCount = 1;

        // Thiết lập hàng tiêu đề
        $sheet->setCellValue('A' . $rowCount, 'STT');
        $sheet->setCellValue('B' . $rowCount, 'Mã lương');
        $sheet->setCellValue('C' . $rowCount, 'Tên nhân viên');
        $sheet->setCellValue('D' . $rowCount, 'Chức vụ');
        $sheet->setCellValue('E' . $rowCount, 'Lương tháng');
        $sheet->setCellValue('F' . $rowCount, 'Ngày công');
        $sheet->setCellValue('G' . $rowCount, 'Thực lãnh');
        $sheet->setCellValue('H' . $rowCount, 'Ngày chấm công');

        // Lấy dữ liệu từ cơ sở dữ liệu
        $sql = "SELECT ma_luong, hinh_anh, nv.id as idNhanVien, ten_nv, ten_chuc_vu, luong_thang, ngay_cong, phu_cap, khoan_nop, tam_ung, thuc_lanh, ngay_cham FROM luong l, nhanvien nv, chuc_vu cv WHERE nv.id = l.nhanvien_id AND nv.chuc_vu_id = cv.id";
        $result = mysqli_query($this->conn, $sql);
        $stt = 0;

        while ($row = mysqli_fetch_array($result)) {
            // Tăng dòng và STT
            $rowCount++;
            $stt++;

            // Đổ dữ liệu vào các ô
            $sheet->setCellValue('A' . $rowCount, $stt);
            $sheet->setCellValue('B' . $rowCount, $row['ma_luong']);
            $sheet->setCellValue('C' . $rowCount, $row['ten_nv']);
            $sheet->setCellValue('D' . $rowCount, $row['ten_chuc_vu']);
            $sheet->setCellValue('E' . $rowCount, number_format($row['luong_thang']) . "vnđ");
            $sheet->setCellValue('F' . $rowCount, $row['ngay_cong']);
            $sheet->setCellValue('G' . $rowCount, number_format($row['thuc_lanh']) . "vnđ");
            $sheet->setCellValue('H' . $rowCount, $row['ngay_cham']);
        }

        // Thêm viền cho bảng
        $styleArray = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                ],
            ],
        ];
        $sheet->getStyle('A1:H' . $rowCount)->applyFromArray($styleArray);

        // Tạo writer để xuất file Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'bang-luong.xlsx';
        $writer->save($filename);

        // Thiết lập header để tải xuống file
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Length: ' . filesize($filename));
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate');
        header('Pragma: no-cache');
        readfile($filename);
        exit;
    }
} 