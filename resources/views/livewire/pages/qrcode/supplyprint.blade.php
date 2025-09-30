<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Print QR Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            text-align: center;
        }

        .qr {
            margin: 2rem auto;
        }

        .info {
            font-size: 1rem;
            margin: 0.5rem 0;
        }

        button {
            margin-top: 2rem;
            padding: 0.5rem 1rem;
            font-size: 1rem;
        }

        @media print {
            button {
                display: none;
            }
        }
    </style>
</head>

<body>
    <div class="qr">
        {!! QrCode::size(200)->generate($supply->supply_sku) !!}
    </div>

    <h2>QR Code Details</h2>
    <div class="info"><strong>Description:</strong> {{ $supply->supply_description }}</div>
    <div class="info"><strong>Item Type:</strong> {{ $supply->itemType->name }}</div>
    <div class="info"><strong>Class:</strong> {{ $supply->supply_item_class }}</div>
    <div class="info"><strong>Allocation:</strong> {{ $supply->allocation->name }}</div>

    <button onclick="window.print()">🖨️ Print</button>
</body>

</html>
