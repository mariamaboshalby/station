@extends('layouts.app')

@section('content')
<div class="container-fluid pb-4" dir="rtl">
    
    <div class="row align-items-center mb-4">
        <div class="col">
            <h2 class="fw-bold text-dark"> سجل الإيرادات التفصيلي</h2>
        </div>
        <div class="col-auto">
            <div class="bg-white px-4 py-2 rounded border shadow-sm">
                <small class="text-muted d-block">إجمالي الفترة</small>
                <span class="fw-bold text-success fs-5">{{ number_format($totalRevenue, 2) }} ج.م</span>
            </div>
        </div>
    </div>

    {{-- فلتر --}}
    <form action="{{ route('reports.revenues') }}" method="GET" class="bg-white p-3 rounded border mb-4 row g-2">
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
                    <th>الشيفت / الموظف</th>
                    <th>نوع الوقود</th>
                    <th>المسدس</th>
                    <th>نوع العملية</th>
                    <th class="text-end px-4">المبلغ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($revenues as $rev)
                    <tr>
                        <td class="px-4 text-nowrap">
                            {{ $rev->created_at->format('Y-m-d') }}
                            <small class="text-muted d-block">{{ $rev->created_at->format('h:i A') }}</small>
                        </td>
                        <td>{{ $rev->shift->user->name ?? '-' }}</td>
                        <td>{{ $rev->nozzle->pump->tank->fuel->name ?? '-' }}</td>
                        <td>{{ $rev->nozzle->name ?? '-' }}</td>
                        <td>
                            <span class="badge {{ $rev->operation_type == 'آجل' ? 'bg-warning text-dark' : 'bg-success' }}">
                                {{ $rev->operation_type ?? 'نقدي' }}
                            </span>
                        </td>
                        <td class="text-end px-4 fw-bold text-success">{{ number_format($rev->total_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">لا توجد إيرادات مسجلة</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
