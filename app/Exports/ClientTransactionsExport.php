<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use App\Models\Client;

class ClientTransactionsExport implements FromCollection, WithHeadings, WithTitle, WithEvents, WithColumnFormatting
{
    protected $client;
    protected $transactions;

    public function __construct(Client $client, $transactions)
    {
        $this->client = $client;
        $this->transactions = $transactions;
    }

    public function collection()
    {
        $data = [];
        
        foreach ($this->transactions as $transaction) {
            $data[] = [
                'التاريخ' => $transaction->created_at->format('Y-m-d H:i'),
                'الشيفت' => $transaction->shift->user->name ?? '-',
                'رقم العربية' => $transaction->transaction->vehicle_number ?? '-',
                'المسدس' => $this->getNozzleInfo($transaction),
                'عدد اللترات' => $transaction->liters,
                'سعر اللتر' => $transaction->price_per_liter,
                'الإجمالي' => $transaction->total_amount,
                'ملاحظات' => '-'
            ];
        }
        
        return collect($data);
    }

    private function getNozzleInfo($transaction)
    {
        if ($transaction->transaction && $transaction->transaction->nozzle) {
            $nozzle = $transaction->transaction->nozzle;
            $fuel = $nozzle->pump->tank->fuel->name ?? '';
            $tank = $nozzle->pump->tank->name ?? '';
            $pump = $nozzle->pump->name ?? '';
            $nozzleName = $nozzle->name ?? '';
            
            return "{$fuel} - تانك: {$tank} - {$pump} - {$nozzleName}";
        }
        
        return '-';
    }

    public function headings(): array
    {
        return [
            ['كشف حساب العميل'],
            ['اسم العميل: ' . $this->client->name],
            ['تاريخ التصدير: ' . now()->format('Y-m-d H:i')],
            [],
            ['التاريخ', 'الشيفت', 'رقم العربية', 'المسدس', 'عدد اللترات', 'سعر اللتر', 'الإجمالي', 'ملاحظات']
        ];
    }

    public function title(): string
    {
        return 'معاملات العميل';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                
                // Set title row styles
                $sheet->getStyle('A1:H1')->getFont()->setBold(true)->setSize(16);
                $sheet->getStyle('A2:H2')->getFont()->setBold(true);
                $sheet->getStyle('A3:H3')->getFont()->setBold(true);
                
                // Set header row styles
                $sheet->getStyle('A5:H5')->getFont()->setBold(true);
                $sheet->getStyle('A5:H5')->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('FFE0E0E0');
                
                // Auto-size columns
                foreach (range('A', 'H') as $column) {
                    $sheet->getColumnDimension($column)->setAutoSize(true);
                }
                
                // Set right-to-left direction
                $sheet->setRightToLeft(true);
                
                // Merge title cells
                $sheet->mergeCells('A1:H1');
                $sheet->mergeCells('A2:H2');
                $sheet->mergeCells('A3:H3');
                
                // Center align title rows
                $sheet->getStyle('A1:H3')->getAlignment()->setHorizontal('center');
            },
        ];
    }

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED2,
            'G' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
        ];
    }
}
