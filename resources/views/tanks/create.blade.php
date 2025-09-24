@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-success text-white text-center fs-5 fw-bold">
                    إضافة تانك جديد
                </div>

                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('tanks.store') }}" method="POST" class="p-3">
                        @csrf

                        <div class="mb-3">
                            <label for="fuel_id" class="form-label fw-bold">نوع الوقود</label>
                            <select name="fuel_id" id="fuel_id" class="form-select" required>
                                <option value="">-- اختر نوع الوقود --</option>
                                @foreach ($fuels as $fuel)
                                    <option value="{{ $fuel->id }}">{{ $fuel->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="tank_name" class="form-label fw-bold">اسم التانك</label>
                            <input type="text" name="tank_name" id="tank_name" 
                                   class="form-control form-control-lg text-center" required>
                        </div>

                        <div class="mb-3">
                            <label for="capacity" class="form-label fw-bold">السعة (لتر)</label>
                            <input type="number" name="capacity" id="capacity" 
                                   class="form-control form-control-lg text-center" required>
                        </div>

                        <div class="mb-3">
                            <label for="pump_count" class="form-label fw-bold">عدد الطلمبات</label>
                            <input type="number" name="pump_count" id="pump_count" 
                                   class="form-control form-control-lg text-center" required min="1">
                        </div>

                        <div class="mb-3">
                            <label for="nozzles_per_pump" class="form-label fw-bold">عدد المسدسات لكل طلمبة</label>
                            <input type="number" name="nozzles_per_pump" id="nozzles_per_pump" 
                                   class="form-control form-control-lg text-center" required min="1">
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-plus-circle me-2"></i> إضافة التانك
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
