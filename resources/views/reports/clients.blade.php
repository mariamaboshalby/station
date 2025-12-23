@extends('layouts.app')

@section('content')
    <div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

        {{-- 🏷️ Header & Actions --}}
        <div class="d-print-none mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark mb-1">👥 تقرير حسابات العملاء</h2>
                <p class="text-muted">متابعة الديون والمستحقات على العملاء</p>
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
                                href="{{ route('reports.clients.export', array_merge(request()->all(), ['type' => 'pdf'])) }}">
                                <i class="fas fa-file-pdf text-danger me-2"></i> تصدير PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('reports.clients.export', array_merge(request()->all(), ['type' => 'excel'])) }}">
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

        {{-- 💰 Summary Card --}}
        <div class="row g-4 mb-5">
            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-warning text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                                <i class="fas fa-exclamation-triangle fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">إجمالي الديون</h6>
                        </div>
                        <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">{{ number_format($totalDebt, 2) }}</h2>
                        <span class="text-white-50 small">جنية مصري</span>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-hand-holding-usd fa-4x text-white"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-info text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                                <i class="fas fa-users fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">عدد العملاء</h6>
                        </div>
                        <h2 class="fw-bold text-white mb-0 display-6">{{ count($clients) }}</h2>
                        <span class="text-white-50 small">عميل</span>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-user-friends fa-4x text-white"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-secondary text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                                <i class="fas fa-chart-line fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">متوسط الدين</h6>
                        </div>
                        <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">
                            {{ count($clients) > 0 ? number_format($totalDebt / count($clients), 2) : '0.00' }}
                        </h2>
                        <span class="text-white-50 small">جنية مصري</span>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-balance-scale fa-4x text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📜 Clients Table --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0 text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>تفاصيل حسابات العملاء</h5>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">{{ count($clients) }} عميل</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead class="bg-light text-uppercase text-secondary small">
                        <tr>
                            <th class="py-3 px-4 border-0">#</th>
                            <th class="py-3 px-4 border-0">اسم العميل</th>
                            <th class="py-3 px-4 border-0">نوع الوقود</th>
                            <th class="py-3 px-4 border-0 text-end">إجمالي المشتريات</th>
                            <th class="py-3 px-4 border-0 text-end">المدفوع</th>
                            <th class="py-3 px-4 border-0 text-end">الرصيد المستحق</th>
                            <th class="py-3 px-4 border-0 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($clients as $index => $client)
                            <tr class="border-bottom border-light">
                                <td class="px-4 text-muted fw-bold">{{ $index + 1 }}</td>
                                <td class="px-4">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center me-2"
                                            style="width: 40px; height: 40px; min-width: 40px;">
                                            <i class="fas fa-user"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $client['name'] }}</span>
                                    </div>
                                </td>
                                <td class="px-4">
                                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                        {{ $client['fuel_type'] }}
                                    </span>
                                </td>
                                <td class="px-4 text-end fw-bold text-secondary" dir="ltr">
                                    {{ number_format($client['total_transactions'], 2) }}
                                </td>
                                <td class="px-4 text-end fw-bold text-success" dir="ltr">
                                    {{ number_format($client['total_payments'], 2) }}
                                </td>
                                <td class="px-4 text-end">
                                    <span class="badge bg-warning text-dark px-3 py-2 fs-6 fw-bold" dir="ltr">
                                        {{ number_format($client['balance'], 2) }} ج.م
                                    </span>
                                </td>
                                <td class="px-4 text-center">
                                    <a href="{{ route('clients.transactions', $client['id']) }}"
                                        class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-eye me-1"></i> التفاصيل
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-smile fa-3x mb-3 opacity-25 text-success"></i>
                                    <p class="mb-0 fw-bold">رائع! لا توجد ديون على العملاء حالياً 🎉</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($clients) > 0)
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="5" class="px-4 py-3 fw-bold text-dark text-end">الإجمالي:</td>
                                <td class="px-4 py-3 text-end">
                                    <span class="badge bg-danger text-white px-3 py-2 fs-5 fw-bold" dir="ltr">
                                        {{ number_format($totalDebt, 2) }} ج.م
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>

        {{-- Custom Styles --}}
        <style>
            .ls-1 {
                letter-spacing: 0.5px;
            }

            .bg-gradient-warning {
                background: linear-gradient(135deg, #ffc107, #e0a800) !important;
            }

            .bg-gradient-info {
                background: linear-gradient(135deg, #0dcaf0, #0aa2c0) !important;
            }

            .bg-gradient-secondary {
                background: linear-gradient(135deg, #6c757d, #343a40) !important;
            }

            .bg-info-subtle {
                background-color: #cfe2ff;
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
