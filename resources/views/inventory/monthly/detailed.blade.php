@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">📋 التقرير التفصيلي للجرد الشهري</h2>
            <p class="text-muted">فترة التقرير: من {{ $startDate }} إلى {{ $endDate }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.monthly.index', ['month' => $month]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i> رجوع
            </a>
            <button onclick="window.print()" class="btn btn-dark">
                <i class="fas fa-print me-2"></i> طباعة
            </button>
        </div>
    </div>

    @if($pumpInventories->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            لا توجد بيانات جرد للشهر المحدد. الرجاء التأكد من وجود سجلات جرد يومي للطلمبات.
        </div>
    @else
        <!-- Group by tanks for monthly report -->
        @foreach($pumpInventories->groupBy('tank_id') as $tankId => $tankInventories)
            @php
                $tank = \App\Models\Tank::with(['pumps.nozzles', 'fuel'])->find($tankId);
                if (!$tank) continue;
                $tankPumps = $tank->pumps;
                $nozzles = $tankPumps->pluck('nozzles')->flatten();
            @endphp
            
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-gas-pump me-2"></i>
                        {{ $tank->name }} - {{ $tank->fuel->name ?? '' }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    <table class="table table-bordered text-center align-middle">
                        <thead>
                            {{-- صف أسماء الطلمبات --}}
                            <tr class="table-secondary text-center">
                                <th rowspan="2" style="width:160px">البيان</th>

                                @foreach ($tankPumps as $pump)
                                    <th colspan="{{ $pump->nozzles->count() }}">
                                        {{ $pump->name }}
                                    </th>
                                @endforeach
                            </tr>

                            {{-- صف أسماء المسدسات --}}
                            <tr class="table-light text-center">
                                @foreach ($tankPumps as $pump)
                                    @foreach ($pump->nozzles as $nozzle)
                                        <th>{{ $nozzle->name }}</th>
                                    @endforeach
                                @endforeach
                            </tr>
                        </thead>

                        <tbody>
                            {{-- بداية الشهر --}}
                            <tr>
                                <td class="fw-bold bg-light">بداية الشهر</td>
                                @foreach ($nozzles as $nozzle)
                                    @php
                                        // Get first day of month reading
                                        $firstDay = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                            ->whereDate('inventory_date', $startDate)
                                            ->first();
                                        // If no first day, get previous day's closing
                                        if (!$firstDay) {
                                            $prevDay = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                                ->whereDate('inventory_date', '<', $startDate)
                                                ->latest('inventory_date')
                                                ->first();
                                            $firstDayReading = $prevDay ? $prevDay->closing_reading : 0;
                                        } else {
                                            $firstDayReading = $firstDay->opening_reading;
                                        }
                                    @endphp
                                    <td>
                                        {{ number_format($firstDayReading, 2) }}
                                    </td>
                                @endforeach
                            </tr>

                            {{-- نهاية الشهر (المبيعات) --}}
                            <tr>
                                <td class="fw-bold bg-light">نهاية الشهر (المبيعات)</td>
                                @foreach ($nozzles as $nozzle)
                                    @php
                                        // Get last day of month reading (end reading)
                                        $lastDay = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                            ->whereDate('inventory_date', $endDate)
                                            ->first();
                                        // If no last day, get latest reading in month
                                        if (!$lastDay) {
                                            $lastDay = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                                ->whereBetween('inventory_date', [$startDate, $endDate])
                                                ->latest('inventory_date')
                                                ->first();
                                        }
                                        $lastDayReading = $lastDay ? $lastDay->closing_reading : $firstDayReading;
                                    @endphp
                                    <td class="text-primary fw-semibold">
                                        {{ number_format($lastDayReading, 2) }}
                                    </td>
                                @endforeach
                            </tr>

                            {{-- المنصرف (الفرق بين البداية والنهاية) --}}
                            <tr>
                                <td class="fw-bold bg-light">المنصرف (الفرق)</td>
                                @foreach ($nozzles as $nozzle)
                                    @php
                                        // Get first day of month reading for this nozzle
                                        $firstDay = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                            ->whereDate('inventory_date', $startDate)
                                            ->first();
                                        if (!$firstDay) {
                                            $prevDay = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                                ->whereDate('inventory_date', '<', $startDate)
                                                ->latest('inventory_date')
                                                ->first();
                                            $firstDayReading = $prevDay ? $prevDay->closing_reading : 0;
                                        } else {
                                            $firstDayReading = $firstDay->opening_reading;
                                        }
                                        
                                        // Get last day of month reading for this nozzle
                                        $lastDay = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                            ->whereDate('inventory_date', $endDate)
                                            ->first();
                                        if (!$lastDay) {
                                            $lastDay = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                                ->whereBetween('inventory_date', [$startDate, $endDate])
                                                ->latest('inventory_date')
                                                ->first();
                                        }
                                        $lastDayReading = $lastDay ? $lastDay->closing_reading : $firstDayReading;
                                        
                                        // Calculate dispensed for this nozzle = End of Month - Beginning of Month
                                        $dispensed = $lastDayReading - $firstDayReading;
                                    @endphp
                                    <td class="text-danger fw-semibold">
                                        {{ number_format($dispensed, 2) }}
                                    </td>
                                @endforeach
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
        
        <!-- Summary Card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">ملخص الشهر</h5>
                <div class="row">
                    <div class="col-md-3">
                        <div class="text-center">
                            <small class="text-muted">إجمالي المبيعات</small>
                            <div class="h4 text-primary fw-bold">{{ number_format($pumpInventories->sum('sales'), 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <small class="text-muted">إجمالي المنصرف</small>
                            <div class="h4 text-danger fw-bold">{{ number_format($pumpInventories->sum('closing_reading') - $pumpInventories->sum('opening_reading'), 2) }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <small class="text-muted">عدد الأيام</small>
                            <div class="h4 text-info fw-bold">{{ $pumpInventories->pluck('inventory_date')->unique()->count() }}</div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center">
                            <small class="text-muted">متوسط يومي</small>
                            <div class="h4 text-success fw-bold">{{ number_format($pumpInventories->sum('sales') / max($pumpInventories->pluck('inventory_date')->unique()->count(), 1), 2) }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>
@endsection
