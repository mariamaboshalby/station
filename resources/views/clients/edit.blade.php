@can('edit client')

    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="card shadow">
                        <div class="card-header bg-success text-white">
                            <h5 class="mb-0">تعديل بيانات العميل: {{ $client->name }}</h5>
                        </div>
                        <div class="card-body">

                            {{-- ✅ عرض أخطاء التحقق --}}
                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul class="mb-0">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            {{-- ✅ نموذج تعديل العميل --}}
                            <form action="{{ route('clients.update', $client->id) }}" method="POST">
                                @csrf
                                @method('PUT')

                                <div class="mb-3">
                                    <label class="form-label">اسم العميل</label>
                                    <input type="text" name="name" class="form-control"
                                        value="{{ old('name', $client->name) }}" required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">المبلغ المدفوع</label>
                                    <input type="number" step="0.01" name="amount_paid" class="form-control"
                                        value="{{ old('amount_paid', $client->amount_paid) }}" required>
                                </div>

                                @php
                                    $fuelPrices = $client->fuelPrices->keyBy('fuel_id');
                                @endphp

                                @foreach ($fuels as $fuel)
                                    <div class="mb-3">
                                        <label class="form-label">سعر {{ $fuel->name }} للعميل (اختياري)</label>
                                        <input type="number" step="0.01" name="fuel_prices[{{ $fuel->id }}]" class="form-control"
                                            value="{{ old('fuel_prices.' . $fuel->id, $fuelPrices->get($fuel->id)?->price_per_liter) }}"
                                            placeholder="الافتراضي: {{ $fuel->price_per_liter }}">
                                    </div>
                                @endforeach

                                <div class="d-flex justify-content-center">
                                    <button type="submit" class="btn btn-success m-1">تحديث البيانات</button>
                                    <a href="{{ route('clients.index') }}" class="btn btn-secondary m-1">رجوع</a>
                                </div>
                            </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
    @endsection
@endcan
