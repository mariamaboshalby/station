@extends('layouts.app')

@section('content')
    <div class="container-fluid py-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

        {{-- 📅 Header & Date Filter --}}
        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold text-dark mb-1"> الصندوق اليومي</h2>
                <p class="text-muted">إدارة حركة النقدية (الإيرادات والمصروفات)</p>
            </div>

            <form action="{{ route('treasury.index') }}" method="GET"
                class="d-flex align-items-center bg-white p-2 rounded-pill shadow-sm">
                <input type="date" name="date" value="{{ $date }}"
                    class="form-control border-0 bg-transparent fw-bold" onchange="this.form.submit()">
                <span class="badg bg-secondary rounded-pill ms-2 text-white px-3 py-2">
                    <i class="fas fa-calendar-alt me-1"></i> التاريخ
                </span>
            </form>

            <a href="{{ route('treasury.index', $viewAll ?? false ? [] : ['view_all' => true]) }}"
                class="btn {{ $viewAll ?? false ? 'btn-primary' : 'btn-outline-primary' }} rounded-pill shadow-sm px-4 text-nowrap">
                <i class="fas {{ $viewAll ?? false ? 'fa-calendar-day' : 'fa-list' }} me-1"></i>
                {{ $viewAll ?? false ? 'عرض يوم محدد' : 'عرض الكل' }}
            </a>

            <div class="dropdown d-inline-block">
                <button class="btn btn-outline-secondary rounded-pill shadow-sm px-4 dropdown-toggle text-nowrap"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-file-export me-1"></i> تصدير
                </button>
                <ul class="dropdown-menu text-end shadow-sm border-0 mt-2">
                    <li>
                        <a class="dropdown-item py-2"
                            href="{{ route('treasury.export', ['type' => 'pdf', 'date' => $date, 'view_all' => $viewAll ? '1' : '0']) }}">
                            <i class="fas fa-file-pdf text-danger me-2"></i> تصدير PDF
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item py-2"
                            href="{{ route('treasury.export', ['type' => 'excel', 'date' => $date, 'view_all' => $viewAll ? '1' : '0']) }}">
                            <i class="fas fa-file-excel text-success me-2"></i> تصدير Excel
                        </a>
                    </li>
                </ul>
            </div>
        </div>

        {{-- 💰 Summary Cards --}}
        <div class="row g-4 mb-5">
            {{-- Capital --}}
            <div class="col-md-2">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-primary text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <h6 class="text-uppercase text-white-50 fw-bold mb-2 ls-1">رأس المال</h6>
                        <h3 class="fw-bold text-white mb-0" dir="ltr">{{ number_format($capital, 2) }}</h3>
                        <div class="mt-2 small text-white-50">رأس مال المحطة</div>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-landmark fa-3x text-white"></i>
                    </div>
                </div>
            </div>

            {{-- Opening Balance --}}
            <div class="col-md-2">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-secondary text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <h6 class="text-uppercase text-white-50 fw-bold mb-2 ls-1">الرصيد الافتتاحي</h6>
                        <h3 class="fw-bold text-white mb-0" dir="ltr">{{ number_format($openingBalance, 2) }}</h3>
                        <div class="mt-2 small text-white-50">مرحل من الأيام السابقة</div>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-history fa-3x text-white"></i>
                    </div>
                </div>
            </div>

            {{-- Income --}}
            <div class="col-md-2">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-success text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <h6 class="text-uppercase text-white-50 fw-bold mb-2 ls-1">إجمالي المقبوضات</h6>
                        <h3 class="fw-bold text-white mb-0" dir="ltr">+ {{ number_format($todayIncome, 2) }}</h3>
                        <div class="mt-2 small text-white-50">حركات اليوم</div>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-arrow-down fa-3x text-white"></i>
                    </div>
                </div>
            </div>

            {{-- Expense --}}
            <div class="col-md-2">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-danger text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <h6 class="text-uppercase text-white-50 fw-bold mb-2 ls-1">إجمالي الدفوعات</h6>
                        <h3 class="fw-bold text-white mb-0" dir="ltr">- {{ number_format($todayExpense, 2) }}</h3>
                        <div class="mt-2 small text-white-50">حركات اليوم</div>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-arrow-up fa-3x text-white"></i>
                    </div>
                </div>
            </div>

            {{-- Current Balance - Closing --}}
            <div class="col-md-3">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-warning text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <h6 class="text-uppercase text-white-50 fw-bold mb-2 ls-1">الرصيد الحالي (الإغلاق)</h6>
                        <h3 class="fw-bold text-white mb-0" dir="ltr">{{ number_format($currentBalance, 2) }}</h3>
                        <div class="mt-2 small text-white-50">النقدية المتوقعة في الخزنة</div>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-wallet fa-3x text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            {{-- 📝 Add Transaction Form --}}
            <div class="col-lg-4">
                <div class="card border-0 shadow-sm rounded-4">
                    <div class="card-header bg-white border-0 p-4 pb-0">
                        <h5 class="fw-bold text-dark mb-0"><i class="fas fa-plus-circle text-primary me-2"></i>إضافة حركة
                            جديدة</h5>
                    </div>
                    <div class="card-body p-4">
                        @if (session('success'))
                            <div class="alert alert-success rounded-3 mb-3">{{ session('success') }}</div>
                        @endif
                        @if ($errors->any())
                            <div class="alert alert-danger rounded-3 mb-3">
                                <ul class="mb-0 small">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('treasury.store') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">نوع الحركة</label>
                                <div class="btn-group w-100" role="group">
                                    <input type="radio" class="btn-check" name="type" id="type_income" value="income"
                                        checked>
                                    <label class="btn btn-outline-success fw-bold py-2" for="type_income">📥 إيراد
                                        (دخول)</label>

                                    <input type="radio" class="btn-check" name="type" id="type_expense"
                                        value="expense">
                                    <label class="btn btn-outline-danger fw-bold py-2" for="type_expense">📤 مصروف
                                        (خروج)</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">تاريخ الحركة</label>
                                <input type="date" name="transaction_date" value="{{ date('Y-m-d') }}"
                                    class="form-control bg-light border-0 fw-bold">
                            </div>

                            <div class="mb-3">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <label class="form-label text-secondary small fw-bold mb-0">المبلغ</label>
                                    <button type="button" id="toggleCalcBtn"
                                        class="btn btn-sm text-primary p-0 small fw-bold" style="font-size: 0.8rem;">
                                        <i class="fas fa-calculator me-1"></i> حاسبة الوقود
                                    </button>
                                </div>
                                <div class="input-group">
                                    <input type="number" step="0.01" name="amount"
                                        class="form-control bg-light border-0 fw-bold" placeholder="0.00" required>
                                    <span class="input-group-text bg-light border-0 text-secondary">ج.م</span>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">التصنيف (البند)</label>
                                <input type="text" name="category" list="categories"
                                    class="form-control bg-light border-0 text-start"
                                    placeholder="مثال: مبيعات بنزين، موردين..." required>
                                <datalist id="categories">
                                    <option value="رأس المال"></option>
                                    <option value="مبيعات بنزين"></option>
                                    <option value="مبيعات سولار"></option>
                                    <option value="غسيل وتشحيم"></option>
                                    <option value="زيوت"></option>
                                    <option value="ماركت"></option>
                                    <option value="توريد نقدية"></option>
                                    <option value="سداد موردين"></option>
                                    <option value="مصاريف تشغيل"></option>
                                    <option value="رواتب"></option>
                                    <option value="مسحوبات مالك"></option>
                                    <option value="عهدة موظف"></option>
                                    <option value="تحصيل من عميل (آجل)"></option>
                                    <option value="فاتورة كهرباء/مياه"></option>
                                    <option value="صيانة وإصلاحات"></option>
                                    <option value="شراء وقود (تفريغ تانك)"></option>
                                </datalist>
                            </div>

                            {{-- 🧮 Fuel Calculator --}}
                            <div id="fuelCalc"
                                class="mb-3 p-3 bg-warning-subtle rounded-3 d-none border border-warning-subtle">
                                <h6 class="text-dark fw-bold small mb-2"><i class="fas fa-gas-pump me-1"></i> حاسبة تكلفة
                                    الوقود (إضافة لترات)</h6>
                                <div class="row g-2">
                                    <div class="col-6">
                                        <label class="small text-muted fw-bold">عدد اللترات</label>
                                        <input type="number" id="calc_liters"
                                            class="form-control form-control-sm border-0 shadow-sm"
                                            placeholder="مثال: 1000">
                                    </div>
                                    <div class="col-6">
                                        <label class="small text-muted fw-bold">سعر الشراء / لتر</label>
                                        <input type="number" id="calc_price" step="0.01"
                                            class="form-control form-control-sm border-0 shadow-sm"
                                            placeholder="مثال: 11.5">
                                    </div>
                                </div>
                                <div class="mt-2 text-end">
                                    <small class="text-muted" id="calc_result">الإجمالي: 0.00 ج.م</small>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label text-secondary small fw-bold">وصف / ملاحظات</label>
                                <textarea name="description" id="description" class="form-control bg-light border-0" rows="2"
                                    placeholder="تفاصيل إضافية..."></textarea>
                            </div>

                            <button type="submit"
                                class="btn btn-dark w-100 py-2 fw-bold rounded-3 shadow-hover transform-scale">
                                <i class="fas fa-save me-2"></i> حفظ العملية
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            {{-- 📋 Transactions List --}}
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm rounded-4 h-100">
                    <div class="card-header bg-white border-0 p-4 pb-0 d-flex justify-content-between align-items-center">
                        <h5 class="fw-bold text-dark mb-0">
                            <i class="fas fa-list-ul text-secondary me-2"></i>
                            {{ $viewAll ? 'سجل جميع الحركات' : 'سجل حركات اليوم (' . $date . ')' }}
                        </h5>
                        <span
                            class="badge {{ count($allTransactions) > 0 ? 'bg-primary' : 'bg-secondary' }} rounded-pill px-3 py-2">
                            {{ count($allTransactions) }} حركة
                        </span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive p-3">
                            <table class="table table-hover align-middle mb-0 custom-table">
                                <thead class="bg-light text-secondary small text-uppercase">
                                    <tr>
                                        <th class="py-3 px-3 border-0 rounded-start">نوع</th>
                                        <th class="py-3 px-3 border-0">البند</th>
                                        <th class="py-3 px-3 border-0">الوصف</th>
                                        <th class="py-3 px-3 border-0 text-end">المبلغ</th>
                                        <th class="py-3 px-3 border-0 text-center rounded-end">إجراء</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($allTransactions as $trx)
                                        <tr class="border-bottom border-light">
                                            <td class="px-3">
                                                @if ($trx['type'] == 'income')
                                                    <span class="badge bg-success-subtle text-success rounded-pill px-3">
                                                        <i class="fas fa-arrow-down me-1"></i> إيراد
                                                    </span>
                                                @else
                                                    <span class="badge bg-danger-subtle text-danger rounded-pill px-3">
                                                        <i class="fas fa-arrow-up me-1"></i> مصروف
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-3 fw-bold text-dark">{{ $trx['category'] }}</td>
                                            <td class="px-3 text-muted small">
                                                {{ $trx['description'] ?? '-' }}
                                                <small class="d-block text-info mt-1">{{ $trx['user'] }}</small>
                                            </td>
                                            <td class="px-3 text-end fw-bold {{ $trx['type'] == 'income' ? 'text-success' : 'text-danger' }}"
                                                dir="ltr">
                                                {{ number_format($trx['amount'], 2) }}
                                            </td>
                                            <td class="px-3 text-center">
                                                @if ($trx['source'] == 'treasury')
                                                    <form
                                                        action="{{ route('treasury.destroy', str_replace('treasury_', '', $trx['id'])) }}"
                                                        method="POST"
                                                        onsubmit="return confirm('هل أنت متأكد من الحذف؟');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="btn btn-sm btn-link text-danger p-0 opacity-50 hover-opacity-100">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span
                                                        class="badge bg-info-subtle text-info px-2 py-1 small">مبيعات</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-5">
                                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                                <p class="mb-0">لا توجد حركات مسجلة لهذا اليوم</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Script for Calculator --}}
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const categoryInput = document.querySelector('input[name="category"]');
                const fuelCalc = document.getElementById('fuelCalc');
                const toggleCalcBtn = document.getElementById('toggleCalcBtn');
                const litersInput = document.getElementById('calc_liters');
                const priceInput = document.getElementById('calc_price');
                const amountInput = document.querySelector('input[name="amount"]');
                const descInput = document.getElementById('description');
                const resultLabel = document.getElementById('calc_result');

                function checkCategory() {
                    const val = categoryInput.value;
                    if (val.includes('شراء') || val.includes('وقود') || val.includes('بنزين') || val.includes(
                            'سولار')) {
                        fuelCalc.classList.remove('d-none');
                    } else {
                        // Don't hide automatically if user opened it manually, unless we want strict behavior
                        // For now, let's only auto-show. Auto-hide might be annoying if they cleared the field to type "Fuel 92"
                        // fuelCalc.classList.add('d-none');
                    }
                }

                // Manual Toggle
                toggleCalcBtn.addEventListener('click', function() {
                    fuelCalc.classList.toggle('d-none');
                });

                // Auto Show on Input
                categoryInput.addEventListener('input', checkCategory);
                categoryInput.addEventListener('change', checkCategory);

                // Calculation Logic
                function updateCalc() {
                    const l = parseFloat(litersInput.value) || 0;
                    const p = parseFloat(priceInput.value) || 0;
                    const total = l * p;

                    if (total > 0) {
                        amountInput.value = total.toFixed(2);
                        resultLabel.innerText = 'الإجمالي: ' + total.toLocaleString() + ' ج.م';

                        // Auto-update description
                        descInput.value = `شراء ${l} لتر × ${p} ج.م`;
                    }
                }

                litersInput.addEventListener('input', updateCalc);
                priceInput.addEventListener('input', updateCalc);

                // Check strictly on load (for validation errors)
                if (categoryInput.value) checkCategory();
            });
        </script>

        {{-- CSS Styles --}}
        <style>
            .ls-1 {
                letter-spacing: 0.5px;
            }

            .bg-gradient-secondary {
                background: linear-gradient(135deg, #6c757d, #343a40) !important;
            }

            .bg-gradient-success {
                background: linear-gradient(135deg, #28a745, #218838) !important;
            }

            .bg-gradient-danger {
                background: linear-gradient(135deg, #dc3545, #bd2130) !important;
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
            }

            .bg-success-subtle {
                background-color: #d1e7dd;
            }

            .bg-danger-subtle {
                background-color: #f8d7da;
            }

            .shadow-hover:hover {
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
                transform: translateY(-2px);
                transition: all 0.3s;
            }

            .custom-table tr:last-child {
                border-bottom: none !important;
            }
        </style>
    </div>
@endsection
