@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">📊 التقرير المجمل للجرد الشهري</h2>
            <p class="text-muted">فترة التقرير: من {{ $startDate }} إلى {{ $endDate }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.monthly.index', ['month' => $month]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i> رجوع
            </a>
            <a href="{{ route('inventory.monthly.detailed', ['month' => $month]) }}" class="btn btn-success">
                <i class="fas fa-list-alt me-2"></i> التقرير التفصيلي
            </a>
            <button onclick="window.print()" class="btn btn-dark">
                <i class="fas fa-print me-2"></i> طباعة
            </button>
        </div>
    </div>

    @if($summary->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            لا توجد بيانات جرد للشهر المحدد. الرجاء التأكد من وجود سجلات جرد يومي للطلمبات.
        </div>
    @else
        <!-- Grand Totals Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-primary bg-gradient text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                <i class="fas fa-tachometer-alt fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">إجمالي المبيعات الشهرية</h6>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0">{{ number_format($grandTotalSales, 2) }}</h2>
                        <small class="opacity-75">لتر</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-success bg-gradient text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                <i class="fas fa-oil-can fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">إجمالي المنصرف الشهري</h6>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0">{{ number_format($grandTotalDispensed, 2) }}</h2>
                        <small class="opacity-75">لتر</small>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card border-0 shadow-sm rounded-4 bg-info bg-gradient text-white">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center mb-3">
                            <div class="bg-white bg-opacity-20 rounded-circle p-3 me-3">
                                <i class="fas fa-money-bill-wave fs-4"></i>
                            </div>
                            <div>
                                <h6 class="mb-0">إجمالي الإيرادات الشهرية</h6>
                            </div>
                        </div>
                        <h2 class="fw-bold mb-0">{{ number_format($grandTotalRevenue, 2) }}</h2>
                        <small class="opacity-75">ج.م</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pump Summary Table -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">ملخص أداء الطلمبات للشهر</h5>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>الطلمبة</th>
                                <th>نوع الوقود</th>
                                <th>إجمالي المبيعات</th>
                                <th>إجمالي المنصرف</th>
                                <th>متوسط المبيعات اليومية</th>
                                <th>إجمالي الإيرادات</th>
                                <th>عدد أيام الجرد</th>
                                <th>الأداء</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($summary as $pumpId => $data)
                                <tr>
                                    <td>
                                        <span class="badge bg-primary fs-6">
                                            {{ $data['pump']->name }}
                                        </span>
                                    </td>
                                    <td>{{ $data['pump']->tank->fuel->name ?? 'N/A' }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($data['total_sales'], 2) }}</td>
                                    <td class="text-end fw-semibold">{{ number_format($data['total_dispensed'], 2) }}</td>
                                    <td class="text-end">{{ number_format($data['avg_daily_sales'], 2) }}</td>
                                    <td class="text-end fw-bold text-success">{{ number_format($data['total_revenue'], 2) }}</td>
                                    <td class="text-center">{{ $data['days_count'] }}</td>
                                    <td class="text-center">
                                        @php
                                            $performance = ($data['total_sales'] / max($grandTotalSales, 1)) * 100;
                                        @endphp
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar 
                                                @if($performance >= 30) bg-success 
                                                @elseif($performance >= 15) bg-warning 
                                                @else bg-danger @endif" 
                                                role="progressbar" 
                                                style="width: {{ $performance }}%">
                                                {{ number_format($performance, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="table-light fw-bold">
                            <tr>
                                <td colspan="2">الإجمالي العام</td>
                                <td class="text-end">{{ number_format($grandTotalSales, 2) }}</td>
                                <td class="text-end">{{ number_format($grandTotalDispensed, 2) }}</td>
                                <td class="text-end">{{ number_format($grandTotalSales / max($summary->sum('days_count'), 1), 2) }}</td>
                                <td class="text-end text-success">{{ number_format($grandTotalRevenue, 2) }}</td>
                                <td class="text-center">{{ $summary->sum('days_count') }}</td>
                                <td class="text-center">-</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <!-- Performance Analysis -->
        <div class="row mt-4">
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-trophy text-warning me-2"></i>
                            أفضل الطلمبات أداءً
                        </h5>
                        @php
                            $topPumps = $summary->sortByDesc('total_sales')->take(3);
                        @endphp
                        @foreach($topPumps as $data)
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span>{{ $data['pump']->name }}</span>
                                <span class="badge bg-success">{{ number_format($data['total_sales'], 2) }} لتر</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="fas fa-chart-line text-info me-2"></i>
                            إحصائيات الشهر
                        </h5>
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">متوسط المبيعات اليومية</small>
                                <div class="fw-bold">{{ number_format($grandTotalSales / max(\Carbon\Carbon::createFromFormat('Y-m-d', $startDate)->diffInDays(\Carbon\Carbon::createFromFormat('Y-m-d', $endDate)) + 1, 1), 2) }} لتر</div>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">متوسط الإيرادات اليومية</small>
                                <div class="fw-bold">{{ number_format($grandTotalRevenue / max(\Carbon\Carbon::createFromFormat('Y-m-d', $startDate)->diffInDays(\Carbon\Carbon::createFromFormat('Y-m-d', $endDate)) + 1, 1), 2) }} ج.م</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
