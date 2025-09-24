@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-md-8">

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-success text-white text-center fs-5 fw-bold">
                    فتح شيفت جديد
                </div>

                <div class="card-body">

                    @if (session('success'))
                        <div class="alert alert-success text-center">
                            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('shifts.store') }}" class="p-3">
                        @csrf

                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-bold">اختر الموظف</label>
                            <select name="user_id" id="user_id" class="form-select" required>
                                <option value="">-- اختر الموظف --</option>
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="start_time" class="form-label fw-bold">بداية الشيفت</label>
                            <input type="datetime-local" name="start_time" id="start_time" 
                                   class="form-control form-control-lg text-center" required>
                        </div>

                        <div class="d-flex justify-content-center mt-4">
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-play-circle me-2"></i> فتح الشيفت
                            </button>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
