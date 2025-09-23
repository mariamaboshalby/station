@extends('layouts.app')

@section('content')
<div class="container">
    <h2>العمليات</h2>
    <a href="{{ route('transactions.create') }}" class="btn btn-primary mb-3">إضافة عملية</a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الموظف</th>
                <th>المسدس</th>
                <th>الطلمبه</th>
                <th>التانك</th>
                <th>النوع</th>
                  <th>السعه الحاليه</th> 
                  <th>الكمية</th>
                <th>التاريخ</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transactions as $transaction)
            <tr>
                <td>{{ $transaction->shift->user->name }}</td>
                <td>{{ $transaction->nozzle->name }}</td>
                <td>{{ $transaction->nozzle->pump->name }}</td>
                <td>{{ $transaction->nozzle->tank->name }}</td>
                <td>{{ $transaction->nozzle->tank->fuel->name }}</td>
                <td>{{ $transaction->tank_level_after}}</td>
                <td>{{ $transaction->liters_dispensed }}</td>
                <td>{{ $transaction->created_at }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
