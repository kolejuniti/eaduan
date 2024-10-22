@extends('layouts.admin')

@section('content')
<div class="container">
    <h1>Admin Dashboard</h1>
    <p>Welcome, {{ Auth::user()->name }}!</p>

    <!-- Add dashboard content here -->
</div>
@endsection
