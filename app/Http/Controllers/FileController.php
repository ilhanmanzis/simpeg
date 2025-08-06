<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class FileController extends Controller
{
    public function showIjazah($filename)
    {
        $path = "pendidikan/ijazah/{$filename}";

        if (!Storage::exists($path)) {
            abort(404);
        }

        // return Storage::download($path); // atau response()->file(...) untuk inline
        return Storage::response($path);
    }
    public function showTranskip($filename)
    {
        $path = "pendidikan/transkipNilai/{$filename}";

        if (!Storage::exists($path)) {
            abort(404);
        }

        // return Storage::download($path); // atau response()->file(...) untuk inline
        return Storage::response($path);
    }

    public function showFoto($filename)
    {
        $path = "register/{$filename}";

        if (!Storage::exists($path)) {
            abort(404);
        }

        $mime = Storage::mimeType($path);
        $content = Storage::get($path);

        return response($content, 200)->header('Content-Type', $mime);
    }
}
