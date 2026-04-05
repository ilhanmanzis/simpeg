<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CekJabatanStrukturalAktif
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        if (!$user || !$user->hasJabatanStrukturalAktif()) {
            abort(403, 'Unauthorized');
        }

        return $next($request);
    }
}
