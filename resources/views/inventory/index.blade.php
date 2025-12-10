@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1"> الجرد {{ $type == 'daily' ? 'اليومي' : 'الشهري' }}</h2>
            <p class="text-muted">متابعة المخزون الفعلي والفروقات</p>
        </div>
        <a href="{{ route('inventory.create', ['type' => $type, 'date' => $date]) }}" class="btn btn-primary shadow-sm px-4 rounded-pill">
            <i class="fas fa-plus me-2"></i> إضافة جرد جديد
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('inventory.index') }}" method="GET" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-bold">نوع الجرد</label>
                    <select name="type" class="form-select" onchange="this.form.submit()">
                        <option value="daily" {{ $type == 'daily' ? 'selected' : '' }}>يومي</option>
                        <option value="monthly" {{ $type == 'monthly' ? 'selected' : '' }}>شهري</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">التاريخ</label>
                    <input type="date" name="date" value="{{ $date }}" class="form-control" onchange="this.form.submit()">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">&nbsp;</label>
                    <a href="{{ route('inventory.report', ['type' => $type]) }}" class="btn btn-secondary w-100">
                        <i class="fas fa-chart-bar me-2"></i> التقارير
                    </a>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success rounded-3">{{ session('success') }}</div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-header bg-white border-0 p-4">
            <h5 class="fw-bold mb-0">سجل الجرد - {{ $date }}</h5>
            @if($inventories->isNotEmpty() && $inventories->first()->supplier)
                <div class="mt-2 text-muted small">
                    <strong>المورد:</strong> {{ $inventories->first()->supplier }} | 
                    <strong>فاتورة:</strong> {{ $inventories->first()->invoice_number }} | 
                    <strong>التاريخ:</strong> {{ $inventories->first()->invoice_date?->format('Y-m-d') }}
                </div>
            @endif
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3 px-4">الخزان</th>
                        <th class="py-3 px-4">نوع الوقود</th>
                        <th class="py-3 px-4 text-end">رصيد أول المدة</th>
                        <th class="py-3 px-4 text-end">الوارد</th>
                        <th class="py-3 px-4 text-end">المنصرف</th>
                        <th class="py-3 px-4 text-end">رصيد آخر المدة</th>
                        <th class="py-3 px-4 text-end">الرصيد الفعلي</th>
                        <th class="py-3 px-4 text-end">الفرق</th>
                        <th class="py-3 px-4">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($inventories as $inv)
                        <tr>
                            <td class="px-4 fw-bold">{{ $inv->tank->name }}</td>
                            <td class="px-4">{{ $inv->fuel_type }}</td>
                            <td class="px-4 text-end" dir="ltr">{{ number_format($inv->opening_balance, 2) }}</td>
                            <td class="px-4 text-end" dir="ltr">{{ number_format($inv->purchases, 2) }}</td>
                            <td class="px-4 text-end text-danger" dir="ltr">{{ number_format($inv->sales, 2) }}</td>
                            <td class="px-4 text-end fw-bold" dir="ltr">{{ number_format($inv->closing_balance, 2) }}</td>
                            <td class="px-4 text-end fw-bold text-primary" dir="ltr">{{ number_format($inv->actual_balance, 2) }}</td>
                            <td class="px-4 text-end fw-bold {{ $inv->difference >= 0 ? 'text-success' : 'text-danger' }}" dir="ltr">
                                {{ $inv->difference >= 0 ? '+' : '' }}{{ number_format($inv->difference, 2) }}
                            </td>
                            <td class="px-4 text-muted small">{{ $inv->notes ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">لا توجد سجلات جرد لهذا التاريخ</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
