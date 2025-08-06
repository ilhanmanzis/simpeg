<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function admin()
    {
        // dd(Auth::user());
        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',

        ];
        return view('admin.dashboard', $data);
    }
    public function dosen()
    {
        // dd(Auth::user());
        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',

        ];
        return view('admin.dashboard', $data);
    }
    public function karyawan()
    {
        // dd(Auth::user());
        $data = [
            'page' => 'Dashboard',
            'selected' => 'Dashboard',
            'title' => 'Dashboard',

        ];
        return view('admin.dashboard', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
