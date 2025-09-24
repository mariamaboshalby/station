@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-primary text-white text-center fs-5 fw-bold">
                    تعديل السعة الحالية لتانك: {{ $tank->name }} ({{ $tank->fuel->name }})
                </div>

                <div class="card-body">

                    @if(session('success'))
                        <div class="alert alert-success text-center">{{ session('success') }}</div>
                    @endif

                    <form action="{{ route('tanks.update', $tank->id) }}" method="POST" class="p-3">
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            <label for="current_level" class="form-label fw-bold">السعة الحالية (لتر)</label>
                            <input 
                                type="number" 
                                name="current_level" 
                                id="current_level" 
                                class="form-control form-control-lg text-center"
                                value="{{ $tank->current_level }}" 
                                required
                            >
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-save me-2"></i> حفظ التعديلات
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
