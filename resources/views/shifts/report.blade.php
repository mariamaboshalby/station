@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h5 class="mb-0 fw-bold">
                        تقرير الشيفت - {{ $shift->user->name }}
                    </h5>
                    <span class="text-muted small">
                        من: {{ \Carbon\Carbon::parse($shift->start_time)->timezone('Africa/Cairo')->format('Y-m-d H:i') }}
                        @if($shift->end_time)
                            | إلى: {{ \Carbon\Carbon::parse($shift->end_time)->timezone('Africa/Cairo')->format('Y-m-d H:i') }}
                        @else
                            | <span class="badge bg-success">مفتوح</span>
                        @endif
                    </span>
                </div>

                <div class="card-body overflow-x-auto">
                    @if($shift->transactions->isEmpty())
                        <div class="alert alert-warning text-center">
                            لا توجد عمليات مسجلة لهذا الشيفت
                        </div>
                    @else
                        <table class="table table-hover table-striped text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>المسدس</th>
                                    <th>النوع</th>
                                    <th>التانك</th>
                                    <th>الكمية (لتر)</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($shift->transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td>{{ $transaction->nozzle->name }}</td>
                                        <td>{{ $transaction->nozzle->tank->fuel->name }}</td>
                                        <td>{{ $transaction->nozzle->tank->name }}</td>
                                        <td class="fw-bold">{{ number_format($transaction->liters_dispensed, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>

            </div>

        </div>
    </div>
</div>
@endsection
