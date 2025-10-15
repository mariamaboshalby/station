@extends('layouts.app')

@section('content')
    <div class="container text-center mt-5">

        <h3 class="mb-4">أهلاً {{ auth()->user()->name }}</h3>

        <div class="d-flex justify-content-center gap-3">
            <!-- زر فتح شيفت -->
            @if ($openShift)
                <button class="btn btn-success btn-lg" disabled>
                    <i class="fas fa-door-open me-2"></i> لا يوجد شيفت مفتوح
                </button>
            @else
                <a href="{{ route('shifts.create') }}" class="btn btn-success btn-lg">
                    <i class="fas fa-door-open me-2"></i> فتح شيفت
                </a>
            @endif


            <!-- زر عملية بيع آجل -->
            <a href="{{ route('transactions.create', ['type' => 'credit']) }}" class="btn btn-primary btn-lg">
                <i class="fas fa-shopping-cart me-2"></i> عملية بيع آجل
            </a>

            <!-- زر إغلاق شيفت -->
            @if ($openShift)
                <a href="{{ route('shifts.closeForm', $openShift->id) }}" class="btn btn-danger btn-lg">
                    <i class="fas fa-lock me-2"></i> إغلاق شيفت
                </a>
            @else
                <button class="btn btn-danger btn-lg" disabled>
                    <i class="fas fa-lock me-2"></i> لا يوجد شيفت مفتوح
                </button>
            @endif
        </div>

    </div>
@endsection
