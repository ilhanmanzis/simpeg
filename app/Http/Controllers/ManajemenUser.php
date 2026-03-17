<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ManajemenUser extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $id = Auth::user()->id_user;

        $data = [
            'selected' =>  'Edit Profile',
            'page' => 'Edit Profile',
            'title' => 'Edit Profile',
            'user' => User::find($id)
        ];

        return view('edit-profile', $data);
    }

    

    

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request)
    {
        $id = Auth::user()->id_user;
        $request->validate([
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',id_user',
            'password' => 'nullable|string|min:6',
        ]);

        $user = User::findOrFail($id);
        $user->email = $request->email;

        if ($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('users.index')->with('success', 'Data berhasil diperbarui');
    }

   
}
