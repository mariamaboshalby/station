@extends('layouts.app')

@section('content')
    <div class="container" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-12">

                <x-card title="إضافة جرد جديد للطلمبات والمسدسات">

                    {{-- التاريخ + رجوع --}}
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <label class="form-label fw-bold">تاريخ الجرد:</label>
                            <input type="date" class="form-control" value="{{ $date }}" readonly
                                style="width:180px">
                        </div>
                        <a href="{{ route('inventory.pump.index', ['date' => $date]) }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-right"></i> العودة
                        </a>
                    </div>

                    <x-alert-success />

                    @if ($pumps->isEmpty())
                        <div class="alert alert-info text-center">
                            لا توجد طلمبات مسجلة
                        </div>
                    @else
                        <form action="{{ route('inventory.pump.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="inventory_date" value="{{ $date }}">

                            @php
                                $groupedByTank = $pumps->groupBy('tank_id');
                            @endphp

                            @foreach ($groupedByTank as $tankIndex => $tankPumps)
                                @php
                                    $tank = $tankPumps->first()->tank;
                                    $nozzles = $tankPumps->flatMap->nozzles;
                                @endphp

                                {{-- عنوان التانك --}}
                                <div class="border text-center fw-bold py-2 bg-light mb-2 mt-4">
                                    اسم التانك : {{ $tank->name }} - {{ $tank->fuel->name }}
                                </div>

                                <div class="table-responsive mb-4">
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
                                                        // Get previous day's closing reading
                                                        $previousDay = \App\Models\PumpInventory::where('nozzle_id', $nozzle->id)
                                                            ->whereDate('inventory_date', '<', $date)
                                                            ->latest('inventory_date')
                                                            ->first();
                                                    @endphp
                                                    <td>
                                                        <input type="number" step="0.01" min="0"
                                                            name="nozzles[{{ $nozzle->id }}][opening_reading]"
                                                            class="form-control text-center opening"
                                                            value="{{ $previousDay->closing_reading ?? $nozzle->meter_reading ?? 0 }}"
                                                            data-nozzle="{{ $nozzle->id }}" >
                                                    </td>
                                                @endforeach
                                            </tr>

                                            {{-- نهاية اليوم --}}
                                            <tr>
                                                <td class="fw-bold bg-light">نهاية اليوم</td>
                                                @foreach ($nozzles as $nozzle)
                                                    <td>
                                                        <input type="number" step="0.01" min="0"
                                                            name="nozzles[{{ $nozzle->id }}][closing_reading]"
                                                            class="form-control text-center closing"
                                                            value="{{ $nozzle->meter_reading ?? 0 }}"
                                                            data-nozzle="{{ $nozzle->id }}" >
                                                    </td>
                                                @endforeach
                                            </tr>

                                            {{-- المنصرف --}}
                                            <tr>
                                                <td class="fw-bold bg-light">المنصرف</td>
                                                @foreach ($nozzles as $nozzle)
                                                    <td>
                                                        <input type="number" readonly
                                                            class="form-control text-center bg-light liters"
                                                            id="liters_{{ $nozzle->id }}" value="0">
                                                    </td>
                                                @endforeach
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            @endforeach

                            {{-- الإجمالي --}}
                            <div class="card mt-4">
                                <div class="card-header bg-success text-white">
                                    <strong>الإجمالي العام</strong>
                                </div>
                                <div class="card-body text-center">
                                    <h5>
                                        إجمالي اللترات المنصرفة :
                                        <span class="badge bg-success fs-5" id="grand_total">0 لتر</span>
                                    </h5>
                                </div>
                            </div>

                            <div class="d-flex justify-content-center mt-4">
                                <x-button type="submit" color="success" icon="save" label="حفظ الجرد" />
                            </div>
                        </form>
                    @endif

                </x-card>
            </div>
        </div>
    </div>

    {{-- JS --}}
    <script>
        function calculate() {
            let grand = 0;

            document.querySelectorAll('.closing').forEach(closing => {
                const nozzleId = closing.dataset.nozzle;
                const opening = document.querySelector(`.opening[data-nozzle="${nozzleId}"]`);
                const litersInput = document.getElementById(`liters_${nozzleId}`);

                const liters = (parseFloat(closing.value) || 0) - (parseFloat(opening.value) || 0);
                litersInput.value = liters.toFixed(2);
                grand += liters;
            });

            document.getElementById('grand_total').textContent = grand.toFixed(2) + ' لتر';
        }

        document.addEventListener('input', function(e) {
            if (e.target.classList.contains('opening') || e.target.classList.contains('closing')) {
                calculate();
            }
        });

        document.addEventListener('DOMContentLoaded', calculate);
    </script>
@endsection
