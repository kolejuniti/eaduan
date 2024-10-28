@extends('layouts.technician')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-12">
            <div class="card mb-3">
                <form method="POST" action="{{ route('technician.damagecomplaint') }}">
                    @csrf
                    <div class="card-header">{{ __('Carian Senarai Kerosakan') }}</div>
                    <div class="card-body">
                        <div class="row g-2 row-cols-2">
                            <div class="col-md-6 col-sm-6 form-floating">
                                <input type="date" name="start_date" id="start_date" class="form-control" placeholder="">
                                <label for="start_date" class="fw-bold">Tarikh Mula</label>
                            </div>
                            <div class="col-md-6 col-sm-6 form-floating">
                                <input type="date" name="end_date" id="end_date" class="form-control" placeholder="">
                                <label for="end_date" class="fw-bold">Tarikh Akhir</label>
                            </div>
                        </div>
                        <div class="row mt-1 g-2 row-cols-2">
                            <div class="col-md-6 col-sm-6 form-floating">
                                <select name="status" id="status" class="form-control">
                                    <option value="">Pilihan Status</option>
                                    @foreach ($status as $item)
                                        <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                                <label for="status">Status</label>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="col-sm-12 text-center">
                            <button class="btn btn-warning" type="submit">Cari</button>
                        </div>
                    </div>
                </form>
            </div>
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @elseif(session('warning'))
                <div class="alert alert-warning">
                    {{ session('warning') }}
                </div>
            @elseif(session('danger'))
                <div class="alert alert-danger">
                    {{ session('danger') }}
                </div>
            @endif
            <div class="table-responsive">
                <table id="myTable" class="table table-bordered small table-sm text-center">
                    <caption>Senarai yang dipaparkan adalah dari tarikh {{ \Carbon\Carbon::parse($start_date)->format('d-m-Y') }} sehingga {{ \Carbon\Carbon::parse($end_date)->format('d-m-Y') }}</caption>
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tarikh Aduan</th>
                            <th>Kategori</th>
                            <th>Nama Pengadu</th>
                            <th>No. Telefon</th>
                            <th>Blok</th>
                            <th>No. Unit</th>
                            <th>Tarikh Tindakan</th>
                            <th>Tarikh Selesai</th>
                            <th>Status</th>
                            <th>Tempoh Aduan (Hari)</th>
                            {{-- <th></th> --}}
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complaintLists as $data)
                        @if ($data->latest_status_id === 1)
                            <tr class="table-info">
                        @elseif ($data->latest_status_id === 2)
                            <tr class="table-warning">
                        @elseif ($data->latest_status_id === 3)
                            <tr class="table-success">
                        @elseif ($data->latest_status_id === 4)
                            <tr class="table-danger">
                        @else
                            <tr>
                        @endif
                            <td></td>
                            <td>{{ $data->date_of_complaint }}</td>
                            <td>{{ $data->damage_type }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-link text-uppercase open-modal" data-id="{{ $data->id }}">{{ $data->complainant_name }}</button>
                            </td>
                            <td class="text-center">{{ $data->phone_number }}</td>
                            <td>{{ $data->block }}</td>
                            <td class="text-center">{{ $data->no_unit }}</td>
                            <td>{{ $data->date_of_action }}</td>
                            <td>{{ $data->date_of_completion }}</td>
                            <td>{{ $data->latest_status }}</td>
                            <td class="text-center">
                                @if ($data->date_of_completion === null)
                                    @if ($data->latest_status_id === 4)
                                    @else
                                    {{ $data->days_since_complaint }}
                                    @endif
                                @else
                                @endif
                            </td>
                            {{-- <td class="text-center">
                                @if ($data->date_of_completion === null)
                                    @if ($data->latest_status_id === 4)
                                        <button type="button" class="btn btn-sm btn-danger" disabled>Batal</button>
                                    @else
                                        <button type="button" class="btn btn-sm btn-danger open-cancel-modal" data-id="{{ $data->id }}" data-bs-toggle="modal" data-bs-target="#cancelModal">Batal</button>
                                    @endif
                                @else
                                    <button type="button" class="btn btn-sm btn-danger" disabled>Batal</button>
                                @endif
                            </td>                             --}}
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="modal fade" id="complaintModal" tabindex="-1" aria-labelledby="complaintModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <form id="complaint-form" action="" method="POST">
                        @csrf
                        @method('PUT') 
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold" id="cancelModalLabel">Maklumat Aduan</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body small">
                                <div class="row mb-1">
                                    <div class="col-md-2">
                                        <label for="complaint-complainant_name" class="fw-bold">Nama</label>
                                    </div>
                                    <div class="col-md-10">
                                        <label id="complaint-complainant_name"></label>
                                    </div>
                                </div>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="complaint-phone_number" class="fw-bold">No. Telefon</label>
                                    </div>
                                    <div class="col-md-10">
                                        <label id="complaint-phone_number"></label>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 mb-2">
                                    <label for="" class="fw-bold">Aduan</label>
                                </div>
                                <table class="table table-bordered table-sm text-center mb-1">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Tarikh</th>
                                            <th>Blok</th>
                                            <th>No. Unit</th>
                                            <th>Kategori</th>
                                            <th>Jenis Kerosakan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><label id="complaint-date_of_complaint"></label></td>
                                            <td><label id="complaint-block"></label></td>
                                            <td><label id="complaint-no_unit"></label></td>
                                            <td><label id="complaint-damage_type"></label></td>
                                            <td><label id="complaint-damage_type_detail"></label></td>
                                        </tr>
                                    </tbody>
                                </table>
                                <div class="row mb-3">
                                    <div class="col-md-2">
                                        <label for="complaint-notes" class="fw-bold">Catatan</label>
                                    </div>
                                    <div class="col-md-10">
                                        <label id="complaint-notes"></label>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 mb-2">
                                    <label for="" class="fw-bold">Log Aduan</label>
                                </div>
                                <table id="complaint-logs-table" class="table table-bordered table-sm text-center">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>#</th>
                                            <th>Tarikh</th>
                                            <th>Status</th>
                                            <th>Catatan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <!-- Dynamic content will be inserted here -->
                                    </tbody>
                                </table>
                                <div id="date_of_action-container">
                                    <!-- date_of_action will be loaded here -->
                                </div> 
                                <div id="technician-container">
                                    <!-- technician will be loaded here -->
                                </div>                      
                            </div>
                            <div id="save-container">
                                <!-- save button will be loaded here -->
                            </div> 
                        </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Cancel Modal -->
            <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-md" role="document">
                    <div class="modal-content">
                        <form id="cancel-complaint-form" action="" method="POST">
                            @csrf
                            @method('PUT') 
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold" id="cancelModalLabel">Batal Aduan</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body small">
                                <p class="fw-bold">Adakah anda pasti untuk membatalkan aduan ini?</p>
                                <textarea name="notes" id="notes" rows="2" class="form-control" required></textarea>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                <button type="submit" class="btn btn-sm btn-danger">Confirm Cancel</button> <!-- Change type to submit -->
                            </div>
                        </form>
                    </div>
                </div>
            </div>                       
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.js"></script>
<script>
    $(document).ready(function() {
        // Initialize DataTables
        var t = $('#myTable').DataTable({
        columnDefs: [
            {
                targets: ['_all'],
                className: 'dt-head-center'
            }
        ],
        layout: {
                top1Start: {
                    div: {
                        html: '<h2>Senarai Aduan Kerosakan</h2>'
                    }
                },
                top1End: {
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Senarai Aduan Kerosakan'
                        },
                        {
                            extend: 'excelHtml5',
                            title: 'Senarai Aduan Kerosakan'
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Senarai Aduan Kerosakan'
                        },
                        {
                            extend: 'print',
                            title: 'Senarai Aduan Kerosakan'
                        }
                    ]
                },
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            }
        });

        // Add row numbering
        t.on('order.dt search.dt', function () {
            let i = 1;
            
            t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                this.data(i++);
            });
        }).draw();

        // Event delegation for dynamically added elements
        $(document).on('click', '.open-modal', function() {
            var id = $(this).data('id');

            $.ajax({
                url: "{{ route('technician.damagecomplaint.detail') }}",  // Ensure the route is correct
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(response) {
                    if (response.complaintLists) {
                        // Find the complaint data based on the ID
                        var complaintData = response.complaintLists.find(d => d.id === id);

                        if (complaintData) {
                            // Set the form action dynamically based on the complaint's ID
                            // Set the action attribute using the route helper
                            $('#complaint-form').attr('action', "{{ route('technician.damagecomplaint.update', ':id') }}".replace(':id', complaintData.id));
                            // Populate modal with the specific complaint data
                            $('#complaint-id').text(complaintData.id);
                            $('#complaint-complainant_name').text(complaintData.complainant_name);
                            $('#complaint-phone_number').text(complaintData.phone_number);
                            $('#complaint-block').text(complaintData.block);
                            $('#complaint-no_unit').text(complaintData.no_unit);
                            $('#complaint-date_of_complaint').text(complaintData.date_of_complaint);
                            $('#complaint-damage_type').text(complaintData.damage_type);
                            $('#complaint-damage_type_detail').text(complaintData.damage_type_detail);
                            $('#complaint-notes').html(complaintData.notes.replace(/\n/g, '<br>'));

                            // Clear any previous logs in the table body
                            $('#complaint-logs-table tbody').empty();

                            // Check if complaintLogs exist for this complaint
                            if (response.complaintLogs && response.complaintLogs[id]) {
                                // Loop through each log and append a new row to the table
                                response.complaintLogs[id].forEach(function(log, index) {
                                    var logRow = `
                                        <tr>
                                            <td>${index + 1}</td>
                                            <td>${log.date_of_log}</td>
                                            <td>${log.log_status}</td>
                                            <td>${log.log_notes}</td>
                                        </tr>
                                    `;
                                    $('#complaint-logs-table tbody').append(logRow);
                                });
                            } else {
                                // If no logs, show a message or leave the table empty
                                $('#complaint-logs-table tbody').append(`
                                    <tr>
                                        <td colspan="4">No logs available for this complaint.</td>
                                    </tr>
                                `);
                            }

                            //handle date_of_action
                            if (complaintData.date_of_action) {
                                $('#date_of_action-container').html(`
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            <label for="date_of_action" class="fw-bold">Tarikh Tindakan</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="date_of_action">${complaintData.date_of_action}</label>
                                        </div>
                                    </div>
                                `);
                            } else {
                                $('#date_of_action-container').html(`
                                    ${(complaintData.date_of_completion === null && complaintData.latest_status_id !== 4) ? `
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            <label for="date_of_action" class="fw-bold">Tarikh Tindakan</label>
                                        </div>
                                        <div class="col-md-4">
                                            <input type="date" name="date_of_action" id="date_of_action" class="form-control form-control-sm" required>
                                        </div>
                                    </div>
                                    ` : ''}
                                `);
                            }

                            // Handle technician
                            let technicianOptions = response.technicians.map((technician) => 
                                `<option value="${technician.id}">${technician.name}</option>`
                            ).join('');

                            // Handle status
                            let statusOptions = response.status.map((status) => 
                                `<option value="${status.id}">${status.name}</option>`
                            ).join('');

                            if (complaintData.technician_id) {
                                $('#technician-container').html(`
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            <label for="technician" class="fw-bold">Juruteknik</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="technician">${complaintData.technician}</label>
                                        </select> 
                                        </div>
                                    </div>
                                    ${(complaintData.date_of_completion === null && complaintData.latest_status_id !== 4) ? `
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            <label for="status" class="fw-bold">Status</label>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="status" class="form-control form-control-sm" required>
                                                <option value="" selected disabled></option>
                                            ${statusOptions}
                                            </select> 
                                        </div>
                                    </div>
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            <label for="notes" class="fw-bold">Tindakan</label>
                                        </div>
                                        <div class="col-md-10">
                                            <textarea name="notes" id="notes" rows="2" class="form-control" required></textarea>
                                        </div>
                                    </div>
                                    ` : ''}
                                `);
                            } else {
                                $('#technician-container').html(`
                                    ${(complaintData.date_of_completion === null && complaintData.latest_status_id !== 4) ? `
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            <label for="technician" class="fw-bold">Juruteknik</label>
                                        </div>
                                        <div class="col-md-4">
                                            <select name="technician" class="form-control form-control-sm" required>
                                                <option value="" selected disabled></option>
                                            ${technicianOptions}
                                            </select> 
                                        </div>
                                    </div>
                                    ` : ''}
                                `);
                            }

                            if (complaintData.date_of_completion === null && complaintData.latest_status_id !== 4) {
                                $('#save-container').html(`
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-danger open-cancel-modal" data-id="${complaintData.id}" data-bs-toggle="modal" data-bs-target="#cancelModal">Batal</button>
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                `);
                            } else {
                                $('#save-container').html(`
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                `);
                            }

                            // Open the modal
                            $('#complaintModal').modal('show');
                        } else {
                            console.error('No complaint data found for this ID');
                        }
                    } else {
                        console.error('No complaint data found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);  // Debugging
                }
            });
        });

        //cancel
        $(document).on('click', '.open-cancel-modal', function() {
            var id = $(this).data('id');

            // Set the action attribute using the route helper
            $('#cancel-complaint-form').attr('action', "{{ route('technician.damagecomplaint.cancel', ':id') }}".replace(':id', id));
        });

    });
</script>
@endsection
