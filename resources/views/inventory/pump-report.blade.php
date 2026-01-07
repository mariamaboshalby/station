@extends('layouts.app')

@section('content')
    <div class="container" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-12">

                <x-card title="تقارير جرد الطلمبات">

                    {{-- الفلاتر --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0 fw-bold">من تاريخ:</label>
                                <input type="date" class="form-control" value="{{ $startDate }}" style="width: 180px"
                                    onchange="window.location.href='?start_date=' + this.value + '&end_date=' + document.getElementById('end_date').value">
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <label class="mb-0 fw-bold">إلى تاريخ:</label>
                                <input type="date" id="end_date" class="form-control" value="{{ $endDate }}" style="width: 180px"
                                    onchange="window.location.href='?start_date=' + document.getElementById('start_date').value + '&end_date=' + this.value">
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <x-button type="link" color="primary" size="sm" icon="arrow-left"
                                label="العودة" :href="route('inventory.pump.index')" />
                        </div>
                    </div>

                    @if ($pumpInventories->isEmpty())
                        <div class="alert alert-info text-center">
                            لا توجد سجلات جرد في الفترة المحددة
                        </div>
                    @else
                        @php
                            $groupedByDate = $pumpInventories->groupBy('inventory_date');
                        @endphp

                        @foreach ($groupedByDate as $date => $dateInventories)
                            @php
                                $groupedByTank = $dateInventories->groupBy('tank_id');
                            @endphp

                            <div class="mb-5">
                                <h5 class="fw-bold text-primary mb-3">
                                    <i class="fas fa-calendar-day me-2"></i>
                                    {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}
                                </h5>

                                @foreach ($groupedByTank as $tankId => $tankInventories)
                                    @php
                                        $tank = $tankInventories->first()->pump->tank;
                                        $groupedByPump = $tankInventories->groupBy('pump_id');
                                    @endphp

                                    <div class="mb-4">
                                        <h6 class="fw-bold text-secondary mb-3">
                                            <i class="fas fa-gas-pump me-2"></i>
                                            {{ $tank->name }} - {{ $tank->fuel->name ?? '' }}
                                        </h6>

                                        <table class="table table-bordered text-center align-middle">
                                            <thead>
                                                {{-- صف أسماء الطلمبات --}}
                                                <tr class="table-secondary text-center">
                                                    <th rowspan="2" style="width:160px">البيان</th>

                                                    @foreach ($groupedByPump as $pumpId => $pumpInventories)
                                                        @php
                                                            $pump = $pumpInventories->first()->pump;
                                                        @endphp
                                                        <th colspan="{{ $pumpInventories->count() }}">
                                                            {{ $pump->name }}
                                                        </th>
                                                    @endforeach
                                                </tr>

                                                {{-- صف أسماء المسدسات --}}
                                                <tr class="table-light text-center">
                                                    @foreach ($groupedByPump as $pumpId => $pumpInventories)
                                                        @foreach ($pumpInventories as $inventory)
                                                            <th>{{ $inventory->nozzle->name }}</th>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            </thead>

                                            <tbody>
                                                {{-- بداية الفترة --}}
                                                <tr>
                                                    <td class="fw-bold bg-light">بداية الفترة</td>
                                                    @foreach ($groupedByPump as $pumpId => $pumpInventories)
                                                        @foreach ($pumpInventories as $inventory)
                                                            <td>
                                                                {{ number_format($inventory->opening_reading, 2) }}
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>

                                                {{-- نهاية الفترة --}}
                                                <tr>
                                                    <td class="fw-bold bg-light">نهاية الفترة</td>
                                                    @foreach ($groupedByPump as $pumpId => $pumpInventories)
                                                        @foreach ($pumpInventories as $inventory)
                                                            <td>
                                                                {{ number_format($inventory->closing_reading, 2) }}
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>

                                                {{-- المنصرف --}}
                                                <tr>
                                                    <td class="fw-bold bg-light">المنصرف</td>
                                                    @foreach ($groupedByPump as $pumpId => $pumpInventories)
                                                        @foreach ($pumpInventories as $inventory)
                                                            <td class="text-danger fw-semibold">
                                                                {{ number_format($inventory->liters_dispensed, 2) }}
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>

                                                {{-- المبيعات --}}
                                                <tr>
                                                    <td class="fw-bold bg-light">المبيعات</td>
                                                    @foreach ($groupedByPump as $pumpId => $pumpInventories)
                                                        @foreach ($pumpInventories as $inventory)
                                                            <td class="text-primary fw-semibold">
                                                                {{ number_format($inventory->sales, 2) }}
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>

                                                {{-- ملاحظات --}}
                                                <tr>
                                                    <td class="fw-bold bg-light">ملاحظات</td>
                                                    @foreach ($groupedByPump as $pumpId => $pumpInventories)
                                                        @foreach ($pumpInventories as $inventory)
                                                            <td class="text-muted small">
                                                                {{ $inventory->notes ?? '-' }}
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>

                                                {{-- المسؤول --}}
                                                <tr>
                                                    <td class="fw-bold bg-light">المسؤول</td>
                                                    @foreach ($groupedByPump as $pumpId => $pumpInventories)
                                                        @foreach ($pumpInventories as $inventory)
                                                            <td class="text-muted small">
                                                                {{ $inventory->user->name ?? '-' }}
                                                            </td>
                                                        @endforeach
                                                    @endforeach
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                @endforeach
                            </div>
                        @endforeach

                        {{-- إجماليات الفترة --}}
                        <div class="mt-4">
                            <h5 class="fw-bold text-success mb-3">
                                <i class="fas fa-chart-line me-2"></i>
                                إجماليات الفترة
                            </h5>
                            
                            <table class="table table-bordered">
                                <thead>
                                    <tr class="table-success text-center">
                                        <th>نوع الوقود</th>
                                        <th>إجمالي المنصرف</th>
                                        <th>إجمالي المبيعات</th>
                                        <th>عدد الأيام</th>
                                        <th>متوسط المنصرف اليومي</th>
                                        <th>متوسط المبيعات اليومي</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $totalsByFuel = $pumpInventories->groupBy('fuel_type')->map(function($inventories) {
                                            return [
                                                'total_dispensed' => $inventories->sum('liters_dispensed'),
                                                'total_sales' => $inventories->sum('sales'),
                                                'days_count' => $inventories->groupBy('inventory_date')->count(),
                                            ];
                                        });
                                    @endphp
                                    
                                    @foreach ($totalsByFuel as $fuelType => $totals)
                                        <tr class="text-center">
                                            <td class="fw-bold">{{ $fuelType }}</td>
                                            <td class="text-danger fw-semibold">{{ number_format($totals['total_dispensed'], 2) }}</td>
                                            <td class="text-primary fw-semibold">{{ number_format($totals['total_sales'], 2) }}</td>
                                            <td>{{ $totals['days_count'] }}</td>
                                            <td>{{ number_format($totals['total_dispensed'] / max($totals['days_count'], 1), 2) }}</td>
                                            <td>{{ number_format($totals['total_sales'] / max($totals['days_count'], 1), 2) }}</td>
                                        </tr>
                                    @endforeach
                                    
                                    {{-- الصف الإجمالي --}}
                                    <tr class="table-dark text-center fw-bold">
                                        <td>الإجمالي العام</td>
                                        <td class="text-danger">{{ number_format($totalsByFuel->sum('total_dispensed'), 2) }}</td>
                                        <td class="text-primary">{{ number_format($totalsByFuel->sum('total_sales'), 2) }}</td>
                                        <td>{{ $pumpInventories->groupBy('inventory_date')->count() }}</td>
                                        <td>{{ number_format($totalsByFuel->sum('total_dispensed') / max($pumpInventories->groupBy('inventory_date')->count(), 1), 2) }}</td>
                                        <td>{{ number_format($totalsByFuel->sum('total_sales') / max($pumpInventories->groupBy('inventory_date')->count(), 1), 2) }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endif

                </x-card>

            </div>
        </div>
    </div>
@endsection
