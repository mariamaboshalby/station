@can('show transaction')
    @extends('layouts.app')

    @section('content')
        <div class="row justify-content-center" dir="rtl">
            <div class="col-12">
                <div class="card shadow-lg border-0 rounded-3">
                    <div class="card-header bg-light text-center fs-5 fw-bold d-flex justify-content-between align-items-center">
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

                        <table class="table table-hover table-striped text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>الموظف</th>
                                    <th>الطرمبة</th>
                                    <th>العميل (آجل)</th>
                                    <th>اللترات الآجل</th>
                                    <th>اللترات كاش</th>
                                    <th>إجمالي السعر</th>
                                    <th>الصورة</th>
                                    <th>ملاحظات</th>
                                    <th>تاريخ العملية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($transactions as $transaction)
                                    <tr>
                                        <td>{{ $transaction->shift->user->name ?? '-' }}</td>
                                        <td>{{ $transaction->pump->name ?? '-' }}-تانك {{ $transaction->pump->tank->name ?? '-' }} - {{ $transaction->pump->tank->fuel->name ?? '-' }}</td>
                                        <td>{{ $transaction->client->name ?? '-' }}</td>
                                        <td>{{ $transaction->credit_liters }}</td>
                                        <td>{{ $transaction->cash_liters }}</td>
                                        <td>{{ number_format($transaction->total_amount, 2) }}</td>

                                        <td>
                                            @if ($transaction->image)
                                                <img src="{{ asset('storage/' . $transaction->image) }}" alt="صورة العملية"
                                                    width="100" height="100" class="rounded shadow-sm">
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $transaction->notes ?? '-' }}</td>
                                        <td>{{ $transaction->created_at->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="9" class="text-center">لا توجد عمليات حتى الآن</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>


                    </div>
                    
                </div>
            </div>
        </div>
    @endsection
@endcan
