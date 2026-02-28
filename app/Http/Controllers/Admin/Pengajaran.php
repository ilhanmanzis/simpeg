<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengajarans;
use App\Models\User;
use App\Services\GoogleDriveService;
use App\Services\SerdosService;
use Illuminate\Http\Request;

class Pengajaran extends Controller
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
            'page' => 'BKD Pengajaran',
            'selected' => 'BKD Pengajaran',
            'title' => 'Data BKD Pengajaran',
            'dosens' => User::where('role', 'dosen')->with([
                'dataDiri',
            ])->when($keyword, function ($query) use ($keyword) {
                $query->searchDosen($keyword);
            })->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('admin.bkd.pengajaran.index', $data);
    }


    /**
     * Display the specified resource.
     */
    public function all(Request $request, string $id)
    {
        $user = User::where('id_user', $id)->with('dataDiri')->firstOrFail();
        $data = [
            'page' => 'BKD Pengajaran',
            'selected' => 'BKD Pengajaran',
            'title' => 'BKD Pengajaran ' . $user->dataDiri->name,
            'pengajarans' => Pengajarans::where('id_user', $id)->with('semester')
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString(),
            'user' => $user
        ];

        return view('admin.bkd.pengajaran.all', $data);
    }

    public function show(string $id)
    {

        $pengajaran = Pengajarans::where('id_pengajaran', $id)->with(['user.dataDiri', 'skPengajaran', 'detail.nilaiPengajaran', 'semester'])->firstOrFail();

        $data = [
            'page' => 'BKD Pengajaran',
            'selected' => 'BKD Pengajaran',
            'title' => 'BKD Pengajaran ' . $pengajaran->user->dataDiri->name,
            'pengajaran' => $pengajaran,
        ];
        return view('admin.bkd.pengajaran.show', $data);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, SerdosService $serdosService)
    {
        $pengajaran = Pengajarans::where('id_pengajaran', $id)->with(['user.dataDiri', 'skPengajaran', 'detail.nilaiPengajaran', 'semester'])->firstOrFail();

        $idUser = $pengajaran->id_user;

        $this->googleDriveService->deleteById($pengajaran->skPengajaran->file_id);
        foreach ($pengajaran->detail as $detail) {
            $this->googleDriveService->deleteById($detail->nilaiPengajaran->file_id);
        }

        $pengajaran->delete();
        $serdosService->clearCache($pengajaran->user->id_user);

        return redirect()->route('admin.bkd.pengajaran.all', ['id' => $idUser])->with('success', 'Data BKD Pengajaran berhasil dihapus.');
    }
}
