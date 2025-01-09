<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Offer for Flatpack Container</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; background-color: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2 { color: #333; }
        .container { width: 80%; margin: 20px auto; }
        .footer { font-size: 0.9em; text-align: center; margin-top: 40px; }
        .attachments { margin-top: 20px; }
        .page-break { page-break-before: always; }
        .content { margin-bottom: 50px; }
    </style>
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
                <div class="container">
                    <h1>Price Offer for Flatpack Container</h1>
                    <p><strong>SUBJECT:</strong> Price offer for Flatpack Container</p>
                    <p><strong>REF:</strong> {{ $orderItems->first()->product->slug }}</p>
                    <p><strong>Date:</strong> {{ $order->created_at->format('d/m/Y') }}</p>
 
  <!-- Display Extras -->
  @if (!empty($order->extra))
  <div class="order-extras">
      <ul>
          <li>
              {!! $order->extra !!}
          </li>
      </ul>
  </div>
@endif 
                    <p>Best Regards.</p>
                    <p><strong>{{ Auth::user()->name }}</strong><br>Branch Manager</p>

                    <h2>Attachments:</h2>
                    <ul>
                        <li>Attachment-1: Price Offer</li>
                        <li>Attachment-2: Technical Specification</li>
                        <li>Attachment-3: Technical Drawing or Image</li>
                    </ul>

                    <div class="page-break"></div>

                    <h2>Attachment-1: Price Offer</h2>
                    <table>
                        <thead>
                            <tr>
                                <th>Product Description</th>
                                <th>Quantity</th>

                                 <th>Unit Price
                                    (USD)
                                    </th>
                                <th>Total Amount
                                    (USD)
                                    </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $cartItem)
                                <tr>
                                    <td>{{ $cartItem->options->description ?? 'No description available' }}</td>

                                    <td>{{ $cartItem->qty }}</td>
                                    <td>{{ $cartItem->price }}</td>

                                    <td>{{ $cartItem->subtotal }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    

                    <div class="page-break"></div>

                    <h2>General Information</h2>
                    <p><strong>Validity:</strong> Offer is valid only for Seven days. All conditions will be revised accordingly after the date is expired.</p>
                    <p><strong>Pricing:</strong> Given offer is EXW - Istanbul according to Incoterms 2000.</p>

                    <h2>Bank Details:</h2>
                    <p><strong>Account holder:</strong> PREFABEX YAPI TEKNOLOJILERI INS SAN VE TIC LTD STI</p>
                    <p><strong>Bank Name:</strong> ALBARAKA TURK</p>
                    <p><strong>USD IBAN:</strong> TR72 0020 3000 0370 7695 0000 02</p>
                    <p><strong>SWIFT CODE:</strong> BTFHTRISXXX</p>
                </div>
            </body>
</html>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Price Offer for Flatpack Container</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; margin: 0; padding: 0; background-color: #f4f4f4; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: #fff; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        h1, h2 { color: #333; }
        .container { width: 80%; margin: 20px auto; }
        .footer { font-size: 0.9em; text-align: center; margin-top: 40px; }
        .attachments { margin-top: 20px; }
        .page-break { page-break-before: always; }
        .content { margin-bottom: 50px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>General Contract Conditions</h1>
        <h2>1. Payment</h2>
        <p>50% advanced payment, 50% before loading.</p>

        <h2>2. Production</h2>
        <ul>
            <li>Production will be completed within 1/3/1900 Month/s since receiving the down payment and order confirmation.</li>
            <li>Production starting date is considered as of the date that the COMPANY receives the advance payment from the CUSTOMER.</li>
            <li>The order becomes definite with the payment done by the CUSTOMER to the COMPANY.</li>
            <li>Delays caused by force majeure such as earthquake, flood, fire and other natural disasters, mobilization, strikes, lockouts, accident or theft during transportation or installation, delays caused by suppliers of raw materials will be added to the deadline.</li>
        </ul>

        <h2>3. Assembling</h2>
        <ul>
            <li>Assembling is not included in our price offer.</li>
            <li>Upon customer request, Prefabex can send a few technicians.</li>
            <li>Upon customer request, Prefabex can send a few semi-skilled workers to help the assembling team.</li>
            <li>Customer will pay for technicians/workers flight tickets, accommodation food, transportation and daily fees of 200 USD per technician per day.</li>
            <li>Assembling is expected to be completed within 0 ###.</li>
        </ul>

        <h2>4. Customer’s Responsibilities</h2>
        <ul>
            <li>Heating and cooling systems</li>
            <li>Water heaters and boilers</li>
            <li>Outer connections for electricity and plumbing</li>
            <li>Obtaining all legal permits</li>
            <li>Customs clearance and taxes in country of destination</li>
            <li>Transportation from port of destination to site</li>
            <li>Any electric, plumbing works outside borders of the buildings (including water tanks and main site networks)</li>
            <li>Concrete slab according to the plan provided by the company</li>
            <li>Crane, forklift, scaffolding</li>
            <li>Securing the goods at the site from theft and inside closed area to protect them from weather conditions</li>
            <li>Earthing and grounding</li>
            <li>Electricity at the worksite</li>
            <li>Clear out the assembly area after work is completed</li>
            <li>Preparation of assembling site before products arrive at the port at the country of destination</li>
            <li>Any task or item that is not listed under company\'s responsibilities</li>
        </ul>

        <h2>5. Company’s Responsibilities</h2>
        <ul>
            <li>The building structure including wall panels, metal parts and roof</li>
            <li>All doors and Windows</li>
            <li>Plumbing and sanitaryware (inside the building)</li>
            <li>Electric network and fittings (inside the building)</li>
            <li>Walls and roof sandwich panel</li>
            <li>Paint</li>
            <li>Packaging, loading and transportation</li>
        </ul>

        <h2>6. Other Conditions</h2>
        <p>CUSTOMER cannot make changes on approved projects or on technical specifications after production begins.</p>

        <h2>7. Warranty Coverage</h2>
        <ul>
            <li>The order subject of this offer, will be under warranty of the COMPANY for one (1) year against defects of production. Warranty period will start after the invoice date. In order to get warranty coverage, the CUSTOMER is required to present the invoice. Damage and defects that are related to the customer are not covered in the warranty.</li>
            <li>The COMPANY is not responsible for the problems that may happen because of adding extra works or parts on the product interior and exterior.</li>
            <li>The COMPANY is not responsible for problems that may occur due to relocating the product to another location.</li>
            <li>Stated values for wind resistance are valid on the condition that the product is fixed to the ground. Fixing process is responsibility of the customer.</li>
        </ul>

        <h2>8. Disagreement</h2>
        <p>In case of a disagreement, both sides will try their best to solve the issue in an amicable settlement. If the disagreement is not solved within thirty (30) business days, courts of Istanbul are authorized to solve the dispute.</p>
        <div class="page-break"></div>

        <h1 style="text-align: center; font-size: 24px; font-weight: bold;">Attachment-2: Technical Specification</h1>

        <div class="specifications-section" style="margin: 20px; font-family: Arial, sans-serif;">
            @foreach ($orderItems as $item)
                <div class="specification" style="margin-bottom: 40px;">
                    <div style="background-color: #40E0D0; color: white; padding: 10px 20px; border-radius: 5px; font-weight: bold; text-transform: uppercase; margin-bottom: 20px;">
                        PREFABEX 
                        <strong>{{ $item->product->name }}</strong> 
                        BUILDING TECHNICAL SPECIFICATIONS
                    </div>
        
                    @if (!empty($item->specifications))
                        @foreach ($item->specifications as $spec)
                            <div>
                                <p style="font-size: 18px; text-decoration: underline; font-weight: bold;">{{ $spec['name'] ?? 'Specification' }}</p>
                                <p style="font-size: 14px; margin: 5px 0;">{{ $spec['title'] ?? 'No title' }}</p>
        
                                <!-- Paragraphs -->
                                @if (!empty($spec['paragraphs']))
                                    <p style="font-size: 14px; line-height: 1.6;">{!! $spec['paragraphs'] !!}</p>
                                @endif
        
                                <!-- Images -->
                               @if (!empty($spec['images']))
                                    @php
                                        $images = is_array($spec['images']) ? $spec['images'] : json_decode($spec['images'], true);
                                    @endphp
                                
                                    @if (is_array($images) && count($images) > 0)
                                        <div class="spec-images">
                                            @foreach ($images as $image)
                                                @php
                                                    $base64Image = $base64EncodeImageA($image);
                                                @endphp

                                                @if ($base64Image)
                                                    <img src="{{ $base64Image }}" alt="spec image" width="100" height="100" style="margin-right: 10px;">
                                                @else
                                                    <p>Image not found: {{ $image }}</p>
                                                @endif
                                            @endforeach
                                        </div>
                                    @else
                                        <p>No images available.</p>
                                    @endif
                                
                            @endif
                            
                            </div>
                        @endforeach
                    @else
                        <p>No specifications available.</p>
                    @endif
                </div>
            @endforeach
        </div>
        
        
         
   
        <div class="section">
            <h2>Attachment-3: Technical Drawing or Image</h2>
            <div class="order-details">
                <h3>Order Details</h3>
                <p>For the technical drawings or images, please refer to the attached documents below:</p>
            
                <div class="order-images">
                    <h3>Order Images</h3> 
                    <div class="image-gallery">
                        @if (!empty($order->images))
                            @foreach (json_decode($order->images) as $image)
                                <!-- استخدم الرابط الكامل للصور -->
                                <img src="{{ public_path('storage/' . $image) }}" alt="Order Image" width="100" height="100" style="margin-right: 10px;">
                             @endforeach
                        @else
                            <p>No images uploaded for this order.</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        
</html>
 