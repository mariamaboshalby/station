@extends('layouts.app')

@section('content')
<div style="font-family: 'Tajawal', sans-serif; direction: rtl;" class="p-4">
    <div class="text-center mb-4">
        <h2>تقرير تفصيلي - {{ $tank->name }}</h2>
        <p>{{ $tank->fuel->name }}</p>
        <p>تاريخ التصدير: {{ now()->format('Y-m-d H:i') }}</p>
    </div>

    <!-- Tank Info -->
    <table class="table table-bordered mb-4" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th colspan="6" style="padding: 15px; border: 1px solid #ddd; text-align: center;">معلومات التانك</th>
            </tr>
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd;">رقم التانك</th>
                <th style="padding: 10px; border: 1px solid #ddd;">السعة الكلية</th>
                <th style="padding: 10px; border: 1px solid #ddd;">المخزون الحالي</th>
                <th style="padding: 10px; border: 1px solid #ddd;">اللترات المسحوبة</th>
                <th style="padding: 10px; border: 1px solid #ddd;">السعر للعميل</th>
                <th style="padding: 10px; border: 1px solid #ddd;">السعر الأصلي</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">{{ $tank->id }}</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: left;">{{ number_format($tank->capacity) }} لتر</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: left;">{{ number_format($tank->current_level ?? 0) }} لتر</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: left;">{{ number_format($tank->liters_drawn ?? 0) }} لتر</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: left;">{{ $tank->fuel->price_per_liter }} ج.م</td>
                <td style="padding: 10px; border: 1px solid #ddd; text-align: left;">{{ $tank->fuel->price_for_owner }} ج.م</td>
            </tr>
        </tbody>
    </table>

    <!-- Pumps and Nozzles -->
    <table class="table table-bordered" style="width: 100%; border-collapse: collapse;">
        <thead style="background-color: #f8f9fa;">
            <tr>
                <th colspan="4" style="padding: 15px; border: 1px solid #ddd; text-align: center;">الطلمبات والمسدسات</th>
            </tr>
            <tr>
                <th style="padding: 10px; border: 1px solid #ddd;">الطلمبة</th>
                <th style="padding: 10px; border: 1px solid #ddd;">المسدس</th>
                <th style="padding: 10px; border: 1px solid #ddd;">العداد الحالي</th>
                <th style="padding: 10px; border: 1px solid #ddd;">الحالة</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tank->pumps as $pump)
                @forelse($pump->nozzles as $nozzle)
                    <tr>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $pump->name }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd;">{{ $nozzle->name }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: left;">{{ number_format($nozzle->meter_reading, 2) }}</td>
                        <td style="padding: 10px; border: 1px solid #ddd; text-align: center;">
                            <span style="background-color: #28a745; color: white; padding: 2px 8px; border-radius: 12px;">نشط</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="padding: 10px; border: 1px solid #ddd; text-align: center;">لا توجد مسدسات لهذه الطلمبة</td>
                    </tr>
                @endforelse
            @empty
                <tr>
                    <td colspan="4" style="padding: 20px; text-align: center; border: 1px solid #ddd;">لا توجد طلمبات لهذا التانك</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Summary -->
    <div class="mt-4">
        <p><strong>ملخص التانك:</strong></p>
        <ul>
            <li>السعة الكلية: {{ number_format($tank->capacity) }} لتر</li>
            <li>المخزون الحالي: {{ number_format($tank->current_level ?? 0) }} لتر</li>
            <li>النسبة المتبقية: {{ $tank->capacity > 0 ? number_format(($tank->current_level / $tank->capacity) * 100, 1) : 0 }}%</li>
            <li>عدد الطلمبات: {{ $tank->pumps->count() }}</li>
            <li>إجمالي المسدسات: {{ $tank->pumps->sum(function($pump) { return $pump->nozzles->count(); }) }}</li>
        </ul>
    </div>
</div>
@endsection
