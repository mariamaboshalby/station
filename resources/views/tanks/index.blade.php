@extends('layouts.app')

@section('content')
<div class="container" dir="rtl">
    <div class="row justify-content-center">
        <div class="col-12">

            <div class="card shadow-lg border-0 rounded-3">
                <div class="card-header bg-light text-center fs-5 fw-bold d-flex justify-content-between align-items-center">
                    <span>قائمة التانكات</span>
                    <a href="{{ route('tanks.create') }}" class="btn btn-sm btn-success">
                        <i class="fas fa-plus-circle me-1"></i> إضافة تانك جديد
                    </a>
                </div>

                <div class="card-body overflow-x-auto">
                    @if(session('success'))
                        <div class="alert alert-success text-center">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table table-hover table-striped text-center">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>الاسم</th>
                                <th>السعة</th>
                                <th>السعة الحالية</th>
                                <th>إجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($tanks as $tank)
                                <tr>
                                    <td>{{ $tank->id }}</td>
                                    <td>تانك : {{ $tank->name }} - {{ $tank->fuel->name }}</td>
                                    <td>{{ $tank->capacity }}</td>
                                    <td>{{ $tank->current_level }}</td>
                                    <td>
                                        <a href="{{ route('tanks.edit', $tank->id) }}" class="btn btn-sm btn-primary">
                                            <i class="fas fa-edit me-1"></i> تعديل
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">لا يوجد تانكات حالياً</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
