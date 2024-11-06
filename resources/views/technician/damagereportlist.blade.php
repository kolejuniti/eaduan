@extends('layouts.technician')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-12">
            <div class="col-md-6 col-sm-6 col-12 ms-auto">
                <form method="POST" action="{{ route('technician.damageReportList') }}">
                @csrf
                    <div class="input-group mb-3">
                        <input type="date" class="form-control" name="start_date">
                        <button class="btn btn-secondary" disabled>-</button>
                        <input type="date" class="form-control" name="end_date">
                        <select name="status" id="status" class="form-control">
                            <option value="">Pilihan Status</option>
                            @foreach ($status as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-warning" type="submit">Cari</button>
                    </div>
                </form>
            </div>            
            <div class="table-responsive">
                <table id="myTable" class="table table-bordered small table-sm text-center">
                    <caption>Senarai yang dipaparkan adalah dari tarikh {{ \Carbon\Carbon::parse($start_date)->format('d-m-Y') }} sehingga {{ \Carbon\Carbon::parse($end_date)->format('d-m-Y') }}</caption>
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tarikh Aduan</th>
                            <th>Kategori</th>
                            <th>Jenis Kerosakan</th>
                            <th>Keterangan</th>
                            <th>Nama Pengadu</th>
                            <th>No. Telefon</th>
                            <th>Blok</th>
                            <th>No. Unit</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complaintLists as $item)
                        @if ($item->latest_status_id === 1)
                            <tr class="table-info">
                        @elseif ($item->latest_status_id === 2)
                            <tr class="table-warning">
                        @elseif ($item->latest_status_id === 3)
                            <tr class="table-success">
                        @elseif ($item->latest_status_id === 4)
                            <tr class="table-danger">
                        @else
                            <tr>
                        @endif
                            <td></td>
                            <td>{{ $item->date_of_complaint }}</td>
                            <td>{{ $item->damage_type }}</td>
                            <td>{{ $item->damage_type_detail }}</td>
                            <td>{{ $item->notes }}</td>
                            <td>{{ $item->complainant_name }}</td>
                            <td class="text-center">{{ $item->phone_number }}</td>
                            <td>{{ $item->block }}</td>
                            <td class="text-center">{{ $item->no_unit }}</td>
                            <td>{{ $item->latest_status }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
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

    });
</script>
@endsection