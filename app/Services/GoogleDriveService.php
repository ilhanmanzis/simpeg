<?php

namespace App\Services;

use Google\Client;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile;
use Google\Service\Drive\Permission;
use Yaza\LaravelGoogleDriveStorage\Gdrive;

class GoogleDriveService
{
    protected $googleClient;
    protected $driveService;

    public function __construct()
    {
        $this->initializeGoogleClient();
    }

    private function initializeGoogleClient()
    {
        $this->googleClient = new Client();
        $this->googleClient->setClientId(config('filesystems.disks.google.clientId'));
        $this->googleClient->setClientSecret(config('filesystems.disks.google.clientSecret'));
        $this->googleClient->addScope(Drive::DRIVE_FILE);

        // Set access token dengan refresh token
        $accessToken = [
            'access_token' => config('filesystems.disks.google.accessToken'),
            'refresh_token' => config('filesystems.disks.google.refreshToken')
        ];

        $this->googleClient->setAccessToken($accessToken);

        // Refresh token jika expired
        if ($this->googleClient->isAccessTokenExpired()) {
            $this->googleClient->fetchAccessTokenWithRefreshToken(config('filesystems.disks.google.refreshToken'));
        }

        $this->driveService = new Drive($this->googleClient);
    }

    /**
     * Upload file menggunakan Gdrive helper dan dapatkan URL
     */
    public function uploadFileAndGetUrl($localPath, $destinationPath)
    {
        try {
            // Upload file menggunakan Gdrive helper
            Gdrive::put($destinationPath, $localPath);

            // Dapatkan file ID dari Google Drive
            $fileId = $this->getFileIdByPath($destinationPath);

            if (!$fileId) {
                throw new \Exception('File ID tidak ditemukan setelah upload');
            }

            // Set file permissions untuk public access
            $this->makeFilePublic($fileId);

            // Generate URLs
            return [
                'file_id' => $fileId,
                'view_url' => $this->getFileUrl($fileId),
                'download_url' => $this->getDownloadUrl($fileId),
                'preview_url' => $this->getPreviewUrl($fileId),
            ];
        } catch (\Exception $e) {
            throw new \Exception('Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Upload file langsung menggunakan Google Drive API
     */
    public function uploadDirectlyToGDrive($localPath, $fileName, $folderStructure)
    {
        try {
            if (!file_exists($localPath)) {
                throw new \Exception('File tidak ditemukan: ' . $localPath);
            }

            // Buat folder structure jika belum ada
            $folderId = $this->createFolderStructure($folderStructure);

            // Upload file
            $fileMetadata = new DriveFile([
                'name' => $fileName,
                'parents' => [$folderId]
            ]);

            $content = file_get_contents($localPath);
            $mimeType = mime_content_type($localPath);

            $file = $this->driveService->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => $mimeType,
                'uploadType' => 'multipart',
                'fields' => 'id'
            ]);

            $fileId = $file->getId();

            // Make file public
            $this->makeFilePublic($fileId);

            return [
                'file_id' => $fileId,
                'view_url' => $this->getFileUrl($fileId),
                'download_url' => $this->getDownloadUrl($fileId),
                'preview_url' => $this->getPreviewUrl($fileId),
            ];
        } catch (\Exception $e) {
            throw new \Exception('Direct upload failed: ' . $e->getMessage());
        }
    }

    private function getFileIdByPath($path)
    {
        try {
            // Pisahkan path menjadi folder dan filename
            $pathParts = explode('/', $path);
            $filename = array_pop($pathParts);

            // Mulai dari root folder (simpeg)
            $parentId = $this->getFolderId('simpeg');

            // Navigasi ke folder yang tepat
            foreach ($pathParts as $folderName) {
                $parentId = $this->getFolderId($folderName, $parentId);
                if (!$parentId) {
                    return null;
                }
            }

            // Cari file dalam folder terakhir
            $query = "name='{$filename}' and '{$parentId}' in parents and trashed=false";
            $response = $this->driveService->files->listFiles([
                'q' => $query,
                'fields' => 'files(id, name)'
            ]);

            $files = $response->getFiles();
            return count($files) > 0 ? $files[0]->getId() : null;
        } catch (\Exception $e) {
            // \Log::error('Error getting file ID: ' . $e->getMessage());
            return null;
        }
    }

    private function getFolderId($folderName, $parentId = null)
    {
        try {
            $query = "name='{$folderName}' and mimeType='application/vnd.google-apps.folder' and trashed=false";

            if ($parentId) {
                $query .= " and '{$parentId}' in parents";
            }

            $response = $this->driveService->files->listFiles([
                'q' => $query,
                'fields' => 'files(id, name)'
            ]);

            $folders = $response->getFiles();
            return count($folders) > 0 ? $folders[0]->getId() : null;
        } catch (\Exception $e) {
            // \Log::error('Error getting folder ID: ' . $e->getMessage());
            return null;
        }
    }

    private function makeFilePublic($fileId)
    {
        try {
            $permission = new Permission();
            $permission->setRole('reader');
            $permission->setType('anyone');

            $this->driveService->permissions->create($fileId, $permission);
            return true;
        } catch (\Exception $e) {
            // \Log::error('Error making file public: ' . $e->getMessage());
            return false;
        }
    }

    private function getFileUrl($fileId)
    {
        return "https://drive.google.com/file/d/{$fileId}/view";
    }

    private function getDownloadUrl($fileId)
    {
        return "https://drive.google.com/uc?export=download&id={$fileId}";
    }

    private function getPreviewUrl($fileId)
    {
        return "https://drive.google.com/file/d/{$fileId}/preview";
    }

    private function createFolderStructure($folders)
    {
        $parentId = null;

        foreach ($folders as $folderName) {
            $existingFolderId = $this->getFolderId($folderName, $parentId);

            if ($existingFolderId) {
                $parentId = $existingFolderId;
            } else {
                // Buat folder baru
                $folderMetadata = new DriveFile([
                    'name' => $folderName,
                    'mimeType' => 'application/vnd.google-apps.folder'
                ]);

                if ($parentId) {
                    $folderMetadata->setParents([$parentId]);
                }

                $folder = $this->driveService->files->create($folderMetadata, [
                    'fields' => 'id'
                ]);

                $parentId = $folder->getId();
            }
        }

        return $parentId;
    }

    /**
     * Delete file from Google Drive
     */
    public function deleteFile($fileId)
    {
        try {
            $this->driveService->files->delete($fileId);
            return true;
        } catch (\Exception $e) {
            // \Log::error('Error deleting file: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Get file info
     */
    public function getFileInfo($fileId)
    {
        try {
            $file = $this->driveService->files->get($fileId, [
                'fields' => 'id,name,mimeType,size,createdTime,modifiedTime,webViewLink,webContentLink'
            ]);

            return [
                'id' => $file->getId(),
                'name' => $file->getName(),
                'mimeType' => $file->getMimeType(),
                'size' => $file->getSize(),
                'createdTime' => $file->getCreatedTime(),
                'modifiedTime' => $file->getModifiedTime(),
                'webViewLink' => $file->getWebViewLink(),
                'webContentLink' => $file->getWebContentLink()
            ];
        } catch (\Exception $e) {
            // \Log::error('Error getting file info: ' . $e->getMessage());
            return null;
        }
    }
}
