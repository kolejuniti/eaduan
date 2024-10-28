@extends('layouts.admin')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-12">
            <div class="card mb-3">
                <form method="POST" action="{{ route('admin.damageReport') }}">
                    @csrf
                    <div class="card-header">{{ __('Carian Senarai Kerosakan') }}</div>
                    <div class="card-body">
                        <div class="row g-2 row-cols-2">
                            <div class="col-md-6 col-sm-6 form-floating">
                                <input type="month" name="month" id="month" class="form-control" placeholder="">
                                <label for="month" class="fw-bold">Bulan</label>
                            </div>
                            <div class="col-md-6 col-sm-6 form-floating">
                                <select name="damagetype" id="damagetype" class="form-control">
                                    <option value="">Pilihan Kategori</option>
                                    @foreach ($damageTypes as $damageType)
                                        <option value="{{ $damageType->id }}">{{ $damageType->name }}</option>
                                    @endforeach
                                </select>                                
                                <label for="category" class="fw-bold">Kategori</label>
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
            <div class="table-responsive">
                <table id="myTable" class="table table-bordered small table-sm text-center">
                    <caption>Senarai yang dipaparkan adalah dari tarikh {{ \Carbon\Carbon::parse($firstDate)->format('d-m-Y') }} sehingga {{ \Carbon\Carbon::parse($lastDate)->format('d-m-Y') }}</caption>
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
                        @foreach ($damageTypes as $damageType)
                        <tr>
                            <td>{{ $damageType->name }}</td>
                            @php
                                $overallTotal = 0; // Initialize the overall total for each damage type
                            @endphp
                            @foreach ($dates as $date) <!-- Loop for each date -->
                                @foreach ($status as $stat) <!-- Loop through each status -->
                                    @if (isset($totalByDamageStatus[$damageType->id][$date][$stat->id]))
                                        @php
                                            $count = $totalByDamageStatus[$damageType->id][$date][$stat->id]['total'];
                                            $overallTotal += $count; // Add to the overall total
                                        @endphp
                                        <td class="text-center">{{ $count }}</td>
                                    @else
                                        <td>0</td> <!-- Display 0 if no count exists -->
                                    @endif
                                @endforeach
                            @endforeach
                            <td class="text-center">{{ $overallTotal }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    @if ($damagetype === null)
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
                    @endif
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
        pageLength: 12,
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
                        html: '<h2>Statistik Kerosakan Mengikut Kategori & Status</h2>'
                    }
                },
                top1End: {
                    buttons: [
                        {
                            extend: 'copy',
                            title: 'Statistik Kerosakan Mengikut Kategori & Status'
                        },
                        {
                            extend: 'excelHtml5',
                            title: 'Statistik Kerosakan Mengikut Kategori & Status'
                        },
                        {
                            extend: 'pdfHtml5',
                            title: 'Statistik Kerosakan Mengikut Kategori & Status'
                        },
                        {
                            extend: 'print',
                            title: 'Statistik Kerosakan Mengikut Kategori & Status'
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
