@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Add New Tank</h2>

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <form action="{{ route('tanks.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="fuel_id" class="form-label">Fuel Type</label>
                <select name="fuel_id" class="form-control" required>
                    @foreach ($fuels as $fuel)
                        <option value="{{ $fuel->id }}">{{ $fuel->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="tank_name" class="form-label">Tank Name</label>
                <input type="text" name="tank_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="capacity" class="form-label">Capacity (Liters)</label>
                <input type="number" name="capacity" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="pump_count" class="form-label">Number of Pumps</label>
                <input type="number" name="pump_count" class="form-control" required min="1">
            </div>

            <div class="mb-3">
                <label for="nozzles_per_pump" class="form-label">Nozzles per Pump</label>
                <input type="number" name="nozzles_per_pump" class="form-control" required min="1">
            </div>

            <button type="submit" class="btn btn-primary">Add Tank</button>
        </form>
    </div>
@endsection
