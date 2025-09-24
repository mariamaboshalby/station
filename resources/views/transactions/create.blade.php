@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-success text-white text-center fs-5 fw-bold">
                    إضافة عملية جديدة
                </div>

                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form action="{{ route('transactions.store') }}" method="POST" class="p-3">
                        @csrf

                        <div class="mb-3">
                            <label for="shift_id" class="form-label fw-bold">الشيفت</label>
                            <select name="shift_id" id="shift_id" class="form-select" required>
                                <option value="">-- اختر الشيفت --</option>
                                @foreach($shifts as $shift)
                                    <option value="{{ $shift->id }}">
                                        {{ $shift->user->name }} - {{ $shift->start_time }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="nozzle_id" class="form-label fw-bold">المسدس</label>
                            <select name="nozzle_id" id="nozzle_id" class="form-select" required>
                                <option value="">-- اختر المسدس --</option>
                                @foreach($nozzles as $nozzle)
                                    <option value="{{ $nozzle->id }}">
                                        {{ $nozzle->name }} - تانك {{ $nozzle->tank->name }} - {{ $nozzle->pump->name }} - {{ $nozzle->pump->tank->fuel->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="liters_dispensed" class="form-label fw-bold">الكمية (لتر)</label>
                            <input type="number" step="0.01" name="liters_dispensed" id="liters_dispensed"
                                   class="form-control form-control-lg text-center" required>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-plus-circle me-2"></i> إضافة العملية
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
