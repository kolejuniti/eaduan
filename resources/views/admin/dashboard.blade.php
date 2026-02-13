@extends('layouts.admin')

@section('content')
<style>
    .admin-dashboard {
        --surface: #ffffff;
        --surface-soft: #f5f8fb;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --brand: #0f4c81;
        --brand-soft: #e9f2fb;
        --accent: #f59e0b;
        --success: #16a34a;
        --danger: #dc2626;
        color: var(--text-main);
    }

    .admin-dashboard .hero {
        background: linear-gradient(135deg, #0f4c81 0%, #1f6aa8 70%, #4f93cc 100%);
        border-radius: 16px;
        padding: 1.5rem;
        color: #fff;
        box-shadow: 0 12px 30px rgba(15, 76, 129, 0.2);
    }

    .admin-dashboard .metric-card {
        border: 0;
        border-radius: 14px;
        background: var(--surface);
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
        height: 100%;
    }

    .admin-dashboard .metric-icon {
        width: 42px;
        height: 42px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        background: var(--brand-soft);
        color: var(--brand);
        font-size: 1.1rem;
    }

    .admin-dashboard .metric-label {
        font-size: 0.8rem;
        letter-spacing: 0.03em;
        color: var(--text-muted);
        text-transform: uppercase;
        margin-bottom: 0.25rem;
    }

    .admin-dashboard .metric-value {
        font-size: 1.7rem;
        font-weight: 700;
        line-height: 1;
    }

    .admin-dashboard .summary-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .admin-dashboard .list-table th {
        font-size: 0.7rem;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        color: var(--text-muted);
        white-space: nowrap;
    }

    .admin-dashboard .list-table td {
        font-size: 0.76rem;
        vertical-align: middle;
    }

    .admin-dashboard .status-badge {
        border-radius: 999px;
        font-weight: 600;
        font-size: 0.72rem;
        padding: 0.3rem 0.65rem;
    }

    .admin-dashboard .bg-open {
        background: #e0f2fe;
        color: #0369a1;
    }

    .admin-dashboard .bg-progress {
        background: #fef3c7;
        color: #92400e;
    }

    .admin-dashboard .bg-closed {
        background: #dcfce7;
        color: #166534;
    }

    .admin-dashboard .bg-cancelled {
        background: #fee2e2;
        color: #991b1b;
    }

    .admin-dashboard .quick-link {
        border: 1px solid #dbe6f2;
        border-radius: 10px;
        padding: 0.55rem 0.85rem;
        text-decoration: none;
        color: var(--brand);
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .admin-dashboard .quick-link:hover {
        background: var(--brand-soft);
        border-color: #bdd3ea;
    }

    .admin-dashboard .quick-link-damage {
        color: #0b5ed7;
        border-color: #b7d4ff;
        background: #eef5ff;
    }

    .admin-dashboard .quick-link-damage:hover {
        background: #dceaff;
        border-color: #9dc4ff;
        color: #0849aa;
    }

    .admin-dashboard .quick-link-general {
        color: #0f766e;
        border-color: #b8ede8;
        background: #ecfdfb;
    }

    .admin-dashboard .quick-link-general:hover {
        background: #d3f7f2;
        border-color: #99e7df;
        color: #0a5c56;
    }

    .admin-dashboard .quick-link-report {
        color: #b45309;
        border-color: #fde0b7;
        background: #fffbeb;
    }

    .admin-dashboard .quick-link-report:hover {
        background: #fef1d1;
        border-color: #fbd18f;
        color: #92400e;
    }

    .admin-dashboard .chart-card {
        border: 0;
        border-radius: 14px;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.08);
    }

    .admin-dashboard .mini-chart-wrap {
        position: relative;
        height: 180px;
    }

    .admin-dashboard .range-toggle .btn {
        border-color: #c8d7e8;
        color: #33567a;
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.2rem 0.6rem;
    }

    .admin-dashboard .range-toggle .btn.active {
        background: #0f4c81;
        border-color: #0f4c81;
        color: #fff;
    }
</style>

<div class="container admin-dashboard">
    <div class="hero mb-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-1 fw-bold">Admin Dashboard</h4>
                <p class="mb-0 opacity-75">Selamat datang, {{ Auth::user()->name }}. Ringkasan aduan semasa dipaparkan di bawah.</p>
            </div>
            <div class="d-flex flex-wrap gap-2">
                <a href="{{ route('admin.damagecomplaint') }}" class="quick-link quick-link-damage"><i class="bi bi-tools me-1"></i> Aduan Kerosakan</a>
                <a href="{{ route('admin.generalcomplaint') }}" class="quick-link quick-link-general"><i class="bi bi-chat-left-text me-1"></i> Aduan Umum</a>
                <a href="{{ route('admin.damageReport') }}" class="quick-link quick-link-report"><i class="bi bi-bar-chart me-1"></i> Laporan</a>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="metric-label">Jumlah Aduan</span>
                        <span class="metric-icon"><i class="bi bi-collection"></i></span>
                    </div>
                    <div class="metric-value">{{ number_format($stats['all_total']) }}</div>
                    <small class="text-muted">Kerosakan + Umum</small>
                </div>
            </div>
        </div>
        <div class="col-lg-3 col-md-6">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <span class="metric-label">Aduan Terbuka</span>
                        <span class="metric-icon" style="background:#fff3cd;color:#8a6d3b;"><i class="bi bi-hourglass-split"></i></span>
                    </div>
                    <div class="metric-value">{{ number_format($stats['open_total']) }}</div>
                    <small class="text-muted">Status baru / dalam tindakan</small>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="metric-label">Hari Ini</div>
                    <div class="metric-value">{{ number_format($stats['today_total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="metric-label">Minggu Ini</div>
                    <div class="metric-value">{{ number_format($stats['week_total']) }}</div>
                </div>
            </div>
        </div>
        <div class="col-lg-2 col-md-4">
            <div class="card metric-card">
                <div class="card-body">
                    <div class="metric-label">Bulan Ini</div>
                    <div class="metric-value">{{ number_format($stats['month_total']) }}</div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-lg-6">
            <div class="card summary-card">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="mb-0 fw-bold">Ringkasan Aduan Kerosakan</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-3"><div class="p-2 rounded bg-open"><small>Baru</small><div class="fw-bold">{{ $stats['damage_new'] }}</div></div></div>
                        <div class="col-3"><div class="p-2 rounded bg-progress"><small>Tindakan</small><div class="fw-bold">{{ $stats['damage_progress'] }}</div></div></div>
                        <div class="col-3"><div class="p-2 rounded bg-closed"><small>Selesai</small><div class="fw-bold">{{ $stats['damage_completed'] }}</div></div></div>
                        <div class="col-3"><div class="p-2 rounded bg-cancelled"><small>Batal</small><div class="fw-bold">{{ $stats['damage_cancelled'] }}</div></div></div>
                    </div>
                    <div class="mt-3 small text-muted">Jumlah keseluruhan: <strong>{{ $stats['damage_total'] }}</strong></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card summary-card">
                <div class="card-header bg-white border-0 pt-3">
                    <h6 class="mb-0 fw-bold">Ringkasan Aduan Umum</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-3"><div class="p-2 rounded bg-open"><small>Baru</small><div class="fw-bold">{{ $stats['general_new'] }}</div></div></div>
                        <div class="col-3"><div class="p-2 rounded bg-progress"><small>Tindakan</small><div class="fw-bold">{{ $stats['general_progress'] }}</div></div></div>
                        <div class="col-3"><div class="p-2 rounded bg-closed"><small>Selesai</small><div class="fw-bold">{{ $stats['general_completed'] }}</div></div></div>
                        <div class="col-3"><div class="p-2 rounded bg-cancelled"><small>Batal</small><div class="fw-bold">{{ $stats['general_cancelled'] }}</div></div></div>
                    </div>
                    <div class="mt-3 small text-muted">Jumlah keseluruhan: <strong>{{ $stats['general_total'] }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <div class="col-12 d-flex justify-content-end">
            <div class="btn-group btn-group-sm range-toggle" role="group" aria-label="Trend Range">
                <button type="button" class="btn btn-outline-secondary trend-range-btn" data-days="7">7 Hari</button>
                <button type="button" class="btn btn-outline-secondary trend-range-btn active" data-days="14">14 Hari</button>
                <button type="button" class="btn btn-outline-secondary trend-range-btn" data-days="30">30 Hari</button>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold" id="damageTrendTitle">Trend 14 Hari: Kerosakan</h6>
                    <span class="small text-muted">Aduan harian</span>
                </div>
                <div class="card-body pt-2">
                    <div class="mini-chart-wrap">
                        <canvas id="damageTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card chart-card">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold" id="generalTrendTitle">Trend 14 Hari: Umum</h6>
                    <span class="small text-muted">Aduan harian</span>
                </div>
                <div class="card-body pt-2">
                    <div class="mini-chart-wrap">
                        <canvas id="generalTrendChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-6">
            <div class="card summary-card">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Aduan Kerosakan Terkini</h6>
                    <a href="{{ route('admin.damagecomplaint') }}" class="small text-decoration-none">Lihat semua</a>
                </div>
                <div class="card-body pt-2">
                    <div class="table-responsive">
                        <table class="table table-sm list-table align-middle">
                            <thead>
                                <tr>
                                    <th>Pengadu</th>
                                    <th>Kategori</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Masa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestDamageComplaints as $item)
                                    @php
                                        $statusName = strtolower($item->status_name ?? 'baru');
                                        $statusClass = str_contains($statusName, 'selesai') ? 'bg-closed' : (str_contains($statusName, 'batal') ? 'bg-cancelled' : (str_contains($statusName, 'tindakan') ? 'bg-progress' : 'bg-open'));
                                    @endphp
                                    <tr>
                                        <td>{{ $item->complainant_name }}</td>
                                        <td>{{ $item->complaint_type }}</td>
                                        <td>{{ $item->block ?? '-' }} / {{ $item->no_unit ?? '-' }}</td>
                                        <td><span class="status-badge {{ $statusClass }}">{{ $item->status_name ?? 'Baru' }}</span></td>
                                        <td class="text-nowrap">{{ $item->reported_at }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Tiada aduan kerosakan.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card summary-card">
                <div class="card-header bg-white border-0 pt-3 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold">Aduan Umum Terkini</h6>
                    <a href="{{ route('admin.generalcomplaint') }}" class="small text-decoration-none">Lihat semua</a>
                </div>
                <div class="card-body pt-2">
                    <div class="table-responsive">
                        <table class="table table-sm list-table align-middle">
                            <thead>
                                <tr>
                                    <th>Pengadu</th>
                                    <th>Jenis Aduan</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Masa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($latestGeneralComplaints as $item)
                                    @php
                                        $statusName = strtolower($item->status_name ?? 'baru');
                                        $statusClass = str_contains($statusName, 'selesai') ? 'bg-closed' : (str_contains($statusName, 'batal') ? 'bg-cancelled' : (str_contains($statusName, 'tindakan') ? 'bg-progress' : 'bg-open'));
                                    @endphp
                                    <tr>
                                        <td>{{ $item->complainant_name }}</td>
                                        <td>{{ $item->complaint_type }}</td>
                                        <td>{{ $item->location ?? '-' }}</td>
                                        <td><span class="status-badge {{ $statusClass }}">{{ $item->status_name ?? 'Baru' }}</span></td>
                                        <td class="text-nowrap">{{ $item->reported_at }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-3">Tiada aduan umum.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.6/dist/chart.umd.min.js"></script>
<script>
    (() => {
        const labelsFull = @json($trendLabels);
        const damageDataFull = @json($damageTrendData);
        const generalDataFull = @json($generalTrendData);

        const sharedOptions = {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: '#0f172a',
                    padding: 10,
                    titleFont: { size: 12 },
                    bodyFont: { size: 12 }
                }
            },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { maxTicksLimit: 7, color: '#6b7280', font: { size: 11 } }
                },
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0, color: '#6b7280', font: { size: 11 } },
                    grid: { color: 'rgba(148, 163, 184, 0.2)' }
                }
            },
            elements: {
                line: { tension: 0.35, borderWidth: 2.5 },
                point: { radius: 0, hoverRadius: 4 }
            }
        };

        const damageChart = new Chart(document.getElementById('damageTrendChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Aduan Kerosakan',
                    data: [],
                    borderColor: '#0f4c81',
                    backgroundColor: 'rgba(15, 76, 129, 0.18)',
                    fill: true
                }]
            },
            options: sharedOptions
        });

        const generalChart = new Chart(document.getElementById('generalTrendChart'), {
            type: 'line',
            data: {
                labels: [],
                datasets: [{
                    label: 'Aduan Umum',
                    data: [],
                    borderColor: '#f59e0b',
                    backgroundColor: 'rgba(245, 158, 11, 0.2)',
                    fill: true
                }]
            },
            options: sharedOptions
        });

        const damageTrendTitle = document.getElementById('damageTrendTitle');
        const generalTrendTitle = document.getElementById('generalTrendTitle');
        const rangeButtons = document.querySelectorAll('.trend-range-btn');

        const setRange = (days) => {
            const labels = labelsFull.slice(-days);
            const damageData = damageDataFull.slice(-days);
            const generalData = generalDataFull.slice(-days);

            damageChart.data.labels = labels;
            damageChart.data.datasets[0].data = damageData;
            damageChart.update();

            generalChart.data.labels = labels;
            generalChart.data.datasets[0].data = generalData;
            generalChart.update();

            damageTrendTitle.textContent = `Trend ${days} Hari: Kerosakan`;
            generalTrendTitle.textContent = `Trend ${days} Hari: Umum`;
        };

        rangeButtons.forEach((btn) => {
            btn.addEventListener('click', () => {
                rangeButtons.forEach((item) => item.classList.remove('active'));
                btn.classList.add('active');
                setRange(Number(btn.dataset.days));
            });
        });

        setRange(14);
    })();
</script>
@endsection
