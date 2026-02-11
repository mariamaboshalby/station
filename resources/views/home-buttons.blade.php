@extends('layouts.app')

@section('content')
    <div class="container text-center mt-5">

        <h3 class="mb-4">أهلاً {{ auth()->user()->name }}</h3>

        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <!-- زر فتح شيفت -->
            @if ($openShift)
                <button class="btn btn-success btn-lg" disabled hidden>
                    <i class="fas fa-door-open me-2"></i> فتح شيفت
                </button>
            @else
                <a href="{{ route('shifts.create') }}" class="btn btn-success btn-lg">
                    <i class="fas fa-door-open me-2"></i> فتح شيفت
                </a>
            @endif

            @if ($openShift)
                <!-- زر عملية بيع آجل -->
                <a href="{{ route('transactions.create', ['type' => 'credit']) }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-cart me-2"></i> عملية بيع آجل
                </a>
            @else
                <button class="btn btn-primary btn-lg" disabled>
                    <i class="fas fa-shopping-cart me-2"></i> عملية بيع آجل
                </button>
            @endif

            <!-- زر تفاصيل الشيفت الحالي -->
            @if ($openShift)
                <a href="{{ route('shifts.report', $openShift->id) }}" class="btn btn-info btn-lg">
                    <i class="fas fa-info-circle me-2"></i> تفاصيل الشيفت الحالي
                </a>
            @endif

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

            <!-- زر تقرير آخر شيفت (يظهر فقط إذا لم يكن هناك شيفت مفتوح) -->
            @if (!$openShift && isset($lastClosedShift))
                <a href="{{ route('shifts.report', $lastClosedShift->id) }}" class="btn btn-warning btn-lg text-dark">
                    <i class="fas fa-file-alt me-2"></i> تقرير آخر وردية (#{{ $lastClosedShift->id }})
                </a>
            @endif
        </div>

    </div>
@endsection
