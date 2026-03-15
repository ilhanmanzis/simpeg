<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengabdians;
use App\Models\User;
use App\Services\GoogleDriveService;
use App\Services\SerdosService;
use Illuminate\Http\Request;

class Pengabdian extends Controller
{
    protected $googleDriveService;

    public function __construct(GoogleDriveService $googleDriveService)
    {
        $this->googleDriveService = $googleDriveService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $keyword = $request->get('dosen');
        $data = [
            'page' => 'BKD Pengabdian',
            'selected' => 'BKD Pengabdian',
            'title' => 'Data BKD Pengabdian',
            'dosens' => User::where('role', 'dosen')->with([
                'dataDiri',
            ])->when($keyword, function ($query) use ($keyword) {
                $query->searchDosen($keyword);
            })->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('admin.bkd.pengabdian.index', $data);
    }



    /**
     * Display the specified resource.
     */
    public function all(Request $request, string $id)
    {
        $judul = $request->get('judul');

        $user = User::where('id_user', $id)->with('dataDiri')->firstOrFail();
        $data = [
            'page' => 'BKD Pengabdian',
            'selected' => 'BKD Pengabdian',
            'title' => 'BKD Pengabdian ' . $user->dataDiri->name,
            'pengabdians' => Pengabdians::where('id_user', $id)
                ->searchJudul($judul)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString(),
            'user' => $user
        ];

        return view('admin.bkd.pengabdian.all', $data);
    }

    public function show(Request $request, string $id)
    {

        $pengabdian = Pengabdians::where('id_pengabdian', $id)->with(['user.dataDiri', 'permohonanPengabdian', 'tugasPengabdian', 'modulPengabdian', 'fotoPengabdian'])->firstOrFail();
        $data = [
            'page' => 'BKD Pengabdian',
            'selected' => 'BKD Pengabdian',
            'title' => 'BKD Pengabdian ' . $pengabdian->user->dataDiri->name,
            'pengabdian' => $pengabdian,
        ];
        return view('admin.bkd.pengabdian.show', $data);
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, SerdosService $serdosService)
    {
        $pengabdian = Pengabdians::where('id_pengabdian', $id)->with(['user.dataDiri', 'permohonanPengabdian', 'tugasPengabdian', 'modulPengabdian', 'fotoPengabdian'])->first();

        $idUser = $pengabdian->id_user;

        $this->googleDriveService->deleteById($pengabdian->permohonanPengabdian->file_id);
        $this->googleDriveService->deleteById($pengabdian->tugasPengabdian->file_id);
        $this->googleDriveService->deleteById($pengabdian->modulPengabdian->file_id);
        $this->googleDriveService->deleteById($pengabdian->fotoPengabdian->file_id);

        $pengabdian->delete();
        $serdosService->clearCache($pengabdian->user->id_user);

        return redirect()->route('admin.bkd.pengabdian.all', ['id' => $idUser])->with('success', 'Data BKD Pengabdian berhasil dihapus.');
    }
}
