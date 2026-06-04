@inject('verifikasiService', 'App\Services\VerifikasiService')

@php
    $badgeClass = $verifikasiService->badgeStatus($status);

    // Ukuran badge
    $size = $size ?? 'md'; // default: medium

    $sizeClass = match($size) {
        'xs' => 'py-0 px-1 text-xs',
        'sm' => 'py-0.5 px-2 text-sm',
        'md' => 'py-1 px-3 text-base',
        default => 'py-1 px-3 text-base',
    };
@endphp

<span class="badge {{ $badgeClass }} {{ $sizeClass }}">
    {{ strtoupper($status) }}
</span>
