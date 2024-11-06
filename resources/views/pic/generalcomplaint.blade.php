@extends('layouts.pic')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-12">
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
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tarikh & Masa</th>
                            <th>Kategori</th>
                            <th>Nama Pengadu</th>
                            <th>No. Telefon</th>
                            <th>Bahagian / Unit</th>
                            <th>Jenis Aduan</th>
                            <th>Lokasi</th>
                            <th>Tarikh Terima</th>
                            <th>Tarikh Tindakan</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complaintLists as $data)
                        @if ($data->status_id === 1)
                            <tr class="table-info">
                        @elseif ($data->status_id === 2)
                            <tr class="table-warning">
                        @elseif ($data->status_id === 3)
                            <tr class="table-success">
                        @elseif ($data->status_id === 4)
                            <tr class="table-danger">
                        @else
                            <tr>
                        @endif
                            <td></td>
                            <td>{{ $data->date_of_complaint }}</td>
                            <td>{{ $data->category }}</td>
                            <td>
                                <button type="button" class="btn btn-sm btn-link text-uppercase open-modal" data-id="{{ $data->id }}">{{ $data->complainant_name }}</button>
                            </td>
                            <td class="text-center">{{ $data->phone_number }}</td>
                            <td>{{ $data->section }}</td>
                            <td>{{ $data->complaint_type }}</td>
                            <td></td>
                            <td>{{ $data->date_of_receipt }}</td>
                            <td>{{ $data->date_of_action }}</td>
                            <td>{{ $data->status }}</td>
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
                                <table class="table table-bordered table-sm text-center mb-3">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>Tarikh & Masa</th>
                                            <th>Bahagian / Unit</th>
                                            <th>Jenis Aduan</th>
                                            <th>Catatan</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><label id="complaint-date_of_complaint"></label></td>
                                            <td><label id="complaint-section"></label></td>
                                            <td><label id="complaint-complaint_type"></label></td>
                                            <td><label id="complaint-notes"></label></td>
                                            <td><label id="complaint-status"></label></td>
                                        </tr>
                                    </tbody>
                                </table>  
                                <div id="date_of_receipt-container">
                                    <!-- date_of_receipt will be loaded here -->
                                </div> 
                                <div id="pic-container">
                                    <!-- pic will be loaded here -->
                                </div> 
                                <div id="date_of_action-container">
                                    <!-- date_of_action will be loaded here -->
                                </div> 
                                <div id="status-container">
                                    <!-- status will be loaded here -->
                                </div> 
                                <div id="action_notes-container">
                                    <!-- action_notes will be loaded here -->
                                </div>  
                                <div id="cancel_notes-container">
                                    <!-- cancel_notes will be loaded here -->
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
                                <textarea name="cancel_notes" id="cancel_notes" rows="2" class="form-control" required></textarea>
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
                        html: '<h2>Senarai Aduan Umum</h2>'
                    }
                },
                top1End: {
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Senarai Aduan Umum'
                        },
                        {
                            extend: 'excelHtml5',
                            title: 'Senarai Aduan Umum'
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Senarai Aduan Umum'
                        },
                        {
                            extend: 'print',
                            title: 'Senarai Aduan Umum'
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
                url: "{{ route('pic.generalcomplaint.detail') }}",  // Ensure the route is correct
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
                            $('#complaint-form').attr('action', "{{ route('pic.generalcomplaint.update', ':id') }}".replace(':id', complaintData.id));
                            // Populate modal with the specific complaint data
                            $('#complaint-id').text(complaintData.id);
                            $('#complaint-complainant_name').text(complaintData.complainant_name);
                            $('#complaint-phone_number').text(complaintData.phone_number);
                            $('#complaint-date_of_complaint').text(complaintData.date_of_complaint);
                            $('#complaint-section').text(complaintData.section);
                            $('#complaint-complaint_type').text(complaintData.complaint_type);
                            $('#complaint-notes').html(complaintData.notes.replace(/\n/g, '<br>'));
                            $('#complaint-status').text(complaintData.status);

                            // Handle date_of_receipt
                            if (complaintData.status_id !== 4) {
                                if (complaintData.date_of_receipt) {
                                    $('#date_of_receipt-container').html(`
                                        <div class="row mb-1">
                                            <div class="col-md-2">
                                                <label for="date_of_receipt" class="fw-bold">Tarikh Terima</label>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="date_of_receipt">${complaintData.date_of_receipt}</label>
                                            </div>
                                        </div>
                                    `);
                                } else {
                                    $('#date_of_receipt-container').html('');
                                }
                            } else {
                                $('#date_of_receipt-container').html(''); // Clear the container if status_id === 4
                            }

                           // Handle User
                            if (complaintData.status_id !== 4) {
                                if (complaintData.user_id) {
                                    $('#pic-container').html(`
                                        <div class="row mb-1">
                                            <div class="col-md-2">
                                                <label for="user" class="fw-bold">Pegawai</label>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="user">${complaintData.user_name}</label>
                                            </div>
                                        </div>
                                    `);
                                } else {
                                    $('#pic-container').html('');
                                }
                            } else {
                                $('#pic-container').html(''); // Clear the container if status_id === 4
                            }

                            // Handle date_of_receipt
                            if (complaintData.status_id !== 4) {
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
                                        <div class="row mb-1">
                                            <div class="col-md-2">
                                                <label for="date_of_action" class="fw-bold">Tarikh Tindakan</label>
                                            </div>
                                            <div class="col-md-4">
                                                <input type="date" name="date_of_action" id="date_of_action" class="form-control form-control-sm" required>
                                            </div>
                                        </div>
                                    `);
                                }
                            } else {
                                $('#date_of_action-container').html(''); // Clear the container if status_id === 4
                            }

                            if (complaintData.status_id === 2) {
                                // Show textarea if status_id is 2
                                $('#action_notes-container').html(`
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            <label for="action_notes" class="fw-bold">Tindakan</label>
                                        </div>
                                        <div class="col-md-10">
                                            <textarea name="action_notes" id="action_notes" rows="2" class="form-control" required>${complaintData.action_notes ?? ''}</textarea>
                                        </div>
                                    </div>
                                `);
                            } else if (complaintData.status_id !== 4) {
                                // Show action notes if available, otherwise show the textarea
                                if (complaintData.action_notes) {
                                    $('#action_notes-container').html(`
                                        <div class="row mb-1">
                                            <div class="col-md-2">
                                                <label for="action_notes" class="fw-bold">Tindakan</label>
                                            </div>
                                            <div class="col-md-10">
                                                <label for="action_notes">${complaintData.action_notes}</label>
                                            </div>
                                        </div>
                                    `);
                                } else {
                                    $('#action_notes-container').html(`
                                        <div class="row mb-1">
                                            <div class="col-md-2">
                                                <label for="action_notes" class="fw-bold">Tindakan</label>
                                            </div>
                                            <div class="col-md-10">
                                                <textarea name="action_notes" id="action_notes" rows="2" class="form-control" required></textarea>
                                            </div>
                                        </div>
                                    `);
                                }
                            } else {
                                // Clear the container if status_id === 4
                                $('#action_notes-container').html('');
                            }

                            // Handle status
                            let statusOptions = response.status.map((status) => 
                                `<option value="${status.id}">${status.name}</option>`
                            ).join('');

                            if (complaintData.status_id !== 4) {
                                if (complaintData.status_id !== 2) {
                                    $('#status-container').html(`
                                        <div class="row mb-1">
                                            <div class="col-md-2">
                                                <label for="status" class="fw-bold">Status</label>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="status">${complaintData.status}</label>
                                            </div>
                                        </div>
                                    `);
                                } else {
                                    $('#status-container').html(`
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
                                    `);
                                }
                            } else {
                                $('#action_notes-container').html(''); // Clear the container if status_id === 4
                            }

                            if (complaintData.cancel_notes) {
                                $('#cancel_notes-container').html(`
                                    <div class="row mb-1">
                                        <div class="col-md-2">
                                            <label for="cancel_notes" class="fw-bold">Sebab Batal</label>
                                        </div>
                                        <div class="col-md-4">
                                            <label for="cancel_notes">${complaintData.cancel_notes}</label>
                                        </div>
                                    </div>
                                `);
                            } else {
                                $('#cancel_notes-container').html('');
                            }

                            if (complaintData.date_of_action === null && complaintData.status_id !== 4) {
                                $('#save-container').html(`
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-sm btn-danger open-cancel-modal" data-id="${complaintData.id}" data-bs-toggle="modal" data-bs-target="#cancelModal">Batal</button>
                                        <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Close</button>
                                        <button type="submit" class="btn btn-sm btn-primary">Save</button>
                                    </div>
                                `);
                            } else if (complaintData.date_of_action !== null && complaintData.status_id === 2) {
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
            $('#cancel-complaint-form').attr('action', "{{ route('pic.generalcomplaint.cancel', ':id') }}".replace(':id', id));
        });

    });
</script>
@endsection
