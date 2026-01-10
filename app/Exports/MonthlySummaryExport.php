<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MonthlySummaryExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $month;

    public function __construct($data, $month)
    {
        $this->data = $data;
        $this->month = $month;
    }

    public function array(): array
    {
        $totalBalance = (($this->data['solarData']['balance'] ?? 0) + ($this->data['benzine92Data']['balance'] ?? 0) + ($this->data['benzine80Data']['balance'] ?? 0) + ($this->data['benzine95Data']['balance'] ?? 0) + ($this->data['oilsData']['balance'] ?? 0));
        $totalReceived = (($this->data['solarData']['received'] ?? 0) + ($this->data['benzine92Data']['received'] ?? 0) + ($this->data['benzine80Data']['received'] ?? 0) + ($this->data['benzine95Data']['received'] ?? 0) + ($this->data['oilsData']['received'] ?? 0));
        $totalDispensed = (($this->data['solarData']['dispensed'] ?? 0) + ($this->data['benzine92Data']['dispensed'] ?? 0) + ($this->data['benzine80Data']['dispensed'] ?? 0) + ($this->data['benzine95Data']['dispensed'] ?? 0) + ($this->data['oilsData']['dispensed'] ?? 0));
        $totalActual = (($this->data['solarData']['actual_balance'] ?? 0) + ($this->data['benzine92Data']['actual_balance'] ?? 0) + ($this->data['benzine80Data']['actual_balance'] ?? 0) + ($this->data['benzine95Data']['actual_balance'] ?? 0) + ($this->data['oilsData']['actual_balance'] ?? 0));
        
        return [
            ['سولار', ($this->data['solarData']['balance'] ?? 0), ($this->data['solarData']['received'] ?? 0), 
                (($this->data['solarData']['balance'] ?? 0) + ($this->data['solarData']['received'] ?? 0)), 
                ($this->data['solarData']['dispensed'] ?? 0), ($this->data['solarData']['actual_balance'] ?? 0)],
            ['بنزين 92', ($this->data['benzine92Data']['balance'] ?? 0), ($this->data['benzine92Data']['received'] ?? 0), 
                (($this->data['benzine92Data']['balance'] ?? 0) + ($this->data['benzine92Data']['received'] ?? 0)), 
                ($this->data['benzine92Data']['dispensed'] ?? 0), ($this->data['benzine92Data']['actual_balance'] ?? 0)],
            ['بنزين 80', ($this->data['benzine80Data']['balance'] ?? 0), ($this->data['benzine80Data']['received'] ?? 0), 
                (($this->data['benzine80Data']['balance'] ?? 0) + ($this->data['benzine80Data']['received'] ?? 0)), 
                ($this->data['benzine80Data']['dispensed'] ?? 0), ($this->data['benzine80Data']['actual_balance'] ?? 0)],
            ['بنزين 95', ($this->data['benzine95Data']['balance'] ?? 0), ($this->data['benzine95Data']['received'] ?? 0), 
                (($this->data['benzine95Data']['balance'] ?? 0) + ($this->data['benzine95Data']['received'] ?? 0)), 
                ($this->data['benzine95Data']['dispensed'] ?? 0), ($this->data['benzine95Data']['actual_balance'] ?? 0)],
            ['زيوت معينة', ($this->data['oilsData']['balance'] ?? 0), ($this->data['oilsData']['received'] ?? 0), 
                (($this->data['oilsData']['balance'] ?? 0) + ($this->data['oilsData']['received'] ?? 0)), 
                ($this->data['oilsData']['dispensed'] ?? 0), ($this->data['oilsData']['actual_balance'] ?? 0)],
            ['الإجمالي', $totalBalance, $totalReceived, ($totalBalance + $totalReceived), $totalDispensed, $totalActual],
        ];
    }

    public function headings(): array
    {
        return [
            'البيان',
            'الرصيد',
            'الوارد',
            'الجملة',
            'المنصرف',
            'الباقي',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E9ECEF']]],
            7 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'CFF4FC']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,
            'B' => 15,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function title(): string
    {
        return 'الجرد الشهري ' . $this->month;
    }
}
