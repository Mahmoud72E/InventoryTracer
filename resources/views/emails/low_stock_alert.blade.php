<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Low Stock Alert</title>
</head>
<body>
    <h2>Low Stock Alert 🚨</h2>

    <p>
        The ingredient <strong>{{ $ingredientName }}</strong> has dropped below
        <strong>50%</strong> of its initial stock.
    </p>

    <p>
        <strong>Initial Stock:</strong> {{ $initialStock }} g<br>
        <strong>Current Stock:</strong> {{ $currentStock }} g
    </p>

    <p>Please restock as soon as possible.</p>
</body>
</html>
