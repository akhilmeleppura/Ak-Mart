<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Trial Balance PDF</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }
        h4 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        th, td {
            border: 1px solid #000;
            padding: 6px;
            text-align: center;
        }
        thead th {
            background-color: #002F6C;
            color: #fff;
        }
        tfoot td {
            background-color: #002F6C;
            color: #fff;
            font-weight: bold;
        }
        a {
            text-decoration: none;
            color: inherit;
        }
    </style>
</head>
<body>
    <h4>Trial Balance</h4>
    <p><strong>Date Range:</strong> {{ $startDate }} to {{ $endDate }}</p>
    <p><strong>Generated On:</strong> {{ now()->format('D d, Y') }}</p>

    <table>
        <thead>
            <tr>
                <th rowspan="2">Account</th>
                <th colspan="2">Opening</th>
                <th colspan="2">Transaction</th>
                <th colspan="2">Closing</th>
            </tr>
            <tr>
                <th>Debit (SAR)</th>
                <th>Credit (SAR)</th>
                <th>Debit (SAR)</th>
                <th>Credit (SAR)</th>
                <th>Debit (SAR)</th>
                <th>Credit (SAR)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalOpeningDebit = $totalOpeningCredit = 0;
                $totalTransactionDebit = $totalTransactionCredit = 0;
                $totalClosingDebit = $totalClosingCredit = 0;
            @endphp

            @foreach($trialBalanceData as $row)
                <tr>
                    <td>{{ $row['account_name'] }}</td>
                    <td>{{ number_format($row['opening_debit'], 2) }}</td>
                    <td>{{ number_format($row['opening_credit'], 2) }}</td>
                    <td>{{ number_format($row['transaction_debit'], 2) }}</td>
                    <td>{{ number_format($row['transaction_credit'], 2) }}</td>
                    <td>{{ number_format($row['closing_debit'], 2) }}</td>
                    <td>{{ number_format($row['closing_credit'], 2) }}</td>
                </tr>
                @php
                    $totalOpeningDebit += $row['opening_debit'];
                    $totalOpeningCredit += $row['opening_credit'];
                    $totalTransactionDebit += $row['transaction_debit'];
                    $totalTransactionCredit += $row['transaction_credit'];
                    $totalClosingDebit += $row['closing_debit'];
                    $totalClosingCredit += $row['closing_credit'];
                @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td>Total</td>
                <td>{{ number_format($totalOpeningDebit, 2) }}</td>
                <td>{{ number_format($totalOpeningCredit, 2) }}</td>
                <td>{{ number_format($totalTransactionDebit, 2) }}</td>
                <td>{{ number_format($totalTransactionCredit, 2) }}</td>
                <td>{{ number_format($totalClosingDebit, 2) }}</td>
                <td>{{ number_format($totalClosingCredit, 2) }}</td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
