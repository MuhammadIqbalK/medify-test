<?php

namespace App\Exports;

use App\Models\MasterItem;

class MasterItemsExcel
{
    public function download()
    {
        $workbook = new \PhpOffice\PhpSpreadsheet\Spreadsheet;
        $sheet = $workbook->getActiveSheet();
        $sheet->setTitle('Master Items');
        $sheet->fromArray(['No', 'Nama kategori', 'Nama items', 'Nama supplier', 'Harga', 'Laba', 'Harga jual'], null, 'A1');
        $row = 2;
        foreach (MasterItem::with(['categories' => fn ($query) => $query->orderBy('name')])->orderBy('id')->lazy(500) as $item) {
            $sheet->setCellValue('A'.$row, $row - 1);
            foreach (['B' => $item->categories->pluck('name')->implode(', '), 'C' => $item->nama, 'D' => $item->supplier] as $column => $value) {
                $sheet->setCellValueExplicit($column.$row, $value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            }
            $sheet->setCellValue('E'.$row, $item->harga_beli);
            $sheet->setCellValue('F'.$row, $item->laba / 100);
            $sheet->setCellValue('G'.$row, '=E'.$row.'*(1+F'.$row.')');
            $row++;
        }
        $lastRow = max(1, $row - 1);
        $sheet->getStyle('A1:G1')->getFont()->setBold(true)->getColor()->setARGB('FFFFFFFF');
        $sheet->getStyle('A1:G1')->getFill()->setFillType('solid')->getStartColor()->setARGB('FF24476A');
        foreach (['A' => 8, 'B' => 35, 'C' => 35, 'D' => 25, 'E' => 20, 'F' => 12, 'G' => 20] as $column => $width) {
            $sheet->getColumnDimension($column)->setWidth($width);
        }
        $sheet->getStyle('A1:G'.$lastRow)->getAlignment()->setWrapText(true)->setVertical('top');
        if ($lastRow > 1) {
            $sheet->getStyle('E2:E'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle('F2:F'.$lastRow)->getNumberFormat()->setFormatCode('0.##%');
            $sheet->getStyle('G2:G'.$lastRow)->getNumberFormat()->setFormatCode('#,##0.00');
        }
        $sheet->freezePane('A2');
        $sheet->setAutoFilter('A1:G'.$lastRow);

        return response()->streamDownload(function () use ($workbook) {
            try {
                (new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($workbook))->save('php://output');
            } finally {
                $workbook->disconnectWorksheets();
            }
        }, 'master-items.xlsx', ['Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet']);
    }
}
