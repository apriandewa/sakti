<div class="modal-header">
    <h4 class="modal-title" id="modal-title-label">
        <i class="fa fa-map-marker text-danger me-10"></i> Detail Geolocation & Jam Presensi ({{ \Carbon\Carbon::parse($targetDate)->translatedFormat('d F Y') }})
    </h4>
    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-25">
    <div class="row">
        <!-- Kolom Check-in (Masuk) -->
        <div class="col-md-6 border-end-md mb-20 mb-md-0">
            <div class="card border shadow-none mb-0">
                <div class="card-header bg-success-light py-10">
                    <h5 class="card-title text-success mb-0"><i class="fa fa-sign-in"></i> PRESENSI MASUK (CHECK-IN)</h5>
                </div>
                <div class="card-body p-15">
                    @if($checkInLog)

                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="font-weight-bold" style="width: 120px;">Jam Presensi</td>
                                <td>: <strong class="text-success font-size-16">{{ $checkInLog['jam'] ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Status</td>
                                <td>: <span class="badge badge-success font-weight-bold">{{ $checkInLog['status'] ?? 'VALID' }}</span></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Akurasi GPS</td>
                                <td>: {{ $checkInLog['accuracy'] ?? '-' }} meter</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Tipe Perangkat</td>
                                <td>: {{ ($checkInLog['device_type'] ?? '') == '0' ? 'Mobile App' : 'Web Browser / Lainnya' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Koordinat</td>
                                <td>: <code>{{ $checkInLog['latitude'] ?? '' }}, {{ $checkInLog['longitude'] ?? '' }}</code></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Alamat Lokasi</td>
                                <td>: <span class="font-size-13 text-muted">{{ $checkInLog['address'] ?? '-' }}</span></td>
                            </tr>
                        </table>
                        @if(!empty($checkInLog['latitude']) && !empty($checkInLog['longitude']))
                            <div class="mt-15">
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $checkInLog['latitude'] }},{{ $checkInLog['longitude'] }}" 
                                   target="_blank" class="btn btn-sm btn-outline btn-success w-100">
                                    <i class="fa fa-map"></i> Buka di Google Maps
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-30 text-muted">
                            <i class="fa fa-times-circle fa-2x d-block mb-10 text-danger"></i>
                            Tidak ada log presensi masuk tercatat di hari ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Kolom Check-out (Pulang) -->
        <div class="col-md-6">
            <div class="card border shadow-none mb-0">
                <div class="card-header bg-danger-light py-10">
                    <h5 class="card-title text-danger mb-0"><i class="fa fa-sign-out"></i> PRESENSI PULANG (CHECK-OUT)</h5>
                </div>
                <div class="card-body p-15">
                    @if($checkOutLog)

                        <table class="table table-borderless table-sm mb-0">
                            <tr>
                                <td class="font-weight-bold" style="width: 120px;">Jam Presensi</td>
                                <td>: <strong class="text-danger font-size-16">{{ $checkOutLog['jam'] ?? '-' }}</strong></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Status</td>
                                <td>: <span class="badge badge-danger font-weight-bold">{{ $checkOutLog['status'] ?? 'VALID' }}</span></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Akurasi GPS</td>
                                <td>: {{ $checkOutLog['accuracy'] ?? '-' }} meter</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Tipe Perangkat</td>
                                <td>: {{ ($checkOutLog['device_type'] ?? '') == '0' ? 'Mobile App' : 'Web Browser / Lainnya' }}</td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Koordinat</td>
                                <td>: <code>{{ $checkOutLog['latitude'] ?? '' }}, {{ $checkOutLog['longitude'] ?? '' }}</code></td>
                            </tr>
                            <tr>
                                <td class="font-weight-bold">Alamat Lokasi</td>
                                <td>: <span class="font-size-13 text-muted">{{ $checkOutLog['address'] ?? '-' }}</span></td>
                            </tr>
                        </table>
                        @if(!empty($checkOutLog['latitude']) && !empty($checkOutLog['longitude']))
                            <div class="mt-15">
                                <a href="https://www.google.com/maps/search/?api=1&query={{ $checkOutLog['latitude'] }},{{ $checkOutLog['longitude'] }}" 
                                   target="_blank" class="btn btn-sm btn-outline btn-danger w-100">
                                    <i class="fa fa-map"></i> Buka di Google Maps
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-30 text-muted">
                            <i class="fa fa-times-circle fa-2x d-block mb-10 text-danger"></i>
                            Tidak ada log presensi pulang tercatat di hari ini.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Tutup</button>
</div>
