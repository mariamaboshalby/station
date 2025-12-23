@extends('layouts.app')

@section('content')
    <div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

        {{-- 🏷️ Header & Actions --}}
        <div class="d-print-none mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark mb-1">💸 تقرير المصروفات التفصيلي</h2>
                <p class="text-muted">عرض وتحليل كافة المصروفات خلال الفترة المحددة</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('reports.index') }}" class="btn btn-outline-secondary shadow-sm px-4 rounded-pill">
                    <i class="fas fa-arrow-right me-2"></i> العودة للتقرير الرئيسي
                </a>
                <div class="dropdown">
                    <button class="btn btn-primary shadow-sm px-4 rounded-pill dropdown-toggle" type="button"
                        data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-file-export me-2"></i> تصدير
                    </button>
                    <ul class="dropdown-menu text-end">
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('reports.expenses.export', array_merge(request()->all(), ['type' => 'pdf'])) }}">
                                <i class="fas fa-file-pdf text-danger me-2"></i> تصدير PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('reports.expenses.export', array_merge(request()->all(), ['type' => 'excel'])) }}">
                                <i class="fas fa-file-excel text-success me-2"></i> تصدير Excel
                            </a>
                        </li>
                    </ul>
                </div>
                <button type="button" onclick="window.print()" class="btn btn-dark shadow-sm px-4 rounded-pill">
                    <i class="fas fa-print me-2"></i> طباعة التقرير
                </button>
            </div>
        </div>

        {{-- 🔍 Filter Card --}}
        <div class="card border-0 shadow-sm rounded-4 mb-5 d-print-none overflow-hidden">
            <div class="card-body p-4 bg-white">
                <form action="{{ route('reports.expenses') }}" method="GET" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label text-secondary small fw-bold text-uppercase ls-1">من تاريخ</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="form-control border-0 bg-light py-2 fw-bold text-dark">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-secondary small fw-bold text-uppercase ls-1">إلى تاريخ</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="form-control border-0 bg-light py-2 fw-bold text-dark">
                    </div>
                    <div class="col-md-4">
                        <button type="submit"
                            class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm transition-all">
                            <i class="fas fa-filter me-2"></i> تحديث البيانات
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- 💰 Summary Cards --}}
        <div class="row g-4 mb-5">
            <div class="col-md-6">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-danger text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                                <i class="fas fa-money-bill-wave fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">إجمالي المصروفات</h6>
                        </div>
                        <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">{{ number_format($totalExpenses, 2) }}
                        </h2>
                        <span class="text-white-50 small">جنية مصري</span>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-file-invoice-dollar fa-4x text-white"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-info text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                                <i class="fas fa-list fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">عدد المصروفات</h6>
                        </div>
                        <h2 class="fw-bold text-white mb-0 display-6">{{ count($expenses) }}</h2>
                        <span class="text-white-50 small">عملية</span>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-receipt fa-4x text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📊 Expenses by Category --}}
        <div class="card border-0 shadow-sm rounded-4 mb-5">
            <div class="card-header bg-white border-bottom p-4">
                <h5 class="fw-bold m-0 text-dark"><i class="fas fa-chart-pie text-danger me-2"></i>تقسيم المصروفات حسب الفئة
                </h5>
            </div>
            <div class="card-body p-4">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-secondary small text-uppercase">
                            <tr>
                                <th class="border-0 rounded-start">الفئة</th>
                                <th class="border-0 text-end">المبلغ</th>
                                <th class="border-0 text-end rounded-end">النسبة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($expensesByCategory as $category => $amount)
                                <tr>
                                    <td class="ps-3 fw-bold text-dark">{{ $category }}</td>
                                    <td class="text-end fw-bold text-danger" dir="ltr">{{ number_format($amount, 2) }}
                                    </td>
                                    <td class="text-end">
                                        <div class="progress" style="height: 25px;">
                                            <div class="progress-bar bg-danger" role="progressbar"
                                                style="width: {{ ($amount / $totalExpenses) * 100 }}%">
                                                {{ number_format(($amount / $totalExpenses) * 100, 1) }}%
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted py-4">لا توجد بيانات</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- 📜 Detailed Expenses List --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0 text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>تفاصيل المصروفات</h5>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">{{ count($expenses) }} مصروف</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead class="bg-light text-uppercase text-secondary small">
                        <tr>
                            <th class="py-3 px-4 border-0">#</th>
                            <th class="py-3 px-4 border-0">التاريخ</th>
                            <th class="py-3 px-4 border-0">الفئة</th>
                            <th class="py-3 px-4 border-0">البيان</th>
                            <th class="py-3 px-4 border-0">المسؤول</th>
                            <th class="py-3 px-4 border-0 text-end">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($expenses as $index => $expense)
                            <tr class="border-bottom border-light">
                                <td class="px-4 text-muted fw-bold">{{ $index + 1 }}</td>
                                <td class="px-4 text-muted fw-bold small">
                                    {{ Carbon\Carbon::parse($expense->transaction_date)->format('Y-m-d') }}
                                    <span class="d-block fw-normal text-secondary mt-1">
                                        {{ Carbon\Carbon::parse($expense->transaction_date)->format('h:i A') }}
                                    </span>
                                </td>
                                <td class="px-4">
                                    <span class="badge rounded-pill px-3 py-2 bg-danger-subtle text-danger">
                                        {{ $expense->category }}
                                    </span>
                                </td>
                                <td class="px-4">
                                    <span class="d-block text-dark">{{ $expense->description ?? '-' }}</span>
                                </td>
                                <td class="px-4 text-muted">
                                    {{ $expense->user->name ?? 'غير محدد' }}
                                </td>
                                <td class="px-4 text-end fw-bold text-danger" dir="ltr">
                                    {{ number_format($expense->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">لا توجد مصروفات مسجلة في هذه الفترة</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Custom Styles --}}
        <style>
            .ls-1 {
                letter-spacing: 0.5px;
            }

            .bg-gradient-danger {
                background: linear-gradient(135deg, #dc3545, #bd2130) !important;
            }

            .bg-gradient-info {
                background: linear-gradient(135deg, #0dcaf0, #0aa2c0) !important;
            }

            .bg-danger-subtle {
                background-color: #f8d7da;
            }

            .transition-all {
                transition: all 0.3s ease;
            }

            .group-hover:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1) !important;
            }

            .custom-table tr:last-child {
                border-bottom: none !important;
            }

            @media print {

                .btn,
                form,
                .d-print-none {
                    display: none !important;
                }

                body {
                    background: #fff !important;
                }

                .container-fluid {
                    padding: 0 !important;
                }

                .card {
                    border: none !important;
                    box-shadow: none !important;
                    margin-bottom: 2rem !important;
                }

                .table-responsive {
                    overflow: visible !important;
                }

                .badge {
                    border: 1px solid #ddd;
                }
            }
        </style>
    </div>
@endsection
