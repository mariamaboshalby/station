@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>الشيفتات</h2>
        <a href="{{ route('shifts.create') }}" class="btn btn-primary mb-3">فتح شيفت جديد</a>
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>الموظف</th>
                    <th>بداية الشيفت</th>
                    <th>نهاية الشيفت</th>
                    <th>خيارات</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($shifts as $shift)
                    <tr>
                        <td>{{ $shift->user->name ?? '---' }}</td>
                        <td>
                            {{ \Carbon\Carbon::parse($shift->start_time)->timezone('Africa/Cairo')->format('Y-m-d H:i:s') }}
                        </td>
                        <td>
                            @if ($shift->end_time)
                                {{ \Carbon\Carbon::parse($shift->end_time)->timezone('Africa/Cairo')->format('Y-m-d H:i:s') }}
                            @else
                                <span class="badge bg-success">مفتوح</span>
                            @endif
                        </td>
                        <td>
                            @if (!$shift->end_time)
                                <a href="{{ route('shifts.close', $shift->id) }}" class="btn btn-danger btn-sm">إغلاق</a>
                            @endif
                            <a href="{{ route('shifts.report', $shift->id) }}" class="btn btn-info btn-sm">تقرير</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
