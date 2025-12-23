<!DOCTYPE html>
<html dir="rtl" lang="ar">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>تقرير الإيرادات</title>
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
        <h2>تقرير الإيرادات</h2>
        <p>من {{ $startDate }} إلى {{ $endDate }}</p>
    </div>

    <table class="summary-table">
        <tr>
            <td><strong>إجمالي الإيرادات</strong><br>{{ number_format($totalRevenue, 2) }}</td>
            <td><strong>نقدي</strong><br>{{ number_format($cashRevenue, 2) }}</td>
            <td><strong>آجل</strong><br>{{ number_format($creditRevenue, 2) }}</td>
        </tr>
    </table>

    <table class="details-table">
        <thead>
            <tr>
                <th>التاريخ</th>
                <th>العميل</th>
                <th>نوع الوقود</th>
                <th>لترات</th>
                <th>سعر اللتر</th>
                <th>الإجمالي</th>
                <th>الموظف</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($revenues as $trx)
                <tr>
                    <td>{{ $trx->created_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $trx->client->name ?? 'نقدي' }}</td>
                    <td>{{ $trx->nozzle->pump->tank->fuel->name ?? '-' }}</td>
                    <td>{{ $trx->cash_liters + $trx->credit_liters }}</td>
                    <td>{{ $trx->nozzle->pump->tank->fuel->price_per_liter ?? '-' }}</td>
                    <td>{{ number_format($trx->total_amount, 2) }}</td>
                    <td>{{ $trx->shift->user->name ?? '-' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: center; font-size: 10px; color: #777;">
        تم استخراج هذا التقرير بتاريخ {{ date('Y-m-d H:i') }}
    </div>
</body>

</html>
