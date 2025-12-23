@extends('layouts.app')

@section('content')
<div style="font-family: 'Tajawal', sans-serif; direction: rtl;" class="p-4">
    <div class="text-center mb-4">
        <h2>تقرير الجرد {{ $type == 'daily' ? 'اليومي' : 'الشهري' }}</h2>
        <p>الفترة من {{ $startDate }} إلى {{ $endDate }}</p>
        <p>تاريخ التصدير: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <table class="table table-bordered" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd;">التاريخ</th>
                <th style="padding: 10px; border: 1px solid #ddd;">نوع الوقود</th>
                <th style="padding: 10px; border: 1px solid #ddd;">التانك</th>
                <th style="padding: 10px; border: 1px solid #ddd;">الرصيد النظري</th>
                <th style="padding: 10px; border: 1px solid #ddd;">الرصيد الفعلي</th>
                <th style="padding: 10px; border: 1px solid #ddd;">الفارق</th>
                <th style="padding: 10px; border: 1px solid #ddd;">المسؤول</th>
            </tr>
        </thead>
        <tbody>
            @forelse($inventories as $inventory)
                <tr>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $inventory->inventory_date }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $inventory->tank->fuel->name ?? '-' }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $inventory->tank->name ?? '-' }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: left;">{{ number_format($inventory->theoretical_balance, 2) }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: left;">{{ number_format($inventory->actual_balance, 2) }}</td>
                    <td style="padding: 10px; border: 1px solid #ddd; text-align: left; color: {{ $inventory->difference >= 0 ? 'green' : 'red' }};">
                        {{ $inventory->difference >= 0 ? '+' : '' }}{{ number_format($inventory->difference, 2) }}
                    </td>
                    <td style="padding: 10px; border: 1px solid #ddd;">{{ $inventory->user->name ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="padding: 20px; text-align: center; border: 1px solid #ddd;">لا توجد بيانات</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot style="background-color: #f8f9fa; font-weight: bold;">
            <tr>
                <td colspan="4" style="padding: 10px; border: 1px solid #ddd; text-align: center;">إجمالي الفروقات</td>
                <td colspan="3" style="padding: 10px; border: 1px solid #ddd; text-align: left; color: {{ $inventories->sum('difference') >= 0 ? 'green' : 'red' }};">
                    {{ $inventories->sum('difference') >= 0 ? '+' : '' }}{{ number_format($inventories->sum('difference'), 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <div class="mt-4">
        <p><strong>ملخص:</strong></p>
        <ul>
            <li>إجمالي الفروقات الموجبة: {{ number_format($inventories->where('difference', '>', 0)->sum('difference'), 2) }}</li>
            <li>إجمالي الفروقات السالبة: {{ number_format(abs($inventories->where('difference', '<', 0)->sum('difference')), 2) }}</li>
            <li>صافي الفارق: {{ number_format($inventories->sum('difference'), 2) }}</li>
        </ul>
    </div>
</div>
@endsection
