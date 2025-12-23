<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\Tank;

class TankReportExport implements FromCollection, WithHeadings, WithTitle, WithEvents, WithColumnFormatting
{
    protected $tank;

    public function __construct(Tank $tank)
    {
        $this->tank = $tank;
    }

    public function collection()
    {
        $data = [];
        
        // Tank info row
        $data[] = [
            'معلومات التانك' => '',
            '' => '',
            '' => '',
            '' => ''
        ];
        
        $data[] = [
            'رقم التانك' => $this->tank->id,
            'السعة الكلية' => $this->tank->capacity . ' لتر',
            'المخزون الحالي' => ($this->tank->current_level ?? 0) . ' لتر',
            'السعر للعميل' => $this->tank->fuel->price_per_liter . ' ج.م'
        ];
        
        $data[] = [
            'اللترات المسحوبة' => ($this->tank->liters_drawn ?? 0) . ' لتر',
            'السعر الأصلي' => $this->tank->fuel->price_for_owner . ' ج.م',
            'النسبة المتبقية' => ($this->tank->capacity > 0 ? number_format(($this->tank->current_level / $this->tank->capacity) * 100, 1) : 0) . '%',
            'عدد الطلمبات' => $this->tank->pumps->count()
        ];
        
        $data[] = []; // Empty row
        
        // Pumps and Nozzles header
        $data[] = [
            'الطلمبات والمسدسات' => '',
            '' => '',
            '' => ''
        ];
        
        $data[] = [
            'الطلمبة' => 'المسدس',
            'العداد الحالي' => 'الحالة'
        ];
        
        foreach ($this->tank->pumps as $pump) {
            foreach ($pump->nozzles as $nozzle) {
                $data[] = [
                    'الطلمبة' => $pump->name,
                    'المسدس' => $nozzle->name,
                    'العداد الحالي' => $nozzle->meter_reading,
                    'الحالة' => 'نشط'
                ];
            }
        }
        
        return collect($data);
    }

    public function headings(): array
    {
        return [
            ['تقرير تفصيلي - ' . $this->tank->name],
            ['نوع الوقود: ' . $this->tank->fuel->name],
            ['تاريخ التصدير: ' . now()->format('Y-m-d H:i')],
            [],
            ['البيان', 'القيمة', 'بيانات إضافية', 'ملاحظات']
        ];
    }

    public function title(): string
    {
        return 'تقرير التانك';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set title row styles
                $sheet->getStyle('A1:D1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A2:D2')->getFont()->setBold(true);
                $sheet->getStyle('A3:D3')->getFont()->setBold(true);
                
                // Set header row styles
                $sheet->getStyle('A5:D5')->getFont()->setBold(true);
                $sheet->getStyle('A5:D5')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0E0E0');
                
                // Auto-size columns
                foreach (range('A', 'D') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Set right-to-left direction
                $sheet->setRightToLeft(true);
                
                // Merge title cells
                $sheet->mergeCells('A1:D1');
                $sheet->mergeCells('A2:D2');
                $sheet->mergeCells('A3:D3');
                
                // Center align title rows
                $sheet->getStyle('A1:D3')->getAlignment()->setHorizontal('center');
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'B' => NumberFormat::FORMAT_NUMBER,
            'C' => NumberFormat::FORMAT_NUMBER,
        ];
    }
}
