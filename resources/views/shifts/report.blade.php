@can('show report')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-12">

                    <x-card :title="'تقرير الشيفت - ' . $shift->user->name" :subtitle="'من: ' .
                        \Carbon\Carbon::parse($shift->start_time)->timezone('Africa/Cairo')->format('Y-m-d H:i') .
                        ($shift->end_time
                            ? ' | إلى: ' .
                                \Carbon\Carbon::parse($shift->end_time)->timezone('Africa/Cairo')->format('Y-m-d H:i')
                            : ' | <span class=\'badge bg-success\'>مفتوح</span>')">

                        @if ($shift->transactions->isEmpty())
                            <x-alert type="warning">لا توجد عمليات مسجلة لهذا الشيفت</x-alert>
                        @else
                            <table class="table table-hover table-striped text-center align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>النوع</th>
                                        <th>التانك</th>
                                        <th>طلمبه</th>
                                        <th>الكمية (لتر)</th>
                                        <th>بدايه الشيفت</th>
                                        <th>نهايه الشيفت</th>
                                        
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($shift->transactions as $transaction)
                                        <tr>
                                            <td>{{ $loop->iteration }}</td>
                                            <td>{{ $transaction->pump->tank->fuel->name }}</td>
                                            <td>{{ $transaction->pump->tank->name }}</td>
                                            <td>{{ $transaction->pump->name }}</td>
                                            <td class="fw-bold">{{ number_format($transaction->liters_dispensed, 2) }}</td>
                                            <td>{{ $shift->start_time}}</td>
                                            <td>{{ $shift->end_time}}</td>
                                       
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @endif

                    </x-card>

                </div>
            </div>
        </div>
    @endsection
@endcan