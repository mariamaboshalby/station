<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>تقرير المصروفات</title>
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
    </style>
</head>

<body>
    <div class="header">
        <h2>تقرير المصروفات</h2>
        <p>من {{ $startDate }} إلى {{ $endDate }}</p>
    </div>

    <table class="summary-table">
        <tr>
            <td><strong>إجمالي المصروفات</strong><br>{{ number_format($totalExpenses, 2) }}</td>
            <td><strong>عدد العمليات</strong><br>{{ count($expenses) }}</td>
        </tr>
    </table>

    <h4 style="margin-bottom: 10px">تفاصيل بحسب الفئة:</h4>
    <ul>
        @foreach ($expensesByCategory as $cat => $amount)
            <li>{{ $cat }}: {{ number_format($amount, 2) }}</li>
        @endforeach
    </ul>

    <h4 style="margin-bottom: 10px">سجل المصروفات:</h4>
    <table class="details-table">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>الفئة</th>
                <th>البيان / الوصف</th>
                <th>المبلغ</th>
                <th>المسؤول</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($expenses as $exp)
                <tr>
                    <td>{{ $exp->transaction_date }}</td>
                    <td>{{ $exp->category }}</td>
                    <td>{{ $exp->description }}</td>
                    <td>{{ number_format($exp->amount, 2) }}</td>
                    <td>{{ $exp->user->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #777;">
        تم استخراج هذا التقرير بتاريخ {{ date('Y-m-d H:i') }}
    </div>
</body>

</html>
