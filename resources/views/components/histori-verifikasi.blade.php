<div class="verifikasi-timeline ms-2 border-start border-2 border-primary border-opacity-25 pb-1 mb-4">
    <h6 class="mb-3 ms-3 text-uppercase fw-bold text-muted small" style="letter-spacing: 0.5px;">
        <i class="fa fa-history me-1"></i> Riwayat Verifikasi
    </h6>
    
    @forelse($histori as $item)
        <div class="position-relative ms-4 mb-4">
            <!-- Timeline Dot -->
            <div class="position-absolute bg-white rounded-circle shadow-sm border border-2 border-primary" 
                 style="width: 16px; height: 16px; left: -25px; top: 4px;"></div>
            
            <!-- Timeline Card -->
            <div class="card shadow-sm border-0 border-top border-3 border-primary">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-light">
                        <x-status-badge :status="$item->status" size="xs" />
                        <span class="badge bg-light text-dark border">
                            <i class="fa fa-clock-o me-1 text-muted"></i> {{ $item->updated_at->format('d M Y H:i') }}
                        </span>
                    </div>

                    @if($item->user)
                        <div class="d-flex align-items-center mb-2 mt-2">
                            <div class="bg-light rounded-circle p-2 me-2 d-flex align-items-center justify-content-center border" style="width: 32px; height: 32px;">
                                <i class="fa fa-user text-primary"></i>
                            </div>
                            <span class="fw-semibold text-dark">{{ $item->user->name }}</span>
                        </div>
                    @endif

                    @if(!empty($item->catatan))
                        <div class="bg-light p-2 rounded mt-2 border-start border-3 border-info">
                            <p class="mb-0 small text-secondary">
                                <i class="fa fa-quote-left text-muted me-1"></i>
                                <em>{{ $item->catatan }}</em>
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @empty
        <div class="ms-4">
            <div class="alert alert-light border border-dashed text-center p-3">
                <i class="fa fa-info-circle text-muted mb-2 fs-4"></i>
                <p class="text-muted mb-0 small">Belum ada histori verifikasi.</p>
            </div>
        </div>
    @endforelse
</div>
