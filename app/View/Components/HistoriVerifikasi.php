<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use App\Services\VerifikasiService;
use Illuminate\Support\Facades\Auth;

class HistoriVerifikasi extends Component
{
    protected $verifikasiService;

    public $verifiable_id;
    public $verifiable_type;
    public $histori;

    public function __construct($verifiableId = null, $verifiableType = null, $verifiable_id = null, $verifiable_type = null, $histori = null)
    {
        $this->verifiable_id = $verifiableId ?? $verifiable_id;
        $this->verifiable_type = $verifiableType ?? $verifiable_type;
        $this->histori = $histori;

        $this->verifikasiService = app(VerifikasiService::class);
    }

    public function render(): View|Closure|string
    {
        $histori = $this->histori;

        if (is_null($histori)) {
            $histori = $this->verifikasiService->getHistori(
                auth()->user(),
                $this->verifiable_id,
                $this->verifiable_type
            );
        }

        return view('components.histori-verifikasi', compact('histori'));
    }
}