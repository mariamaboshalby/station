@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">📦 الجرد</h2>
            <p class="text-muted">إدارة وتقارير الجرد اليومي والشهري</p>
        </div>
    </div>

    <!-- Quick Actions Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-day fa-3x text-primary mb-3"></i>
                    <h5 class="card-title">اليومي المفصل</h5>
                    <p class="card-text text-muted">عرض الجرد اليومي المفصل</p>
                    <a href="{{ route('inventory.daily.summary') }}" class="btn btn-primary">
                        <i class="fas fa-eye me-2"></i>عرض الجرد
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-calendar-alt fa-3x text-success mb-3"></i>
                    <h5 class="card-title">الجرد الشهري</h5>
                    <p class="card-text text-muted">عرض تقارير الجرد الشهرية</p>
                    <a href="{{ route('inventory.monthly.index') }}" class="btn btn-success">
                        <i class="fas fa-chart-bar me-2"></i>تقارير شهرية
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-gas-pump fa-3x text-warning mb-3"></i>
                    <h5 class="card-title">جرد الطلمبات</h5>
                    <p class="card-text text-muted">إدارة جرد الطلمبات والمسدسات</p>
                    <a href="{{ route('inventory.pump.index') }}" class="btn btn-warning">
                        <i class="fas fa-tachometer-alt me-2"></i>جرد الطلمبات
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-file-alt fa-3x text-info mb-3"></i>
                    <h5 class="card-title">تقارير</h5>
                    <p class="card-text text-muted">عرض تقارير الجرد المفصلة</p>
                    <a href="{{ route('inventory.report') }}" class="btn btn-info">
                        <i class="fas fa-chart-line me-2"></i>التقارير
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center">
                    <i class="fas fa-edit fa-3x text-success mb-3"></i>
                    <h5 class="card-title">الرصيد الفعلي</h5>
                    <p class="card-text text-muted">إدخال الرصيد الفعلي يدوياً</p>
                    <a href="{{ route('inventory.actual.balance.form') }}" class="btn btn-success">
                        <i class="fas fa-calculator me-2"></i>إدخال رصيد
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity Summary -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-4">
                <i class="fas fa-chart-pie me-2"></i>ملخص سريع
            </h5>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-gas-pump text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">عدد التانكات</h6>
                            <div class="fw-bold">{{ \App\Models\Tank::count() }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-tachometer-alt text-success"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">عدد الطلمبات</h6>
                            <div class="fw-bold">{{ \App\Models\Pump::count() }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-shower text-warning"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">عدد المسدسات</h6>
                            <div class="fw-bold">{{ \App\Models\Nozzle::count() }}</div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 rounded-circle p-3">
                                <i class="fas fa-database text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="mb-0">سجلات الجرد اليوم</h6>
                            <div class="fw-bold">{{ \App\Models\PumpInventory::whereDate('inventory_date', now()->toDateString())->count() }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
