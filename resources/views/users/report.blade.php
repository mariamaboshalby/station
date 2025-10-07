@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-12">

            <x-card :title="'شيفتات الموظف: ' . $user->name">
                <div class="mb-3">
                    <a href="{{ url()->previous() }}" class="btn btn-sm btn-outline-secondary">رجوع</a>
                </div>

                @if($shifts->isEmpty())
                    <div class="alert alert-warning text-center">لا توجد شيفتات لهذا الموظف حالياً</div>
                @endif

                @foreach($shifts as $shift)
                    <div class="mb-4">
                        <div class="card shadow-sm mb-2">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <div>
                                    <strong>شيفت #{{ $shift->id }}</strong>
                                    — {{ $shift->user->name ?? '---' }}
                                </div>
                                <div class="small text-muted">
                                    من:
                                    {{ \Carbon\Carbon::parse($shift->start_time)->timezone('Africa/Cairo')->format('Y-m-d H:i') }}
                                    @if ($shift->end_time)
                                        | إلى:
                                        {{ \Carbon\Carbon::parse($shift->end_time)->timezone('Africa/Cairo')->format('Y-m-d H:i') }}
                                    @else
                                        | <span class="badge bg-success">مفتوح</span>
                                    @endif
                                </div>
                            </div>

                            <div class="card-body">
                                @if ($shift->transactions->isEmpty())
                                    <div class="alert alert-warning text-center">لا توجد عمليات مسجلة لهذا الشيفت</div>
                                @else
                                    <table class="table table-hover table-striped text-center align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>#</th>
                                                <th>نوع الوقود</th>
                                                <th>التانك</th>
                                                <th>الطلمبة</th>
                                                <th>المسدس</th>
                                                <th>الكمية (لتر)</th>
                                                <th>السعر الإجمالي</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($shift->transactions as $transaction)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    <td>{{ optional($transaction->nozzle->tank->fuel)->name ?? '---' }}</td>
                                                    <td>{{ optional($transaction->nozzle->tank)->name ?? '---' }}</td>
                                                    <td>{{ optional($transaction->nozzle->pump)->name ?? '---' }}</td>
                                                    <td>{{ optional($transaction->nozzle)->name ?? '---' }}</td>
                                                    <td class="fw-bold">{{ number_format($transaction->liters_dispensed, 2) }}</td>
                                                    <td>{{ number_format($transaction->total_price, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- لو استخدمت paginate بدل get: --}}
                {{-- <div class="d-flex justify-content-center mt-3"> {{ $shifts->links() }} </div> --}}
            </x-card>

        </div>
    </div>
</div>
@endsection
