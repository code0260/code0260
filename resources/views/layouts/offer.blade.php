<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>@yield('title', 'Default Title')</title>

    @push('styles')
        <link rel="stylesheet" href="{{ asset('css/offer-pdf.css') }}">
    @endpush
</head>

<body>
    <div class="container">
        @yield('intro') <!-- محتوى الصفحة -->
    </div>

    <div class="page-break"></div>

    <div class="container">
        @yield('priceOffer')
    </div>

    <div class="page-break"></div>

    <div class="container">
        @yield('technicalSpecification')
    </div>

    <div class="page-break"></div>

    <div class="container">
        @yield('technicalDrawingOrImage')
    </div>
    @stack('scripts')
</body>

</html>
