<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DailySummaryExport implements FromArray, WithHeadings, WithStyles, WithTitle, WithColumnWidths
{
    protected $data;
    protected $date;

    public function __construct($data, $date)
    {
        $this->data = $data;
        $this->date = $date;
    }

    public function array(): array
    {
        return [
            ['الرصيد أول اليوم', '-', $this->data['solarData']['opening_balance'] ?? 0, $this->data['benzine92Data']['opening_balance'] ?? 0, $this->data['benzine80Data']['opening_balance'] ?? 0, $this->data['benzine95Data']['opening_balance'] ?? 0],
            ['الوارد', $this->data['invoiceNumber'] ?? '-', $this->data['solarData']['received'] ?? 0, $this->data['benzine92Data']['received'] ?? 0, $this->data['benzine80Data']['received'] ?? 0, $this->data['benzine95Data']['received'] ?? 0],
            ['البيعات', $this->data['dispensedInvoiceNumber'] ?? '-', $this->data['solarData']['sales'] ?? 0, $this->data['benzine92Data']['sales'] ?? 0, $this->data['benzine80Data']['sales'] ?? 0, $this->data['benzine95Data']['sales'] ?? 0],
            ['المنصرف', $this->data['dispensedInvoiceNumber'] ?? '-', $this->data['solarData']['dispensed'] ?? 0, $this->data['benzine92Data']['dispensed'] ?? 0, $this->data['benzine80Data']['dispensed'] ?? 0, $this->data['benzine95Data']['dispensed'] ?? 0],
            ['الجملة', '-', 
                (($this->data['solarData']['opening_balance'] ?? 0) + ($this->data['solarData']['received'] ?? 0) - ($this->data['solarData']['dispensed'] ?? 0)),
                (($this->data['benzine92Data']['opening_balance'] ?? 0) + ($this->data['benzine92Data']['received'] ?? 0) - ($this->data['benzine92Data']['dispensed'] ?? 0)),
                (($this->data['benzine80Data']['opening_balance'] ?? 0) + ($this->data['benzine80Data']['received'] ?? 0) - ($this->data['benzine80Data']['dispensed'] ?? 0)),
                (($this->data['benzine95Data']['opening_balance'] ?? 0) + ($this->data['benzine95Data']['received'] ?? 0) - ($this->data['benzine95Data']['dispensed'] ?? 0))
            ],
            [],
            ['ملخص الحركة اليومية'],
            [],
            ['البيان', 'سولار', 'بنزين 92', 'بنزين 80', 'بنزين 95'],
            ['مجموع الوارد', 
                (($this->data['solarData']['received'] ?? 0) + ($this->data['solarData']['opening_balance'] ?? 0)),
                (($this->data['benzine92Data']['received'] ?? 0) + ($this->data['benzine92Data']['opening_balance'] ?? 0)),
                (($this->data['benzine80Data']['received'] ?? 0) + ($this->data['benzine80Data']['opening_balance'] ?? 0)),
                (($this->data['benzine95Data']['received'] ?? 0) + ($this->data['benzine95Data']['opening_balance'] ?? 0))
            ],
            ['مجموع المنصرف', 
                ($this->data['solarData']['dispensed'] ?? 0),
                ($this->data['benzine92Data']['dispensed'] ?? 0),
                ($this->data['benzine80Data']['dispensed'] ?? 0),
                ($this->data['benzine95Data']['dispensed'] ?? 0)
            ],
            ['الرصيد', 
                (($this->data['solarData']['opening_balance'] ?? 0) + ($this->data['solarData']['received'] ?? 0) - ($this->data['solarData']['dispensed'] ?? 0)),
                (($this->data['benzine92Data']['opening_balance'] ?? 0) + ($this->data['benzine92Data']['received'] ?? 0) - ($this->data['benzine92Data']['dispensed'] ?? 0)),
                (($this->data['benzine80Data']['opening_balance'] ?? 0) + ($this->data['benzine80Data']['received'] ?? 0) - ($this->data['benzine80Data']['dispensed'] ?? 0)),
                (($this->data['benzine95Data']['opening_balance'] ?? 0) + ($this->data['benzine95Data']['received'] ?? 0) - ($this->data['benzine95Data']['dispensed'] ?? 0))
            ],
            ['الرصيد الفعلي نهاية اليوم', 
                ($this->data['solarData']['actual_balance'] ?? 0),
                ($this->data['benzine92Data']['actual_balance'] ?? 0),
                ($this->data['benzine80Data']['actual_balance'] ?? 0),
                ($this->data['benzine95Data']['actual_balance'] ?? 0)
            ],
            ['العجز أو الزيادة', 
                ((($this->data['solarData']['opening_balance'] ?? 0) + ($this->data['solarData']['received'] ?? 0) - ($this->data['solarData']['dispensed'] ?? 0)) - ($this->data['solarData']['actual_balance'] ?? 0)),
                ((($this->data['benzine92Data']['opening_balance'] ?? 0) + ($this->data['benzine92Data']['received'] ?? 0) - ($this->data['benzine92Data']['dispensed'] ?? 0)) - ($this->data['benzine92Data']['actual_balance'] ?? 0)),
                ((($this->data['benzine80Data']['opening_balance'] ?? 0) + ($this->data['benzine80Data']['received'] ?? 0) - ($this->data['benzine80Data']['dispensed'] ?? 0)) - ($this->data['benzine80Data']['actual_balance'] ?? 0)),
                ((($this->data['benzine95Data']['opening_balance'] ?? 0) + ($this->data['benzine95Data']['received'] ?? 0) - ($this->data['benzine95Data']['dispensed'] ?? 0)) - ($this->data['benzine95Data']['actual_balance'] ?? 0))
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'البيان',
            'رقم الفاتورة',
            'سولار',
            'بنزين 92',
            'بنزين 80',
            'بنزين 95',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E9ECEF']]],
            8 => ['font' => ['bold' => true, 'size' => 14]],
            10 => ['font' => ['bold' => true], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => 'E9ECEF']]],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 20,
            'C' => 15,
            'D' => 15,
            'E' => 15,
            'F' => 15,
        ];
    }

    public function title(): string
    {
        return 'الجرد اليومي ' . $this->date;
    }
}
