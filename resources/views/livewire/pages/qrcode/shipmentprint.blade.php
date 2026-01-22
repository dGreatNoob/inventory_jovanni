<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shipment QR Code - {{ $shipment->shipping_plan_num }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
            background: #f8f9fa;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }
        .header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 16px;
        }
        .content {
            padding: 40px;
        }
        .qr-section {
            text-align: center;
            margin-bottom: 40px;
        }
        .qr-code {
            display: inline-block;
            padding: 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .qr-code svg {
            width: 200px;
            height: 200px;
        }
        .qr-text {
            font-family: 'Courier New', monospace;
            font-size: 18px;
            font-weight: bold;
            color: #333;
            letter-spacing: 1px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }
        .detail-card {
            background: #f8f9fa;
            border-radius: 8px;
            padding: 25px;
            border-left: 4px solid #667eea;
        }
        .detail-card h3 {
            margin: 0 0 15px 0;
            color: #667eea;
            font-size: 18px;
            font-weight: 600;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            padding-bottom: 8px;
            border-bottom: 1px solid #e9ecef;
        }
        .detail-row:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .detail-label {
            font-weight: 500;
            color: #666;
        }
        .detail-value {
            font-weight: 600;
            color: #333;
        }
        .items-section {
            margin-top: 40px;
        }
        .items-section h3 {
            color: #667eea;
            font-size: 20px;
            margin-bottom: 20px;
            border-bottom: 2px solid #e9ecef;
            padding-bottom: 10px;
        }
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        .items-table th,
        .items-table td {
            padding: 15px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
        }
        .items-table th {
            background: #667eea;
            color: white;
            font-weight: 600;
            font-size: 14px;
        }
        .items-table tbody tr:hover {
            background: #f8f9fa;
        }
        .items-table .total-row {
            background: #e3f2fd;
            font-weight: bold;
        }
        .print-btn {
            display: block;
            width: 200px;
            margin: 40px auto 0;
            padding: 15px 30px;
            background: #667eea;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        .print-btn:hover {
            background: #5a67d8;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }
        @media print {
            body {
                background: white !important;
                padding: 0 !important;
            }
            .container {
                box-shadow: none !important;
                border-radius: 0 !important;
            }
            .print-btn {
                display: none !important;
            }
        }
        @media (max-width: 768px) {
            .content {
                padding: 20px;
            }
            .details-grid {
                grid-template-columns: 1fr;
            }
            .qr-code svg {
                width: 150px;
                height: 150px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Shipment QR Code</h1>
            <p>{{ $shipment->shipping_plan_num }}</p>
        </div>

        <div class="content">
            <div class="qr-section">
                <div class="qr-code">
                    {!! QrCode::size(200)->generate($shipment->shipping_plan_num) !!}
                </div>
                <div class="qr-text">{{ $shipment->shipping_plan_num }}</div>
            </div>

            <div class="details-grid">
                <div class="detail-card">
                    <h3>Shipment Information</h3>
                    <div class="detail-row">
                        <span class="detail-label">Reference:</span>
                        <span class="detail-value">{{ $shipment->shipping_plan_num }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Status:</span>
                        <span class="detail-value">{{ str_replace('_', ' ', ucfirst($shipment->shipping_status)) }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Ship Date:</span>
                        <span class="detail-value">{{ $shipment->scheduled_ship_date ? \Carbon\Carbon::parse($shipment->scheduled_ship_date)->format('M d, Y') : 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Delivery Method:</span>
                        <span class="detail-value">{{ $shipment->delivery_method ?? 'N/A' }}</span>
                    </div>
                </div>

                <!-- <div class="detail-card">
                    <h3>Customer Information</h3>
                    <div class="detail-row">
                        <span class="detail-label">Name:</span>
                        <span class="detail-value">{{ $shipment->customer_name ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Phone:</span>
                        <span class="detail-value">{{ $shipment->customer_phone ?? 'N/A' }}</span>
                    </div>
                    <div class="detail-row">
                        <span class="detail-label">Address:</span>
                        <span class="detail-value">{{ $shipment->customer_address ?? 'N/A' }}</span>
                    </div>
                </div> -->
            </div>

            @if($shipment->salesOrder && $shipment->salesOrder->items->count() > 0)
            <div class="items-section">
                <h3>Order Items</h3>
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>SKU</th>
                            <th>Description</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($shipment->salesOrder->items as $order)
                        <tr>
                            <td style="font-family: monospace; font-weight: 500;">{{ $order->product->supply_sku ?? 'N/A' }}</td>
                            <td>{{ $order->product->supply_description ?? 'N/A' }}</td>
                            <td>{{ number_format($order->quantity, 2) }}</td>
                            <td>₱{{ number_format($order->unit_price, 2) }}</td>
                            <td style="font-weight: bold;">₱{{ number_format($order->quantity * $order->unit_price, 2) }}</td>
                        </tr>
                        @endforeach
                        <tr class="total-row">
                            <td colspan="4" style="text-align: right; font-weight: bold;">Total Amount:</td>
                            <td style="font-weight: bold;">₱{{ number_format($shipment->salesOrder->items->sum('subtotal'), 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            @endif

            <button class="print-btn" onclick="window.print()">🖨️ Print QR Code</button>
        </div>
    </div>
</body>
</html>