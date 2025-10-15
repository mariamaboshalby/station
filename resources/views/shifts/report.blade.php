@can('show report')
    @extends('layouts.app')

    @section('content')
        <div class="row justify-content-center" dir="rtl">
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
                                    <th>لترات اجل</th>
                                    <th>لترات كاش</th>
                                    <th>صوره</th>
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
                                        <td>{{ $transaction->credit_liters }}</td>
                                        <td>{{ $transaction->cash_liters }}</td>
                                        <td>
                                            @if ($transaction->hasMedia('transactions'))
                                                <a href="{{ $transaction->getFirstMediaUrl('transactions') }}" target="_blank">
                                                    <img src="{{ $transaction->getFirstMediaUrl('transactions') }}"
                                                        alt="صورة العملية" width="100" height="80"
                                                        class="rounded shadow-sm border border-light"
                                                        style="object-fit: cover; cursor: zoom-in;">
                                                </a>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{ $shift->start_time }}</td>

                                        <td>{{ $shift->end_time }}</td>

                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif

                </x-card>

            </div>
        </div>
    @endsection
@endcan
