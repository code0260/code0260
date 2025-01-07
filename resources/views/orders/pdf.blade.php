<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 20px;
            background-color: #f7f7f7;
            color: #333;
        }
        h1, h3 {
            color: #444;
            font-weight: bold;
        }
        h1 {
            font-size: 32px;
            border-bottom: 2px solid #444;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        h3 {
            font-size: 24px;
            margin-top: 30px;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            line-height: 1.5;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            background-color: #fff;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px 15px;
            text-align: left;
        }
        th {
            background-color: #f0f0f0;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .specification {
            margin: 15px 0;
        }
        .specifications-section {
            background-color: #f9f9f9;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .specifications-section strong {
            color: #333;
        }
        .spec-images {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            justify-content: start;
        }
        .spec-images img {
            width: 200px;  /* زيادة العرض */
            height: 200px; /* زيادة الارتفاع */
            object-fit: cover; /* حفظ جودة الصورة */
            border: 2px solid #ddd;
            border-radius: 8px;
            transition: transform 0.3s ease;
        }
        .spec-images img:hover {
            transform: scale(1.1); /* تأثير التكبير عند المرور */
        }
        .totals-table {
            background-color: #fff;
            padding: 15px;
            border-radius: 8px;
        }
        .totals-table th {
            background-color: #f0f0f0;
        }
        .totals-table td {
            text-align: right;
            font-weight: bold;
        }
        .totals-table .total {
            font-size: 18px;
            color: #555;
        }
    </style>
    
</head>
<body>

    <h1>Order Details</h1>

    <h3>Order Number: {{$order->id}}</h3>
    <p>Date: {{$order->created_at}}</p>
    <p>Total: ${{$order->subtotal}}</p>

    <h3>Order Information</h3>
    <table>
        <thead>
            <tr>
                <th>PRODUCT</th>
                <th>SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($order->orderItems as $item)
            <tr>
                <td>{{$item->product->name}} x {{$item->quantity}}</td>
                <td class="text-right">${{$item->price}}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Order Details (Including Specifications)</h3>
    <div class="specifications-section">
        @foreach ($order->orderItems as $item)
        <div class="specification">
            <strong>{{ $item->product->name }}:</strong>
            @foreach ($item->product->specifications as $spec)
            <div>
                <p><strong>{{ $spec->name }}:</strong> {{ $spec->title ?? 'No title' }}</p>

                <!-- Display paragraphs -->
                @if (!empty($spec['paragraphs']))
                <p>{!! $spec['paragraphs'] !!}</p>
            @endif
                <!-- عرض الصور -->
                @if (!empty($spec->images))
                <div class="spec-images">
                    @foreach (json_decode($spec->images) as $image)
                    @php
                        $base64Image = call_user_func($base64EncodeImage, public_path('uploads/products/specifications/' . $image));
                    @endphp
                    @if ($base64Image)
                        <img src="{{ $base64Image }}" alt="spec image">
                    @else
                        <p>Image not found: {{$image}}</p>
                    @endif
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @endforeach
    </div>

    <h3>Totals</h3>
    <div class="totals-table">
        <table>
            <tbody>
                <tr>
                    <th>SUBTOTAL</th>
                    <td>${{$order->subtotal}}</td>
                </tr>
            </tbody>
        </table>
    </div>

</body>
</html>