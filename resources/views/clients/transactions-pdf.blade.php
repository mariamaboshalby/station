<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>كشف حساب</title>
    <style>
        * {
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
            margin: 10px;
            font-size: 12px;
        }

        h2 {
            text-align: center;
            margin: 5px 0;
            font-size: 14px;
        }

        .info {
            text-align: center;
            margin: 5px 0;
            font-size: 10px;
        }

        .info p {
            margin: 2px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }

        th,
        td {
            border: 1px solid #000;
            padding: 3px 4px;
            text-align: center;
            font-size: 10px;
        }

        th {
            background-color: #ccc;
            font-weight: bold;
        }

        tfoot th {
            font-weight: bold;
        }

        img {
            max-width: 100%;
            height: auto;
            display: block;
            margin: 5px 0;
        }
    </style>
</head>

<body>
    <h2>كشف حساب العميل</h2>
    <div class="info">
        <p>الاسم: {{ $client->name }}</p>
        <p>التاريخ: {{ now()->format('Y-m-d') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 12%;">التاريخ</th>
                <th style="width: 12%;">الموظف</th>
                <th style="width: 12%;">العربية</th>
                <th style="width: 12%;">المسدس</th>
                <th style="width: 10%;">اللترات</th>
                <th style="width: 10%;">السعر</th>
                <th style="width: 12%;">الاجمالي</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($transactions as $r)
                <tr>
                    <td>{{ $r->created_at->format('Y-m-d') }}</td>
                    <td>{{ $r->shift->user->name ?? '-' }}</td>
                    <td>{{ $r->transaction->vehicle_number ?? '-' }}</td>
                    <td>{{ $r->transaction->nozzle->pump->tank->fuel->name ?? '' }}
                        <br> تانك: {{ $r->transaction->nozzle->pump->tank->name ?? '' }}
                        <br>{{ $r->transaction->nozzle->pump->name ?? '' }}
                        <br>{{ $r->transaction->nozzle->name ?? '' }}
                    </td>
                    <td>{{ $r->liters }}</td>
                    <td>{{ $r->price_per_liter }}</td>
                    <td>{{ $r->total_amount }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="6">الاجمالي</th>
                <th>{{ $transactions->sum('total_amount') }}</th>
            </tr>
        </tfoot>
    </table>
</body>

</html>
