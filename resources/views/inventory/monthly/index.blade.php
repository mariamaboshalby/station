@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">📊 الجرد الشهري التلقائي</h2>
            <p class="text-muted">تقارير شهرية مفصلة ومجملة مأخوذة تلقائياً من جرد الطلمبات اليومي</p>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('inventory.monthly.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">اختر الشهر</label>
                    <input type="month" name="month" value="{{ $month }}" class="form-control" required>
                </div>
                <div class="col-md-8 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i> عرض التقارير
                    </button>
                    <a href="{{ route('inventory.monthly.detailed', ['month' => $month]) }}" class="btn btn-success">
                        <i class="fas fa-list-alt me-2"></i> تقرير تفصيلي
                    </a>
                    <a href="{{ route('inventory.monthly.summary', ['month' => $month]) }}" class="btn btn-info">
                        <i class="fas fa-chart-bar me-2"></i> تقرير مجمل
                    </a>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-list-alt text-success fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">التقرير التفصيلي</h5>
                            <p class="text-muted small mb-0">جميع بيانات الجرد اليومي للشهر بالتفصيل</p>
                        </div>
                    </div>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <span>جميع الطلمبات والمسدسات</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <span>بيانات يومية كاملة</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check text-success me-2"></i>
                            <span>مبيعات ومنصرف لكل يوم</span>
                        </li>
                    </ul>
                    <a href="{{ route('inventory.monthly.detailed', ['month' => $month]) }}" class="btn btn-success w-100">
                        <i class="fas fa-eye me-2"></i> عرض التقرير التفصيلي
                    </a>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3 me-3">
                            <i class="fas fa-chart-bar text-info fs-4"></i>
                        </div>
                        <div>
                            <h5 class="fw-bold mb-1">التقرير المجمل</h5>
                            <p class="text-muted small mb-0">إجماليات شهرية مجمعة لكل طلمبة</p>
                        </div>
                    </div>
                    <ul class="list-unstyled">
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check text-info me-2"></i>
                            <span>إجمالي المبيعات الشهرية</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check text-info me-2"></i>
                            <span>إجمالي المنصرف الشهري</span>
                        </li>
                        <li class="d-flex align-items-center mb-2">
                            <i class="fas fa-check text-info me-2"></i>
                            <span>متوسط المبيعات اليومية</span>
                        </li>
                    </ul>
                    <a href="{{ route('inventory.monthly.summary', ['month' => $month]) }}" class="btn btn-info w-100">
                        <i class="fas fa-chart-line me-2"></i> عرض التقرير المجمل
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-info-circle text-primary me-2"></i>
                معلومات الشهر المحدد
            </h5>
            <div class="row">
                <div class="col-md-4">
                    <div class="bg-light rounded p-3">
                        <small class="text-muted">بداية الشهر</small>
                        <div class="fw-bold">{{ $startDate }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded p-3">
                        <small class="text-muted">نهاية الشهر</small>
                        <div class="fw-bold">{{ $endDate }}</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="bg-light rounded p-3">
                        <small class="text-muted">عدد الأيام</small>
                        <div class="fw-bold">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $startDate)->diffInDays(\Carbon\Carbon::createFromFormat('Y-m-d', $endDate)) + 1 }} يوم</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
