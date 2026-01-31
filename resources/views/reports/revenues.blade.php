@extends('layouts.app')

@section('content')
    <div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

        {{-- 🏷️ Header & Actions --}}
        <div class="d-print-none mb-4 d-flex justify-content-between align-items-center">
            <div>
                <h2 class="fw-bold text-dark mb-1">💰 تقرير الإيرادات التفصيلي</h2>
                <p class="text-muted">عرض وتحليل كافة الإيرادات من مبيعات الوقود</p>
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
                                href="{{ route('reports.revenues.export', array_merge(request()->all(), ['type' => 'pdf'])) }}">
                                <i class="fas fa-file-pdf text-danger me-2"></i> تصدير PDF
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                                href="{{ route('reports.revenues.export', array_merge(request()->all(), ['type' => 'excel'])) }}">
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
                <form action="{{ route('reports.revenues') }}" method="GET" class="row g-3 align-items-end">
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
            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-success text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                                <i class="fas fa-chart-line fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">إجمالي الإيرادات</h6>
                        </div>
                        <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">{{ number_format($totalRevenue, 2) }}
                        </h2>
                        <span class="text-white-50 small">جنية مصري</span>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-money-bill-wave fa-4x text-white"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-primary text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                                <i class="fas fa-wallet fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">الإيرادات النقدية</h6>
                        </div>
                        <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">{{ number_format($cashRevenue, 2) }}
                        </h2>
                        <span class="text-white-50 small">جنية مصري</span>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-cash-register fa-4x text-white"></i>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div
                    class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-warning text-white">
                    <div class="card-body p-4 position-relative z-1">
                        <div class="d-flex align-items-center mb-3">
                            <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                                <i class="fas fa-file-invoice fa-lg"></i>
                            </div>
                            <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">الإيرادات الآجلة</h6>
                        </div>
                        <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">{{ number_format($creditRevenue, 2) }}
                        </h2>
                        <span class="text-white-50 small">جنية مصري</span>
                    </div>
                    <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                        <i class="fas fa-receipt fa-4x text-white"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- 📜 Detailed Revenues List --}}
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold m-0 text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>تفاصيل الإيرادات</h5>
                <span class="badge bg-light text-dark border rounded-pill px-3 py-2">{{ count($revenues) }} عملية</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0 custom-table">
                    <thead class="bg-light text-uppercase text-secondary small">
                        <tr>
                            <th class="py-3 px-4 border-0">#</th>
                            <th class="py-3 px-4 border-0">التاريخ</th>
                            <th class="py-3 px-4 border-0">الموظف / الشيفت</th>
                            <th class="py-3 px-4 border-0">نوع الوقود</th>
                            <th class="py-3 px-4 border-0">المسدس</th>
                            <th class="py-3 px-4 border-0">نوع العملية</th>
                            <th class="py-3 px-4 border-0 text-end">المبلغ</th>
                        </tr>
                    </thead>
                    <tbody class="border-top-0">
                        @forelse($revenues as $index => $rev)
                            @php
                                $isCredit = $rev->clientRefuelings->isNotEmpty();
                                $amount = 0;
                                if ($isCredit) {
                                    $amount = $rev->clientRefuelings->sum('total_amount');
                                } elseif ($rev->cash_liters > 0) {
                                    $basePrice = $rev->nozzle->pump->tank->fuel->price_per_liter ?? 0;
                                    $amount = $rev->cash_liters * $basePrice;
                                }
                            @endphp
                            <tr class="border-bottom border-light">
                                <td class="px-4 text-muted fw-bold">{{ $index + 1 }}</td>
                                <td class="px-4 text-muted fw-bold small">
                                    {{ $rev->created_at->format('Y-m-d') }}
                                    <span class="d-block fw-normal text-secondary mt-1">
                                        {{ $rev->created_at->format('h:i A') }}
                                    </span>
                                </td>
                                <td class="px-4">
                                    <span class="d-block text-dark fw-bold">{{ $rev->shift->user->name ?? '-' }}</span>
                                    <small class="text-muted">شيفت #{{ $rev->shift_id }}</small>
                                </td>
                                <td class="px-4">
                                    <span class="badge bg-info-subtle text-info px-3 py-2 rounded-pill">
                                        {{ $rev->nozzle->pump->tank->fuel->name ?? 'غير محدد' }}
                                    </span>
                                </td>
                                <td class="px-4 text-muted">{{ $rev->nozzle->name ?? '-' }}</td>
                                <td class="px-4">
                                    @if ($rev->client_id)
                                        <span class="badge bg-warning-subtle text-warning px-3 py-2">
                                            آجل
                                        </span>
                                        <small class="d-block text-muted mt-1">{{ $rev->client->name ?? '-' }}</small>
                                    @else
                                        <span class="badge bg-success-subtle text-success px-3 py-2">
                                            نقدي
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 text-end fw-bold text-success" dir="ltr">
                                    {{ number_format($amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                    <p class="mb-0">لا توجد إيرادات مسجلة في هذه الفترة</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($revenues) > 0)
                        <tfoot class="bg-light">
                            <tr>
                                <td colspan="6" class="px-4 py-3 fw-bold text-dark text-end">الإجمالي:</td>
                                <td class="px-4 py-3 text-end">
                                    <span class="badge bg-success text-white px-3 py-2 fs-5 fw-bold" dir="ltr">
                                        {{ number_format($totalRevenue, 2) }} ج.م
                                    </span>
                                </td>
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

            .bg-gradient-success {
                background: linear-gradient(135deg, #28a745, #218838) !important;
            }

            .bg-gradient-primary {
                background: linear-gradient(135deg, #0d6efd, #0a58ca) !important;
            }

            .bg-gradient-warning {
                background: linear-gradient(135deg, #ffc107, #e0a800) !important;
            }

            .bg-success-subtle {
                background-color: #d1e7dd;
            }

            .bg-warning-subtle {
                background-color: #fff3cd;
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
