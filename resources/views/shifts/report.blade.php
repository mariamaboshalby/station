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
                                    <th>عميل</th>
                                    <th>لترات كاش</th>
                                    <th>إجمالي السعر</th>
                                    <th>صوره</th>
                                    <th>بدايه الشيفت</th>
                                    <th>نهايه الشيفت</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($shift->transactions as $transaction)
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        @php
                                            $fuelName = $transaction->pump->tank->fuel->name;
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
                                        <td><span class="badge {{ $badgeClass }} fs-6">{{ $fuelName }}</span></td>
                                        <td>{{ $transaction->pump->tank->name }}</td>
                                        <td>{{ $transaction->pump->name }}</td>
                                        <td>{{ $transaction->credit_liters }}</td>
                                        <td>{{ $transaction->client->name ??'كاش' }}</td>
                                        <td>{{ $transaction->cash_liters }}</td>
                                        @php
                                            // 1. تحديد السعر: سعر الموظف للوقود المحدد (لو موجود) أو سعر الوقود الرسمي
                                            $fuelP = $transaction->pump->tank->fuel;
                                            $fuelId = $fuelP->id;
                                            // البحث في أسعار الموظف عن سعر لهذا الوقود
                                            $empPriceObj = $shift->user->fuelPrices->firstWhere('fuel_id', $fuelId);
                                            $finalPrice = $empPriceObj
                                                ? $empPriceObj->price
                                                : $fuelP->price_per_liter ?? 0;

                                            // 2. حساب إجمالي اللترات
                                            $totalLiters =
                                                ($transaction->credit_liters ?? 0) + ($transaction->cash_liters ?? 0);

                                            // 3. حساب المبلغ (فقط على أساس الكاش، لأن الأجل دين على العملاء)
                                            $accountableAmount = ($transaction->cash_liters ?? 0) * $finalPrice;
                                        @endphp
                                        <td class="fw-bold">{{ number_format($accountableAmount, 2) }}</td>
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

                @if (!$shift->transactions->isEmpty())
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header bg-primary text-white fw-bold">
                            <i class="fas fa-chart-bar me-1"></i> ملخص المبيعات حسب نوع الوقود
                        </div>
                        <div class="card-body">
                            @php
                                // تجميع البيانات حسب نوع الوقود
                                $fuelSummary = [];
                                $grandTotal = 0;

                                foreach ($shift->transactions as $transaction) {
                                    $fuel = $transaction->pump->tank->fuel;
                                    $fuelId = $fuel->id;
                                    $fuelName = $fuel->name;

                                    // تحديد السعر (سعر الموظف أو السعر الرسمي)
                                    $empPriceObj = $shift->user->fuelPrices->firstWhere('fuel_id', $fuelId);
                                    $finalPrice = $empPriceObj ? $empPriceObj->price : ($fuel->price_per_liter ?? 0);

                                    // حساب اللترات (فقط الكاش، لأن الأجل دين على العملاء)
                                    $cashLiters = ($transaction->cash_liters ?? 0);
                                    $amount = $cashLiters * $finalPrice;

                                    // تجميع البيانات
                                    if (!isset($fuelSummary[$fuelName])) {
                                        $fuelSummary[$fuelName] = [
                                            'liters' => 0,
                                            'amount' => 0,
                                            'price' => $finalPrice,
                                            'fuel_name' => $fuelName
                                        ];
                                    }

                                    $fuelSummary[$fuelName]['liters'] += $cashLiters;
                                    $fuelSummary[$fuelName]['amount'] += $amount;
                                    $grandTotal += $amount;
                                }
                            @endphp

                            <table class="table table-bordered text-center align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>نوع الوقود</th>
                                        <th>إجمالي اللترات</th>
                                        <th>السعر/لتر</th>
                                        <th>إجمالي المبلغ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($fuelSummary as $summary)
                                        @php
                                            $badgeClass = '';
                                            if (str_contains($summary['fuel_name'], '95')) {
                                                $badgeClass = 'bg-danger text-white';
                                            } elseif (str_contains($summary['fuel_name'], '80')) {
                                                $badgeClass = 'bg-primary text-white';
                                            } elseif (str_contains($summary['fuel_name'], '92')) {
                                                $badgeClass = 'bg-success text-white';
                                            } elseif (str_contains($summary['fuel_name'], 'سولار')) {
                                                $badgeClass = 'bg-warning text-dark';
                                            } else {
                                                $badgeClass = 'bg-secondary text-white';
                                            }
                                        @endphp
                                        <tr>
                                            <td><span class="badge {{ $badgeClass }} fs-6">{{ $summary['fuel_name'] }}</span></td>
                                            <td class="fw-bold">{{ number_format($summary['liters'], 2) }} لتر</td>
                                            <td>{{ number_format($summary['price'], 2) }} ج.م</td>
                                            <td class="fw-bold text-success">{{ number_format($summary['amount'], 2) }} ج.م</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-dark">
                                    <tr>
                                        <td colspan="3" class="fw-bold fs-5">إجمالي المبلغ المستحق</td>
                                        <td class="fw-bold fs-5 text-warning">{{ number_format($grandTotal, 2) }} ج.م</td>
                                    </tr>
                                </tfoot>
                            </table>

                            <div class="alert alert-info mt-3 mb-0 text-center">
                                <i class="fas fa-hand-holding-usd me-2"></i>
                                <strong>الموظف {{ $shift->user->name }} يجب أن يسلم مبلغ: {{ number_format($grandTotal, 2) }} جنيه</strong>
                            </div>
                        </div>
                    </div>
                @endif

                @if ($shift->notes || $shift->penalty_amount > 0)
                    <div class="card mt-4 shadow-sm">
                        <div class="card-header bg-warning-subtle fw-bold">
                            <i class="fas fa-info-circle me-1"></i> ملخص الإغلاق
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @if ($shift->penalty_amount > 0)
                                    <div class="col-md-6 mb-3">
                                        <div class="p-3 bg-danger bg-opacity-10 border border-danger rounded">
                                            <h6 class="text-danger fw-bold"><i class="fas fa-coins me-1"></i> غرامات مستحقة</h6>
                                            <p class="fs-4 mb-0 fw-bold">{{ number_format($shift->penalty_amount, 2) }} ج.م</p>
                                        </div>
                                    </div>
                                @endif

                                <div class="@if ($shift->penalty_amount > 0) col-md-6 @else col-12 @endif">
                                    <h6 class="fw-bold text-muted">ملاحظات الإغلاق:</h6>
                                    <p class="mb-0 bg-light p-3 rounded">
                                        {{ $shift->notes ?? 'لا يوجد ملاحظات' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endsection
@endcan
