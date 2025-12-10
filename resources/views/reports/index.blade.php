@extends('layouts.app')



@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">
    
    {{-- 🏷️ Header & Actions --}}
    <div class="d-print-none mb-4 d-flex justify-content-between align-items-center">
        <div>
            <h2 class="fw-bold text-dark mb-1"> كشف الحساب المالي</h2>
            <p class="text-muted">متابعة الإيرادات والمصروفات وصافي الأرباح</p>
        </div>
        <button type="button" onclick="window.print()" class="btn btn-dark shadow-sm px-4 rounded-pill">
            <i class="fas fa-print me-2"></i> طباعة التقرير
        </button>
    </div>

    {{-- 🔍 Filter Card --}}
    <div class="card border-0 shadow-sm rounded-4 mb-5 d-print-none overflow-hidden">
        <div class="card-body p-4 bg-white">
            <form action="{{ route('reports.index') }}" method="GET" class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label text-secondary small fw-bold text-uppercase ls-1">نوع المعاملة</label>
                    <select name="payment_type" class="form-select border-0 bg-light py-2 fw-bold text-dark">
                        <option value="all" {{ $paymentType == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="cash" {{ $paymentType == 'cash' ? 'selected' : '' }}>💵 نقدي (كاش + مصروفات)</option>
                        <option value="credit" {{ $paymentType == 'credit' ? 'selected' : '' }}>📄 آجل</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label text-secondary small fw-bold text-uppercase ls-1">من تاريخ</label>
                    <input type="date" name="start_date" value="{{ $startDate }}" class="form-control border-0 bg-light py-2 fw-bold text-dark">
                </div>
                <div class="col-md-3">
                    <label class="form-label text-secondary small fw-bold text-uppercase ls-1">إلى تاريخ</label>
                    <input type="date" name="end_date" value="{{ $endDate }}" class="form-control border-0 bg-light py-2 fw-bold text-dark">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold rounded-3 shadow-sm transition-all">
                        <i class="fas fa-filter me-2"></i> تحديث البيانات
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 💰 Summary Cards --}}
    <div class="row g-4 mb-5">
        {{-- Capital --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-primary text-white">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                            <i class="fas fa-landmark fa-lg"></i>
                        </div>
                        <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">رأس المال</h6>
                    </div>
                    <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">{{ number_format($capital, 2) }}</h2>
                    <span class="text-white-50 small">جنية مصري</span>
                </div>
                <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                    <i class="fas fa-university fa-4x text-white"></i>
                </div>
            </div>
        </div>
        
        {{-- Total Revenue --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-success text-white">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                            <i class="fas fa-arrow-down fa-lg"></i>
                        </div>
                        <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">إجمالي الإيرادات</h6>
                    </div>
                    <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">{{ number_format($totalRevenue, 2) }}</h2>
                    <span class="text-white-50 small">جنية مصري</span>
                </div>
                <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                    <i class="fas fa-chart-line fa-4x text-white"></i>
                </div>
            </div>
        </div>

        {{-- Total Expense --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-danger text-white">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                            <i class="fas fa-arrow-up fa-lg"></i>
                        </div>
                        <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">إجمالي المصروفات</h6>
                    </div>
                    <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">{{ number_format($totalExpense, 2) }}</h2>
                    <span class="text-white-50 small">جنية مصري</span>
                </div>
                <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                    <i class="fas fa-file-invoice-dollar fa-4x text-white"></i>
                </div>
            </div>
        </div>

        {{-- Net Profit --}}
        <div class="col-md-3">
            <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative group-hover bg-gradient-secondary text-white">
                <div class="card-body p-4 position-relative z-1">
                    <div class="d-flex align-items-center mb-3">
                        <div class="icon-box bg-white bg-opacity-25 rounded-circle p-3 me-3 text-white">
                            <i class="fas fa-wallet fa-lg"></i>
                        </div>
                        <h6 class="text-uppercase text-white-50 fw-bold mb-0 ls-1">صافي الربح / الخسارة</h6>
                    </div>
                    <h2 class="fw-bold text-white mb-0 display-6" dir="ltr">{{ number_format($netProfit, 2) }}</h2>
                    <span class="text-white-50 small">جنية مصري</span>
                </div>
                <div class="position-absolute bottom-0 end-0 p-3 opacity-25">
                    <i class="fas fa-coins fa-4x text-white"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- 📊 Analytics Section --}}
    <div class="row g-4 mb-5">
        {{-- Revenue Breakdown --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-pie text-success me-2"></i>تفاصيل الإيرادات</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="border-0 rounded-start">النوع</th>
                                    <th class="border-0 text-end rounded-end">المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($revenueByType as $type => $amount)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">{{ $type }}</td>
                                        <td class="text-end fw-bold text-success" dir="ltr">{{ number_format($amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Expense Breakdown --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-header bg-transparent border-0 p-4 pb-0">
                    <h5 class="fw-bold text-dark mb-0"><i class="fas fa-chart-bar text-danger me-2"></i>تفاصيل المصروفات</h5>
                </div>
                <div class="card-body p-4">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-secondary small text-uppercase">
                                <tr>
                                    <th class="border-0 rounded-start">التصنيف</th>
                                    <th class="border-0 text-end rounded-end">المبلغ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($expenseByCategory as $cat => $amount)
                                    <tr>
                                        <td class="ps-3 fw-bold text-dark">{{ $cat }}</td>
                                        <td class="text-end fw-bold text-danger" dir="ltr">{{ number_format($amount, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-center text-muted py-4">لا توجد بيانات</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 📜 Detailed Log --}}
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold m-0 text-dark"><i class="fas fa-list-ul me-2 text-primary"></i>سجل المعاملات اليومي</h5>
            <span class="badge bg-light text-dark border rounded-pill px-3 py-2">{{ count($reportData) }} عملية</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0 custom-table">
                <thead class="bg-light text-uppercase text-secondary small">
                    <tr>
                        <th class="py-3 px-4 border-0">التاريخ</th>
                        <th class="py-3 px-4 border-0">النوع</th>
                        <th class="py-3 px-4 border-0">البيان</th>
                        <th class="py-3 px-4 border-0 text-end">القيمة</th>
                        <th class="py-3 px-4 border-0 text-end">الرصيد</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    @forelse($reportData as $row)
                        <tr class="border-bottom border-light">
                            <td class="px-4 text-muted fw-bold small">
                                {{ $row['date']->format('Y-m-d') }} <span class="d-block fw-normal text-secondary mt-1">{{ $row['date']->format('h:i A') }}</span>
                            </td>
                            <td class="px-4">
                                <span class="badge rounded-pill px-3 py-2 {{ $row['is_revenue'] ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }}">
                                    {{ $row['is_revenue'] ? 'إيراد' : 'مصروف' }}
                                </span>
                            </td>
                            <td class="px-4">
                                <span class="d-block fw-bold text-dark">{{ $row['type'] }}</span>
                                <small class="text-muted">{{ $row['description'] }}</small>
                            </td>
                            <td class="px-4 text-end fw-bold {{ $row['is_revenue'] ? 'text-success' : 'text-danger' }}" dir="ltr">
                                {{ $row['is_revenue'] ? '+' : '-' }} {{ number_format($row['amount'], 2) }}
                            </td>
                            <td class="px-4 text-end fw-bold text-dark" dir="ltr">
                                {{ number_format($row['balance'], 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                                <p class="mb-0">لا توجد معاملات مسجلة في هذه الفترة</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    {{-- Custom Styles for this page only --}}
    <style>
        .ls-1 { letter-spacing: 0.5px; }
        .bg-gradient-secondary { background: linear-gradient(135deg, #6c757d, #343a40) !important; }
        .bg-gradient-success { background: linear-gradient(135deg, #28a745, #218838) !important; }
        .bg-gradient-danger { background: linear-gradient(135deg, #dc3545, #bd2130) !important; }
        .bg-gradient-warning { background: linear-gradient(135deg, #ffc107, #e0a800) !important; }
        .bg-gradient-primary { background: linear-gradient(45deg, #0d6efd, #0a58ca); }
        .bg-success-subtle { background-color: #d1e7dd; }
        .bg-danger-subtle { background-color: #f8d7da; }
        .transition-all { transition: all 0.3s ease; }
        .group-hover:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
        .custom-table tr:last-child { border-bottom: none !important; }
        
        @media print {
            .btn, form, .d-print-none { display: none !important; }
            body { background: #fff !important; }
            .container-fluid { padding: 0 !important; }
            .card { border: none !important; box-shadow: none !important; margin-bottom: 2rem !important; }
            .table-responsive { overflow: visible !important; }
            .badge { border: 1px solid #ddd; }
        }
    </style>
</div>
@endsection
