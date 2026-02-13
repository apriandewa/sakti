@extends('frontend.main')

@section('container')

<main class="main">

<section id="statistik" class="section">

    <!-- Section Title -->
    <div class="container section-title text-center" data-aos="fade-up">
        <h2>Statistik Layanan Informasi</h2>
        <p>Statistik Layanan Informasi PPID Kabupaten Indragiri Hulu</p>
    </div>

    <div class="container">

        {{-- =========================
              TABEL RESPONSIVE
          ==========================--}}
          <div class="card shadow-sm border-0 mb-4" data-aos="fade-up">
              <div class="card-body">
                  <h5 class="card-title mb-3">Tabel Data Statistik</h5>

                  <div class="table-responsive">
                      <table class="table table-bordered table-hover align-middle text-center">
                          <thead class="table-primary">
                              <tr>
                                  <th>Tahun</th>
                                  <th>Pemohon</th>
                                  <th>Diminta</th>
                                  <th>Diberikan</th>
                                  <th>Ditolak</th>
                                  <th>Keterangan</th>
                              </tr>
                          </thead>

                          <tbody>
                              @forelse($statistik as $item)
                                  <tr>
                                      <td>{{ $item->tahun ?? '-' }}</td>
                                      <td>{{ number_format($item->pemohon ?? 0) }}</td>
                                      <td>{{ number_format($item->diminta ?? 0) }}</td>
                                      <td>{{ number_format($item->diberikan ?? 0) }}</td>
                                      <td>{{ number_format($item->ditolak ?? 0) }}</td>
                                      <td>{{ $item->keterangan ?? '-' }}</td>
                                  </tr>
                              @empty
                                  <tr>
                                      <td colspan="6">Data tidak tersedia</td>
                                  </tr>
                              @endforelse
                          </tbody>

                          {{-- =========================
                              TOTAL ROW
                          ==========================--}}
                          @if($statistik->count() > 0)
                          <tfoot class="table-light fw-bold">
                              <tr>
                                  <td>TOTAL</td>
                                  <td>{{ number_format($statistik->sum('pemohon')) }}</td>
                                  <td>{{ number_format($statistik->sum('diminta')) }}</td>
                                  <td>{{ number_format($statistik->sum('diberikan')) }}</td>
                                  <td>{{ number_format($statistik->sum('ditolak')) }}</td>
                                  <td>-</td>
                              </tr>
                          </tfoot>
                          @endif

                      </table>
                  </div>

              </div>
          </div>



        {{-- =========================
            GRAFIK (LEBIH PROPORSIONAL)
        ==========================--}}
        <div class="card shadow-sm border-0" data-aos="fade-up">
            <div class="card-body">
                <h5 class="card-title mb-3">Grafik Statistik per Tahun</h5>

                <div style="height:350px;">
                    <canvas id="statistikChart"></canvas>
                </div>
            </div>
        </div>

    </div>

</section>

</main>

{{-- =========================
    CHART JS
==========================--}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {

    const ctx = document.getElementById('statistikChart');

    const statistikData = @json($statistik);

    const labels = statistikData.map(item => item.tahun);
    const pemohon = statistikData.map(item => item.pemohon ?? 0);
    const diminta = statistikData.map(item => item.diminta ?? 0);
    const diberikan = statistikData.map(item => item.diberikan ?? 0);
    const ditolak = statistikData.map(item => item.ditolak ?? 0);

    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Pemohon',
                    data: pemohon,
                    backgroundColor: '#0d6efd',
                    borderRadius: 6
                },
                {
                    label: 'Diminta',
                    data: diminta,
                    backgroundColor: '#198754',
                    borderRadius: 6
                },
                {
                    label: 'Diberikan',
                    data: diberikan,
                    backgroundColor: '#ffc107',
                    borderRadius: 6
                },
                {
                    label: 'Ditolak',
                    data: ditolak,
                    backgroundColor: '#dc3545',
                    borderRadius: 6
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false, // penting agar mengikuti div height
            plugins: {
                legend: {
                    position: 'top'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });

});
</script>

@endsection
