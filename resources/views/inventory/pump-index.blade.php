@extends('layouts.app')

@section('content')
    <div class="container" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-12">

                <x-card title="جرد الطلمبات والمسدسات">

                    {{-- التاريخ + الأزرار --}}
                    <div class="d-flex justify-content-between mb-4">
                        <div class="d-flex align-items-center gap-2">
                            <label class="mb-0 fw-bold">التاريخ:</label>
                            <input type="date" class="form-control" value="{{ $date }}" style="width: 180px"
                                onchange="window.location.href='?date=' + this.value">
                        </div>

                        <div class="d-flex gap-2">
                            <x-button type="link" color="success" size="sm" icon="plus-circle"
                                label="إضافة جرد جديد" :href="route('inventory.pump.create', ['date' => $date])" />
                            <x-button type="link" color="info" size="sm" icon="chart-bar" label="تقارير الجرد"
                                :href="route('inventory.pump.report')" />
                            <x-button type="link" color="primary" size="sm" icon="table" label="اليومي المفصل"
                                :href="route('inventory.daily.summary', ['date' => $date])" />
                        </div>
                    </div>

                    <x-alert-success />

                    @if ($pumps->isEmpty())
                        <div class="alert alert-info text-center">
                            لا توجد طلمبات مسجلة
                        </div>
                    @else
                        @php
                            $groupedByTank = $pumps->groupBy('tank_id');
                        @endphp

                        @foreach ($groupedByTank as $tankId => $tankPumps)
                            @php
                                $tank = $tankPumps->first()->tank;
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

                                        {{-- قراءة المسدس نهاية اليوم --}}
                                        <tr>
                                            <td class="fw-bold bg-light">قراءة المسدس</td>
                                            @foreach ($nozzles as $nozzle)
                                                @php
                                                    // Get today's closing reading as meter reading
                                                    $today = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                                        ->whereDate('inventory_date', $date)
                                                        ->first();
                                                    $meterReading = $today ? $today->closing_reading : 0;
                                                @endphp
                                                <td class="text-primary fw-semibold">
                                                    {{ number_format($meterReading, 2) }}
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
                                                    $today = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                                        ->whereDate('inventory_date', $date)
                                                        ->first();
                                                    
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
                    @endif

                </x-card>

            </div>
        </div>
    </div>
@endsection
