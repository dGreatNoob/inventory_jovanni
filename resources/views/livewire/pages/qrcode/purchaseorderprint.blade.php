<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Print Purchase Order QR Code</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .label {
            width: 110mm;
            min-height: 60mm;
            box-sizing: border-box;
            border: 1px dashed #888;
            margin: 10mm auto;
            padding: 8mm 8mm 6mm 8mm;
            background: #fff;
        }
        .header {
            display: flex;
            align-items: center;
            gap: 18px;
            margin-bottom: 8px;
        }
        .qr {
            flex: 0 0 70px;
        }
        .qr img, .qr svg {
            width: 70px;
            height: 70px;
        }
        .po-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            font-size: 13px;
            line-height: 1.3;
        }
        .po-number {
            font-family: monospace;
            font-size: 16px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 2px;
        }
        .row {
            display: flex;
            flex-direction: row;
            gap: 8px;
            margin-bottom: 1px;
        }
        .label-title {
            color: #666;
            font-weight: 500;
        }
        .label-value {
            color: #222;
            font-weight: 500;
        }
        .section {
            margin-top: 8px;
            font-size: 12px;
        }
        .items-table {
            width: 100%;
            margin-top: 6px;
            border-collapse: collapse;
            font-size: 11px;
        }
        .items-table th,
        .items-table td {
            border: 1px solid #d1d5db;
            padding: 2px 4px;
            text-align: left;
        }
        .items-table th {
            background-color: #f3f4f6;
            font-weight: bold;
        }
        .print-button {
            margin-top: 1rem;
            padding: 0.5rem 1.2rem;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 0.5rem;
            font-size: 1rem;
            cursor: pointer;
        }
        .print-button:hover {
            background-color: #2563eb;
        }
        @media print {
            .print-button { display: none; }
            body { background: #fff; }
            .label { margin: 0; box-shadow: none; }
        }
    </style>
</head>
<body>
    <div class="label">
        <div class="header">
            <div class="qr">
                {!! QrCode::size(70)->generate($purchaseOrder->po_num) !!}
            </div>
            <div class="po-info">
                <div class="po-number">PO#: {{ $purchaseOrder->po_num }}</div>
                <div class="row"><span class="label-title">Supplier:</span><span class="label-value">{{ $purchaseOrder->supplier->name ?? 'N/A' }}</span></div>
                <div class="row"><span class="label-title">Status:</span><span class="label-value">{{ str_replace('_', ' ', ucfirst($purchaseOrder->status)) }}</span></div>
                <div class="row"><span class="label-title">Order Date:</span><span class="label-value">{{ $purchaseOrder->order_date ? $purchaseOrder->order_date->format('M d, Y') : 'N/A' }}</span></div>
                <div class="row"><span class="label-title">Delivery Date:</span><span class="label-value">{{ $purchaseOrder->del_on ? $purchaseOrder->del_on->format('M d, Y') : 'N/A' }}</span></div>
                <div class="row"><span class="label-title">Department:</span><span class="label-value">{{ $purchaseOrder->department->name ?? 'N/A' }}</span></div>
                <div class="row"><span class="label-title">Total Qty:</span><span class="label-value">{{ number_format($purchaseOrder->supplyOrders->sum('order_qty'), 2) }}</span></div>
                <div class="row"><span class="label-title">Total Price:</span><span class="label-value">₱{{ number_format($purchaseOrder->supplyOrders->sum('order_total_price'), 2) }}</span></div>
            </div>
        </div>
        @if($purchaseOrder->supplyOrders->count() > 0)
        <div class="section">
            <strong>Order Items</strong>
            <table class="items-table">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>Description</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($purchaseOrder->supplyOrders as $order)
                    <tr>
                        <td style="font-family: monospace;">{{ $order->supplyProfile->supply_sku ?? 'N/A' }}</td>
                        <td>{{ $order->supplyProfile->supply_description ?? 'N/A' }}</td>
                        <td>{{ number_format($order->order_qty, 2) }}</td>
                        <td>₱{{ number_format($order->unit_price, 2) }}</td>
                        <td style="font-weight: bold;">₱{{ number_format($order->order_total_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
        <button class="print-button" onclick="window.print()">🖨️ Print</button>
    </div>
</body>
</html> 