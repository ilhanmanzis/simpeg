<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class PresensiSemuaExport extends DefaultValueBinder implements
    FromArray,
    WithHeadings,
    WithCustomStartCell,
    WithEvents,
    WithCustomValueBinder
{
    protected $rows;
    protected $periode;

    public function __construct($rows, $periode)
    {
        $this->rows = $rows;
        $this->periode = $periode;
    }
    public function bindValue(Cell $cell, $value)
    {
        if ($value === 0) {
            $cell->setValueExplicit(0, DataType::TYPE_NUMERIC);
            return true;
        }

        return parent::bindValue($cell, $value);
    }

    /** DATA */

    public function array(): array
    {
        return array_map(function ($r) {

            return [
                (int)($r['no'] ?? 0),
                $r['npp'] ?? '',
                $r['nama'] ?? '',

                (string)($r['hadir'] ?? 0),
                (string)($r['sakit'] ?? 0),
                (string)($r['izin'] ?? 0),

                (string)($r['memenuhi'] ?? 0),
                (string)($r['tidak_memenuhi'] ?? 0),

                $r['durasi'] ?? '00:00',

                (string)($r['SS'] ?? 0),
                (string)($r['SM'] ?? 0),
                (string)($r['PS'] ?? 0),
                (string)($r['PM'] ?? 0),

                (string)($r['total_sks'] ?? 0),

                (string)($r['Sem'] ?? 0),
                (string)($r['Bim'] ?? 0),
                (string)($r['Uji'] ?? 0),
                (string)($r['KKL'] ?? 0),
                (string)($r['TL'] ?? 0),
            ];
        }, $this->rows);
    }

    /** HEADER KOLOM */
    public function headings(): array
    {
        return [
            'No',
            'NPP',
            'Nama',

            'Hadir',
            'Sakit',
            'Izin',

            'Memenuhi',
            'Tidak Memenuhi',

            'Durasi',

            'SKS Siang',
            'SKS Malam',
            'SKS Praktikum Siang',
            'SKS Praktikum Malam',

            'Total SKS',

            'Seminar',
            'Bimbingan',
            'Penguji',
            'KKL',
            'Tugas Luar'
        ];
    }

    /** HEADER TABEL MULAI BARIS 3 */
    public function startCell(): string
    {
        return 'A3';
    }

    /** JUDUL DI BARIS 1 */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {

                $sheet = $event->sheet->getDelegate();

                $rowCount = count($this->rows);
                $lastRow = $rowCount + 3; // baris data terakhir (header di baris 3)

                // ===== JUDUL =====
                $sheet->setCellValue('A1', 'Daftar Presensi ' . $this->periode);
                $sheet->mergeCells('A1:S1');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                // ===== HEADER =====
                $sheet->getStyle('A3:S3')->getFont()->setBold(true);

                // ===== AUTO WIDTH =====
                foreach (range('A', 'S') as $col) {
                    $sheet->getColumnDimension($col)->setAutoSize(true);
                }

                // ===== FORMAT ANGKA (AGAR 0 MUNCUL) =====
                $sheet->getStyle("D4:S{$lastRow}")
                    ->getNumberFormat()
                    ->setFormatCode('0');

                // ===== CENTER ALIGNMENT =====
                $sheet->getStyle("A3:S{$lastRow}")
                    ->getAlignment()
                    ->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);

                $sheet->getStyle("A3:A{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                // ===== BORDER SESUAI JUMLAH DATA =====
                $sheet->getStyle("A3:S{$lastRow}")
                    ->getBorders()
                    ->getAllBorders()
                    ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                $sheet->getStyle('A3:S3')
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                // ===== ALIGNMENT DATA =====

                // ===== ALIGNMENT DATA =====

                // No → center
                $sheet->getStyle("A4:A{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // NPP → left
                $sheet->getStyle("B4:B{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Nama → left
                $sheet->getStyle("C4:C{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // Hadir sampai Tidak Memenuhi → center
                $sheet->getStyle("D4:H{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);

                // Durasi → left
                $sheet->getStyle("I4:I{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_LEFT);

                // SKS sampai Tugas Luar → center
                $sheet->getStyle("J4:S{$lastRow}")
                    ->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
