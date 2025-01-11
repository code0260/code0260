@extends('layouts.offer')

@section('title', 'Price Offer for Flatpack Container')

@section('intro')
    <h1>Intro</h1>
@endsection

@section('priceOffer')
    <h1>priceOffer</h1>
    @include('orders.components.products-table', ['cartItems' => $cartItems])
@endsection

@section('technicalSpecification')
    <h1>technicalSpecification</h1>
@endsection

@section('technicalDrawingOrImage')
    <h1>technicalDrawingOrImage</h1>
@endsection
