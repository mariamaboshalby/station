@extends('layouts.app')

@section('content')
<div class="container">
    <h2>تعديل السعه الحاليه لتانك:{{ $tank->name }} {{ $tank->fuel->name }} </h2>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form action="{{ route('tanks.update', $tank->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="current_level" class="form-label">السعة الحالية</label>
            <input type="number" name="current_level" id="current_level" class="form-control" value="{{ $tank->current_level }}" required>
        </div>

        <button type="submit" class="btn btn-primary">حفظ التعديلات</button>
    </form>
</div>
@endsection
