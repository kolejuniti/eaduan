@extends('layouts.admin')

@section('content')
<link href="https://cdn.datatables.net/v/bs5/jq-3.7.0/jszip-3.10.1/dt-2.1.0/b-3.1.0/b-colvis-3.1.0/b-html5-3.1.0/b-print-3.1.0/cr-2.0.3/datatables.min.css" rel="stylesheet">
<div class="container">
    <div class="row justify-content-center mb-3">
        <div class="col-md-12 col-sm-12">
            @if(session('msg_error'))
                <div class="alert alert-danger">
                    {{ session('msg_error') }}
                </div>
            @endif
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            <div class="col-md-12 col-sm-12">
                <div style="display: flex; justify-content: right; align-items: right;">
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">Tambah Pegawai</button>
                </div>
            </div>
            <!-- Modal -->
            <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h6 class="modal-title fw-bold" id="cancelModalLabel">Maklumat Pegawai</h6>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <form method="POST" action="{{ route('admin.user.register') }}">
                            @csrf
                            <div class="modal-body">
                                <div class="col-md-12 col-sm-12 mb-2">
                                    <div class="form-floating">
                                        <input type="text" name="name" id="name" class="form-control" placeholder="" required autofocus>
                                        <label for="name">Nama Pegawai</label>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 mb-2">
                                    <div class="form-floating">
                                        <input type="text" name="email" id="email" class="form-control" placeholder="" required autofocus>
                                        <label for="name">Email</label>
                                    </div>
                                </div>
                                <div class="col-md-12 col-sm-12 mb-2">
                                    <div class="form-floating">
                                        <select name="section" id="section" class="form-control" required>
                                            <option value="">Pilihan Bahagian / Unit</option>
                                            @foreach ($sections as $section)
                                                <option value="{{ $section->id }}">{{ $section->name }}</option>
                                            @endforeach
                                        </select>
                                        <label for="section">Jenis Bahagian / Unit</label>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-sm btn-secondary" data-bs-dismiss="modal">Tutup</button>
                                <button class="btn btn-sm btn-primary" type="submit">Daftar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="table-responsive">
        <table id="myTable" class="table table-bordered table-sm text-center">
            <thead class="table-dark">
                <tr>
                    <th>#</th>
                    <th>Nama Pegawai</th>
                    <th>Email</th>
                    <th>Bahagian / Unit</th>
                    <th>Aktif</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $item)
                <tr>
                    <td></td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->email }}</td>
                    <td>{{ $item->section }}</td>
                    <form method="POST" action="{{ route('admin.user.update', $item->id) }}" id="form-{{ $item->id }}">
                        @csrf
                        <td style="display: flex; justify-content: center; align-items: center;">
                            <input type="hidden" name="status" value="0">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="status" value="1" {{ $item->status === 1 ? 'checked' : '' }} 
                                       onchange="document.getElementById('form-{{ $item->id }}').submit();">
                            </div>
                        </td>
                    </form>
                </tr>
                @endforeach
            </tbody>
        </table>
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
                        html: '<h2>Senarai Pegawai</h2>'
                    }
                },
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
