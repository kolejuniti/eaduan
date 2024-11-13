@extends('layouts.staff')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-12">
            <div class="table-responsive">
                <table id="myTable" class="table table-bordered small table-sm text-center">
                    <caption>Aduan kerosakan akan diselesaikan dalam tempoh masa 7 hari bekerja.</caption>
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tarikh & Masa</th>
                            <th>Kategori</th>
                            <th>Jenis Kerosakan</th>
                            <th>Tarikh Tindakan</th>
                            <th>Tarikh Selesai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($damageLists as $data)
                        <tr>
                            <td></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-link text-uppercase open-modal" data-id="{{ $data->id }}">{{ $data->date_of_complaint }}</button>
                            </td>
                            <td>{{ $data->damage_types }}</td>
                            <td>{{ $data->damage_type_details }}</td>
                            <td>{{ $data->date_of_action }}</td>
                            <td>{{ $data->date_of_completion }}</td>
                            <td>{{ $data->status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Modal for complaint details -->
            <div class="modal fade" id="complaintModal" tabindex="-1" aria-labelledby="complaintModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
                    <div class="modal-content">
                        <div class="modal-body small">
                            <div class="col-md-12 col-sm-12 mb-2">
                                <label for="" class="fw-bold">Maklumat Aduan</label>
                            </div>
                            <table class="table table-bordered table-sm text-center mb-1">
                                <thead class="table-dark">
                                    <tr>
                                        <th>Tarikh & Masa</th>
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
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</div>

<!-- Include external JavaScript libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize DataTables
        var t = $('#myTable').DataTable({
            columnDefs: [
                { targets: ['_all'], className: 'dt-head-center' }
            ]
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
                url: "{{ route('staff.damagereport.detail') }}",  // Ensure the route is correct
                type: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    id: id
                },
                success: function(response) {
                    if (response.complaintLists) {
                        // Populate modal with the complaint data
                        var complaintData = response.complaintLists;
                        
                        $('#complaint-date_of_complaint').text(complaintData.date_of_complaint);
                        $('#complaint-block').text(complaintData.block);
                        $('#complaint-no_unit').text(complaintData.no_unit);
                        $('#complaint-damage_type').text(complaintData.damage_type);
                        $('#complaint-damage_type_detail').text(complaintData.damage_type_detail);
                        $('#complaint-notes').html(complaintData.notes.replace(/\n/g, '<br>'));

                        // Clear previous logs
                        $('#complaint-logs-table tbody').empty();

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
                            $('#date_of_action-container').html('');
                        }

                        // Check if complaintLogs exist for this complaint
                        if (response.complaintLogs && response.complaintLogs[complaintData.id]) {
                            response.complaintLogs[complaintData.id].forEach(function(log, index) {
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
                            $('#complaint-logs-table tbody').append(`
                                <tr>
                                    <td colspan="4">No logs available for this complaint.</td>
                                </tr>
                            `);
                        }

                        // Open the modal
                        $('#complaintModal').modal('show');
                    } else {
                        console.error('No complaint data found');
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);  // Debugging
                }
            });
        });
    });
</script>
@endsection
