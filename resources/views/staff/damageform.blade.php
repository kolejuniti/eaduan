@extends('layouts.staff')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-sm-8 col-12">
            @if (session('success'))
                <div class="alert alert-success">
                    <ul>
                        @foreach (session('success') as $success)
                            <li>{{ $success }}</li>
                        @endforeach
                    </ul>
                </div>
            @elseif(session('danger'))
                <div class="alert alert-danger">
                    <ul>
                        @foreach (session('danger') as $danger)
                            <li>{{ $danger }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <div class="card">
                <div class="card-header">{{ __('Borang Aduan Kerosakan') }}</div>
                <form method="POST" action="{{ route('staff.damageform.submit') }}" class="needs-validation" novalidate>
                <div class="card-body">
                    @csrf
                    <div class="col-md-12 col-sm-12 mb-2">
                        <label for="" class="fw-bold">Maklumat Staf</label>
                    </div>
                    <div class="g-2 mb-2">
                        <div class="col-md-12 col-sm-12 form-floating">
                            <input type="text" name="name" id="name" value="{{ $staff->name }}" class="form-control" placeholder="" readonly>
                            <label for="name">Nama Penuh</label>
                        </div>
                    </div>
                    <div class="g-2 mb-3">
                        <div class="col-md-6 col-sm-6 form-floating">
                            <input type="text" name="no_staf" id="no_staf" value="{{ $staff->no_staf }}" class="form-control" placeholder="" readonly>
                            <label for="no_staf">No. Staf</label>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 mb-2">
                        <label for="" class="fw-bold">Maklumat Aduan</label>
                    </div>
                    <div class="row g-2 mb-2 row-cols-1">
                        <div class="col-md-6 col-sm-6 form-floating">
                            <input type="date" name="date_of_complaint" id="date_of_complaint" class="form-control" placeholder="" required readonly>
                            <label for="date_of_complaint">Tarikh Aduan</label>
                        </div>
                        <div class="col-md-6 col-sm-6 form-floating">
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="" required maxlength="12">
                            <label for="phone">No. Telefon</label>
                        </div>
                    </div>
                    <div class="row g-2 mb-3 row-cols-2">
                        <div class="col-md-12 col-sm-12 col-12 form-floating">
                            <select name="no_unit" id="no_unit_id" class="form-control" required>
                                <option value="">Pilihan Lokasi</option>
                                @foreach($locations as $group_name => $locationNames)
                                    <optgroup label="{{ $group_name }}">
                                        @foreach($locationNames as $location)
                                            <option value="{{ $location->name }}" data-group="{{ $group_name }}">{{ $location->name }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            <input type="hidden" name="block" id="block_id"> 
                            <input type="hidden" name="location" id="location_id" value="UNITI VILLAGE">                           
                            <label for="no_unit">Lokasi</label>
                        </div>
                    </div>
                    <div class="col-md-12 col-sm-12 mb-2">
                        <label for="" class="fw-bold">Maklumat Kerosakan</label>
                    </div>
                    <div class="row g-2 mb-2 row-cols-1">
                        <div class="col-md-6 col-sm-6 form-floating">
                            <select name="damagetypes" id="damagetypes" class="form-control" required>
                                <option value="">Pilihan Kategori</option>
                                @foreach ($damagetypes as $damagetype)
                                    <option value="{{ $damagetype->id }}">{{ $damagetype->name }}</option>
                                @endforeach
                            </select>
                            <label for="damagetypes">Kategori</label>
                        </div>
                        <div class="col-md-6 col-sm-6 form-floating">
                            <select name="damagetypedetails" id="damagetypedetails" class="form-control" required>
                                <option value="">Pilihan Jenis Kerosakan</option>
                            </select>
                            <label for="damagetypedetails">Jenis Kerosakan</label>
                        </div>
                    </div>
                    <div class="mb-2 form-floating">
                        <textarea name="notes" id="notes" class="form-control"></textarea>
                        <label for="notes">Catatan</label>
                    </div>
                </div>
                <div class="card-footer">
                    <div class="col-sm-12 text-center">
                        <button class="btn btn-primary" type="submit">Daftar</button>
                    </div>
                </div>
            </form>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
        document.getElementById('no_unit_id').addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        var block = selectedOption.getAttribute('data-group');
        document.getElementById('block_id').value = block;
    });
</script>
<script>
    $(document).ready(function() {
        $('#damagetypes').change(function() {
            var damagetypeId = $(this).val();

            // Clear the damagetypedetails dropdown
            $('#damagetypedetails').empty().append('<option value="">Pilihan Jenis Kerosakan</option>');

            if (damagetypeId) {
                // Send an AJAX request to get the damage type details based on damagetypeId
                $.ajax({
                    url: '/staff/get-damagetypedetails/' + damagetypeId, // Adjust the URL according to your route
                    type: 'GET',
                    success: function(data) {
                        // Populate the damagetypedetails dropdown with the received data
                        $.each(data, function(key, value) {
                            $('#damagetypedetails').append('<option value="' + value.id + '">' + value.name + '</option>');
                        });
                    },
                    error: function(xhr, status, error) {
                        console.log('An error occurred while fetching damage type details:', error);
                    }
                });
            }
        });
    });
</script>
<script>
    // Format date to yyyy-mm-dd for input type="date"
    function getFormattedDate() {
        var today = new Date();
        var day = String(today.getDate()).padStart(2, '0');
        var month = String(today.getMonth() + 1).padStart(2, '0'); // January is 0
        var year = today.getFullYear();

        return year + '-' + month + '-' + day;
    }

    // Set the current date in the input field
    document.getElementById('date_of_complaint').value = getFormattedDate();
</script>
<script>
    (function() {
        'use strict';
        window.addEventListener('load', function() {
            var forms = document.getElementsByClassName('needs-validation');
            var validation = Array.prototype.filter.call(forms, function(form) {
                form.addEventListener('submit', function(event) {
                    if (form.checkValidity() === false) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });
        }, false);
    })();
</script>
@endsection
