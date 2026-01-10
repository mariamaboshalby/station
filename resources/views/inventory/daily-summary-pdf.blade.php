<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>الجرد اليومي - {{ $date }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        h2, h3 { text-align: center; }
        .fw-bold { font-weight: bold; }
        .table-secondary { background-color: #e9ecef; }
        .table-warning { background-color: #fff3cd; }
        .table-info { background-color: #cff4fc; }
    </style>
</head>
<body>
    <h2>الجرد اليومي المفصل</h2>
    <p style="text-align: center;">التاريخ: {{ $date }}</p>
    
    <table>
        <thead>
            <tr>
                <th>البيان</th>
                <th>رقم الفاتورة</th>
                <th>سولار</th>
                <th>بنزين 92</th>
                <th>بنزين 80</th>
                <th>بنزين 95</th>
            </tr>
        </thead>
        <tbody>
            <tr class="table-secondary">
                <td class="fw-bold">الرصيد أول اليوم</td>
                <td>-</td>
                <td>{{ number_format($solarData['opening_balance'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine92Data['opening_balance'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine80Data['opening_balance'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine95Data['opening_balance'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td class="fw-bold">الوارد</td>
                <td>{{ $invoiceNumber ?? '-' }}</td>
                <td>{{ number_format($solarData['received'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine92Data['received'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine80Data['received'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine95Data['received'] ?? 0, 0) }}</td>
            </tr>
            <tr class="table-warning">
                <td class="fw-bold">البيعات</td>
                <td>{{ $dispensedInvoiceNumber ?? '-' }}</td>
                <td>{{ number_format($solarData['sales'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine92Data['sales'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine80Data['sales'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine95Data['sales'] ?? 0, 0) }}</td>
            </tr>
            <tr>
                <td class="fw-bold">المنصرف</td>
                <td>{{ $dispensedInvoiceNumber ?? '-' }}</td>
                <td>{{ number_format($solarData['dispensed'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine92Data['dispensed'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine80Data['dispensed'] ?? 0, 0) }}</td>
                <td>{{ number_format($benzine95Data['dispensed'] ?? 0, 0) }}</td>
            </tr>
            <tr class="table-info">
                <td class="fw-bold">الجملة</td>
                <td>-</td>
                <td>{{ number_format(($solarData['opening_balance'] ?? 0) + ($solarData['received'] ?? 0) - ($solarData['dispensed'] ?? 0), 0) }}</td>
                <td>{{ number_format(($benzine92Data['opening_balance'] ?? 0) + ($benzine92Data['received'] ?? 0) - ($benzine92Data['dispensed'] ?? 0), 0) }}</td>
                <td>{{ number_format(($benzine80Data['opening_balance'] ?? 0) + ($benzine80Data['received'] ?? 0) - ($benzine80Data['dispensed'] ?? 0), 0) }}</td>
                <td>{{ number_format(($benzine95Data['opening_balance'] ?? 0) + ($benzine95Data['received'] ?? 0) - ($benzine95Data['dispensed'] ?? 0), 0) }}</td>
            </tr>
        </tbody>
    </table>

    <h3>ملخص الحركة اليومية</h3>
    <table>
        <thead>
            <tr>
                <th>البيان</th>
                <th>سولار</th>
                <th>بنزين 92</th>
                <th>بنزين 80</th>
                <th>بنزين 95</th>
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
            <tr class="table-info">
                <td class="fw-bold">العجز أو الزيادة</td>
                <td>{{ number_format(($solarData['opening_balance'] ?? 0) + ($solarData['received'] ?? 0) - ($solarData['dispensed'] ?? 0) - ($solarData['actual_balance'] ?? 0), 0) }}</td>
                <td>{{ number_format(($benzine92Data['opening_balance'] ?? 0) + ($benzine92Data['received'] ?? 0) - ($benzine92Data['dispensed'] ?? 0) - ($benzine92Data['actual_balance'] ?? 0), 0) }}</td>
                <td>{{ number_format(($benzine80Data['opening_balance'] ?? 0) + ($benzine80Data['received'] ?? 0) - ($benzine80Data['dispensed'] ?? 0) - ($benzine80Data['actual_balance'] ?? 0), 0) }}</td>
                <td>{{ number_format(($benzine95Data['opening_balance'] ?? 0) + ($benzine95Data['received'] ?? 0) - ($benzine95Data['dispensed'] ?? 0) - ($benzine95Data['actual_balance'] ?? 0), 0) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
