@extends('layouts.technician')

@section('content')
<div class="container">
    <h1>Technician Dashboard</h1>
    <p>Welcome, {{ Auth::user()->name }}!</p>

    <!-- Add dashboard content here -->
</div>
@endsection
