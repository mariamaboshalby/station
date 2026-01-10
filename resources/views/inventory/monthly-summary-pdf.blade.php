<!DOCTYPE html>
<html dir="rtl" lang="ar">
<head>
    <meta charset="UTF-8">
    <title>الجرد الشهري - {{ $month }}</title>
    <style>
        body { font-family: 'DejaVu Sans', sans-serif; direction: rtl; }
        table { width: 100%; border-collapse: collapse; margin: 20px 0; }
        th, td { border: 1px solid #000; padding: 8px; text-align: center; }
        th { background-color: #f0f0f0; font-weight: bold; }
        h2 { text-align: center; }
        .fw-bold { font-weight: bold; }
        .table-info { background-color: #cff4fc; }
    </style>
</head>
<body>
    <h2>الجرد الشهري المجمل</h2>
    <p style="text-align: center;">الفترة: من {{ $startDate }} إلى {{ $endDate }}</p>
    
    <table>
        <thead>
            <tr>
                <th>البيان</th>
                <th>الرصيد</th>
                <th>الوارد</th>
                <th>الجملة</th>
                <th>المنصرف</th>
                <th>الباقي</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="fw-bold">سولار</td>
                <td>{{ number_format($solarData['balance'] ?? 0, 2) }}</td>
                <td>{{ number_format($solarData['received'] ?? 0, 2) }}</td>
                <td>{{ number_format(($solarData['balance'] ?? 0) + ($solarData['received'] ?? 0), 2) }}</td>
                <td>{{ number_format($solarData['dispensed'] ?? 0, 2) }}</td>
                <td>{{ number_format($solarData['actual_balance'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="fw-bold">بنزين 92</td>
                <td>{{ number_format($benzine92Data['balance'] ?? 0, 2) }}</td>
                <td>{{ number_format($benzine92Data['received'] ?? 0, 2) }}</td>
                <td>{{ number_format(($benzine92Data['balance'] ?? 0) + ($benzine92Data['received'] ?? 0), 2) }}</td>
                <td>{{ number_format($benzine92Data['dispensed'] ?? 0, 2) }}</td>
                <td>{{ number_format($benzine92Data['actual_balance'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="fw-bold">بنزين 80</td>
                <td>{{ number_format($benzine80Data['balance'] ?? 0, 2) }}</td>
                <td>{{ number_format($benzine80Data['received'] ?? 0, 2) }}</td>
                <td>{{ number_format(($benzine80Data['balance'] ?? 0) + ($benzine80Data['received'] ?? 0), 2) }}</td>
                <td>{{ number_format($benzine80Data['dispensed'] ?? 0, 2) }}</td>
                <td>{{ number_format($benzine80Data['actual_balance'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="fw-bold">بنزين 95</td>
                <td>{{ number_format($benzine95Data['balance'] ?? 0, 2) }}</td>
                <td>{{ number_format($benzine95Data['received'] ?? 0, 2) }}</td>
                <td>{{ number_format(($benzine95Data['balance'] ?? 0) + ($benzine95Data['received'] ?? 0), 2) }}</td>
                <td>{{ number_format($benzine95Data['dispensed'] ?? 0, 2) }}</td>
                <td>{{ number_format($benzine95Data['actual_balance'] ?? 0, 2) }}</td>
            </tr>
            <tr>
                <td class="fw-bold">زيوت معينة</td>
                <td>{{ number_format($oilsData['balance'] ?? 0, 2) }}</td>
                <td>{{ number_format($oilsData['received'] ?? 0, 2) }}</td>
                <td>{{ number_format(($oilsData['balance'] ?? 0) + ($oilsData['received'] ?? 0), 2) }}</td>
                <td>{{ number_format($oilsData['dispensed'] ?? 0, 2) }}</td>
                <td>{{ number_format($oilsData['actual_balance'] ?? 0, 2) }}</td>
            </tr>
            <tr class="table-info">
                <td class="fw-bold">الإجمالي</td>
                <td class="fw-bold">{{ number_format(($solarData['balance'] ?? 0) + ($benzine92Data['balance'] ?? 0) + ($benzine80Data['balance'] ?? 0) + ($benzine95Data['balance'] ?? 0) + ($oilsData['balance'] ?? 0), 2) }}</td>
                <td class="fw-bold">{{ number_format(($solarData['received'] ?? 0) + ($benzine92Data['received'] ?? 0) + ($benzine80Data['received'] ?? 0) + ($benzine95Data['received'] ?? 0) + ($oilsData['received'] ?? 0), 2) }}</td>
                <td class="fw-bold">{{ number_format((($solarData['balance'] ?? 0) + ($solarData['received'] ?? 0)) + (($benzine92Data['balance'] ?? 0) + ($benzine92Data['received'] ?? 0)) + (($benzine80Data['balance'] ?? 0) + ($benzine80Data['received'] ?? 0)) + (($benzine95Data['balance'] ?? 0) + ($benzine95Data['received'] ?? 0)) + (($oilsData['balance'] ?? 0) + ($oilsData['received'] ?? 0)), 2) }}</td>
                <td class="fw-bold">{{ number_format(($solarData['dispensed'] ?? 0) + ($benzine92Data['dispensed'] ?? 0) + ($benzine80Data['dispensed'] ?? 0) + ($benzine95Data['dispensed'] ?? 0) + ($oilsData['dispensed'] ?? 0), 2) }}</td>
                <td class="fw-bold">{{ number_format(($solarData['actual_balance'] ?? 0) + ($benzine92Data['actual_balance'] ?? 0) + ($benzine80Data['actual_balance'] ?? 0) + ($benzine95Data['actual_balance'] ?? 0) + ($oilsData['actual_balance'] ?? 0), 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
