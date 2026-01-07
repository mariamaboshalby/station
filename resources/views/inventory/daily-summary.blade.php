@extends('layouts.app')

@section('content')
<div class="container-fluid pb-5" dir="rtl" style="font-family: 'Tajawal', sans-serif;">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-dark mb-1">📊 اليومي المفصل</h2>
            <p class="text-muted">تاريخ الجرد: {{ $date ?? date('Y-m-d') }}</p>
            @if(!request()->has('debug'))
                <a href="{{ request()->url() . (request()->getQueryString() ? '?' . request()->getQueryString() . '&debug=1' : '?debug=1') }}" class="btn btn-sm btn-outline-info">
                    <i class="fas fa-bug me-1"></i> Debug
                </a>
            @endif
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-2"></i> رجوع
            </a>
            <button onclick="window.print()" class="btn btn-dark">
                <i class="fas fa-print me-2"></i> طباعة
            </button>
        </div>
    </div>

    <!-- Daily Summary Table -->
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-4">
            <h4 class="fw-bold text-center mb-4">سجل الجرد اليومي المفصل</h4>
            
            <!-- Date Selection Form -->
            <form action="{{ route('inventory.daily.summary') }}" method="GET" class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">اختر التاريخ</label>
                    <input type="date" name="date" value="{{ $date ?? date('Y-m-d') }}" class="form-control" required>
                </div>
                <div class="col-md-8 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search me-2"></i> عرض الجرد
                    </button>
                </div>
            </form>
            
            <!-- Debug Info -->
            @if(request()->has('debug'))
                <div class="alert alert-info">
                    <h6>Debug Information:</h6>
                    <p>Date: {{ $date }}</p>
                    <p>Pump Inventories Count: {{ $pumpInventoriesCount ?? 'N/A' }}</p>
                    <p>Inventory Purchases Count: {{ $inventoryPurchasesCount ?? 'N/A' }}</p>
                    <p>Transactions Count: {{ $transactionsCount ?? 'N/A' }}</p>
                    
                    @if(isset($transactions))
                        <h6>Raw Transaction Data:</h6>
                        <pre>{{ json_encode($transactions->toArray(), JSON_PRETTY_PRINT) }}</pre>
                    @endif
                    
                    @if(isset($inventoryPurchases))
                        <h6>Raw Expense Data:</h6>
                        <pre>{{ json_encode($inventoryPurchases->toArray(), JSON_PRETTY_PRINT) }}</pre>
                        
                        <h6>Controller Processing Results:</h6>
                        @if(isset($processingDebug))
                            @foreach($processingDebug as $debug)
                                <div class="border p-2 mb-2">
                                    <p><strong>Description:</strong> {{ $debug['description'] ?? 'N/A' }}</p>
                                    <p><strong>Extracted Liters:</strong> {{ $debug['extracted_liters'] ?? 'N/A' }}</p>
                                    <p><strong>Tank ID:</strong> {{ $debug['tank_id'] ?? 'N/A' }}</p>
                                    <p><strong>Fuel Type:</strong> {{ $debug['fuel_type'] ?? 'N/A' }}</p>
                                    <p><strong>Will Add To:</strong> {{ $debug['will_add_to'] ?? $debug['error'] ?? 'N/A' }}</p>
                                </div>
                            @endforeach
                        @else
                            <p>No processing debug data available</p>
                        @endif
                    @endif
                    
                    <h6>Solar Data:</h6>
                    <pre>{{ json_encode([
                        'opening_balance' => $solarData['opening_balance'] ?? 0,
                        'received' => $solarData['received'] ?? 0,
                        'dispensed' => $solarData['dispensed'] ?? 0
                    ], JSON_PRETTY_PRINT) }}</pre>
                    <h6>Benzine 92 Data:</h6>
                    <pre>{{ json_encode([
                        'opening_balance' => $benzine92Data['opening_balance'] ?? 0,
                        'received' => $benzine92Data['received'] ?? 0,
                        'dispensed' => $benzine92Data['dispensed'] ?? 0
                    ], JSON_PRETTY_PRINT) }}</pre>
                    <h6>Benzine 80 Data:</h6>
                    <pre>{{ json_encode([
                        'opening_balance' => $benzine80Data['opening_balance'] ?? 0,
                        'received' => $benzine80Data['received'] ?? 0,
                        'dispensed' => $benzine80Data['dispensed'] ?? 0
                    ], JSON_PRETTY_PRINT) }}</pre>
                    <h6>Benzine 95 Data:</h6>
                    <pre>{{ json_encode([
                        'opening_balance' => $benzine95Data['opening_balance'] ?? 0,
                        'received' => $benzine95Data['received'] ?? 0,
                        'dispensed' => $benzine95Data['dispensed'] ?? 0
                    ], JSON_PRETTY_PRINT) }}</pre>
                </div>
            @endif
            
            @if(($solarData['opening_balance'] ?? 0) == 0 && ($solarData['received'] ?? 0) == 0 && ($solarData['dispensed'] ?? 0) == 0)
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    لا توجد بيانات جرد للتاريخ المحدد. الرجاء التأكد من وجود سجلات جرد يومي.
                </div>
            @endif
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover text-center" style="font-size: 14px;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 15%;">الجهة الوارد منها<br>أو جهة التفريغ</th>
                            <th class="text-center" style="width: 10%;">رقم وتاريخ<br>الفاتورة</th>
                            <th class="text-center" style="width: 8%;">سولار</th>
                            <th class="text-center" style="width: 8%;">بنزين ٩٢</th>
                            <th class="text-center" style="width: 8%;">بنزين ٨٠</th>
                            <th class="text-center" style="width: 8%;">بنزين ٩٥</th>

                        </tr>
                    </thead>
                    <tbody>
                        <!-- Opening Balance Row -->
                        <tr class="table-secondary">
                            <td class="fw-bold">الرصيد أول اليوم</td>
                            <td>-</td>
                            <td class="fw-bold">{{ number_format($solarData['opening_balance'] ?? 0, 0) }}</td>
                            <td class="fw-bold">{{ number_format($benzine92Data['opening_balance'] ?? 0, 0) }}</td>
                            <td class="fw-bold">{{ number_format($benzine80Data['opening_balance'] ?? 0, 0) }}</td>
                            <td class="fw-bold">{{ number_format($benzine95Data['opening_balance'] ?? 0, 0) }}</td>
                                                      
                        </tr>
                        
                        <!-- Received Row -->
                        <tr>
                            <td class="fw-bold text-success">الوارد</td>
                            <td>{{ $invoiceNumber ?? '-' }}</td>
                            <td class="text-success">{{ number_format($solarData['received'] ?? 0, 0) }}</td>
                            <td class="text-success">{{ number_format($benzine92Data['received'] ?? 0, 0) }}</td>
                            <td class="text-success">{{ number_format($benzine80Data['received'] ?? 0, 0) }}</td>
                            <td class="text-success">{{ number_format($benzine95Data['received'] ?? 0, 0) }}</td>
                            
                        </tr>
                        
                        <!-- Sales Row -->
                        <tr class="table-warning">
                            <td class="fw-bold">البيعات</td>
                            <td>{{ $dispensedInvoiceNumber ?? '-' }}</td>
                            <td class="fw-bold">{{ number_format($solarData['sales'] ?? 0, 0) }}</td>
                            <td class="fw-bold">{{ number_format($benzine92Data['sales'] ?? 0, 0) }}</td>
                            <td class="fw-bold">{{ number_format($benzine80Data['sales'] ?? 0, 0) }}</td>
                            <td class="fw-bold">{{ number_format($benzine95Data['sales'] ?? 0, 0) }}</td>
                      
                        </tr>
                        
                        <!-- Dispensed Row -->
                        <tr>
                            <td class="fw-bold text-danger">المنصرف</td>
                            <td>{{ $dispensedInvoiceNumber ?? '-' }}</td>
                            <td class="text-danger">{{ number_format($solarData['dispensed'] ?? 0, 0) }}</td>
                            <td class="text-danger">{{ number_format($benzine92Data['dispensed'] ?? 0, 0) }}</td>
                            <td class="text-danger">{{ number_format($benzine80Data['dispensed'] ?? 0, 0) }}</td>
                            <td class="text-danger">{{ number_format($benzine95Data['dispensed'] ?? 0, 0) }}</td>
                       
                        </tr>
                        
                        <!-- Total Row -->
                        <tr class="table-info fw-bold">
                            <td>الجملة</td>
                            <td>-</td>
                            <td>{{ number_format(($solarData['opening_balance'] ?? 0) + ($solarData['received'] ?? 0) - ($solarData['dispensed'] ?? 0), 0) }}</td>
                            <td>{{ number_format(($benzine92Data['opening_balance'] ?? 0) + ($benzine92Data['received'] ?? 0) - ($benzine92Data['dispensed'] ?? 0), 0) }}</td>
                            <td>{{ number_format(($benzine80Data['opening_balance'] ?? 0) + ($benzine80Data['received'] ?? 0) - ($benzine80Data['dispensed'] ?? 0), 0) }}</td>
                            <td>{{ number_format(($benzine95Data['opening_balance'] ?? 0) + ($benzine95Data['received'] ?? 0) - ($benzine95Data['dispensed'] ?? 0), 0) }}</td>
                        
                        </tr>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>

    <!-- Separate Summary Table -->
    <div class="card border-0 shadow-sm rounded-4 mt-4">
        <div class="card-body p-4">
            <h5 class="fw-bold text-center mb-4">ملخص الحركة اليومية</h5>
            <div class="table-responsive">
                <table class="table table-bordered text-center" style="font-size: 14px;">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="width: 20%;">البيان</th>
                            <th class="text-center">سولار</th>
                            <th class="text-center">بنزين ٩٢</th>
                            <th class="text-center">بنزين ٨٠</th>
                            <th class="text-center">بنزين ٩٥</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="fw-bold">مجموع الوارد</td>
                            <td>{{ number_format(($solarData['received'] ?? 0) + ($solarData['opening_balance'] ?? 0), 0) }}</td>
                            <td>{{ number_format(($benzine92Data['received'] ?? 0) + ($benzine92Data['opening_balance'] ?? 0), 0) }}</td>
                            <td>{{ number_format(($benzine80Data['received'] ?? 0) + ($benzine80Data['opening_balance'] ?? 0), 0) }}</td>
                            <td>{{ number_format(($benzine95Data['received'] ?? 0) + ($benzine95Data['opening_balance'] ?? 0), 0) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">مجموع المنصرف</td>
                            <td>{{ number_format($solarData['dispensed'] ?? 0, 0) }}</td>
                            <td>{{ number_format($benzine92Data['dispensed'] ?? 0, 0) }}</td>
                            <td>{{ number_format($benzine80Data['dispensed'] ?? 0, 0) }}</td>
                            <td>{{ number_format($benzine95Data['dispensed'] ?? 0, 0) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">الرصيد</td>
                            <td>{{ number_format(($solarData['opening_balance'] ?? 0) + ($solarData['received'] ?? 0) - ($solarData['dispensed'] ?? 0), 0) }}</td>
                            <td>{{ number_format(($benzine92Data['opening_balance'] ?? 0) + ($benzine92Data['received'] ?? 0) - ($benzine92Data['dispensed'] ?? 0), 0) }}</td>
                            <td>{{ number_format(($benzine80Data['opening_balance'] ?? 0) + ($benzine80Data['received'] ?? 0) - ($benzine80Data['dispensed'] ?? 0), 0) }}</td>
                            <td>{{ number_format(($benzine95Data['opening_balance'] ?? 0) + ($benzine95Data['received'] ?? 0) - ($benzine95Data['dispensed'] ?? 0), 0) }}</td>
                        </tr>
                        <tr>
                            <td class="fw-bold">الرصيد الفعلي نهاية اليوم</td>
                            <td>{{ number_format($solarData['actual_balance'] ?? 0, 0) }}</td>
                            <td>{{ number_format($benzine92Data['actual_balance'] ?? 0, 0) }}</td>
                            <td>{{ number_format($benzine80Data['actual_balance'] ?? 0, 0) }}</td>
                            <td>{{ number_format($benzine95Data['actual_balance'] ?? 0, 0) }}</td>
                        </tr>
                        <tr class="table-info fw-bold">
                            <td>العجز أو الزيادة</td>
                            <td class="{{ (($solarData['opening_balance'] ?? 0) + ($solarData['received'] ?? 0) - ($solarData['dispensed'] ?? 0) - ($solarData['actual_balance'] ?? 0)) < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(($solarData['opening_balance'] ?? 0) + ($solarData['received'] ?? 0) - ($solarData['dispensed'] ?? 0) - ($solarData['actual_balance'] ?? 0), 0) }}
                            </td>
                            <td class="{{ (($benzine92Data['opening_balance'] ?? 0) + ($benzine92Data['received'] ?? 0) - ($benzine92Data['dispensed'] ?? 0) - ($benzine92Data['actual_balance'] ?? 0)) < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(($benzine92Data['opening_balance'] ?? 0) + ($benzine92Data['received'] ?? 0) - ($benzine92Data['dispensed'] ?? 0) - ($benzine92Data['actual_balance'] ?? 0), 0) }}
                            </td>
                            <td class="{{ (($benzine95Data['opening_balance'] ?? 0) + ($benzine95Data['received'] ?? 0) - ($benzine95Data['dispensed'] ?? 0) - ($benzine95Data['actual_balance'] ?? 0)) < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(($benzine95Data['opening_balance'] ?? 0) + ($benzine95Data['received'] ?? 0) - ($benzine95Data['dispensed'] ?? 0) - ($benzine95Data['actual_balance'] ?? 0), 0) }}
                            </td>
                            <td class="{{ (($benzine80Data['opening_balance'] ?? 0) + ($benzine80Data['received'] ?? 0) - ($benzine80Data['dispensed'] ?? 0) - ($benzine80Data['actual_balance'] ?? 0)) < 0 ? 'text-danger' : 'text-success' }}">
                                {{ number_format(($benzine80Data['opening_balance'] ?? 0) + ($benzine80Data['received'] ?? 0) - ($benzine80Data['dispensed'] ?? 0) - ($benzine80Data['actual_balance'] ?? 0), 0) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>
@endsection

<style>
@media print {
    .d-flex.gap-2 {
        display: none !important;
    }
    
    form {
        display: none !important;
    }
    
    .card {
        page-break-inside: avoid;
    }
    
    .table {
        font-size: 12px;
    }
    
    .table th,
    .table td {
        padding: 4px !important;
        vertical-align: middle !important;
    }
}

.table th {
    background-color: #f8f9fa;
    font-weight: bold;
    font-size: 12px;
    vertical-align: middle;
}

.table td {
    vertical-align: middle;
}

.text-success {
    color: #198754 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.table-secondary {
    background-color: #e9ecef !important;
}

.table-info {
    background-color: #cff4fc !important;
}
</style>
                