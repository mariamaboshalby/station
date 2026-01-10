@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">🎯 إدخال الرصيد الفعلي</h2>
            <p class="text-muted">تاريخ: {{ $date }}</p>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.daily.summary', ['date' => $date]) }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i> اليومي المفصل
            </a>
            <a href="{{ route('inventory.index') }}" class="btn btn-dark">
                <i class="fas fa-home me-2"></i> الرئيسية
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-center mb-4">إدخال الأرصدة الفعلية لأنواع الوقود</h4>
            
            <form action="{{ route('inventory.actual.balance.store') }}" method="POST">
                @csrf
                
                <!-- Date Selection -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">التاريخ</label>
                        <input type="date" name="balance_date" value="{{ $date }}" class="form-control" required>
                    </div>
                </div>

                <!-- Fuel Types Table -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th class="text-center">نوع الوقود</th>
                                <th class="text-center">الرصيد الفعلي (لتر)</th>
                                <th class="text-center">ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($fuels as $fuel)
                                <tr>
                                    <td class="fw-bold">{{ $fuel->name }}</td>
                                    <td>
                                        <input type="number" 
                                               name="balances[{{ $fuel->id }}][fuel_id]" 
                                               value="{{ $fuel->id }}" 
                                               hidden>
                                        <input type="number" 
                                               step="0.01"
                                               name="balances[{{ $fuel->id }}][actual_balance]" 
                                               value="{{ $existingBalances[$fuel->id]->actual_balance ?? 0 }}" 
                                               class="form-control text-center" 
                                               placeholder="0.00"
                                               min="0"
                                               required>
                                    </td>
                                    <td>
                                        <input type="text" 
                                               name="balances[{{ $fuel->id }}][notes]" 
                                               value="{{ $existingBalances[$fuel->id]->notes ?? '' }}" 
                                               class="form-control" 
                                               placeholder="ملاحظات اختيارية">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Submit Button -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg px-5">
                        <i class="fas fa-save me-2"></i>
                        حفظ الرصيد الفعلي
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Instructions -->
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <h5 class="fw-bold mb-3">
                <i class="fas fa-info-circle me-2"></i>
                تعليمات هامة
            </h5>
            <ul class="mb-0">
                <li>الرصيد الفعلي هو الكمية الفعلية الموجودة في التانكات في نهاية اليوم</li>
                <li>يمكن إدخال الأرصدة يدوياً لكل نوع من أنواع الوقود</li>
                <li>سيتم استخدام هذه الأرصدة في تقرير اليومي المفصل بدلاً من قراءة العداد</li>
                <li>في حالة عدم إدخال رصيد فعلي، سيتم استخدام قراءة العداد التلقائية</li>
            </ul>
        </div>
    </div>

</div>
@endsection
