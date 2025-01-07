@extends('layouts.admin')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <h3>Edit Product Type</h3>
            <form action="{{route('admin.category.update')}}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="id" value="{{ $category->id }}">
                <fieldset>
                    <label for="name">Name:</label>
                    <input type="text" name="name" value="{{ $category->name }}" required>
                </fieldset>
                <fieldset>
                    <label for="code">Code:</label>
                    <input type="text" name="code" value="{{ $category->code }}" required>
                </fieldset>
                
                <button type="submit">Update</button>
            </form>
        </div>
    </div>
</div>
@endsection
