<!DOCTYPE html>
<html>
<head>
    <title>Inventory Report</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 14px; color: #333; }
        h1 { color: #dc2626; border-bottom: 2px solid #dc2626; padding-bottom: 5px; }
        h2 { color: #4b5563; margin-top: 30px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f9fafb; font-weight: bold; }
        .text-right { text-align: right; }
        .text-red { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <h1>System Analytics & Inventory Report</h1>
    <p><strong>Generated on:</strong> {{ \Carbon\Carbon::now()->format('F j, Y, g:i a') }}</p>
    <p><strong>Timeframe:</strong> {{ $timeframe === 'all' ? 'All Time' : 'Current Month' }}</p>

    <h2>Consumption by Department</h2>
    <table>
        <thead>
            <tr><th>Department</th><th class="text-right">Items Fulfilled</th></tr>
        </thead>
        <tbody>
            @foreach($deptUsage as $usage)
            <tr>
                <td>{{ $usage->name }}</td>
                <td class="text-right">{{ $usage->total_qty }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h2>Low Stock Alerts</h2>
    <table>
        <thead>
            <tr><th>Consumable Item</th><th>Current Stock</th><th>Threshold</th></tr>
        </thead>
        <tbody>
            @forelse($lowStock as $inv)
            <tr>
                <td>{{ $inv->consumableType->name }}</td>
                <td class="text-red">{{ $inv->stock_level }}</td>
                <td>{{ $inv->threshold }}</td>
            </tr>
            @empty
            <tr><td colspan="3" style="text-align:center;">All inventory levels are healthy!</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
