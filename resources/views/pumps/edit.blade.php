@extends('layouts.app')

@section('content')
<div class="container card p-3">
    <h2 class="mb-4 text-center fw-bold text-primary">
        <i class="fa fa-edit me-2"></i> تعديل الطلمبة: {{ $pump->name }}
    </h2>

    <x-alert-success />
    @if(session('error'))
        <div class="alert alert-danger text-center">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
        </div>
    @endif

    <form action="{{ route('pumps.update', $pump->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">
                    <i class="fa fa-cog me-1"></i> اسم الطلمبة
                </label>
                <input type="text" name="name" value="{{ old('name', $pump->name) }}" 
                       class="form-control" required>
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">
                    <i class="fa fa-oil-can me-1"></i> التانك التابع
                </label>
                <input type="text" value="{{ $pump->tank->name }}" 
                       class="form-control" readonly>
            </div>
        </div>

        {{-- عرض المسدسات التابعة للطلمبة --}}
        <div class="mt-4">
            <h5 class="text-primary mb-3">
                <i class="fa fa-tachometer-alt me-2"></i> المسدسات التابعة للطلمبة
            </h5>
            
            @if($pump->nozzles->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-dark">
                            <tr>
                                <th>اسم المسدس</th>
                                <th>العداد الحالي</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($pump->nozzles as $nozzle)
                                <tr>
                                    <td>{{ $nozzle->name }}</td>
                                    <td>{{ number_format($nozzle->meter_reading, 2) }}</td>
                                    <td>
                                        <a href="{{ route('nozzles.updateMeter', $nozzle->id) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-edit me-1"></i> تعديل العداد
                                        </a>
                                        <a href="{{ route('nozzles.updateName', $nozzle->id) }}" 
                                           class="btn btn-sm btn-outline-info ms-1">
                                            <i class="fa fa-tag me-1"></i> تعديل الاسم
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info text-center">
                    <i class="fa fa-info-circle me-2"></i>
                    لا توجد مسدسات تابعة لهذه الطلمبة
                </div>
            @endif
        </div>

        <div class="mt-4 text-center">
            <button type="submit" class="btn btn-success px-4">
                <i class="fa fa-save me-1"></i> حفظ التعديلات
            </button>
            <a href="{{ route('tanks.report', $pump->tank_id) }}" 
               class="btn btn-outline-secondary px-4">
                <i class="fa fa-arrow-left me-1"></i> رجوع
            </a>
        </div>
    </form>
</div>
@endsection
