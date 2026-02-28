<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Penunjangs;
use App\Models\User;
use App\Services\GoogleDriveService;
use App\Services\SerdosService;
use Illuminate\Http\Request;

class Penunjang extends Controller
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
            'page' => 'BKD Penunjang',
            'selected' => 'BKD Penunjang',
            'title' => 'Data BKD Penunjang',
            'dosens' => User::where('role', 'dosen')->with([
                'dataDiri',
            ])->when($keyword, function ($query) use ($keyword) {
                $query->searchDosen($keyword);
            })->orderBy('created_at', 'desc')->paginate(10)->withQueryString()
        ];

        return view('admin.bkd.penunjang.index', $data);
    }



    /**
     * Display the specified resource.
     */
    public function all(Request $request, string $id)
    {
        $judul = $request->get('judul');
        $user = User::where('id_user', $id)->with('dataDiri')->firstOrFail();
        $data = [
            'page' => 'BKD Penunjang',
            'selected' => 'BKD Penunjang',
            'title' => 'BKD Penunjang ' . $user->dataDiri->name,
            'penunjangs' => Penunjangs::where('id_user', $id)
                ->searchJudul($judul)
                ->orderBy('created_at', 'desc')
                ->paginate(10)
                ->withQueryString(),
            'user' => $user
        ];

        return view('admin.bkd.penunjang.all', $data);
    }

    public function show(string $id)
    {

        $penunjang = Penunjangs::where('id_penunjang', $id)->with(['user.dataDiri', 'dokumenPenunjang'])->firstOrFail();

        $data = [
            'page' => 'BKD Penunjang',
            'selected' => 'BKD Penunjang',
            'title' => 'BKD Penunjang ' . $penunjang->user->dataDiri->name,
            'penunjang' => $penunjang,
        ];
        return view('admin.bkd.penunjang.show', $data);
    }



    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id, SerdosService $serdosService)
    {
        $penunjang = Penunjangs::where('id_penunjang', $id)->with(['user.dataDiri', 'dokumenPenunjang'])->firstOrFail();

        $idUser = $penunjang->id_user;

        $this->googleDriveService->deleteById($penunjang->dokumenPenunjang->file_id);

        $penunjang->delete();
        $serdosService->clearCache($penunjang->user->id_user);

        return redirect()->route('admin.bkd.penunjang.all', ['id' => $idUser])->with('success', 'Data BKD Penunjang berhasil dihapus.');
    }
}
