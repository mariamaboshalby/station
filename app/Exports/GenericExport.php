<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class GenericExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle
{
    protected $collection;
    protected $headings;
    protected $mapCallback;
    protected $title;

    public function __construct($collection, $headings, $mapCallback, $title = 'Report')
    {
        $this->collection = $collection;
        $this->headings = $headings;
        $this->mapCallback = $mapCallback;
        $this->title = $title;
    }

    public function collection()
    {
        return $this->collection;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($row): array
    {
        return call_user_func($this->mapCallback, $row);
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->setRightToLeft(true);
        return [
            1 => ['font' => ['bold' => true, 'size' => 12], 'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]],
        ];
    }

    public function title(): string
    {
        return $this->title;
    }
}
