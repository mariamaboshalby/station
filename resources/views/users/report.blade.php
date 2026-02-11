@extends('layouts.app')

@section('content')
    <div class="row justify-content-center" dir="rtl">
        <div class="col-12">
            <x-card :title="'شيفتات الموظف: ' . $user->name">
                @if ($shifts->isEmpty())
                    <div class="alert alert-warning text-center">لا توجد شيفتات لهذا الموظف حالياً</div>
                @endif

                @foreach ($shifts as $shift)
                    <div class="mb-4">
                        <div class="card shadow-sm mb-2 overflow-auto">
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
                                                <th>اللترات الآجل</th>
                                                <th>اللترات كاش</th>
                                                <th>إجمالي السعر</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($shift->transactions as $transaction)
                                                <tr>
                                                    <td>{{ $loop->iteration }}</td>
                                                    @php
                                                        $fuelName =
                                                            optional($transaction->pump->tank->fuel)->name ?? '---';
                                                        $badgeClass = '';
                                                        if (str_contains($fuelName, '95')) {
                                                            $badgeClass = 'bg-danger text-white';
                                                        } elseif (str_contains($fuelName, '80')) {
                                                            $badgeClass = 'bg-primary text-white';
                                                        } elseif (str_contains($fuelName, '92')) {
                                                            $badgeClass = 'bg-success text-white';
                                                        } elseif (str_contains($fuelName, 'سولار')) {
                                                            $badgeClass = 'bg-warning text-dark';
                                                        } else {
                                                            $badgeClass = 'bg-secondary text-white';
                                                        }
                                                    @endphp
                                                    <td>
                                                        <span
                                                            class="badge {{ $badgeClass }} fs-6">{{ $fuelName }}</span>
                                                    </td>
                                                    <td>{{ optional($transaction->pump->tank)->name ?? '---' }}</td>
                                                    <td>{{ optional($transaction->pump)->name ?? '---' }}</td>
                                                    <td>{{ $transaction->credit_liters }}</td>
                                                    <td>{{ $transaction->cash_liters }}</td>
                                                    @php
                                                        // 1. تحديد السعر: سعر الموظف للوقود المحدد (لو موجود) أو سعر الوقود الرسمي
                                                        $fuelP = $transaction->pump->tank->fuel;
                                                        $fuelId = $fuelP->id;
                                                        // البحث في أسعار الموظف عن سعر لهذا الوقود
                                                        $empPriceObj = $user->fuelPrices->firstWhere(
                                                            'fuel_id',
                                                            $fuelId,
                                                        );
                                                        $finalPrice = $empPriceObj
                                                            ? $empPriceObj->price
                                                            : $fuelP->price_per_liter ?? 0;

                                                        // 2. حساب إجمالي اللترات
                                                        $totalLiters =
                                                            ($transaction->credit_liters ?? 0) +
                                                            ($transaction->cash_liters ?? 0);

                                                        // 3. حساب المبلغ
                                                        $accountableAmount = $totalLiters * $finalPrice;
                                                    @endphp
                                                    <td class="fw-bold">{{ number_format($accountableAmount, 2) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

            </x-card>

        </div>

    </div>
@endsection
