<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>فاتورة</title>
    <style>
        body {
            font-family: 'DejaVu Sans', sans-serif;
            direction: rtl;
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: left;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <h1>فاتورة رقم: {{ $data['invoice_number'] }}</h1>
    <p>التاريخ: {{ $data['date'] }}</p>
    <p>اسم العميل: {{ $data['customer_name'] }}</p>

    <table>
        <thead>
            <tr>
                <th>المنتج</th>
                <th>الكمية</th>
                <th>السعر</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data['items'] as $item)
            <tr>
                <td>{{ $item['name'] }}</td>
                <td>{{ $item['quantity'] }}</td>
                <td>{{ $item['price'] }} ريال</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" class="total">الإجمالي:</td>
                <td>{{ $data['total'] }} ريال</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
