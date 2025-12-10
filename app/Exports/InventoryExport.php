<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class InventoryExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $inventories;

    public function __construct($inventories)
    {
        $this->inventories = $inventories;
    }

    public function collection()
    {
        return $this->inventories;
    }

    public function headings(): array
    {
        return [
            'التاريخ',
            'النوع',
            'الخزان',
            'نوع الوقود',
            'رصيد أول المدة',
            'الوارد',
            'المنصرف',
            'رصيد آخر المدة',
            'الرصيد الفعلي',
            'الفرق',
            'ملاحظات',
            'المستخدم',
        ];
    }

    public function map($inventory): array
    {
        return [
            $inventory->inventory_date->format('Y-m-d'),
            $inventory->type == 'daily' ? 'يومي' : 'شهري',
            $inventory->tank->name,
            $inventory->fuel_type,
            number_format($inventory->opening_balance, 2),
            number_format($inventory->purchases, 2),
            number_format($inventory->sales, 2),
            number_format($inventory->closing_balance, 2),
            number_format($inventory->actual_balance, 2),
            number_format($inventory->difference, 2),
            $inventory->notes ?? '-',
            $inventory->user->name,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
