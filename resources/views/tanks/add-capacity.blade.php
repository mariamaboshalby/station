@extends('layouts.app')

@section('content')
    <div class="container" dir="rtl">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <x-card title="إضافة سعة للتانك {{ $tank->name }}">

                    <form action="{{ route('tanks.addCapacity', $tank->id) }}" method="POST">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label">السعة الحالية</label>
                            <input type="text" class="form-control" value="{{ $tank->current_level }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">السعة الكلية</label>
                            <input type="text" class="form-control" value="{{ $tank->capacity }}" disabled>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-bold">الكمية المضافة (لتر)</label>
                            <input type="number" step="0.01" name="amount" class="form-control fw-bold" min="1"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label font-bold">⚙️ سعر الشراء / لتر (سعر المورد)</label>
                            <div class="input-group">
                                <input type="number" step="0.01" name="cost_per_liter"
                                    class="form-control fw-bold bg-light" value="{{ $tank->fuel->price_for_owner ?? 0 }}"
                                    placeholder="0.00" id="cost_per_liter" required>
                                <span class="input-group-text">ج.م</span>
                            </div>
                            <div class="form-text text-success small mt-1">
                                <i class="fas fa-check-circle me-1"></i>
                                تم التعبئة تلقائياً من سعر المورد المسجّل. يمكنك تعديله إذا كان السعر مختلف.
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="form-check form-switch p-0">
                                <label class="form-check-label fw-bold ms-5" for="deduct_from_treasury">
                                    💰 خصم المبلغ من الخزنة (تسجيل كمصروف)
                                </label>
                                <input class="form-check-input float-end ms-2" type="checkbox" name="deduct_from_treasury"
                                    id="deduct_from_treasury" checked style="margin-right: -2.5em;">
                            </div>
                            <div class="form-text text-muted small mt-1">
                                ✅ مفعّل افتراضياً - سيتم تسجيل المصروف في الخزنة والتقارير
                            </div>
                        </div>

                        <div class="alert alert-info mb-3" id="total_cost_preview">
                            <i class="fas fa-calculator me-1"></i>
                            <strong>إجمالي التكلفة:</strong>
                            <span id="total_cost_display" class="fw-bold">0.00 ج.م</span>
                        </div>

                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-plus"></i> إضافة
                        </button>
                        <a href="{{ route('tanks.index') }}" class="btn btn-secondary">رجوع</a>
                    </form>

                    <script>
                        // حساب التكلفة تلقائياً
                        const amountInput = document.querySelector('input[name="amount"]');
                        const costInput = document.querySelector('input[name="cost_per_liter"]');
                        const totalDisplay = document.getElementById('total_cost_display');

                        function updateTotal() {
                            const amount = parseFloat(amountInput.value) || 0;
                            const cost = parseFloat(costInput.value) || 0;
                            const total = amount * cost;
                            totalDisplay.textContent = total.toLocaleString('ar-EG', {
                                minimumFractionDigits: 2,
                                maximumFractionDigits: 2
                            }) + ' ج.م';
                        }

                        amountInput.addEventListener('input', updateTotal);
                        costInput.addEventListener('input', updateTotal);
                    </script>

                </x-card>

            </div>
        </div>
    </div>
@endsection
