<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class TreasuryExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['allTransactions'];
    }

    public function headings(): array
    {
        $title = $this->data['viewAll'] ? 'سجل جميع حركات الخزنة' : 'حركات الخزنة ليوم ' . $this->data['date'];

        return [
            [$title],
            ['الرصيد الافتتاحي: ' . number_format($this->data['openingBalance'], 2)],
            ['الرصيد الحالي: ' . number_format($this->data['currentBalance'], 2)],
            [],
            ['التاريخ', 'النوع', 'البند', 'الوصف', 'المبلغ', 'المستخدم', 'المصدر'],
        ];
    }

    public function map($row): array
    {
        return [
            is_string($row['date']) ? $row['date'] : $row['date']->format('Y-m-d H:i'),
            $row['type'] == 'income' ? 'إيراد' : 'مصروف',
            $row['category'],
            $row['description'],
            number_format($row['amount'], 2),
            $row['user'],
            $row['source'] == 'sales' ? 'مبيعات' : 'خزنة',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        
        return [
            1 => ['font' => ['bold' => true, 'size' => 16], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
            2 => ['font' => ['bold' => true]],
            3 => ['font' => ['bold' => true]],
            5 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'E8EBED']],
            ],
        ];
    }

    public function title(): string
    {
        return 'تقرير الخزنة';
    }
}
