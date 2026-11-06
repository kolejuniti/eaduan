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
                        <input type="month" class="form-control" name="month" value="{{ request('month') }}">
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

            <!-- Ringkasan Status Aduan Umum -->
            <div class="card shadow-sm border-0 mt-4 mb-4">
                <div class="card-header bg-white border-bottom py-3">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <h5 class="mb-0 fw-bold text-dark">
                                <i class="bi bi-card-checklist me-2 text-primary"></i>Ringkasan Status Aduan Umum
                            </h5>
                            <small class="text-muted">
                                Jumlah keseluruhan dari {{ \Carbon\Carbon::parse($firstDate)->format('d-m-Y') }} sehingga {{ \Carbon\Carbon::parse($lastDate)->format('d-m-Y') }}
                            </small>
                        </div>
                        <span class="badge bg-light text-dark border px-3 py-2">
                            <i class="bi bi-collection me-1"></i> Jumlah Aduan: <strong>{{ number_format($grandTotal) }}</strong>
                        </span>
                    </div>
                </div>
                <div class="card-body p-3 p-md-4">
                    @php
                        $statusConfig = [
                            1 => [
                                'bg' => '#e0f2fe',
                                'border' => '#bae6fd',
                                'text' => '#0369a1',
                                'icon' => 'bi-envelope-open',
                                'badge' => 'bg-info text-dark',
                            ],
                            2 => [
                                'bg' => '#fef3c7',
                                'border' => '#fde68a',
                                'text' => '#92400e',
                                'icon' => 'bi-hourglass-split',
                                'badge' => 'bg-warning text-dark',
                            ],
                            3 => [
                                'bg' => '#dcfce7',
                                'border' => '#bbf7d0',
                                'text' => '#166534',
                                'icon' => 'bi-check-circle-fill',
                                'badge' => 'bg-success text-white',
                            ],
                        ];
                    @endphp

                    <!-- Metric Cards -->
                    <div class="row g-3 mb-4">
                        @foreach ($status as $stat)
                            @php
                                $cfg = $statusConfig[$stat->id] ?? [
                                    'bg' => '#f3f4f6',
                                    'border' => '#e5e7eb',
                                    'text' => '#374151',
                                    'icon' => 'bi-tag',
                                    'badge' => 'bg-secondary text-white',
                                ];
                                $count = $statusSummary[$stat->id]['total'] ?? 0;
                                $pct = $statusSummary[$stat->id]['percentage'] ?? 0;
                            @endphp
                            <div class="col-12 col-sm-6 col-lg-3">
                                <div class="p-3 rounded border h-100 d-flex flex-column justify-content-between" style="background-color: {{ $cfg['bg'] }}; border-color: {{ $cfg['border'] }} !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="small fw-bold text-uppercase" style="color: {{ $cfg['text'] }};">{{ $stat->name }}</span>
                                        <i class="bi {{ $cfg['icon'] }} fs-5" style="color: {{ $cfg['text'] }};"></i>
                                    </div>
                                    <div class="my-1">
                                        <span class="fs-2 fw-bold" style="color: {{ $cfg['text'] }};">{{ number_format($count) }}</span>
                                    </div>
                                    <div class="small" style="color: {{ $cfg['text'] }}; opacity: 0.85;">
                                        {{ $pct }}% daripada keseluruhan
                                    </div>
                                </div>
                            </div>
                        @endforeach
                        <div class="col-12 col-sm-6 col-lg-3">
                            <div class="p-3 rounded border h-100 d-flex flex-column justify-content-between" style="background-color: #f8fafc; border-color: #cbd5e1 !important;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="small fw-bold text-uppercase text-secondary">Jumlah Keseluruhan</span>
                                    <i class="bi bi-bar-chart-fill fs-5 text-secondary"></i>
                                </div>
                                <div class="my-1">
                                    <span class="fs-2 fw-bold text-dark">{{ number_format($grandTotal) }}</span>
                                </div>
                                <div class="small text-muted">
                                    100% Aduan
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Summary Table -->
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover table-sm text-center align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-start ps-3" style="width: 50%;">Status</th>
                                    <th style="width: 25%;">Jumlah Aduan</th>
                                    <th style="width: 25%;">Peratusan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($status as $stat)
                                    @php
                                        $cfg = $statusConfig[$stat->id] ?? [
                                            'badge' => 'bg-secondary text-white',
                                        ];
                                        $count = $statusSummary[$stat->id]['total'] ?? 0;
                                        $pct = $statusSummary[$stat->id]['percentage'] ?? 0;
                                    @endphp
                                    <tr>
                                        <td class="text-start ps-3">
                                            <span class="badge {{ $cfg['badge'] }} me-2">&bull;</span>
                                            <strong>{{ $stat->name }}</strong>
                                        </td>
                                        <td class="fw-bold">{{ number_format($count) }}</td>
                                        <td>{{ $pct }}%</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td class="text-start ps-3">Jumlah Keseluruhan</td>
                                    <td>{{ number_format($grandTotal) }}</td>
                                    <td>100%</td>
                                </tr>
                            </tfoot>
                        </table>
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
                            title: 'Statistik Aduan Umum Mengikut Kategori & Status'
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
