@extends('layouts.app')

@section('content')
<div class="container">
    <h2>إضافة عملية جديدة</h2>
    <form method="POST" action="{{ route('transactions.store') }}">
        @csrf
        <div class="mb-3">
            <label for="shift_id" class="form-label">الشيفت</label>
            <select name="shift_id" class="form-control">
                @foreach($shifts as $shift)
                    <option value="{{ $shift->id }}">{{ $shift->user->name }} - {{ $shift->start_time }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="nozzle_id" class="form-label">المسدس</label>
            <select name="nozzle_id" class="form-control">
                @foreach($nozzles as $nozzle)
                    <option value="{{ $nozzle->id }}">
                         {{ $nozzle->name }} - تانك {{ $nozzle->tank->name }} -{{ $nozzle->pump->name }}-{{ $nozzle->pump->tank->fuel->name }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="liters_dispensed" class="form-label">الكمية (لتر)</label>
            <input type="number" step="0.01" name="liters_dispensed" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">حفظ</button>
    </form>
</div>
@endsection
