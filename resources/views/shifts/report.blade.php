@extends('layouts.app')

@section('content')
<div class="container">
    <h2>تقرير الشيفت للموظف: {{ $shift->user->name }}</h2>
    <p>من: {{ $shift->start_time }} - إلى: {{ $shift->end_time ?? 'مفتوح' }}</p>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>المسدس</th>
                <th>النوع</th>
                <th>التانك</th>
                <th>الكمية (لتر)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($shift->transactions as $transaction)
            <tr>
                <td>{{ $transaction->nozzle->name }}</td>
                <td>{{ $transaction->nozzle->tank->fuel->name }}</td>
                <td>{{ $transaction->nozzle->tank->name }}</td>
                <td>{{ $transaction->liters_dispensed }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
