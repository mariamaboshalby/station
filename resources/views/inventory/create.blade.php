@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="mb-4">
        <h2 class="fw-bold text-dark mb-1"> إضافة جرد {{ $type == 'daily' ? 'يومي' : 'شهري' }}</h2>
        <p class="text-muted">تسجيل الرصيد الفعلي للخزانات</p>
    </div>

    <form action="{{ route('inventory.store') }}" method="POST">
        @csrf
        <input type="hidden" name="inventory_date" value="{{ $date }}">
        <input type="hidden" name="type" value="{{ $type }}">

        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-header bg-primary text-white p-4">
                <h5 class="mb-0">التاريخ: {{ $date }} | النوع: {{ $type == 'daily' ? 'يومي' : 'شهري' }}</h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3 mb-4 bg-light p-3 rounded">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">جهة الوارد / التفريغ</label>
                        <input type="text" name="supplier" class="form-control" placeholder="اسم المورد">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">رقم الفاتورة</label>
                        <input type="text" name="invoice_number" class="form-control" placeholder="رقم الفاتورة">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">تاريخ الفاتورة</label>
                        <input type="date" name="invoice_date" class="form-control">
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 px-4">الخزان</th>
                                <th class="py-3 px-4">نوع الوقود</th>
                                <th class="py-3 px-4 text-end">رصيد أول المدة</th>
                                <th class="py-3 px-4 text-end">الوارد</th>
                                <th class="py-3 px-4 text-end">المنصرف</th>
                                <th class="py-3 px-4 text-end">رصيد آخر المدة</th>
                                <th class="py-3 px-4 text-end">الرصيد الفعلي *</th>
                                <th class="py-3 px-4 text-end">الفرق</th>
                                <th class="py-3 px-4">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($tanks as $index => $tank)
                                <tr>
                                    <td class="px-4 fw-bold">{{ $tank->name }}</td>
                                    <td class="px-4">{{ $tank->fuel->name }}</td>
                                    <td class="px-4 text-end" dir="ltr">{{ number_format($tank->current_level, 2) }}</td>
                                    <td class="px-4">
                                        <input type="hidden" name="inventories[{{ $index }}][tank_id]" value="{{ $tank->id }}">
                                        <input type="number" step="0.01" name="inventories[{{ $index }}][purchases]" 
                                               class="form-control text-end" placeholder="0.00" value="0">
                                    </td>
                                    <td class="px-4 text-end text-danger" dir="ltr">{{ number_format($tank->liters_drawn, 2) }}</td>
                                    <td class="px-4 text-end fw-bold" dir="ltr">{{ number_format($tank->current_level - $tank->liters_drawn, 2) }}</td>
                                    <td class="px-4">
                                        <input type="number" step="0.01" name="inventories[{{ $index }}][actual_balance]" 
                                               class="form-control text-end fw-bold" placeholder="0.00" required>
                                    </td>
                                    <td class="px-4 text-end fw-bold text-muted" dir="ltr">-</td>
                                    <td class="px-4">
                                        <input type="text" name="inventories[{{ $index }}][notes]" 
                                               class="form-control" placeholder="ملاحظات...">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold">
                <i class="fas fa-save me-2"></i> حفظ الجرد
            </button>
            <a href="{{ route('inventory.index', ['type' => $type]) }}" class="btn btn-secondary px-5 py-2">
                <i class="fas fa-times me-2"></i> إلغاء
            </a>
        </div>
    </form>
</div>
@endsection
