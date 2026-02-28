<?php

namespace App\View\Components;

use App\Services\SerdosService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class DosenSidebar extends Component
{
    /**
     * Create a new component instance.
     */
    protected SerdosService $serdosService;

    public function __construct(SerdosService $serdosService)
    {
        $this->serdosService = $serdosService;
    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $id = Auth::user()->id_user;

        $userId = Auth::user()->id_user;

        $memenuhiSerdos = $this->serdosService->check($userId);


        return view('components.dosen-sidebar', compact('memenuhiSerdos'));
    }
}
