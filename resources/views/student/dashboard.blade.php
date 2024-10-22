@extends('layouts.student')

@section('content')
<div class="container">
    <h1>Student Dashboard</h1>
    <p>Welcome, {{ Auth::guard('student')->user()->name }}!</p>

    <!-- Add dashboard content here -->
</div>
@endsection
