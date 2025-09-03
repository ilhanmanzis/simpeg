<?php

namespace App\View\Components;

use App\Models\Settings;
use App\Models\User;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Header extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //

    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $data = [
            'user' => User::where('id_user', Auth::user()->id_user)->first(),
            'setting' => Settings::first()
        ];
        return view('components.header', $data);
    }
}
