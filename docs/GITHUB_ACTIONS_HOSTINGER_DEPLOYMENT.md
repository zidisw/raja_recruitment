# GitHub Actions to Hostinger Semi-Auto Deployment Guide

Dokumen ini menjelaskan cara menyiapkan alur deployment untuk project Laravel/Livewire seperti `raja_recruitment`, dari awal setup GitHub Actions sampai bisa deploy semi-otomatis ke Hostinger/cPanel.

Target pembaca:

- Developer yang ingin menyalin pola deployment ini ke project lain.
- Developer yang perlu memahami alur CI, deploy, secret, dan konfigurasi server.

## 1. Gambaran Besar

Alur yang dipakai project ini adalah:

```text
Developer lokal
  -> push branch / buat pull request
  -> GitHub Actions CI jalan otomatis
  -> kode di-merge ke main
  -> deployment production dijalankan manual dari GitHub Actions
  -> GitHub Actions SSH ke Hostinger
  -> rsync file aplikasi dan aset
  -> jalankan migrate/cache/restart queue di server
```

Model ini disebut semi-otomatis karena:

- validasi kode berjalan otomatis via CI,
- tetapi deploy production masih dipicu manual dari GitHub Actions,
- sehingga developer tetap punya kontrol sebelum production berubah.

## 2. Komponen Yang Dipakai

Project ini memakai komponen berikut:

- Laravel 12
- Livewire
- GitHub Actions
- Hostinger/cPanel
- SSH key khusus deployment
- `rsync` untuk sinkronisasi file

Workflow yang sudah ada di repo:

- [.github/workflows/ci.yml](../.github/workflows/ci.yml)
- [.github/workflows/deploy.yml](../.github/workflows/deploy.yml)

## 3. Struktur CI

CI di repo ini berjalan pada:

- `push` ke branch `main`
- `pull_request`

CI melakukan 2 job utama:

### Backend

- checkout source code
- setup PHP 8.4
- generate `APP_KEY` untuk environment testing
- install Composer dependencies
- `composer audit`
- `composer test`

### Frontend

- checkout source code
- setup PHP 8.4
- install Composer dependencies tanpa dev package
- setup Node 20
- `npm ci`
- `npm audit --audit-level=high`
- `npm run build`

Tujuan CI:

- memastikan test tidak rusak,
- memastikan dependency audit aman,
- memastikan frontend build berhasil sebelum deploy.

## 4. Struktur Deploy Production

Workflow deploy production ada di [.github/workflows/deploy.yml](../.github/workflows/deploy.yml).

Workflow ini dijalankan manual lewat GitHub Actions, bukan otomatis dari push.

Input yang tersedia:

- `ref` - branch, tag, atau commit SHA yang ingin dideploy
- `run_migrations` - apakah migration dijalankan di server
- `sync_static_media` - apakah `public/rjs-photos` ikut disinkronkan

Default saat ini:

- `ref = main`
- `run_migrations = true`
- `sync_static_media = true`

## 5. Prasyarat Di Hostinger

Sebelum workflow deploy dipakai, server Hostinger harus siap menerima file dan command dari GitHub Actions.

### Folder produksi

Contoh struktur yang dipakai repo ini:

```text
/home/u1635790/laravel_core
/home/u1635790/public_html
/home/u1635790/public_html/build
/home/u1635790/public_html/storage -> /home/u1635790/laravel_core/storage/app/public
```

### index.php di document root

File `public_html/index.php` harus diarahkan ke Laravel core, bukan ke folder publik repository langsung:

```php
require __DIR__.'/../laravel_core/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel_core/bootstrap/app.php';
$app->usePublicPath(__DIR__);
```

### Direktori yang harus bisa ditulis

Pastikan server bisa menulis ke:

- `storage`
- `bootstrap/cache`
- folder upload publik yang dipakai aplikasi

## 6. Secrets Yang Harus Dibuat Di GitHub

Semua secret production disimpan di GitHub Secrets, bukan di repository.

Secret yang dipakai workflow ini:

- `HOSTINGER_HOST`
- `HOSTINGER_USER`
- `HOSTINGER_PORT`
- `HOSTINGER_SSH_PRIVATE_KEY`
- `HOSTINGER_CORE_PATH`
- `HOSTINGER_PUBLIC_PATH`

Nilai yang umum:

```text
HOSTINGER_CORE_PATH=/home/u1635790/laravel_core
HOSTINGER_PUBLIC_PATH=/home/u1635790/public_html
```

Catatan:

- `HOSTINGER_SSH_PRIVATE_KEY` harus private key khusus deploy.
- Public key-nya dipasang di Hostinger.
- Kalau key harus diganti, rotate di server dan update secret di GitHub.

## 7. Langkah Setup Awal Dari Nol

Kalau developer lain ingin meniru pola ini ke project baru, urutannya seperti ini.

### Langkah 1: Siapkan project Laravel

- pastikan aplikasi Laravel sudah berjalan lokal,
- pastikan `composer install` dan `npm install` berhasil,
- pastikan test dan build frontend berhasil.

### Langkah 2: Buat workflow CI

Buat file `.github/workflows/ci.yml` dengan pola berikut:

- trigger `push` dan `pull_request`,
- setup PHP sesuai versi project,
- install dependency,
- jalankan audit,
- jalankan test backend,
- jalankan build frontend.

Prinsip penting:

- CI harus gagal kalau test gagal,
- CI harus gagal kalau build frontend gagal,
- CI harus gagal kalau dependency audit bermasalah.

### Langkah 3: Buat workflow deploy production

Buat file `.github/workflows/deploy.yml` dengan pola berikut:

- trigger `workflow_dispatch`,
- menerima input `ref`, `run_migrations`, dan `sync_static_media`,
- checkout source code sesuai `ref`,
- install dependency production,
- build asset frontend,
- login ke server via SSH,
- sync file dengan `rsync`,
- jalankan command deploy di server.

### Langkah 4: Siapkan server Hostinger

- buat folder core Laravel,
- buat document root yang mengarah ke public,
- pastikan symlink storage benar,
- pastikan PHP CLI di server sesuai kebutuhan project,
- pastikan `php artisan migrate --force` bisa dijalankan dari server.

### Langkah 5: Simpan secrets

Tambahkan secret ke GitHub repository:

- host,
- user,
- port,
- private key,
- path core,
- path public.

### Langkah 6: Uji deploy ke environment non-production dulu

Sebelum production, sebaiknya uji alurnya di staging atau domain test.

Tujuan uji:

- memastikan SSH konek,
- memastikan `rsync` tidak menghapus file penting,
- memastikan storage symlink benar,
- memastikan migration aman.

## 8. Alur Deploy Yang Dipakai Project Ini

Workflow deploy production saat ini melakukan langkah berikut:

1. Checkout branch/tag/SHA yang dipilih.
2. Setup PHP 8.4.
3. Audit Composer dependencies.
4. Install Composer dependencies produksi dengan `--no-dev`.
5. Setup Node 20.
6. Build frontend assets.
7. Start SSH agent dengan private key deploy.
8. Test koneksi SSH ke Hostinger.
9. Sync source code Laravel ke folder core.
10. Sync `public/build` ke public root.
11. Sync static media jika input `sync_static_media` aktif.
12. Sync aset root public seperti favicon dan logo.
13. Jalankan perintah remote di server.

Langkah remote di server meliputi:

- create folder yang dibutuhkan
- jalankan migration jika diminta
- clear cache config/route/view
- pastikan symlink `storage` benar
- cache config dan route
- restart queue

## 9. Kenapa Deploy Ini Semi-Auto, Bukan Auto Penuh

Di project ini deploy production tidak otomatis pada push ke `main`.

Alasannya:

- ada kontrol manual sebelum production berubah,
- migration bisa diputuskan per-deploy,
- static media bisa diaktifkan hanya saat dibutuhkan,
- developer bisa memilih commit SHA tertentu untuk rollback/forward deploy.

Jadi praktik operasionalnya:

- push ke `main` -> CI jalan otomatis,
- setelah lolos review -> buka GitHub Actions,
- jalankan `Deploy Production (Hostinger SSH)` secara manual.

## 10. Cara Deploy Harian

Untuk update biasa tanpa perubahan schema database:

```text
ref: main
run_migrations: false
sync_static_media: false
```

Untuk perubahan database migration:

```text
ref: main
run_migrations: true
sync_static_media: false
```

Untuk perubahan aset publik statis:

```text
ref: main
run_migrations: false
sync_static_media: true
```

Untuk perubahan code + migration + aset statis:

```text
ref: main
run_migrations: true
sync_static_media: true
```

## 11. Checklist Sebelum Deploy

Checklist yang aman dipakai:

- `git status` bersih
- test lolos
- build frontend lolos
- migration sudah dibaca dan dipahami
- backup database kalau ada migration schema
- pastikan secret GitHub masih valid
- pastikan server Hostinger sedang sehat

## 12. Checklist Setelah Deploy

Setelah workflow selesai, cek hal berikut:

- halaman utama bisa dibuka,
- login bisa dilakukan,
- halaman Livewire tidak error,
- upload file masih jalan,
- storage symlink masih benar,
- queue worker tidak stuck,
- log production tidak menunjukkan error baru.

## 13. Rollback

Rollback code bisa dilakukan dengan menjalankan workflow deploy ke commit SHA sebelumnya.

Contoh alur rollback:

1. Cari commit stabil di GitHub.
2. Buka Actions.
3. Jalankan `Deploy Production (Hostinger SSH)`.
4. Isi `ref` dengan SHA commit stabil.
5. Gunakan `run_migrations = false` kecuali memang perlu forward fix migration.

Catatan penting:

- rollback code tidak otomatis rollback database,
- kalau migration sudah merusak schema/data, biasanya solusi aman adalah forward fix atau restore backup,
- hindari `migrate:rollback` di production tanpa analisis dulu.

## 14. Praktik Aman Untuk Developer Lain

Kalau dokumen ini mau diterapkan di project lain, ikuti prinsip berikut:

- jangan hardcode credential di workflow,
- selalu pakai secret GitHub untuk SSH dan path server,
- pisahkan CI dan deploy production,
- jadikan deploy production manual untuk kontrol lebih aman,
- gunakan `rsync` dengan pengecualian yang jelas,
- pastikan storage upload user tidak tertimpa saat sync file,
- jalankan `composer audit` dan `npm audit` di CI.

## 15. File Referensi Di Repo Ini

- [.github/workflows/ci.yml](../.github/workflows/ci.yml)
- [.github/workflows/deploy.yml](../.github/workflows/deploy.yml)
- [docs/DEPLOYMENT_RUNBOOK.md](DEPLOYMENT_RUNBOOK.md)
- [README.md](../README.md)

## 16. Ringkasan Singkat

Kalau mau meniru setup ini ke project lain, intinya adalah:

- CI otomatis jalan saat push/PR,
- deploy production dijalankan manual dari GitHub Actions,
- file aplikasi disinkronkan ke Hostinger via SSH + rsync,
- migration hanya dijalankan saat diperlukan,
- storage upload user harus tetap aman,
- semua secret disimpan di GitHub, bukan di repo.
