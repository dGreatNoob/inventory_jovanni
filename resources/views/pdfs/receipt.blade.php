<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Receipt - {{ $branch->name }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            margin: 20px;
            line-height: 1.3;
        }
        
        .header {
            margin-bottom: 20px;
        }
        
        .header-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
        }
        
        .delivery-info {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            text-align: right;
        }
        
        .header-line {
            margin: 5px 0;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .text-center {
            text-align: center;
        }
        
        .text-right {
            text-align: right;
        }
        
        .totals {
            margin-top: 30px;
        }
        
        .totals-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        
        .footer {
            margin-top: 50px;
            border-top: 1px solid #000;
            padding-top: 20px;
        }
        
        .footer-row {
            display: flex;
            justify-content: space-between;
            margin: 20px 0;
        }
        
        .powered-by {
            text-align: right;
            font-style: italic;
            color: #666;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-info">
            <div class="header-line"><strong>Brand :</strong> Jovanni Bags</div>
            <div class="header-line"><strong>Vendor Code:</strong> 104148</div>
            <div class="header-line"><strong>Dept Code:</strong> 041072007</div>
            <div class="header-line"><strong>Date:</strong> {{ $generated_date }}</div>
        </div>
        
        <div class="delivery-info">
            <div class="header-line"><strong>Delivered To:</strong> {{ $branch->name }}</div>
            <div class="header-line"><strong>Series #:</strong> {{ $batch->ref_no }}</div>
            <div class="header-line"><strong>Ship Via:</strong> {{ $branch->name }}</div>
            <div class="header-line"><strong>Address:</strong> {{ $branch->address }}</div>
        </div>
    </div>
    
    <table>
        <thead>
            <tr>
                <th style="width: 60%;">Product Description</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 15%;">Unit Price</th>
                <th style="width: 15%;">Total Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            <tr>
                <td>{{ $item['product_description'] }}</td>
                <td class="text-center">{{ $item['quantity'] }}</td>
                <td class="text-right">{{ number_format($item['unit_price'], 2) }}</td>
                <td class="text-right">{{ number_format($item['total_price'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="totals">
        <div class="totals-row">
            <div style="width: 60%;"><strong>Total item/s:</strong> {{ $total_items }}</div>
            <div style="width: 40%;"><strong>Total Quantity:</strong> {{ $total_quantity }}</div>
            <div style="width: 40%;"><strong>Total Price:</strong> {{ number_format($total_price, 2) }}</div>
        </div>
    </div>
    
    <div class="footer">
        <div class="footer-row">
            <div><strong>NO. OF BOX(ES):</strong> __________________</div>
            <div><strong>RECEIVED BY:</strong> ___________________________</div>
            <div><strong>DATE:</strong> ___________</div>
        </div>
        <div class="footer-row">
            <div><strong>RUN DATE:</strong> {{ $generated_date }} {{ $generated_time }}</div>
        </div>
    </div>
</body>
</html>