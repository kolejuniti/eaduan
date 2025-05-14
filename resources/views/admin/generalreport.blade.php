@extends('layouts.admin')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-12">
            <div class="col-md-3 col-sm-3 col-12 ms-auto">
                <form method="POST" action="{{ route('admin.generalReport') }}">
                @csrf
                    <div class="input-group mb-3">
                        <input type="month" class="form-control" name="month">
                        <button class="btn btn-warning" type="submit">Cari</button>
                    </div>
                </form>
            </div>
            <div class="table-responsive">
                <!--Kategori & Status-->
                <table id="myTable" class="table table-bordered small table-sm text-center mb-3">
                    <caption>Laporan yang dipaparkan adalah dari tarikh {{ \Carbon\Carbon::parse($firstDate)->format('d-m-Y') }} sehingga {{ \Carbon\Carbon::parse($lastDate)->format('d-m-Y') }}</caption>
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" style="width: 15%;">Kategori</th>
                            @foreach ($dates as $date)
                                <th class="text-center" colspan="3">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</th>
                            @endforeach
                            <th rowspan="2" style="width: 10%;">Jumlah</th>
                        </tr>
                        <tr>
                            @foreach ($dates as $date) <!-- Loop for each date -->
                                @foreach ($status as $item)
                                    <th style="width: 5%;">{{ $item->name }}</th>                               
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($categoryTypes as $categoryType)
                        <tr>
                            <td>{{ $categoryType }}</td>
                            @php
                                $overallTotal = 0; // Initialize the overall total for each damage type
                            @endphp
                            @foreach ($dates as $date) <!-- Loop for each date -->
                                @foreach ($status as $stat) <!-- Loop through each status -->
                                    @if (isset($totalByCategoryTypeStatus[$categoryType][$date][$stat->id]))
                                        @php
                                            $count = $totalByCategoryTypeStatus[$categoryType][$date][$stat->id]['total'];
                                            $overallTotal += $count; // Add to the overall total
                                        @endphp
                                        <td class="text-center">{{ $count }}</td>
                                    @else
                                        <td class="text-center">0</td> <!-- Display 0 if no count exists -->
                                    @endif
                                @endforeach
                            @endforeach
                            <td class="text-center">{{ $overallTotal }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-danger">
                        <tr>
                            <td>Jumlah</td>
                            @php
                                $overallTotal = 0; // Initialize the overall total for each row (date)
                            @endphp
                            @foreach ($dates as $date)
                                @foreach ($status as $stat)
                                    @php
                                        $count = $totalByStatus[$date][$stat->id]['total'] ?? 0; // Default to 0 if not set
                                        $overallTotal += $count; // Add to the overall total
                                    @endphp
                                    <td>{{ $count }}</td>
                                @endforeach
                            @endforeach
                            <td>{{ $overallTotal }}</td>
                        </tr>
                    </tfoot>
                </table>
                <!--Jenis Aduan & Status-->
                <table id="myTable2" class="table table-bordered small table-sm text-center">
                    <caption>Laporan yang dipaparkan adalah dari tarikh {{ \Carbon\Carbon::parse($firstDate)->format('d-m-Y') }} sehingga {{ \Carbon\Carbon::parse($lastDate)->format('d-m-Y') }}</caption>
                    <thead class="table-dark">
                        <tr>
                            <th rowspan="2" style="width: 15%;">Jenis Aduan</th>
                            @foreach ($dates as $date)
                                <th class="text-center" colspan="3">{{ \Carbon\Carbon::parse($date)->format('d-m-Y') }}</th>
                            @endforeach
                            <th rowspan="2" style="width: 10%;">Jumlah</th>
                        </tr>
                        <tr>
                            @foreach ($dates as $date) <!-- Loop for each date -->
                                @foreach ($status as $item)
                                    <th style="width: 5%;">{{ $item->name }}</th>                               
                                @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($complaintTypes as $complaintType)
                        <tr>
                            <td>{{ $complaintType->name }}</td>
                            @php
                                $overallTotal = 0; // Initialize the overall total for each damage type
                            @endphp
                            @foreach ($dates as $date) <!-- Loop for each date -->
                                @foreach ($status as $stat) <!-- Loop through each status -->
                                    @if (isset($totalByComplaintTypeStatus[$complaintType->id][$date][$stat->id]))
                                        @php
                                            $count = $totalByComplaintTypeStatus[$complaintType->id][$date][$stat->id]['total'];
                                            $overallTotal += $count; // Add to the overall total
                                        @endphp
                                        <td class="text-center">{{ $count }}</td>
                                    @else
                                        <td class="text-center">0</td> <!-- Display 0 if no count exists -->
                                    @endif
                                @endforeach
                            @endforeach
                            <td class="text-center">{{ $overallTotal }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-danger">
                        <tr>
                            <td>Jumlah</td>
                            @php
                                $overallTotal = 0; // Initialize the overall total for each row (date)
                            @endphp
                            @foreach ($dates as $date)
                                @foreach ($status as $stat)
                                    @php
                                        $count = $totalByComplaintStatus[$date][$stat->id]['total'] ?? 0; // Default to 0 if not set
                                        $overallTotal += $count; // Add to the overall total
                                    @endphp
                                    <td>{{ $count }}</td>
                                @endforeach
                            @endforeach
                            <td>{{ $overallTotal }}</td>
                        </tr>
                    </tfoot>
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
                className: 'dt-head-center',
                orderable: false
            }
        ],
        layout: {
                top1Start: {
                    div: {
                        html: '<h2>Statistik Aduan Umum Mengikut Kategori & Status</h2>'
                    }
                },
                top1End: {
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Statistik Aduan Umum Mengikut Kategori & Status'
                        },
                        {
                            extend: 'excelHtml5',
                            title: 'Statistik Aduan Umum Mengikut Kategori & Status'
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Statistik Aduan Umum Mengikut Kategori & Status',
                            orientation: 'landscape', // <-- set orientation here
                            pageSize: 'A4' // optional: default is A4
                        },
                        {
                            extend: 'print',
                            title: 'Statistik Aduan Umum Mengikut Kategori & Status'
                        }
                    ]
                },
                topStart: null,
                topEnd: 'search',
                bottomStart: null,
                bottomEnd: null
            }
        });

    });
</script>
<script>

    $(document).ready(function() {
        // Initialize DataTables
        var t = $('#myTable2').DataTable({
        columnDefs: [
            {
                targets: ['_all'],
                className: 'dt-head-center',
                orderable: false
            }
        ],
        layout: {
                top1Start: {
                    div: {
                        html: '<h2>Statistik Aduan Umum Mengikut Jenis Aduan & Status</h2>'
                    }
                },
                top1End: {
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Statistik Aduan Umum Mengikut Jenis Aduan & Status'
                        },
                        {
                            extend: 'excelHtml5',
                            title: 'Statistik Aduan Umum Mengikut Jenis Aduan & Status'
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Statistik Aduan Umum Mengikut Jenis Aduan & Status'
                        },
                        {
                            extend: 'print',
                            title: 'Statistik Aduan Umum Mengikut Jenis Aduan & Status'
                        }
                    ]
                },
                topStart: null,
                topEnd: 'search',
                bottomStart: null,
                bottomEnd: null
            }
        });

    });
</script>
@endsection
