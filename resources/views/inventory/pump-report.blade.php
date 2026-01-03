@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">📊 تقارير جرد الطلمبات</h2>
            <p class="text-muted">فترة التقرير: من {{ $startDate }} إلى {{ $endDate }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.pump.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i> رجوع
            </a>
            <button onclick="window.print()" class="btn btn-dark">
                <i class="fas fa-print me-2"></i> طباعة
            </button>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4">
            <form action="{{ route('inventory.pump.report') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="start_date" class="form-label fw-bold text-primary">
                            <i class="fas fa-calendar-alt me-1"></i>من تاريخ
                        </label>
                        <input type="date" name="start_date" id="start_date" class="form-control" 
                               value="{{ $startDate }}">
                    </div>
                    <div class="col-md-4">
                        <label for="end_date" class="form-label fw-bold text-primary">
                            <i class="fas fa-calendar-alt me-1"></i>إلى تاريخ
                        </label>
                        <input type="date" name="end_date" id="end_date" class="form-control" 
                               value="{{ $endDate }}">
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100 me-2">
                            <i class="fas fa-filter"></i> عرض التقرير
                        </button>
                        <a href="{{ route('inventory.pump.index') }}" class="btn btn-outline-secondary w-100">
                            <i class="fas fa-times"></i> إلغاء
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if($pumpInventories->isEmpty())
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            لا توجد بيانات جرد في الفترة المحددة.
        </div>
    @else
        <!-- Group by date and show tanks with pumps and nozzles -->
        @foreach($pumpInventories->groupBy('inventory_date') as $date => $dateInventories)
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-day me-2"></i>
                        {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}
                    </h5>
                </div>
                <div class="card-body p-4">
                    
                    <!-- Group by tanks -->
                    @foreach($dateInventories->groupBy('tank_id') as $tankId => $tankInventories)
                        @php
                            $tank = \App\Models\Tank::with(['pumps.nozzles', 'fuel'])->find($tankId);
                            if (!$tank) continue;
                            $tankPumps = $tank->pumps;
                            $nozzles = $tankPumps->pluck('nozzles')->flatten();
                        @endphp
                        
                        <div class="mb-5">
                            <h6 class="fw-bold text-secondary mb-3">
                                <i class="fas fa-gas-pump me-2"></i>
                                {{ $tank->name }} - {{ $tank->fuel->name ?? '' }}
                            </h6>
                            
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
                                    {{-- بداية اليوم --}}
                                    <tr>
                                        <td class="fw-bold bg-light">بداية اليوم</td>
                                        @foreach ($nozzles as $nozzle)
                                            @php
                                                // Get previous day's closing reading as start of today
                                                $start = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                                    ->whereDate('inventory_date', '<', $date)
                                                    ->latest('inventory_date')
                                                    ->first();
                                            @endphp
                                            <td>
                                                {{ number_format($start->closing_reading ?? 0, 2) }}
                                            </td>
                                        @endforeach
                                    </tr>

                                    {{-- مبيعات اليوم --}}
                                    <tr>
                                        <td class="fw-bold bg-light">مبيعات اليوم</td>
                                        @foreach ($nozzles as $nozzle)
                                            @php
                                                // Get today's sales from inventory record
                                                $today = $dateInventories->where('nozzle_id', $nozzle->id)->first();
                                                $sales = $today ? $today->sales : 0;
                                            @endphp
                                            <td class="text-primary fw-semibold">
                                                {{ number_format($sales, 2) }}
                                            </td>
                                        @endforeach
                                    </tr>

                                    {{-- المنصرف --}}
                                    <tr>
                                        <td class="fw-bold bg-light">المنصرف</td>
                                        @foreach ($nozzles as $nozzle)
                                            @php
                                                // Get previous day's closing reading as start of shift
                                                $start = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                                    ->whereDate('inventory_date', '<', $date)
                                                    ->latest('inventory_date')
                                                    ->first();
                                                
                                                // Get today's closing reading as end of shift
                                                $today = $dateInventories->where('nozzle_id', $nozzle->id)->first();
                                                
                                                // Calculate cash liters = End of Shift - Start of Shift
                                                $startReading = $start ? $start->closing_reading : 0;
                                                $endReading = $today ? $today->closing_reading : 0;
                                                $dispensed = $endReading - $startReading;
                                            @endphp
                                            <td class="text-danger fw-semibold">
                                                {{ number_format($dispensed, 2) }}
                                            </td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach

        <!-- Summary Card -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-3">ملخص الفترة</h5>
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
