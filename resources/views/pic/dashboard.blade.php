@extends('layouts.pic')

@section('content')
<div class="container">
    <h1>Person In Charge Dashboard</h1>
    <p>Welcome, {{ Auth::user()->name }}!</p>

    <!-- Add dashboard content here -->
</div>
@endsection
