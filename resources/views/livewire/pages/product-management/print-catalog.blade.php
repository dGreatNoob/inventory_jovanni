<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Catalog</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; margin: 0; padding: 8mm; color: #111827; }
        .header { display: flex; justify-content: space-between; align-items: baseline; margin-bottom: 6mm; }
        .title { font-size: 16pt; font-weight: 700; }
        .print { font-size: 10pt; color: #2563eb; text-decoration: none; }

        /* Tight 5x5 grid */
        .grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 3mm; }
        .card { page-break-inside: avoid; height: 52mm; }
        .img { width: 100%; height: 38mm; object-fit: cover; background: #f3f4f6; }
        .name { margin-top: 1mm; font-size: 9pt; font-weight: 700; text-transform: uppercase; line-height: 1.1; height: 8mm; overflow: hidden; }
        .price { margin-top: 0.5mm; font-size: 10pt; font-weight: 800; }

        @page { size: A4; margin: 12mm; }
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="header no-print">
        <div class="title">Product Catalog</div>
        <a href="#" class="print" onclick="window.print();return false;">Print</a>
    </div>

    <div class="grid">
        @foreach($products as $product)
            @php $cover = $product->images->first(); @endphp
            <div class="card">
                @if($cover)
                    <img class="img" src="{{ asset('storage/photos/' . $cover->filename) }}" alt="{{ $product->name }}">
                @else
                    <div class="img"></div>
                @endif
                <div class="name">{{ $product->name }}</div>
                <div class="price">₱{{ number_format($product->price, 2) }}</div>
            </div>
        @endforeach
    </div>
    <script>
        window.addEventListener('load', function () {
            // Give a tiny delay to ensure images render sharp before printing
            setTimeout(function () {
                window.print();
            }, 150);
            // After print, go back to previous page
            function goBack() {
                if (window.history.length > 1) {
                    window.history.back();
                }
            }
            if ('onafterprint' in window) {
                window.onafterprint = goBack;
            } else {
                // Fallback for older browsers
                setTimeout(goBack, 1000);
            }
        });
    </script>
</body>
</html>


