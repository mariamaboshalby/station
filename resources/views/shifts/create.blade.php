@extends('layouts.app')

@section('content')
<div class="container">
    <h2>فتح شيفت جديد</h2>
    <form method="POST" action="{{ route('shifts.store') }}">
        @csrf
        <div class="mb-3">
            <label for="user_id" class="form-label">اختر الموظف</label>
            <select name="user_id" class="form-control">
                @foreach($users as $user)
                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label for="start_time" class="form-label">بداية الشيفت</label>
            <input type="datetime-local" name="start_time" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">فتح</button>
    </form>
</div>
@endsection
