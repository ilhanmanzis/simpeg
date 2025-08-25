<?php

namespace App\Http\Controllers;

use App\Models\Dokumens;
use App\Services\GoogleDriveService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    public function __construct(private GoogleDriveService $drive) {}

    public function showIjazah($filename)
    {
        $path = "pendidikan/ijazah/{$filename}";
        if (!Storage::exists($path)) {
            abort(404);
        }
        return Storage::response($path);
    }

    public function showSk($filename)
    {
        $path = "sk/{$filename}";
        if (!Storage::exists($path)) {
            abort(404);
        }
        return Storage::response($path);
    }

    public function showTranskip($filename)
    {
        $path = "pendidikan/transkipNilai/{$filename}";
        if (!Storage::exists($path)) {
            abort(404);
        }
        return Storage::response($path);
    }

    public function showFoto($filename)
    {
        $path = "register/{$filename}";
        if (!Storage::exists($path)) {
            abort(404);
        }
        $mime    = Storage::mimeType($path) ?: 'application/octet-stream';
        $content = Storage::get($path);

        return response($content, 200)->header('Content-Type', $mime);
    }

    public function showFotoPerubahan($filename)
    {
        $path = "perubahanProfile/{$filename}";
        if (!Storage::exists($path)) {
            abort(404);
        }
        $mime    = Storage::mimeType($path) ?: 'application/octet-stream';
        $content = Storage::get($path);

        return response($content, 200)->header('Content-Type', $mime);
    }

    /**
     * Tampilkan dokumen yang disimpan di Google Drive berdasarkan nomor_dokumen (FK ke tabel dokumens).
     * Menggunakan file_id (lebih akurat & cepat daripada path).
     */
    public function showFotoDrive(string $id)
    {
        $dok = Dokumens::where('nomor_dokumen', $id)->first();
        if (!$dok || !$dok->file_id) {
            // fallback: kalau belum ada file_id di data lama, arahkan ke link view (kalau ada) 
            if ($dok && $dok->view_url) {
                return redirect()->away($dok->view_url);
            }
            abort(404);
        }
        // Ambil stream dari Google Drive 
        $dl = $this->drive->downloadFileStream($dok->file_id); // Stream ke browser (tanpa load semua ke RAM) 
        return response()->stream(function () use ($dl) { // baca dari stream kecil-kecil agar hemat memori 
            $stream = $dl['body'];
            while (!$stream->eof()) {
                echo $stream->read(8192);
                flush();
            }
        }, 200, [
            'Content-Type' => $dl['mime'],
            'Content-Disposition' => 'inline; filename="' . addslashes($dl['name']) . '"', // opsional: caching header 
            'Cache-Control' => 'private, max-age=3600',
        ]);
    }
}
