<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Jovanni | Purchase Order QR Code</title>
    <style>
        body {
            font-family: 'Inter', 'Arial', sans-serif;
            background-color: #f9fafb;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }

        .container {
            background: #ffffff;
            padding: 40px 50px;
            border-radius: 16px;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            text-align: center;
            width: 380px;
        }

        h1 {
            font-size: 22px;
            font-weight: 700;
            color: #1e3a8a;
            margin-bottom: 4px;
        }

        .subtitle {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .qr-code {
            margin: 0 auto 24px;
        }

        .po-number {
            font-size: 18px;
            font-weight: 700;
            color: #2563eb;
            margin-bottom: 4px;
        }

        .po-label {
            font-size: 13px;
            color: #6b7280;
            margin-bottom: 24px;
        }

        .details {
            text-align: left;
            font-size: 14px;
            color: #111827;
            background: #f9fafb;
            border-radius: 10px;
            padding: 18px 20px;
            line-height: 1.7;
        }

        .details p {
            margin: 4px 0;
        }

        .details span {
            font-weight: 600;
            color: #1f2937;
            display: inline-block;
            width: 110px;
        }

        .footer {
            font-size: 12px;
            color: #9ca3af;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Jovanni</h1>
        <div class="subtitle">Purchase Order QR Code</div>

        <div class="qr-code">
            {!! QrCode::size(230)->generate($purchaseOrder->po_num) !!}
        </div>

        <div class="po-number">
            {{ $purchaseOrder->po_num }}
        </div>
        <div class="po-label">Purchase Order Number</div>

        <div class="details">
            <p><span>Status:</span> {{ ucfirst($purchaseOrder->status) }}</p>
            <p><span>Supplier:</span> {{ $purchaseOrder->supplier?->name ?? 'N/A' }}</p>
            <p><span>Department:</span> {{ $purchaseOrder->department?->name ?? 'N/A' }}</p>
            <p><span>Order Date:</span> {{ \Carbon\Carbon::parse($purchaseOrder->order_date)->format('M d, Y') }}</p>
            <p><span>Total Quantity:</span> {{ number_format($purchaseOrder->total_qty, 2) }}</p>
            <p><span>Total Price:</span> ₱{{ number_format($purchaseOrder->total_price, 2) }}</p>
        </div>

        <div class="footer">
            Generated on {{ now()->format('M d, Y \a\t h:i A') }} | Jovanni Inventory System
        </div>
    </div>
</body>
</html>