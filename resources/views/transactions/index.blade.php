@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-light text-center fs-5 fw-bold d-flex justify-content-between align-items-center">
                    <span>قائمة العمليات</span>
                    <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus-circle me-1"></i> إضافة عملية جديدة
                    </a>
                </div>

                <div class="card-body overflow-x-auto">
                    @if(session('success'))
                        <div class="alert alert-success text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-hover table-striped text-center">
                        <thead class="table-light">
                            <tr>
                                <th>الموظف</th>
                                <th>المسدس</th>
                                <th>الطلمبة</th>
                                <th>التانك</th>
                                <th>نوع الوقود</th>
                                <th>السعة الحالية</th>
                                <th>الكمية (لتر)</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->shift->user->name }}</td>
                                    <td>{{ $transaction->nozzle->name }}</td>
                                    <td>{{ $transaction->nozzle->pump->name }}</td>
                                    <td>{{ $transaction->nozzle->tank->name }}</td>
                                    <td>{{ $transaction->nozzle->tank->fuel->name }}</td>
                                    <td>{{ $transaction->tank_level_after }}</td>
                                    <td>{{ $transaction->liters_dispensed }}</td>
                                    <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="text-center">لا توجد عمليات حتى الآن</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <div class="d-flex justify-content-center mt-3">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
