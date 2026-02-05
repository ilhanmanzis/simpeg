<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Setting extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = [
            'page' => 'Setting',
            'selected' => 'Setting',
            'title' => 'Setting',
            'setting' => Settings::first()
        ];

        return view('admin.setting.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,svg,webp|max:2048',
            'register' => 'required|in:aktif,nonaktif',
        ]);

        $setting = Settings::first();
        $setting->name = $request->name;
        $setting->register = $request->register;


        if ($request->hasFile('logo')) {
            $logoName = time() . '.' . $request->logo->extension();
            $request->logo->storeAs('logo', $logoName, 'public');


            // Hapus logo lama jika ada
            if ($setting->logo && Storage::disk('public')->exists('logo/' . $setting->logo)) {
                Storage::disk('public')->delete('logo/' . $setting->logo);
            }

            $setting->logo = $logoName;
        }

        $setting->save();

        return redirect()->route('admin.setting')->with('success', 'Setting berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
