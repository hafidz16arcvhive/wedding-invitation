# 💒 LoveStory Invitation — Panduan Setup Lengkap

Website pemesanan undangan pernikahan digital dengan fitur:
- Katalog desain undangan
- Tampilan undangan elegan (countdown, galeri foto, RSVP, ucapan, QRIS)
- Panel admin (login khusus admin)
- Database MySQL via XAMPP

---

## 📦 Struktur File

```
wedding-invitation/
├── index.php                  ← Halaman utama (katalog desain)
├── .htaccess                  ← Keamanan & URL routing
├── config/
│   └── database.php           ← Konfigurasi database & helper functions
├── admin/
│   ├── login.php              ← Halaman login admin
│   ├── dashboard.php          ← Panel admin
│   └── logout.php             ← Logout admin
├── invitation/
│   ├── index.php              ← Template undangan pernikahan
│   └── .htaccess              ← URL routing undangan
├── database/
│   └── wedding.sql            ← Script buat database & tabel
└── assets/                    ← (Buat manual) CSS, JS, gambar
    ├── images/
    │   ├── templates/         ← Thumbnail template undangan
    │   ├── couples/           ← Foto pasangan
    │   └── qris/              ← Gambar QRIS
    └── music/                 ← File musik background (.mp3)
```

---

## 🚀 Langkah Setup (Dari Nol)

### LANGKAH 1 — Install XAMPP
1. Download XAMPP dari https://www.apachefriends.org
2. Install, lalu buka **XAMPP Control Panel**
3. Klik **Start** untuk **Apache** dan **MySQL**
4. Pastikan keduanya berwarna hijau (running)

### LANGKAH 2 — Salin File Proyek
1. Buka folder `C:\xampp\htdocs\` (Windows) atau `/Applications/XAMPP/htdocs/` (Mac)
2. Buat folder baru bernama `wedding-invitation`
3. Salin semua file proyek ini ke dalam folder tersebut

### LANGKAH 3 — Setup Database
1. Buka browser, akses: **http://localhost/phpmyadmin**
2. Klik tab **SQL** di bagian atas
3. Buka file `database/wedding.sql` dengan text editor
4. Copy semua isinya, paste ke kotak SQL di phpMyAdmin
5. Klik tombol **Go** / **Jalankan**
6. Seharusnya muncul pesan "Database berhasil dibuat!"

### LANGKAH 4 — Konfigurasi Database
Buka file `config/database.php` dan sesuaikan jika perlu:
```php
define('DB_HOST', 'localhost');  // biasanya tidak perlu diubah
define('DB_USER', 'root');       // default XAMPP
define('DB_PASS', '');           // default XAMPP = kosong
define('DB_NAME', 'wedding_invitation');
define('BASE_URL', 'http://localhost/wedding-invitation'); // sesuaikan nama folder
```

### LANGKAH 5 — Aktifkan mod_rewrite Apache
1. Buka XAMPP Control Panel → Apache → **Config** → `httpd.conf`
2. Cari baris: `#LoadModule rewrite_module modules/mod_rewrite.so`
3. Hapus tanda `#` di depannya
4. Cari `AllowOverride None` (di bagian `<Directory "C:/xampp/htdocs">`)
5. Ubah menjadi `AllowOverride All`
6. Simpan dan **restart Apache** di XAMPP

### LANGKAH 6 — Buat Folder Assets
```
wedding-invitation/assets/images/templates/
wedding-invitation/assets/images/couples/
wedding-invitation/assets/images/qris/
wedding-invitation/assets/music/
```

### LANGKAH 7 — Akses Website
- **Halaman Utama:** http://localhost/wedding-invitation/
- **Login Admin:** http://localhost/wedding-invitation/admin/login.php
- **Contoh Undangan:** http://localhost/wedding-invitation/invitation/rizky-aulia

---

## 🔐 Login Admin

Default credentials:
- **Username:** `admin`
- **Password:** `password`

> ⚠️ Setelah berhasil login, segera ganti password!

Untuk ganti password, jalankan SQL ini di phpMyAdmin:
```sql
USE wedding_invitation;
UPDATE admin 
SET password = '$2y$10$...' -- hash baru dari password_hash()
WHERE username = 'admin';
```

Atau buat file `generate_hash.php` sementara:
```php
<?php
echo password_hash('password_baru_kamu', PASSWORD_BCRYPT);
```
Akses di browser, copy hasilnya, paste ke SQL di atas.

---

## 📝 Cara Tambah Undangan Baru

### Via Database (phpMyAdmin):
```sql
USE wedding_invitation;
INSERT INTO weddings (
  slug, template_id,
  groom_name, bride_name,
  akad_date, akad_time, akad_venue, akad_address,
  reception_date, reception_time_start, reception_time_end,
  reception_venue, reception_address,
  is_active, is_published
) VALUES (
  'budi-siti',    -- URL: /invitation/budi-siti
  1,              -- template_id (1=Royal Garden)
  'Budi', 'Siti',
  '2025-09-20', '08:00:00', 'Masjid Al-Hidayah', 'Jl. Mawar No. 5, Bangil',
  '2025-09-20', '10:00:00', '14:00:00',
  'Gedung Pertemuan Indah', 'Jl. Melati No. 10, Pasuruan',
  1, 1
);
```

### Upload Foto:
1. Simpan foto ke `assets/images/couples/`
2. Update database:
```sql
UPDATE weddings 
SET couple_photo = 'assets/images/couples/budi-siti-foto.jpg'
WHERE slug = 'budi-siti';
```

---

## 🎵 Cara Tambah Musik Background
1. Siapkan file `.mp3` (pilih lagu instrumental romantis)
2. Simpan ke `assets/music/background.mp3`
3. Update database:
```sql
UPDATE weddings 
SET music_url = 'assets/music/background.mp3'
WHERE slug = 'rizky-aulia';
```

---

## 📷 Cara Tambah Galeri Foto
```sql
UPDATE weddings 
SET gallery_photos = '["assets/images/couples/foto1.jpg","assets/images/couples/foto2.jpg","assets/images/couples/foto3.jpg"]'
WHERE slug = 'rizky-aulia';
```

---

## 🔄 Teknologi yang Digunakan

| Teknologi | Fungsi |
|-----------|--------|
| PHP 8.x | Backend server-side |
| MySQL (XAMPP) | Database |
| PDO | Koneksi database yang aman |
| Tailwind CSS (CDN) | Styling / tampilan |
| Bootstrap Icons (CDN) | Icon |
| Google Fonts | Tipografi elegan |
| Vanilla JS | Interaktivitas (countdown, RSVP, dll) |

---

## 🛡️ Keamanan yang Sudah Diterapkan

- **Password Hashing** — bcrypt via `password_hash()` / `password_verify()`
- **PDO Prepared Statements** — mencegah SQL Injection
- **XSS Prevention** — `htmlspecialchars()` pada semua output
- **Session Management** — session PHP untuk autentikasi admin
- **Directory Listing Disabled** — via .htaccess
- **Config folder protection** — via .htaccess

---

## ❓ Troubleshooting

**Q: Muncul "Koneksi database gagal"**
A: Pastikan MySQL sudah Start di XAMPP. Cek juga nama database di `config/database.php`

**Q: URL undangan tidak bekerja (/invitation/rizky-aulia)**
A: Aktifkan `mod_rewrite` Apache (lihat Langkah 5)

**Q: Gambar tidak muncul**
A: Pastikan path gambar di database sesuai, dan file gambar ada di folder assets/

**Q: Musik tidak main**
A: Browser modern memblokir autoplay. User harus klik tombol musik.

---

## 📞 Butuh Bantuan?
Tanya ke Claude di claude.ai! 😊
