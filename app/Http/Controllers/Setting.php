<?php

namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Models\SettingLokasiPresensi;

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
            'setting' => Settings::first(),
            'lokasi' => SettingLokasiPresensi::first()
        ];

        return view('admin.setting.index', $data);
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


    public function updateLokasiPresensi(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meter' => 'required|integer|min:1',
        ]);

        // karena hanya 1 record
        $lokasi = SettingLokasiPresensi::first();

        if (!$lokasi) {
            $lokasi = new SettingLokasiPresensi();
        }

        $lokasi->latitude = $request->latitude;
        $lokasi->longitude = $request->longitude;
        $lokasi->radius_meter = $request->radius_meter;
        $lokasi->save();

        return redirect()
            ->route('admin.setting')
            ->with('success', 'Setting lokasi presensi berhasil diperbarui.');
    }
}
