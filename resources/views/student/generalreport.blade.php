@extends('layouts.student')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12 col-sm-12 col-12">
            <div class="table-responsive">
                <table id="myTable" class="table table-bordered small table-sm text-center">
                    <caption>Aduan umum akan diselesaikan dalam tempoh masa 7 hari bekerja.</caption>
                    <thead class="table-dark">
                        <tr>
                            <th>#</th>
                            <th>Tarikh & Masa</th>
                            <th>Bahagian / Unit</th>
                            <th>Jenis Aduan</th>
                            <th>Tarikh Terima</th>
                            <th>Tarikh Tindakan</th>
                            <th>Tindakan</th>
                            <th>Status</th>
                            <th>Sebab Batal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ( $generalLists as $generalList )
                        <tr>
                            <td style="width: 1%;"></td>
                            <td class="text-center" style="width: 10%;">{{ $generalList->date_of_complaint }}</td>
                            <td style="width: 10%;">{{ $generalList->section }}</td>
                            <td style="width: 10%;">{{ $generalList->complaint_types }}</td>
                            <td style="width: 10%;">{{ $generalList->date_of_receipt }}</td>
                            <td style="width: 10%;">{{ $generalList->date_of_action }}</td>
                            <td style="width: 20%;">{{ $generalList->action_notes }}</td>
                            <td style="width: 1%;">{{ $generalList->status }}</td>
                            <td style="width: 20%;">{{ $data->cancel_notes }}</td>
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
                // top1End: {
                //     buttons: [
                //         {
                //             extend: 'copy',
                //             title: 'Senarai Permohonan Pelajar'
                //         },
                //         {
                //             extend: 'excelHtml5',
                //             title: 'Senarai Permohonan Pelajar'
                //         },
                //         {
                //             extend: 'pdfHtml5',
                //             title: 'Senarai Permohonan Pelajar'
                //         },
                //         {
                //             extend: 'print',
                //             title: 'Senarai Permohonan Pelajar'
                //         }
                //     ]
                // },
                topStart: 'pageLength',
                topEnd: 'search',
                bottomStart: 'info',
                bottomEnd: 'paging'
            }
        });
        t.on('order.dt search.dt', function () {
            let i = 1;
        
            t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
                this.data(i++);
            });
        }).draw();
    });
</script>
@endsection
