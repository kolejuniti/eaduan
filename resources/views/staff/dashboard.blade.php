@extends('layouts.staff')

@section('content')
<div class="container">
    <h1>Staff Dashboard</h1>
    <p>Welcome, {{ Auth::guard('staff')->user()->name }}!</p>

    <!-- Add dashboard content here -->
</div>
@endsection
