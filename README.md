# 📌 SIMPEG - Sistem Informasi Kepegawaian

STMIK El Rahma Yogyakarta

SIMPEG adalah aplikasi Sistem Informasi Kepegawaian yang dikembangkan untuk memenuhi kebutuhan mata kuliah **Kerja Praktek** di kampus **STMIK El Rahma Yogyakarta**.  
Sistem ini ditujukan untuk membantu pengelolaan data kepegawaian dengan tiga jenis pengguna utama:

-   **Admin** → mengelola data pegawai, dokumen, serta akses pengguna.
-   **Dosen** → mengunggah dan mengelola dokumen/arsip terkait (Sertifikasi, BKD, dan lain-lain).
-   **Karyawan** → mengunggah dan mengelola dokumen/arsip terkait(Sertifikasi dan lain lain).

Semua file dan foto yang diunggah oleh pengguna **disimpan di Google Drive** menggunakan integrasi API, sehingga lebih aman dan terstruktur.

---

## ⚙️ Cara Install & Menjalankan Aplikasi

### 1. Clone Repository

```bash
git clone https://github.com/ilhanmanzis/simpeg.git
cd simpeg
```

### 2. Install Dependency

```

composer install
npm install

```

### 3. Konfigurasi Environment

```

cp .env.example .env

```

Edit file .env, contoh:

```

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simpeg
DB_USERNAME=root
DB_PASSWORD=

# setup google drive
FILESYSTEM_CLOUD=google
GOOGLE_DRIVE_CLIENT_ID=""
GOOGLE_DRIVE_CLIENT_SECRET=""
GOOGLE_DRIVE_REFRESH_TOKEN=""
GOOGLE_DRIVE_FOLDER_ID=""
# Redirect otomatis di-generate dari route; tapi sediakan fallback:
GOOGLE_DRIVE_REDIRECT_URI="${APP_URL}/oauth/google/callback"

```

### 4. Generate Key

```

php artisan key:generate

```

### 5. Migrasi Database dan Seeder

```

php artisan migrate --seed

```

### 6. Jalankan Aplikasi

```

php artisan serve

```

### 7. link storage

```

php artisan storage:link

```

### 8. Bahasa Indonseia untuk validasi input

```

php artisan lang:add id

```

##

### Develpoment

```

npm run dev

```

### Build Vite

```

npm run build

```

---

## 🔑 Konfigurasi Google Drive API

Aplikasi ini menggunakan **Google Drive API** untuk menyimpan file/dokumen.  
Ikuti langkah berikut untuk mendapatkan `GOOGLE_DRIVE_CLIENT_ID`, `GOOGLE_DRIVE_CLIENT_SECRET`, dan `GOOGLE_DRIVE_FOLDER_ID`.

### 1. Membuat Project di Google Cloud Console

1. Buka [Google Cloud Console](https://console.cloud.google.com/).
2. Login dengan akun Google Anda.
3. Klik menu **Select a Project** → **New Project**.
4. Beri nama project (misalnya: `simpeg-drive`), lalu klik **Create**.

### 2. Mengaktifkan Google Drive API

1. Masuk ke project yang baru dibuat.
2. Buka menu **API & Services** → **Library**.
3. Cari **Google Drive API**.
4. Klik **Enable**.

### 3. Membuat OAuth 2.0 Client ID & Secret

1. Masuk ke menu **API & Services** → **Credentials**.
2. Klik **Create Credentials** → pilih **OAuth Client ID**.
3. Jika diminta, isi **OAuth Consent Screen**:
    - User Type: pilih **External** (agar bisa login pakai akun Google biasa).
    - Isi **App name**, **User support email**, dan **Developer contact email**.
    - Simpan & Continue hingga selesai.
4. Kembali ke **Credentials**, pilih **OAuth Client ID**:
    - Application type: **Web Application**.
    - Authorized redirect URIs → tambahkan URL berikut (sesuaikan dengan domain lokal/production):
        - `http://localhost:8000/oauth/google/callback`
        - `https://your-domain.com/oauth/google/callback`
5. Klik **Create** → akan muncul **Client ID** dan **Client Secret**.
6. Salin ke file `.env`:
    ```env
    GOOGLE_DRIVE_CLIENT_ID="xxxxxxxxxxxxxxxx.apps.googleusercontent.com"
    GOOGLE_DRIVE_CLIENT_SECRET="xxxxxxxxxxxxxxxxxxx"
    ```

### 4. Mendapatkan Folder ID Google Drive

1. Buka [Google Drive](https://drive.google.com/).
2. Buat folder baru untuk penyimpanan file (misalnya simpeg-uploads).
3. Klik kanan folder → Get Link.
4. Salin URL yang muncul, contohnya:
    ```
    https://drive.google.com/drive/folders/xxxxxxxxxxxxxxxx
    ```
5. Folder ID adalah bagian setelah /folders/, contoh:

    ```
    xxxxxxxxxxxxxxx
    ```

6. Tambahkan ke file .env:

    ```
    GOOGLE_DRIVE_FOLDER_ID="xxxxxxxxxxxxxxx"
    ```
