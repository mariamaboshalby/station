@extends('layouts.app')

@section('content')
<div class="container-fluid pb-4" dir="rtl">
    
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark">📋 سجل المصروفات</h2>
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="fas fa-plus me-2"></i> تسجيل مصروف جديد
            </button>
        </div>
    </div>

    {{-- فلتر --}}
    <form action="{{ route('expenses.index') }}" method="GET" class="bg-white p-3 rounded border mb-4 row g-2">
        <div class="col-md-4">
            <input type="date" name="start_date" value="{{ $startDate }}" class="form-control">
        </div>
        <div class="col-md-4">
            <input type="date" name="end_date" value="{{ $endDate }}" class="form-control">
        </div>
        <div class="col-md-4">
            <button type="submit" class="btn btn-secondary w-100">تصفية</button>
        </div>
    </form>

    {{-- الجدول --}}
    <div class="bg-white rounded border overflow-hidden">
        <table class="table table-hover align-middle mb-0">
            <thead class="bg-light">
                <tr>
                    <th class="py-3 px-4">التاريخ</th>
                    <th>بواسطة</th>
                    <th>الفئة</th>
                    <th>المبلغ</th>
                    <th class="w-50">الوصف</th>
                    <th>إجراءات</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                    <tr>
                        <td class="px-4 text-nowrap">{{ $expense->transaction_date->format('Y-m-d') }}</td>
                        <td>{{ $expense->user->name ?? 'غير معروف' }}</td>
                        <td>
                            <span class="badge bg-danger bg-opacity-10 text-danger border px-2">
                                {{ $expense->category }}
                            </span>
                        </td>
                        <td class="fw-bold text-dark" dir="ltr">{{ number_format($expense->amount, 2) }}</td>
                        <td class="text-muted small">{{ $expense->description ?? '-' }}</td>
                        <td>
                            <form action="{{ route('expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('حذف هذا المصروف؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger border-0"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">لا توجد مصروفات مسجلة</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- مودال إضافة مصروف --}}
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog" dir="rtl">
        <form action="{{ route('expenses.store') }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold">تسجيل مصروف جديد</h5>
                <button type="button" class="btn-close m-0" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">تاريخ المصروف</label>
                    <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">الفئة</label>
                    <input type="text" name="category" list="expense_categories" class="form-control" placeholder="اختر أو اكتب..." required>
                    <datalist id="expense_categories">
                        <option value="سداد موردين"></option>
                        <option value="مصاريف تشغيل"></option>
                        <option value="رواتب"></option>
                        <option value="فاتورة كهرباء/مياه"></option>
                        <option value="صيانة وإصلاحات"></option>
                        <option value="شراء وقود (تفريغ تانك)"></option>
                        <option value="مسحوبات مالك"></option>
                        <option value="نثريات"></option>
                    </datalist>
                </div>
                <div class="mb-3">
                    <label class="form-label">المبلغ</label>
                    <input type="number" step="0.01" name="amount" class="form-control" placeholder="0.00" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">ملاحظات / وصف</label>
                    <textarea name="description" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">إلغاء</button>
                <button type="submit" class="btn btn-danger px-4">حفظ المصروف</button>
            </div>
        </form>
    </div>
</div>
@endsection
