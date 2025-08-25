<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;
use Google\Http\MediaFileUpload;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;



class GoogleDriveService
{
    protected Client $googleClient;
    protected Drive $driveService;

    public function __construct()
    {
        $this->initializeGoogleClient();
    }

    private function initializeGoogleClient(): void
    {
        $this->googleClient = new Client([
            'application_name' => config('app.name', 'simpeg') . ' Drive Uploader',
        ]);

        $this->googleClient->setClientId(config('services.google_drive.client_id'));
        $this->googleClient->setClientSecret(config('services.google_drive.client_secret'));
        $this->googleClient->setAccessType('offline');
        $this->googleClient->setPrompt('consent'); // aman; tidak selalu memunculkan consent ulang
        $this->googleClient->setScopes(config('services.google_drive.scopes')); // drive.file

        $this->googleClient->setHttpClient(new GuzzleClient([
            'timeout'         => 300,
            'read_timeout'    => 300,
            'connect_timeout' => 10,
            'headers'         => ['Expect' => ''],
        ]));

        // 1) Ambil token dari cache kalau ada
        $cachedToken = Cache::get('gdrive:token');

        if (is_array($cachedToken) && !empty($cachedToken['access_token'])) {
            $this->googleClient->setAccessToken($cachedToken);
        } else {
            // 2) Bootstrap: refresh token dari storage persisten dulu, ENV terakhir
            $refresh = Cache::get('gdrive:refresh_token')
                ?? config('services.google_drive.refresh_token'); // fallback TERAKHIR (sebaiknya kosongkan di .env setelah sukses)

            if (!$refresh) {
                throw new \RuntimeException(
                    'Refresh token belum ada. Jalankan /oauth/google/redirect untuk menghubungkan Google Drive.'
                );
            }

            $new = $this->googleClient->fetchAccessTokenWithRefreshToken($refresh);
            if (isset($new['error'])) {
                throw new \RuntimeException('Failed to refresh token: ' . ($new['error_description'] ?? $new['error']));
            }

            $this->googleClient->setAccessToken($new);
            $this->cacheToken($this->googleClient->getAccessToken());

            // simpan refresh token baru jika Google merotasi
            if (!empty($new['refresh_token'])) {
                Cache::forever('gdrive:refresh_token', $new['refresh_token']);
            }
        }

        // 3) Pastikan token segar
        $this->ensureFreshToken(300);

        $this->driveService = new Drive($this->googleClient);
    }

    private function cacheToken(array $token): void
    {
        $current = Cache::get('gdrive:token', []);
        if (empty($token['refresh_token'])) {
            $token['refresh_token'] = $current['refresh_token'] ?? null;
        } else {
            // simpan persisten kalau ada refresh token baru
            Cache::forever('gdrive:refresh_token', $token['refresh_token']);
        }

        if (!isset($token['created'])) {
            $token['created'] = time();
        }
        $expiresIn = (int)($token['expires_in'] ?? 3600);
        $ttl       = max(60, $expiresIn - 120);

        Cache::put('gdrive:token', $token, $ttl);
    }


    private function ensureFreshToken(int $minRemainingSeconds = 300): void
    {
        // Ambil token saat ini (bisa kosong array)
        $tok = (array) $this->googleClient->getAccessToken();

        // Hitung sisa umur access token sekarang (antisipasi clock skew)
        $created   = isset($tok['created']) ? (int)$tok['created'] : (time() - 3600);
        $expiresIn = isset($tok['expires_in']) ? (int)$tok['expires_in'] : 3600;
        $remaining = ($created + $expiresIn) - time();

        // Jika masih cukup segar & tidak dianggap expired oleh client → selesai
        if ($remaining > $minRemainingSeconds && !$this->googleClient->isAccessTokenExpired()) {
            return;
        }

        // Ambil refresh token: dari token aktif → storage persisten → ENV (fallback TERAKHIR)
        $refresh = $tok['refresh_token']
            ?? Cache::get('gdrive:refresh_token')
            ?? config('services.google_drive.refresh_token');

        if (empty($refresh)) {
            // Tidak ada refresh token sama sekali → wajib reconnect
            Cache::forget('gdrive:token');
            throw new \RuntimeException('Refresh token tidak ditemukan. Silakan hubungkan ulang Google Drive.');
        }

        // Minta access token baru menggunakan refresh token
        $new = $this->googleClient->fetchAccessTokenWithRefreshToken($refresh);

        // Tangani error refresh
        if (isset($new['error'])) {
            $err = strtolower((string)$new['error']);
            // Kasus umum: refresh token invalid / dicabut / kedaluwarsa → perlu reconnect
            if ($err === 'invalid_grant' || str_contains($err, 'invalid')) {
                Cache::forget('gdrive:token');
                // Opsional: hapus juga refresh token persisten jika ingin “bersih total”
                // Cache::forget('gdrive:refresh_token');
                throw new \RuntimeException('Refresh token invalid/ditolak. Silakan hubungkan ulang Google Drive.');
            }

            // Error lain (jaringan, quota, dsb.)
            throw new \RuntimeException('Failed to refresh token: ' . ($new['error_description'] ?? $new['error']));
        }

        // Set access token baru ke client
        $this->googleClient->setAccessToken($new);

        // Simpan access token (TTL pendek) + pertahankan refresh token (kalau tidak dikirim ulang)
        $this->cacheToken($this->googleClient->getAccessToken());

        // Jika Google merotasi & memberi refresh_token baru → simpan persisten (tanpa TTL)
        if (!empty($new['refresh_token'])) {
            Cache::forever('gdrive:refresh_token', $new['refresh_token']);
        }
    }


    /**
     * Upload cepat: streaming + resumable, parent by folderId
     */
    public function uploadDirectFast(string $localPath, string $fileName, array $folderStructure = [], bool $makePublic = true): array
    {
        if (!is_file($localPath)) {
            throw new \InvalidArgumentException("File tidak ditemukan: $localPath");
        }

        $rootId = (string) config('services.google_drive.root_folder');
        if (!$rootId) {
            throw new \RuntimeException('Set GOOGLE_DRIVE_FOLDER_ID di .env.');
        }

        $parentId = $this->resolveFolderPathToId($folderStructure, $rootId);
        $fileId   = $this->uploadResumable($localPath, $fileName, $parentId);

        if ($makePublic) {
            $this->makeFilePublic($fileId);
        }

        return [
            'file_id'      => $fileId,
            'view_url'     => $this->getFileUrl($fileId),
            'download_url' => $this->getDownloadUrl($fileId),
            'preview_url'  => $this->getPreviewUrl($fileId),
        ];
    }

    private function uploadResumable(string $localPath, string $fileName, string $parentId): string
    {
        // Pastikan token cukup “segar” (≥5 menit sisa)
        $this->ensureFreshToken(300);

        $fileMeta = new DriveFile([
            'name'    => $fileName,
            'parents' => [$parentId],
        ]);

        $chunkSize = 10 * 1024 * 1024; // 10MB
        $fileSize  = filesize($localPath);

        $this->googleClient->setDefer(true);

        /** @var RequestInterface $request */   // <-- beri tahu Intelephense tipe sebenarnya
        $request = $this->driveService->files->create(
            $fileMeta,
            [
                'uploadType'        => 'resumable',
                'fields'            => 'id',
                'supportsAllDrives' => true,
            ]
        );

        $media = new MediaFileUpload(
            $this->googleClient,
            $request,
            'application/octet-stream',
            null,   // stream manual
            true,   // resumable
            $chunkSize
        );
        $media->setFileSize($fileSize);

        $handle = fopen($localPath, 'rb');
        $status = null;
        while (!feof($handle)) {
            $chunk  = fread($handle, $chunkSize);
            $status = $media->nextChunk($chunk);   // saat selesai -> DriveFile
        }
        fclose($handle);

        $this->googleClient->setDefer(false);

        if ($status instanceof DriveFile && $status->getId()) {
            return $status->getId();
        }

        throw new \RuntimeException('Gagal mendapatkan fileId setelah upload (resumable).');
    }

    private function resolveFolderPathToId(array $folders, string $rootId): string
    {
        $parent = $rootId;
        foreach ($folders as $name) {
            if (!$name) continue;
            $parent = $this->getOrCreateChildFolderId((string) $name, $parent);
        }
        return $parent;
    }

    private function getOrCreateChildFolderId(string $name, string $parentId): string
    {
        $key = 'gdrive:folder:' . $parentId . ':' . $name;
        return Cache::remember($key, 86400, function () use ($name, $parentId) {
            $resp = $this->driveService->files->listFiles([
                'q' => sprintf(
                    "name='%s' and mimeType='application/vnd.google-apps.folder' and '%s' in parents and trashed=false",
                    $this->escapeQuery($name),
                    $parentId
                ),
                'fields' => 'files(id,name)',
                'supportsAllDrives' => true,
                'pageSize' => 1,
            ]);
            $items = $resp->getFiles();
            if (!empty($items)) return $items[0]->getId();

            $folderMeta = new DriveFile([
                'name'     => $name,
                'mimeType' => 'application/vnd.google-apps.folder',
                'parents'  => [$parentId],
            ]);
            $created = $this->driveService->files->create($folderMeta, [
                'fields' => 'id',
                'supportsAllDrives' => true,
            ]);
            return $created->getId();
        });
    }

    public function makeFilePublic(string $fileId): bool
    {
        try {
            $perm = new Permission();
            $perm->setRole('reader');
            $perm->setType('anyone');
            $this->driveService->permissions->create($fileId, $perm, ['supportsAllDrives' => true]);
            return true;
        } catch (\Throwable $e) {
            logger()->warning('makeFilePublic failed: ' . $e->getMessage());
            return false;
        }
    }

    public function getFileUrl(string $fileId): string
    {
        return "https://drive.google.com/file/d/{$fileId}/view";
    }
    public function getDownloadUrl(string $fileId): string
    {
        return "https://drive.google.com/uc?export=download&id={$fileId}";
    }
    public function getPreviewUrl(string $fileId): string
    {
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    public function getFileInfo(string $fileId): ?array
    {
        try {
            $f = $this->driveService->files->get($fileId, [
                'fields' => 'id,name,mimeType,size,createdTime,modifiedTime,webViewLink,webContentLink,parents',
                'supportsAllDrives' => true,
            ]);
            return [
                'id'            => $f->getId(),
                'name'          => $f->getName(),
                'mimeType'      => $f->getMimeType(),
                'size'          => $f->getSize(),
                'createdTime'   => $f->getCreatedTime(),
                'modifiedTime'  => $f->getModifiedTime(),
                'webViewLink'   => $f->getWebViewLink(),
                'webContentLink' => $f->getWebContentLink(),
                'parents'       => $f->getParents(),
            ];
        } catch (\Throwable $e) {
            logger()->warning('getFileInfo failed: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteById(string $fileId): bool
    {
        $this->driveService->files->delete($fileId, ['supportsAllDrives' => true]);
        return true;
    }

    public function moveFileTo(string $fileId, array $folderStructure, ?string $newName = null): bool
    {
        $rootId = (string) config('services.google_drive.root_folder');
        $parentId = $this->resolveFolderPathToId($folderStructure, $rootId);

        $file = $this->driveService->files->get($fileId, [
            'fields' => 'id,parents',
            'supportsAllDrives' => true,
        ]);

        $parentsArr = $file->getParents() ?: [];
        $oldParents = $parentsArr ? implode(',', $parentsArr) : '';

        $params = [
            'addParents' => $parentId,
            'removeParents' => $oldParents,
            'supportsAllDrives' => true,
            'fields' => 'id',
        ];

        if ($newName !== null && $newName !== '') {
            $meta = new DriveFile(['name' => $newName]);
            $this->driveService->files->update($fileId, $meta, $params);
        } else {
            $this->driveService->files->update($fileId, new DriveFile(), $params);
        }
        return true;
    }

    private function escapeQuery(string $value): string
    {
        return str_replace("'", "\\'", $value);
    }

    /**
     * Kompatibilitas API lama kamu
     * $destinationPath: "folder1/folder2/nama.ext"
     */
    public function uploadFileAndGetUrl(string $localPath, string $destinationPath, bool $makePublic = true): array
    {
        $parts = array_values(array_filter(explode('/', $destinationPath)));
        $fileName = array_pop($parts) ?: (basename($localPath) ?: Str::uuid()->toString());
        return $this->uploadDirectFast($localPath, $fileName, $parts, $makePublic);
    }

    public function downloadFileStream(string $fileId): array
    {
        // 1) Ambil metadata untuk mime & nama
        $meta = $this->driveService->files->get($fileId, [
            'fields'            => 'id,mimeType,name',
            'supportsAllDrives' => true,
        ]);
        $mime = $meta->getMimeType() ?: 'application/octet-stream';
        $name = $meta->getName() ?: $fileId;

        // 2) Ambil konten (alt=media)
        $res = $this->driveService->files->get($fileId, [
            'alt'               => 'media',
            'supportsAllDrives' => true,
        ]);

        // 3) Normalisasi ke StreamInterface
        if ($res instanceof StreamInterface) {
            $body = $res;
            $size = $res->getSize() ?? 0;
        } elseif ($res instanceof ResponseInterface) {
            $body = $res->getBody();
            $size = $body->getSize() ?? 0;
        } else {
            // library kadang mengembalikan string (jarang); bungkus jadi stream
            $contents = (string) $res;
            $stream = \GuzzleHttp\Psr7\Utils::streamFor($contents);
            $body = $stream;
            $size = strlen($contents);
        }

        return [
            'mime' => $mime,
            'name' => $name,
            'body' => $body, // StreamInterface
            'size' => (int) $size,
        ];
    }
}
