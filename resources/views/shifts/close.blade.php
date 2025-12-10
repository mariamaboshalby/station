@can('close shift')
    @extends('layouts.app')

    @section('content')
        <div class="container" dir="rtl">
            <div class="row justify-content-center">
                <div class="col-lg-8"> {{-- عرض أقل للتركيز --}}

                    <div class="card shadow-sm border-0 rounded-3">
                        
                        {{-- الهيدر الأحمر البسيط --}}
                        <div class="card-header bg-danger text-white text-center py-3">
                            <h4 class="mb-0 fw-bold">إغلاق الشيفت</h4>
                            <small class="opacity-75">{{ $shift->user->name }} - {{ $shift->start_time->format('Y-m-d h:i A') }}</small>
                        </div>

                        <div class="card-body p-4 bg-white">

                            <x-alert-success />

                            <form action="{{ route('shifts.close', $shift->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                @method('PATCH')

                                {{-- عرض المبيعات الآجلة إن وجدت بشكل بسيط --}}
                                @if($totalCreditLiters > 0)
                                    <div class="mb-4">
                                        <label class="form-label text-muted">إجمالي المبيعات الآجلة في هذا الشيفت</label>
                                        <input type="text" class="form-control bg-light" value="{{ number_format($totalCreditLiters, 2) }} لتر" readonly>
                                    </div>
                                @endif

                                {{-- حلقة المسدسات: عرض بسيط --}}
                                @foreach ($shift->nozzleReadings as $index => $reading)
                                    <div class="mb-4 p-3 rounded bg-light border-start border-4 border-danger">
                                        
                                        <div class="d-flex justify-content-between mb-2">
                                            <label class="fw-bold text-dark">
                                                {{ $reading->nozzle->name }} 
                                                <small class="text-muted">({{ $reading->nozzle->pump->tank->fuel->name ?? '' }})</small>
                                            </label>
                                            <span class="text-muted small">قراءة البداية: {{ $reading->start_reading }}</span>
                                        </div>

                                        <div class="form-group">
                                            <input type="number" step="0.01" 
                                                name="nozzle_end_readings[{{ $reading->nozzle_id }}]" 
                                                class="form-control form-control-lg end-reading-input"
                                                placeholder="اكتب القراءة النهائية هنا"
                                                required
                                                min="{{ $reading->start_reading }}"
                                                data-start="{{ $reading->start_reading }}">
                                            
                                            <div class="d-flex justify-content-between mt-1">
                                                <small class="text-muted">القراءة الحالية بالعداد: {{ $reading->nozzle->meter_reading }}</small>
                                                <small class="text-success fw-bold calculated-liters" style="display:none">0.00 لتر</small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach

                                {{-- الإجمالي --}}
                                <div class="text-center mb-4 pt-2 border-top">
                                    <label class="text-muted mb-1">إجمالي السحب (لتر)</label>
                                    <h2 class="fw-bold text-dark" id="total-liters-display">0.00</h2>
                                </div>

                                {{-- الصورة --}}
                                <div class="mb-3">
                                    <label class="form-label fw-bold">صورة العداد عند الإغلاق</label>
                                    <div class="input-group">
                                        <input type="file" name="end_meter_image" class="form-control" accept="image/*" required>
                                        <label class="input-group-text">Choose File</label>
                                    </div>
                                </div>

                                {{-- الملاحظات --}}
                                <div class="mb-4">
                                    <label class="form-label fw-bold">ملاحظات</label>
                                    <textarea name="notes" class="form-control" rows="3" placeholder="أدخل ملاحظات إن وجدت"></textarea>
                                </div>

                                {{-- زر الإغلاق --}}
                                <button type="submit" class="btn btn-danger w-100 py-2 fs-5 fw-bold shadow-sm">
                                    <i class="fas fa-lock me-2"></i> إغلاق الشيفت
                                </button>

                            </form>
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const inputs = document.querySelectorAll('.end-reading-input');
                const totalDisplay = document.getElementById('total-liters-display');

                function calculate() {
                    let total = 0;
                    inputs.forEach(input => {
                        const start = parseFloat(input.dataset.start);
                        const end = parseFloat(input.value);
                        const container = input.closest('.mb-4');
                        const litersSpan = container.querySelector('.calculated-liters');

                        if (!isNaN(end) && end >= start) {
                            const diff = end - start;
                            total += diff;
                            litersSpan.textContent = diff.toFixed(2) + ' لتر';
                            litersSpan.style.display = 'block';
                            input.classList.remove('is-invalid');
                            input.classList.add('is-valid');
                        } else {
                            if(input.value !== '') {
                                input.classList.add('is-invalid');
                                input.classList.remove('is-valid');
                            }
                            litersSpan.style.display = 'none';
                        }
                    });
                    totalDisplay.textContent = total.toFixed(2);
                }

                inputs.forEach(input => {
                    input.addEventListener('input', calculate);
                });
            });
        </script>
    @endsection
@endcan
