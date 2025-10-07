@can('show transaction')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-12">
                    <div class="card shadow-lg border-0 rounded-3">
                        <div
                            class="card-header bg-light text-center fs-5 fw-bold d-flex justify-content-between align-items-center">
                            <span>قائمة العمليات</span>
                            @can('add transaction')
                                <a href="{{ route('transactions.create') }}" class="btn btn-sm btn-success">
                                    <i class="fas fa-plus-circle me-1"></i> إضافة عملية جديدة
                                </a>
                            @endcan

                        </div>

                        <div class="card-body overflow-x-auto">
                            @if (session('success'))
                                <div class="alert alert-success text-center">
                                    {{ session('success') }}
                                </div>
                            @endif

                            <table class="table table-hover table-striped text-center">
                                <thead class="table-light">
                                    <tr>
                                        <th>الموظف</th>
                                        <th>الطلمبة</th>
                                        <th>التانك</th>
                                        <th>نوع الوقود</th>
                                        <th>السعة الحالية</th>
                                        <th>الكمية (لتر)</th>
                                        <th>السعر الكلي للكميه المباعه</th>
                                        <th>الصوره</th>
                                        <th>التاريخ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($transactions as $transaction)
                                        <tr>
                                            <td>{{ $transaction->shift->user->name }}</td>
                                            <td>{{ $transaction->pump->name }}</td>
                                            <td>{{ $transaction->pump->tank->name }}</td>
                                            <td>{{ $transaction->pump->tank->fuel->name }}</td>
                                            <td>{{ $transaction->tank_level_after }}</td>
                                            <td>{{ $transaction->liters_dispensed }}</td>
                                            <td>{{ $transaction->total_price }}</td>
                                            <td><img src="{{ $transaction->image}}" alt=""> </td>
                                            <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">لا توجد عمليات حتى الآن</td>
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
@endcan
