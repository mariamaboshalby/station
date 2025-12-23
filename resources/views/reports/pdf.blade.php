<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>التقرير المالي</title>
    <style>
        body {
            font-family: sans-serif;
            /* Best for Arabic in DomPDF */
            direction: rtl;
            text-align: right;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .header h1 {
            color: #333;
            margin: 0;
            font-size: 24px;
        }

        .header p {
            color: #666;
            margin: 5px 0 0;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 15px;
            border: 1px solid #eee;
            background: #f9f9f9;
            text-align: center;
        }

        .summary-label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 5px;
        }

        .summary-value {
            font-size: 18px;
            font-weight: bold;
            color: #000;
        }

        .details-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }

        .details-table th,
        .details-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
        }

        .details-table th {
            background-color: #f2f2f2;
            color: #333;
            font-weight: bold;
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }

        .text-right {
            text-align: right !important;
        }

        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 10px;
            color: #777;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1>التقرير المالي</h1>
        <p>الفترة من {{ $startDate }} إلى {{ $endDate }}</p>
    </div>

    <!-- Summary -->
    <table class="summary-table">
        <tr>
            <td>
                <span class="summary-label">إجمالي الإيرادات</span>
                <span class="summary-value text-success">{{ number_format($totalRevenue, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">إجمالي المصروفات</span>
                <span class="summary-value text-danger">{{ number_format($totalExpense, 2) }}</span>
            </td>
            <td>
                <span class="summary-label">صافي الربح</span>
                <span class="summary-value" style="color: {{ $netProfit >= 0 ? '#28a745' : '#dc3545' }}">
                    {{ number_format($netProfit, 2) }}
                </span>
            </td>
        </tr>
    </table>

    <!-- Details -->
    <h3 style="margin-bottom: 10px; color: #333;">تفاصيل المعاملات</h3>
    <table class="details-table">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>النوع</th>
                <th>الفئة</th>
                <th class="text-right">البيان</th>
                <th>إيراد</th>
                <th>مصروف</th>
                <th>الرصيد</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($reportData as $row)
                <tr>
                    <td>{{ $row['date']->format('Y-m-d') }}<br><small>{{ $row['date']->format('H:i') }}</small></td>
                    <td>
                        @if ($row['is_revenue'])
                            <span class="text-success">إيراد</span>
                        @else
                            <span class="text-danger">مصروف</span>
                        @endif
                    </td>
                    <td>{{ $row['type'] }}</td>
                    <td class="text-right">{{ $row['description'] }}</td>
                    <td>
                        @if ($row['is_revenue'])
                            {{ number_format($row['amount'], 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td>
                        @if (!$row['is_revenue'])
                            {{ number_format($row['amount'], 2) }}
                        @else
                            -
                        @endif
                    </td>
                    <td style="font-weight: bold;">{{ number_format($row['balance'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>تم استخراج هذا التقرير بتاريخ {{ date('Y-m-d H:i') }}</p>
    </div>
</body>

</html>
