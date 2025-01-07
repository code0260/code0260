@extends('layouts.admin')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>New Product Type</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{route('admin.index')}}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li><i class="icon-chevron-right"></i></li>
                    <li><div class="text-tiny">New Product Type</div></li>
                </ul>
            </div>
            <div class="wg-box">
                <form action="{{route('admin.category.store')}}" method="POST">
                    @csrf
                    <fieldset>
                        <label for="name">Name:</label>
                        <input type="text" name="name" value="{{old('name')}}" required>
                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                    </fieldset>
                    <fieldset>
                        <label for="code">Code:</label>
                        <input type="text" name="code" maxlength="3" value="{{old('code')}}" required>
                        @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                    </fieldset>
                   
                    <button type="submit">Save</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
