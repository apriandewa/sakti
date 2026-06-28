<?php

namespace App\Http\Controllers\Backend\Documentation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

class DocumentationController extends Controller
{
    public function __construct()
    {
        $this->view = 'backend.documentation.';
    }

    private function renderMarkdown($filePath, $title)
    {
        $path = base_path($filePath);
        if (!File::exists($path)) {
            abort(404, 'Document not found.');
        }

        $content = File::get($path);
        $html = Str::markdown($content);

        return view($this->view . 'markdown', [
            'title' => $title,
            'html' => $html
        ]);
    }

    public function prdPortal()
    {
        return $this->renderMarkdown('docs/PRD_Portal_Diskominfotik.md', 'PRD Portal Diskominfotik');
    }

    public function prdPresensi()
    {
        return $this->renderMarkdown('docs/PRD_Presensi_Pegawai.md', 'PRD Presensi Pegawai');
    }

    public function planPortal()
    {
        return $this->renderMarkdown('docs/Implementation_Plan_Portal_Diskominfotik.md', 'Implementation Plan Portal');
    }

    public function planPresensi()
    {
        return $this->renderMarkdown('docs/Implementation_Plan_Presensi_Pegawai.md', 'Implementation Plan Presensi');
    }

    public function slides()
    {
        return view($this->view . 'slides', [
            'title' => 'Konsep / Rancangan Aplikasi'
        ]);
    }

    public function manualBook()
    {
        return view($this->view . 'manual_book', [
            'title' => 'Buku Petunjuk Penggunaan Aplikasi'
        ]);
    }
}
