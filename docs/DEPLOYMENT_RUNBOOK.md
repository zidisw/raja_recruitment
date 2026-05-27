# Deployment Runbook

Dokumen ini menjelaskan alur update dan deploy project Raja Recruitment dari local development sampai production Hostinger/cPanel.

Tujuan utamanya:

- Update production dilakukan lewat GitHub dan GitHub Actions.
- Developer tidak perlu upload file manual satu per satu ke hPanel/cPanel.
- Secret production tetap berada di server/GitHub Secrets, bukan di repository.
- Setiap deploy punya checklist yang jelas: kapan perlu migration, kapan tidak.

## Ringkasan Arsitektur

Alur production saat ini:

```text
Developer local
  -> push ke GitHub
  -> GitHub Actions CI
  -> GitHub Actions Deploy Production
  -> Hostinger/cPanel via SSH + rsync
  -> /home/u1635790/laravel_core
  -> /home/u1635790/public_html
```

Struktur production:

```text
/home/u1635790/laravel_core     Laravel core app
/home/u1635790/public_html      document root domain
/home/u1635790/public_html/build
/home/u1635790/public_html/storage -> /home/u1635790/laravel_core/storage/app/public
```

File `public_html/index.php` harus mengarah ke Laravel core:

```php
require __DIR__.'/../laravel_core/vendor/autoload.php';
$app = require_once __DIR__.'/../laravel_core/bootstrap/app.php';
$app->usePublicPath(__DIR__);
```

Jangan commit file `.env`, password database, password email, atau private SSH key.

## Workflow GitHub Actions

Ada dua workflow utama:

- `.github/workflows/ci.yml`
- `.github/workflows/deploy.yml`

CI berjalan untuk validasi code. Deploy production dijalankan manual lewat GitHub Actions dengan input:

- `ref`: branch/tag/SHA yang mau dideploy, biasanya `main`.
- `run_migrations`: jalankan `php artisan migrate --force` di production.
- `sync_static_media`: sync isi `public/rjs-photos` ke production.

Deploy production melakukan:

- checkout source code sesuai `ref`
- composer audit
- install composer dependency production (`--no-dev`)
- build asset frontend dengan npm/Vite
- rsync Laravel core ke `/home/u1635790/laravel_core`
- rsync `public/build` ke `/home/u1635790/public_html/build`
- optional sync `public/rjs-photos`
- jalankan post-deploy command di server
- clear/cache config dan route
- restart queue

Catatan: `view:cache` sengaja tidak dijalankan karena Livewire view path membuat proses ini lambat di project ini.

## GitHub Secrets Production

Secrets disimpan di GitHub, bukan di repository.

Nama secret yang dipakai workflow:

```text
HOSTINGER_HOST
HOSTINGER_USER
HOSTINGER_PORT
HOSTINGER_SSH_PRIVATE_KEY
HOSTINGER_CORE_PATH
HOSTINGER_PUBLIC_PATH
```

Nilai yang umum:

```text
HOSTINGER_CORE_PATH=/home/u1635790/laravel_core
HOSTINGER_PUBLIC_PATH=/home/u1635790/public_html
```

`HOSTINGER_SSH_PRIVATE_KEY` adalah private key khusus deploy. Jangan dibagikan ke developer. Jika perlu rotate, buat key baru, pasang public key-nya di Hostinger, lalu update secret ini.

## Jenis Deploy

### 1. Deploy Code-Only

Pakai ini untuk perubahan PHP/Blade/CSS/JS biasa yang tidak mengubah struktur database.

Contoh:

- fix Livewire component
- fix Blade UI
- update route/controller/model logic tanpa schema baru
- update asset frontend
- tambah test

Input workflow:

```text
ref: main
run_migrations: false
sync_static_media: false
```

Ini adalah pilihan default yang paling aman untuk update kecil.

### 2. Deploy Dengan Migration

Pakai ini kalau ada perubahan di `database/migrations`.

Contoh:

- tambah table
- tambah kolom
- ubah index
- ubah foreign key
- ubah tipe kolom

Input workflow:

```text
ref: main
run_migrations: true
sync_static_media: false
```

Sebelum deploy migration:

- backup database production dulu
- baca isi file migration
- pastikan migration tidak menghapus data tanpa rencana rollback
- jalankan test lokal
- kalau migration berat, deploy di jam sepi

### 3. Deploy Static Media

Pakai ini hanya kalau ada file statis baru/berubah di:

```text
public/rjs-photos
```

Input workflow:

```text
ref: main
run_migrations: false
sync_static_media: true
```

Catatan penting:

- Upload user/candidate tidak di-sync dari repository.
- Upload user berada di `storage/app/public`.
- `public_html/storage` harus menjadi symlink ke `laravel_core/storage/app/public`.
- Jangan sync static media kalau tidak diperlukan.

### 4. Deploy Full

Pakai ini kalau perubahan code juga membawa migration dan static media.

Input workflow:

```text
ref: main
run_migrations: true
sync_static_media: true
```

Gunakan hanya kalau memang dua-duanya dibutuhkan.

### 5. Rollback Code

Rollback code dilakukan dengan menjalankan workflow deploy ke commit sebelumnya.

Langkah:

1. Cari commit SHA yang stabil di GitHub.
2. Buka GitHub Actions.
3. Pilih `Deploy Production (Hostinger SSH)`.
4. Klik `Run workflow`.
5. Isi `ref` dengan SHA commit stabil.
6. Biasanya pakai:

```text
run_migrations: false
sync_static_media: false
```

Peringatan:

- Rollback code tidak otomatis rollback database.
- Kalau migration sudah dijalankan dan mengubah data/schema, buat forward fix migration atau restore backup.
- Jangan menjalankan `migrate:rollback` di production tanpa membaca migration dan backup dulu.

## Cara Menentukan Perlu Migration Atau Tidak

Cara paling cepat:

```bash
git diff --name-only HEAD~1 HEAD -- database/migrations
```

Jika output kosong, commit terakhir tidak membawa migration.

Untuk melihat semua file pada commit terakhir:

```bash
git show --name-only --oneline HEAD
```

Jika ada file seperti ini:

```text
database/migrations/2026_05_27_123456_add_column_to_applications_table.php
```

berarti deploy perlu mempertimbangkan `run_migrations: true`.

Di production, status migration bisa dicek dengan:

```bash
cd /home/u1635790/laravel_core
php artisan migrate:status
```

Jika migration di repository belum berstatus `Ran` di server, berarti belum dijalankan.

Aturan praktis:

```text
Hanya code/view/test berubah       run_migrations=false
Ada file database/migrations baru  run_migrations=true
Ada migration destructive          backup + review manual dulu
Ragu-ragu                          cek git diff + php artisan migrate:status
```

## Prosedur Deploy Production

### Sebelum Deploy

Di local:

```bash
git status
composer audit --no-interaction
php artisan test
npm audit --audit-level=high
npm run build
vendor/bin/pint --test
```

Di Windows, Pint bisa dijalankan dengan:

```powershell
vendor\bin\pint.bat --test
```

Pastikan:

- working tree bersih
- perubahan sudah dipush ke GitHub
- CI di GitHub hijau
- migration decision sudah jelas
- database sudah dibackup jika ada migration

### Menjalankan Deploy

1. Buka repository GitHub.
2. Masuk ke tab `Actions`.
3. Pilih `Deploy Production (Hostinger SSH)`.
4. Klik `Run workflow`.
5. Isi input:

```text
ref: main
run_migrations: false/true
sync_static_media: false/true
```

6. Jalankan.
7. Tunggu sampai workflow selesai hijau.

### Setelah Deploy

Cek halaman utama dan fitur penting:

- login admin
- dashboard
- daftar kandidat
- detail kandidat
- daftar lowongan
- form kandidat
- upload/download dokumen jika terkait perubahan

Cek log server jika ada error:

```bash
tail -100 /home/u1635790/laravel_core/storage/logs/laravel.log
```

Cek symlink storage:

```bash
ls -la /home/u1635790/public_html/storage
```

Hasil yang aman harus menunjukkan `storage -> /home/u1635790/laravel_core/storage/app/public`.

## Akses Untuk Developer Lain

Pisahkan akses menjadi tiga level:

```text
1. Akses code GitHub
2. Akses menjalankan deploy via GitHub Actions
3. Akses SSH langsung ke Hostinger
```

Tidak semua developer harus punya ketiganya.

### 1. Memberi Akses Code GitHub

Owner repository dapat menambahkan developer melalui:

```text
GitHub repository -> Settings -> Collaborators and teams
```

Rekomendasi permission:

```text
Read      untuk reviewer/pemantau
Triage    untuk issue management
Write     untuk developer yang boleh push branch/PR
Maintain  untuk lead yang boleh manage settings non-secret
Admin     sangat terbatas, hanya owner/maintainer utama
```

Alur kerja yang disarankan:

```text
developer buat branch
developer push branch
buat Pull Request
CI berjalan
review code
merge ke main
deploy manual dari Actions
```

### 2. Memberi Kemampuan Deploy Via GitHub Actions

Developer tidak perlu memegang private SSH deploy key.

Cara yang lebih aman:

- Simpan SSH private key hanya di GitHub Secret.
- Beri developer akses GitHub sesuai peran.
- Batasi deploy production dengan GitHub Environment `production`.
- Tambahkan required reviewer untuk environment production.

Setting yang disarankan:

```text
GitHub repository -> Settings -> Environments -> production
```

Aktifkan:

```text
Required reviewers
```

Lalu tambahkan orang yang boleh menyetujui deploy, misalnya:

```text
Project owner
Tech lead
Senior maintainer
```

Dengan pola ini:

- developer bisa push PR
- CI tetap otomatis
- deploy production tetap perlu approval
- private key production tidak tersebar

### 3. Memberi Akses SSH Hostinger

SSH langsung hanya diberikan ke orang yang benar-benar perlu akses server.

Setiap developer harus punya SSH key sendiri. Jangan saling membagikan private key.

Generate key di Windows PowerShell:

```powershell
ssh-keygen -t ed25519 -C "nama.developer@raja-recruitment"
```

Jika panel/server tidak menerima ED25519, gunakan RSA:

```powershell
ssh-keygen -t rsa -b 4096 -C "nama.developer@raja-recruitment"
```

File yang dibagikan ke admin server adalah public key:

```text
~/.ssh/id_ed25519.pub
```

Jangan kirim file private key:

```text
~/.ssh/id_ed25519
```

Admin server memasang public key melalui cPanel/Hostinger SSH Access atau `~/.ssh/authorized_keys`.

Tes koneksi:

```powershell
ssh -i "$env:USERPROFILE\.ssh\id_ed25519" -p <PORT> <USER>@<HOST> "pwd && php -v"
```

Ganti `<PORT>`, `<USER>`, dan `<HOST>` sesuai akses yang diberikan admin.

## Rotasi Deploy Key GitHub Actions

Lakukan rotasi kalau:

- private key pernah terlihat/terkirim
- developer keluar dari project dan pernah punya akses key
- ada indikasi akses tidak sah
- kebijakan keamanan meminta rotasi berkala

Langkah:

1. Generate SSH key baru khusus deploy.
2. Pasang public key baru di Hostinger/cPanel.
3. Update GitHub Secret `HOSTINGER_SSH_PRIVATE_KEY` dengan private key baru.
4. Jalankan workflow deploy ringan untuk test koneksi.
5. Hapus public key lama dari Hostinger.

Jangan hapus key lama sebelum key baru terbukti bisa connect.

## Environment Production

File `.env` production berada di server, bukan repository.

Pastikan production memakai:

```text
APP_ENV=production
APP_DEBUG=false
APP_URL=https://rodajayasakti.id
SESSION_SECURE_COOKIE=true
```

Kalau `.env` berubah di server, jalankan:

```bash
cd /home/u1635790/laravel_core
php artisan config:clear
php artisan config:cache
```

Jika password database/email pernah muncul di chat, screenshot, atau dokumen, lakukan rotasi password di provider terkait lalu update `.env` production.

## Troubleshooting

### GitHub Actions: `error in libcrypto`

Biasanya isi `HOSTINGER_SSH_PRIVATE_KEY` salah format.

Pastikan secret berisi private key lengkap:

```text
-----BEGIN OPENSSH PRIVATE KEY-----
...
-----END OPENSSH PRIVATE KEY-----
```

Jangan masukkan public key (`.pub`).

### GitHub Actions: `REMOTE HOST IDENTIFICATION HAS CHANGED`

Ini terjadi jika host key yang diterima runner berbeda dari known_hosts.

Workflow saat ini memakai:

```text
StrictHostKeyChecking=no
UserKnownHostsFile=/dev/null
```

Ini dipilih karena gateway SSH Hostinger pernah memberi host key yang tidak konsisten. Trade-off-nya: validasi host key strict tidak aktif pada workflow deploy.

Jika nanti Hostinger sudah stabil dan ingin lebih ketat, aktifkan kembali known_hosts dengan host key yang sudah diverifikasi.

### Website 500 Setelah Deploy

Cek log:

```bash
tail -100 /home/u1635790/laravel_core/storage/logs/laravel.log
```

Cek cache:

```bash
cd /home/u1635790/laravel_core
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
```

### Asset CSS/JS Tidak Update

Cek apakah `public/build` tersync:

```bash
ls -la /home/u1635790/public_html/build
```

Lalu hard refresh browser.

### Upload/File Kandidat 404

Cek symlink storage:

```bash
ls -la /home/u1635790/public_html/storage
```

Jika bukan symlink ke `laravel_core/storage/app/public`, perbaiki symlink setelah backup/cek isi folder existing.

### Migration Gagal

Jangan langsung rollback sembarang.

Langkah aman:

1. Simpan error lengkap dari Actions/log.
2. Cek migration yang gagal.
3. Cek `php artisan migrate:status`.
4. Jika data/schema sudah berubah sebagian, buat fix-forward migration atau restore backup.
5. Jalankan ulang deploy hanya setelah penyebab jelas.

## Checklist Cepat

Sebelum merge:

```text
[ ] tests pass
[ ] build pass
[ ] composer audit pass
[ ] npm audit pass
[ ] tidak ada secret di commit
[ ] reviewer sudah approve
```

Sebelum deploy:

```text
[ ] CI hijau di GitHub
[ ] tentukan run_migrations true/false
[ ] tentukan sync_static_media true/false
[ ] backup database jika ada migration
[ ] deploy dijalankan dari ref yang benar
```

Sesudah deploy:

```text
[ ] Actions hijau
[ ] homepage bisa dibuka
[ ] login admin bisa
[ ] fitur utama dicek
[ ] storage symlink aman
[ ] laravel.log tidak muncul error baru
```

