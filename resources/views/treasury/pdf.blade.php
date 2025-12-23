<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>تقرير الخزنة</title>
    <style>
        body {
            font-family: sans-serif;
            direction: rtl;
            text-align: right;
            padding: 20px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
        }

        .summary-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }

        .summary-table td {
            padding: 10px;
            border: 1px solid #eee;
            text-align: center;
            background: #f9f9f9;
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
        }

        .text-success {
            color: #28a745;
        }

        .text-danger {
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="header">
        <h2>{{ $viewAll ? 'سجل جميع حركات الخزنة' : 'تقرير الخزنة ليوم ' . $date }}</h2>
    </div>

    <!-- ملخص -->
    <table class="summary-table">
        <tr>
            <td>
                <strong>الرصيد الافتتاحي</strong><br>
                {{ number_format($openingBalance, 2) }}
            </td>
            <td>
                <span class="text-success"><strong>إجمالي الإيرادات</strong></span><br>
                {{ number_format($todayIncome, 2) }}
            </td>
            <td>
                <span class="text-danger"><strong>إجمالي المصروفات</strong></span><br>
                {{ number_format($todayExpense, 2) }}
            </td>
            <td>
                <strong>الرصيد الحالي</strong><br>
                {{ number_format($currentBalance, 2) }}
            </td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>التاريخ والوقت</th>
                <th>النوع</th>
                <th>البند</th>
                <th>الوصف</th>
                <th>المستخدم</th>
                <th>المبلغ</th>
                <th>المصدر</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($allTransactions as $trx)
                <tr>
                    <td>{{ is_string($trx['date']) ? $trx['date'] : $trx['date']->format('Y-m-d H:i') }}</td>
                    <td>
                        @if ($trx['type'] == 'income')
                            <span class="text-success">إيراد</span>
                        @else
                            <span class="text-danger">مصروف</span>
                        @endif
                    </td>
                    <td>{{ $trx['category'] }}</td>
                    <td>{{ $trx['description'] }}</td>
                    <td>{{ $trx['user'] }}</td>
                    <td style="font-weight: bold;">{{ number_format($trx['amount'], 2) }}</td>
                    <td>
                        @if ($trx['source'] == 'sales')
                            <span style="color: #17a2b8;">مبيعات</span>
                        @else
                            <span>خزنة</span>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #777;">
        تم استخراج هذا التقرير بتاريخ {{ date('Y-m-d H:i') }}
    </div>
</body>

</html>
