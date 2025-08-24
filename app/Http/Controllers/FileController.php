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
            if ($dok && $dok->view_url) return redirect()->away($dok->view_url);
            abort(404);
        }

        // Ambil metadata utk ETag & Last-Modified
        $meta = $this->drive->getFileInfo($dok->file_id) ?? [];
        $modified = $meta['modifiedTime'] ?? null;
        $etag = '"' . sha1($dok->file_id . '|' . ($modified ?? '')) . '"';

        // Conditional GET: If-None-Match / If-Modified-Since
        $req = request();
        $headers304 = [
            'ETag'           => $etag,
            'Cache-Control'  => 'private, no-cache, no-store, must-revalidate, max-age=0',
            'Pragma'         => 'no-cache',
            'Expires'        => '0',
        ];
        if ($modified) {
            $headers304['Last-Modified'] = gmdate('D, d M Y H:i:s', strtotime($modified)) . ' GMT';
        }
        if ($req->headers->get('If-None-Match') === $etag) {
            return response('', 304, $headers304);
        }
        if ($modified && $req->headers->has('If-Modified-Since')) {
            $ims = strtotime($req->headers->get('If-Modified-Since'));
            if ($ims !== false && $ims >= strtotime($modified)) {
                return response('', 304, $headers304);
            }
        }

        // Ambil stream terbaru dari Google Drive
        $dl = $this->drive->downloadFileStream($dok->file_id);

        $respHeaders = [
            'Content-Type'        => $dl['mime'],
            'Content-Disposition' => 'inline; filename="' . addslashes($dl['name']) . '"',
            // MATIKAN cache agresif; pakai no-store + no-cache + must-revalidate
            'Cache-Control'       => 'private, no-cache, no-store, must-revalidate, max-age=0',
            'Pragma'              => 'no-cache',
            'Expires'             => '0',
            'ETag'                => $etag,
            // (opsional) bantu server/proxy
            'X-Accel-Buffering'   => 'no',
        ];
        if (isset($dl['size'])) {
            $respHeaders['Content-Length'] = (string) $dl['size'];
        }
        if ($modified) {
            $respHeaders['Last-Modified'] = gmdate('D, d M Y H:i:s', strtotime($modified)) . ' GMT';
        }

        return response()->stream(function () use ($dl) {
            $stream = $dl['body'];
            while (!$stream->eof()) {
                echo $stream->read(8192);
                flush();
            }
        }, 200, $respHeaders);
    }
}
