@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1"> تقرير الجرد اليومي</h2>
            <p class="text-muted">تحليل الفروقات والمخزون</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.export', ['type' => $type, 'start_date' => $startDate, 'end_date' => $endDate]) }}" class="btn btn-success px-4 rounded-pill">
                <i class="fas fa-file-excel me-2"></i> تصدير Excel
            </a>
            <button onclick="window.print()" class="btn btn-dark px-4 rounded-pill">
                <i class="fas fa-print me-2"></i> طباعة
            </button>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('inventory.report') }}" method="GET" class="row g-3">
                <input type="hidden" name="type" value="daily">
                <div class="col-md-5">
                    <label class="form-label fw-bold">من تاريخ</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
                </div>
                <div class="col-md-5">
                    <label class="form-label fw-bold">إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-filter me-2"></i> تحديث
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-success text-white">
                <div class="card-body p-4">
                    <h6 class="text-white-50 mb-2">إجمالي الفروقات الموجبة</h6>
                    <h3 class="fw-bold mb-0" dir="ltr">+ {{ number_format($inventories->where('difference', '>', 0)->sum('difference'), 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-danger text-white">
                <div class="card-body p-4">
                    <h6 class="text-white-50 mb-2">إجمالي الفروقات السالبة</h6>
                    <h3 class="fw-bold mb-0" dir="ltr">{{ number_format($inventories->where('difference', '<', 0)->sum('difference'), 2) }}</h3>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                <div class="card-body p-4">
                    <h6 class="text-white-50 mb-2">صافي الفروقات</h6>
                    <h3 class="fw-bold mb-0" dir="ltr">{{ number_format($inventories->sum('difference'), 2) }}</h3>
                </div>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-0">سجل الجرد التفصيلي</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4">التاريخ</th>
                        <th class="py-3 px-4">الخزان</th>
                        <th class="py-3 px-4">الوقود</th>
                        <th class="py-3 px-4 text-end">رصيد أول المدة</th>
                        <th class="py-3 px-4 text-end">الوارد</th>
                        <th class="py-3 px-4 text-end">المنصرف</th>
                        <th class="py-3 px-4 text-end">رصيد آخر المدة</th>
                        <th class="py-3 px-4 text-end">الرصيد الفعلي</th>
                        <th class="py-3 px-4 text-end">الفرق</th>
                        <th class="py-3 px-4">المستخدم</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inv)
                        <tr>
                            <td class="px-4">{{ $inv->inventory_date->format('Y-m-d') }}</td>
                            <td class="px-4 fw-bold">{{ $inv->tank->name }}</td>
                            <td class="px-4">{{ $inv->fuel_type }}</td>
                            <td class="px-4 text-end" dir="ltr">{{ number_format($inv->opening_balance, 2) }}</td>
                            <td class="px-4 text-end text-success" dir="ltr">{{ number_format($inv->purchases, 2) }}</td>
                            <td class="px-4 text-end text-danger" dir="ltr">{{ number_format($inv->sales, 2) }}</td>
                            <td class="px-4 text-end fw-bold" dir="ltr">{{ number_format($inv->closing_balance, 2) }}</td>
                            <td class="px-4 text-end fw-bold text-primary" dir="ltr">{{ number_format($inv->actual_balance, 2) }}</td>
                            <td class="px-4 text-end fw-bold {{ $inv->difference >= 0 ? 'text-success' : 'text-danger' }}" dir="ltr">
                                {{ $inv->difference >= 0 ? '+' : '' }}{{ number_format($inv->difference, 2) }}
                            </td>
                            <td class="px-4 text-muted small">{{ $inv->user->name }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center text-muted py-5">لا توجد بيانات</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
