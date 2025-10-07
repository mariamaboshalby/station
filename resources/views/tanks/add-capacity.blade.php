@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-6">

            <x-card title="إضافة سعة للتانك {{ $tank->name }}">

                <form action="{{ route('tanks.addCapacity', $tank->id) }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">السعة الحالية</label>
                        <input type="text" class="form-control" value="{{ $tank->current_level }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">السعة الكلية</label>
                        <input type="text" class="form-control" value="{{ $tank->capacity }}" disabled>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">الكمية المضافة</label>
                        <input type="number" name="amount" class="form-control" min="1" required>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-plus"></i> إضافة
                    </button>
                    <a href="{{ route('tanks.index') }}" class="btn btn-secondary">رجوع</a>
                </form>

            </x-card>

        </div>
    </div>
</div>
@endsection
