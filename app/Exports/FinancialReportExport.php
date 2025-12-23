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

class FinancialReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $reportData;
    protected $startDate;
    protected $endDate;
    protected $totalRevenue;
    protected $totalExpense;
    protected $netProfit;

    public function __construct($reportData, $startDate, $endDate, $totalRevenue, $totalExpense, $netProfit)
    {
        $this->reportData = $reportData;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->totalRevenue = $totalRevenue;
        $this->totalExpense = $totalExpense;
        $this->netProfit = $netProfit;
    }

    public function collection()
    {
        return collect($this->reportData);
    }

    public function headings(): array
    {
        return [
            ['كشف الحساب المالي'],
            ['من ' . $this->startDate . ' إلى ' . $this->endDate],
            [],
            ['التاريخ', 'النوع', 'الفئة', 'البيان', 'المبلغ', 'الرصيد'],
        ];
    }

    public function map($row): array
    {
        return [
            $row['date']->format('Y-m-d H:i'),
            $row['category'],
            $row['type'],
            $row['description'],
            number_format($row['amount'], 2),
            number_format($row['balance'], 2),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 16],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            2 => [
                'font' => ['size' => 12],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            ],
            4 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E8EBED']
                ],
            ],
        ];
    }

    public function title(): string
    {
        return 'التقرير المالي';
    }
}
