@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">📊 الجرد الشهري المجمل</h2>
            <p class="text-muted">فترة التقرير: من {{ $startDate ?? date('Y-m-01') }} إلى {{ $endDate ?? date('Y-m-t') }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.monthly.index', ['month' => $month ?? date('Y-m')]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i> رجوع
            </a>
            <button onclick="window.print()" class="btn btn-dark">
                <i class="fas fa-print me-2"></i> طباعة
            </button>
        </div>
    </div>

    <!-- Main Summary Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-center mb-4">الجرد الشهري المجمل</h4>
            
            <!-- Debug Info (remove in production) -->
            @if(request()->has('debug'))
                <div class="alert alert-info">
                    <h6>Debug Information:</h6>
                    <p>Month: {{ $month }}</p>
                    <p>Start Date: {{ $startDate }}</p>
                    <p>End Date: {{ $endDate }}</p>
                    <p>Solar Data: {{ json_encode($solarData ?? []) }}</p>
                </div>
            @endif
            
            @if(($solarData['balance'] ?? 0) == 0 && ($solarData['dispensed'] ?? 0) == 0)
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    لا توجد بيانات جرد للشهر المحدد. الرجاء التأكد من وجود سجلات جرد يومي للطلمبات.
                    <br>
                    <small>أو قد لا تكون هناك بيانات للفترة من {{ $startDate }} إلى {{ $endDate }}</small>
                </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 20%;">البيان</th>
                            <th class="text-center" style="width: 15%;">الرصيد</th>
                            <th class="text-center" style="width: 15%;">الوارد</th>
                            <th class="text-center" style="width: 15%;">الجملة</th>
                            <th class="text-center" style="width: 15%;">المنصرف</th>
                            <th class="text-center" style="width: 20%;">الباقي</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Solar -->
                        <tr>
                            <td class="fw-bold">سولار</td>
                            <td>{{ number_format($solarData['balance'] ?? 0, 2) }}</td>
                            <td>{{ number_format($solarData['received'] ?? 0, 2) }}</td>
                            <td class="fw-bold">{{ number_format(($solarData['balance'] ?? 0) + ($solarData['received'] ?? 0), 2) }}</td>
                            <td>{{ number_format($solarData['dispensed'] ?? 0, 2) }}</td>
                            <td class="fw-bold text-primary">{{ number_format(($solarData['balance'] ?? 0) + ($solarData['received'] ?? 0) - ($solarData['dispensed'] ?? 0), 2) }}</td>
                        </tr>
                        
                        <!-- Benzine 92 -->
                        <tr>
                            <td class="fw-bold">بنزين ٩٢</td>
                            <td>{{ number_format($benzine92Data['balance'] ?? 0, 2) }}</td>
                            <td>{{ number_format($benzine92Data['received'] ?? 0, 2) }}</td>
                            <td class="fw-bold">{{ number_format(($benzine92Data['balance'] ?? 0) + ($benzine92Data['received'] ?? 0), 2) }}</td>
                            <td>{{ number_format($benzine92Data['dispensed'] ?? 0, 2) }}</td>
                            <td class="fw-bold text-primary">{{ number_format(($benzine92Data['balance'] ?? 0) + ($benzine92Data['received'] ?? 0) - ($benzine92Data['dispensed'] ?? 0), 2) }}</td>
                        </tr>
                        
                        <!-- Benzine 80 -->
                        <tr>
                            <td class="fw-bold">بنزين ٨٠</td>
                            <td>{{ number_format($benzine80Data['balance'] ?? 0, 2) }}</td>
                            <td>{{ number_format($benzine80Data['received'] ?? 0, 2) }}</td>
                            <td class="fw-bold">{{ number_format(($benzine80Data['balance'] ?? 0) + ($benzine80Data['received'] ?? 0), 2) }}</td>
                            <td>{{ number_format($benzine80Data['dispensed'] ?? 0, 2) }}</td>
                            <td class="fw-bold text-primary">{{ number_format(($benzine80Data['balance'] ?? 0) + ($benzine80Data['received'] ?? 0) - ($benzine80Data['dispensed'] ?? 0), 2) }}</td>
                        </tr>
                        
                        <!-- Benzine 95 -->
                        <tr>
                            <td class="fw-bold">بنزين ٩٥</td>
                            <td>{{ number_format($benzine95Data['balance'] ?? 0, 2) }}</td>
                            <td>{{ number_format($benzine95Data['received'] ?? 0, 2) }}</td>
                            <td class="fw-bold">{{ number_format(($benzine95Data['balance'] ?? 0) + ($benzine95Data['received'] ?? 0), 2) }}</td>
                            <td>{{ number_format($benzine95Data['dispensed'] ?? 0, 2) }}</td>
                            <td class="fw-bold text-primary">{{ number_format(($benzine95Data['balance'] ?? 0) + ($benzine95Data['received'] ?? 0) - ($benzine95Data['dispensed'] ?? 0), 2) }}</td>
                        </tr>
                        
                        <!-- زيوت معينة -->
                        <tr>
                            <td class="fw-bold">زيوت معينة</td>
                            <td>{{ number_format($oilsData['balance'] ?? 0, 2) }}</td>
                            <td>{{ number_format($oilsData['received'] ?? 0, 2) }}</td>
                            <td class="fw-bold">{{ number_format(($oilsData['balance'] ?? 0) + ($oilsData['received'] ?? 0), 2) }}</td>
                            <td>{{ number_format($oilsData['dispensed'] ?? 0, 2) }}</td>
                            <td class="fw-bold text-primary">{{ number_format(($oilsData['balance'] ?? 0) + ($oilsData['received'] ?? 0) - ($oilsData['dispensed'] ?? 0), 2) }}</td>
                        </tr>
                        
                        <!-- Total Row -->
                        <tr class="table-info fw-bold">
                            <td>الإجمالي</td>
                            <td>{{ number_format(($solarData['balance'] ?? 0) + ($benzine92Data['balance'] ?? 0) + ($benzine80Data['balance'] ?? 0) + ($benzine95Data['balance'] ?? 0) + ($oilsData['balance'] ?? 0), 2) }}</td>
                            <td>{{ number_format(($solarData['received'] ?? 0) + ($benzine92Data['received'] ?? 0) + ($benzine80Data['received'] ?? 0) + ($benzine95Data['received'] ?? 0) + ($oilsData['received'] ?? 0), 2) }}</td>
                            <td>{{ number_format((($solarData['balance'] ?? 0) + ($solarData['received'] ?? 0)) + (($benzine92Data['balance'] ?? 0) + ($benzine92Data['received'] ?? 0)) + (($benzine80Data['balance'] ?? 0) + ($benzine80Data['received'] ?? 0)) + (($benzine95Data['balance'] ?? 0) + ($benzine95Data['received'] ?? 0)) + (($oilsData['balance'] ?? 0) + ($oilsData['received'] ?? 0)), 2) }}</td>
                            <td>{{ number_format(($solarData['dispensed'] ?? 0) + ($benzine92Data['dispensed'] ?? 0) + ($benzine80Data['dispensed'] ?? 0) + ($benzine95Data['dispensed'] ?? 0) + ($oilsData['dispensed'] ?? 0), 2) }}</td>
                            <td class="text-primary">{{ number_format(((($solarData['balance'] ?? 0) + ($solarData['received'] ?? 0)) - ($solarData['dispensed'] ?? 0)) + ((($benzine92Data['balance'] ?? 0) + ($benzine92Data['received'] ?? 0)) - ($benzine92Data['dispensed'] ?? 0)) + ((($benzine80Data['balance'] ?? 0) + ($benzine80Data['received'] ?? 0)) - ($benzine80Data['dispensed'] ?? 0)) + ((($benzine95Data['balance'] ?? 0) + ($benzine95Data['received'] ?? 0)) - ($benzine95Data['dispensed'] ?? 0)) + ((($oilsData['balance'] ?? 0) + ($oilsData['received'] ?? 0)) - ($oilsData['dispensed'] ?? 0)), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-primary bg-gradient text-white">
                <div class="card-body p-4 text-center">
                    <h6 class="mb-2">إجمالي الرصيد</h6>
                    <h3 class="fw-bold mb-0">{{ number_format(($solarData['balance'] ?? 0) + ($benzine92Data['balance'] ?? 0) + ($benzine80Data['balance'] ?? 0) + ($benzine95Data['balance'] ?? 0) + ($oilsData['balance'] ?? 0), 2) }}</h3>
                    <small>لتر</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-success bg-gradient text-white">
                <div class="card-body p-4 text-center">
                    <h6 class="mb-2">إجمالي الوارد</h6>
                    <h3 class="fw-bold mb-0">{{ number_format(($solarData['received'] ?? 0) + ($benzine92Data['received'] ?? 0) + ($benzine80Data['received'] ?? 0) + ($benzine95Data['received'] ?? 0) + ($oilsData['received'] ?? 0), 2) }}</h3>
                    <small>لتر</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-warning bg-gradient text-white">
                <div class="card-body p-4 text-center">
                    <h6 class="mb-2">إجمالي المنصرف</h6>
                    <h3 class="fw-bold mb-0">{{ number_format(($solarData['dispensed'] ?? 0) + ($benzine92Data['dispensed'] ?? 0) + ($benzine80Data['dispensed'] ?? 0) + ($benzine95Data['dispensed'] ?? 0) + ($oilsData['dispensed'] ?? 0), 2) }}</h3>
                    <small>لتر</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 bg-info bg-gradient text-white">
                <div class="card-body p-4 text-center">
                    <h6 class="mb-2">إجمالي الباقي</h6>
                    <h3 class="fw-bold mb-0">{{ number_format(((($solarData['balance'] ?? 0) + ($solarData['received'] ?? 0)) - ($solarData['dispensed'] ?? 0)) + ((($benzine92Data['balance'] ?? 0) + ($benzine92Data['received'] ?? 0)) - ($benzine92Data['dispensed'] ?? 0)) + ((($benzine80Data['balance'] ?? 0) + ($benzine80Data['received'] ?? 0)) - ($benzine80Data['dispensed'] ?? 0)) + ((($benzine95Data['balance'] ?? 0) + ($benzine95Data['received'] ?? 0)) - ($benzine95Data['dispensed'] ?? 0)) + ((($oilsData['balance'] ?? 0) + ($oilsData['received'] ?? 0)) - ($oilsData['dispensed'] ?? 0)), 2) }}</h3>
                    <small>لتر</small>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

<style>
@media print {
    .d-flex.gap-2 {
        display: none !important;
    }
    
    .card {
        page-break-inside: avoid;
    }
    
    .table {
        font-size: 12px;
    }
}
</style>
