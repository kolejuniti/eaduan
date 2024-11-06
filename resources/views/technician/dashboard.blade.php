@extends('layouts.technician')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <h1 class="mb-3">Technician Dashboard</h1>
    {{-- <p>Welcome, {{ Auth::user()->name }}!</p> --}}

    <!-- Add dashboard content here -->
    <!--Top 5 new complaint-->
    <table id="myTable" class="table table-bordered small table-sm text-center mb-3">
        <caption>5 aduan yang terkini.</caption>
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Tarikh & Masa</th>
                <th>Kategori</th>
                <th>Nama Pengadu</th>
                <th>No. Telefon</th>
                <th>Blok</th>
                <th>No. Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($newComplaintLists as $item)
            <tr class="table-info">
                <td style="width: 1%;"></td>
                <td style="width: 15%;">{{ $item->date_of_complaint }}</td>
                <td style="width: 10%;">{{ $item->damage_type }}</td>
                <td>{{ $item->complainant_name }}</td>
                <td class="text-center" style="width: 10%;">{{ $item->phone_number }}</td>
                <td style="width: 15%;">{{ $item->block }}</td>
                <td class="text-center" style="width: 15%;">{{ $item->no_unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!--Top 5 in progress complaint-->
    <table id="myTable2" class="table table-bordered small table-sm text-center">
        <caption>5 aduan yang terkini.</caption>
        <thead class="table-dark">
            <tr>
                <th>#</th>
                <th>Tarikh & Masa</th>
                <th>Kategori</th>
                <th>Nama Pengadu</th>
                <th>No. Telefon</th>
                <th>Blok</th>
                <th>No. Unit</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($inProgressComplaint as $item)
            <tr class="table-warning">
                <td style="width: 1%;"></td>
                <td style="width: 15%;">{{ $item->date_of_complaint }}</td>
                <td style="width: 10%;">{{ $item->damage_type }}</td>
                <td>{{ $item->complainant_name }}</td>
                <td class="text-center" style="width: 10%;">{{ $item->phone_number }}</td>
                <td style="width: 15%;">{{ $item->block }}</td>
                <td class="text-center" style="width: 15%;">{{ $item->no_unit }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
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
                        html: '<h2>Senarai Aduan Baru</h2>'
                    }
                },
                // top1End: {
                //     buttons: [
                //         {
                //             extend: 'copy',
                //             title: 'Senarai Aduan (Baru)'
                //         },
                //         {
                //             extend: 'excelHtml5',
                //             title: 'Senarai Aduan (Baru)'
                //         },
                //         {
                //             extend: 'pdfHtml5',
                //             title: 'Senarai Aduan (Baru)'
                //         },
                //         {
                //             extend: 'print',
                //             title: 'Senarai Aduan (Baru)'
                //         }
                //     ]
                // },
                topStart: null,
                topEnd: null,
                bottomStart: null,
                bottomEnd: null
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
<script>
    $(document).ready(function() {
        // Initialize DataTables
        var t = $('#myTable2').DataTable({
        columnDefs: [
            {
                targets: ['_all'],
                className: 'dt-head-center'
            }
        ],
        layout: {
                top1Start: {
                    div: {
                        html: '<h2>Senarai Aduan Dalam Proses</h2>'
                    }
                },
                // top1End: {
                //     buttons: [
                //         {
                //             extend: 'copy',
                //             title: 'Senarai Aduan (Baru)'
                //         },
                //         {
                //             extend: 'excelHtml5',
                //             title: 'Senarai Aduan (Baru)'
                //         },
                //         {
                //             extend: 'pdfHtml5',
                //             title: 'Senarai Aduan (Baru)'
                //         },
                //         {
                //             extend: 'print',
                //             title: 'Senarai Aduan (Baru)'
                //         }
                //     ]
                // },
                topStart: null,
                topEnd: null,
                bottomStart: null,
                bottomEnd: null
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
