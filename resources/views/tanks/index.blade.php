@extends('layouts.app')

@section('content')
<div class="container">
    <h2>قائمة التانكات</h2>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>الاسم</th>
                <th>السعة</th>
                <th>السعه الحاليه</th>
                <th>إجراءات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tanks as $tank)
                <tr>
                    <td>تانك : {{ $tank->name }} {{ $tank->fuel->name }}</td>
                    <td>{{ $tank->capacity }}</td>
                    <td>{{ $tank->current_level }}</td>
                    <td>
                        <a href="{{ route('tanks.edit', $tank->id) }}" class="btn btn-sm btn-warning">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
