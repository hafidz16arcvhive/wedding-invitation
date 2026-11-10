<?php
// ============================================================
// FILE: admin/wedding_add.php
// Form tambah undangan baru (bagian CREATE dari CRUD)
//
// Alur kerja:
// 1. Tampilkan form kosong (GET request)
// 2. User mengisi form dan submit
// 3. Server terima data (POST request)
// 4. Validasi — cek field yang wajib diisi
// 5. Jika gagal → tampilkan form lagi + pesan error (data tetap ada)
// 6. Jika sukses → insert ke DB → redirect ke weddings.php
// ============================================================

require_once '../config/database.php';
requireLogin();

// ── Ambil daftar template untuk dropdown ─────────────────────
$templates = db_rows("SELECT id, name FROM invitation_templates WHERE is_active = 1 ORDER BY id");

// ── Inisialisasi variabel error & data form ───────────────────
$errors = [];   // Array untuk menampung pesan error validasi
$form   = [];   // Array untuk menyimpan data form (sticky form)

// ── Proses form jika ada POST request ────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil semua input dari form, bersihkan spasi di awal/akhir
    $form = [
        'template_id'          => (int) ($_POST['template_id'] ?? 0),
        'groom_name'           => trim($_POST['groom_name'] ?? ''),
        'groom_full_name'      => trim($_POST['groom_full_name'] ?? ''),
        'groom_father'         => trim($_POST['groom_father'] ?? ''),
        'groom_mother'         => trim($_POST['groom_mother'] ?? ''),
        'bride_name'           => trim($_POST['bride_name'] ?? ''),
        'bride_full_name'      => trim($_POST['bride_full_name'] ?? ''),
        'bride_father'         => trim($_POST['bride_father'] ?? ''),
        'bride_mother'         => trim($_POST['bride_mother'] ?? ''),
        'akad_date'            => trim($_POST['akad_date'] ?? ''),
        'akad_time'            => trim($_POST['akad_time'] ?? ''),
        'akad_venue'           => trim($_POST['akad_venue'] ?? ''),
        'akad_address'         => trim($_POST['akad_address'] ?? ''),
        'akad_maps_url'        => trim($_POST['akad_maps_url'] ?? ''),
        'reception_date'       => trim($_POST['reception_date'] ?? ''),
        'reception_time_start' => trim($_POST['reception_time_start'] ?? ''),
        'reception_time_end'   => trim($_POST['reception_time_end'] ?? ''),
        'reception_venue'      => trim($_POST['reception_venue'] ?? ''),
        'reception_address'    => trim($_POST['reception_address'] ?? ''),
        'reception_maps_url'   => trim($_POST['reception_maps_url'] ?? ''),
        'love_story'           => trim($_POST['love_story'] ?? ''),
        'bank_name'            => trim($_POST['bank_name'] ?? ''),
        'bank_account_number'  => trim($_POST['bank_account_number'] ?? ''),
        'bank_account_name'    => trim($_POST['bank_account_name'] ?? ''),
        'is_published'         => isset($_POST['is_published']) ? 1 : 0,
    ];

    // ── VALIDASI ──────────────────────────────────────────────
    // Cek field-field yang wajib diisi
    if (empty($form['groom_name']))     $errors[] = 'Nama panggilan mempelai pria wajib diisi.';
    if (empty($form['bride_name']))     $errors[] = 'Nama panggilan mempelai wanita wajib diisi.';
    if (empty($form['reception_date'])) $errors[] = 'Tanggal resepsi wajib diisi.';
    if ($form['template_id'] <= 0)      $errors[] = 'Pilih template undangan.';

    // ── GENERATE SLUG ─────────────────────────────────────────
    // Slug = URL-friendly string dari nama pasangan
    // Contoh: "Muhammad Rizky" & "Aulia" → "rizky-aulia"
    if (empty($errors)) {
        // Ambil nama panggilan, ubah ke lowercase, ganti spasi dengan strip
        $groom_slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $form['groom_name']));
        $bride_slug = strtolower(preg_replace('/[^a-zA-Z0-9]/', '-', $form['bride_name']));
        $slug       = trim($groom_slug . '-' . $bride_slug, '-');
        $slug       = preg_replace('/-+/', '-', $slug); // hapus strip berulang

        // Cek apakah slug sudah dipakai
        $existing = db_row("SELECT id FROM weddings WHERE slug = ?", [$slug], 's');
        if ($existing) {
            // Jika sudah ada, tambahkan angka di belakang
            // Contoh: "rizky-aulia" → "rizky-aulia-2"
            $counter = 2;
            while (db_row("SELECT id FROM weddings WHERE slug = ?", [$slug . '-' . $counter], 's')) {
                $counter++;
            }
            $slug = $slug . '-' . $counter;
        }
    }

    // ── INSERT KE DATABASE (jika tidak ada error) ─────────────
    if (empty($errors)) {
        $new_id = db_insert("
            INSERT INTO weddings (
                slug, template_id,
                groom_name, groom_full_name, groom_father, groom_mother,
                bride_name, bride_full_name, bride_father, bride_mother,
                akad_date, akad_time, akad_venue, akad_address, akad_maps_url,
                reception_date, reception_time_start, reception_time_end,
                reception_venue, reception_address, reception_maps_url,
                love_story,
                bank_name, bank_account_number, bank_account_name,
                is_published, is_active
            ) VALUES (
                ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?,
                ?, ?, ?, ?, ?,
                ?, ?, ?,
                ?, ?, ?,
                ?,
                ?, ?, ?,
                ?, 1
            )
        ", [
            $slug,                      $form['template_id'],
            $form['groom_name'],        $form['groom_full_name'],
            $form['groom_father'],      $form['groom_mother'],
            $form['bride_name'],        $form['bride_full_name'],
            $form['bride_father'],      $form['bride_mother'],
            $form['akad_date']       ?: null,
            $form['akad_time']       ?: null,
            $form['akad_venue'],        $form['akad_address'],
            $form['akad_maps_url'],
            $form['reception_date'],
            $form['reception_time_start'] ?: null,
            $form['reception_time_end']   ?: null,
            $form['reception_venue'],   $form['reception_address'],
            $form['reception_maps_url'],
            $form['love_story'],
            $form['bank_name'],         $form['bank_account_number'],
            $form['bank_account_name'],
            $form['is_published'],
        ]);

        if ($new_id > 0) {
            // Berhasil — set flash message dan redirect
            $_SESSION['flash_message'] = 'Undangan ' . $form['groom_name'] . ' & ' . $form['bride_name'] . ' berhasil ditambahkan! URL: /invitation/' . $slug;
            $_SESSION['flash_type']    = 'success';
            redirect(BASE_URL . '/admin/weddings.php');
        } else {
            $errors[] = 'Gagal menyimpan ke database. Silakan coba lagi.';
        }
    }
}

$page_title = 'Tambah Undangan';
?>

<?php include '_head.php'; ?>
<body class="bg-gray-50">
<div class="flex min-h-screen">

    <?php include '_sidebar.php'; ?>

    <main class="flex-1 overflow-auto">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-100 px-8 py-4 sticky top-0 z-10
                        flex items-center justify-between">
            <div>
                <h1 class="section-title">Tambah Undangan Baru</h1>
                <p class="font-body text-gray-400 text-xs mt-0.5">
                    Isi data pasangan dan detail acara
                </p>
            </div>
            <!-- Tombol kembali -->
            <a href="weddings.php" class="btn-secondary">
                <i class="bi bi-arrow-left"></i> KEMBALI
            </a>
        </header>

        <div class="p-8">

            <!-- ── Tampilkan Error Validasi ── -->
            <?php if (!empty($errors)): ?>
            <div class="alert-error px-4 py-3 mb-6 font-body text-sm">
                <div class="flex items-center gap-2 mb-2 font-medium">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Mohon perbaiki kesalahan berikut:
                </div>
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $err): ?>
                    <li><?= clean($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- ── FORM ── -->
            <!-- method="POST" = kirim data secara POST -->
            <!-- Kita tidak pakai enctype multipart di sini dulu, upload foto nanti di halaman edit -->
            <form method="POST" action="">

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 1: PILIH TEMPLATE               -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-palette text-gold me-2"></i>Template Undangan
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <!-- Grid pilihan template -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php foreach ($templates as $tpl): ?>
                            <label class="cursor-pointer group">
                                <!-- Input radio tersembunyi -->
                                <input type="radio" name="template_id"
                                       value="<?= $tpl['id'] ?>"
                                       class="hidden template-radio"
                                       <?= ($form['template_id'] ?? 0) == $tpl['id'] ? 'checked' : '' ?>>
                                <!-- Kotak visual yang bisa diklik -->
                                <div class="template-option border-2 border-gray-200 p-4 text-center
                                            transition-all duration-200 hover:border-gold">
                                    <div class="text-3xl mb-2">💌</div>
                                    <div class="font-body text-sm font-medium text-charcoal">
                                        <?= clean($tpl['name']) ?>
                                    </div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="font-body text-gray-400 text-xs mt-3">
                            * Klik salah satu template di atas untuk memilih
                        </p>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 2: DATA MEMPELAI PRIA           -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-person text-gold me-2"></i>Mempelai Pria
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                            <div class="form-group">
                                <label class="form-label">
                                    Nama Panggilan <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="groom_name"
                                       value="<?= clean($form['groom_name'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Rizky"
                                       required>
                                <p class="font-body text-gray-400 text-xs mt-1">
                                    Nama yang ditampilkan di undangan
                                </p>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="groom_full_name"
                                       value="<?= clean($form['groom_full_name'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Muhammad Rizky Pratama">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Ayah</label>
                                <input type="text" name="groom_father"
                                       value="<?= clean($form['groom_father'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Bapak Ahmad Fauzi">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Ibu</label>
                                <input type="text" name="groom_mother"
                                       value="<?= clean($form['groom_mother'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Ibu Siti Aminah">
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 3: DATA MEMPELAI WANITA         -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-person text-gold me-2"></i>Mempelai Wanita
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                            <div class="form-group">
                                <label class="form-label">
                                    Nama Panggilan <span class="text-red-400">*</span>
                                </label>
                                <input type="text" name="bride_name"
                                       value="<?= clean($form['bride_name'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Aulia"
                                       required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="bride_full_name"
                                       value="<?= clean($form['bride_full_name'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Aulia Rahmawati">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Ayah</label>
                                <input type="text" name="bride_father"
                                       value="<?= clean($form['bride_father'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Bapak Hendra Wijaya">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Ibu</label>
                                <input type="text" name="bride_mother"
                                       value="<?= clean($form['bride_mother'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Ibu Dewi Lestari">
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 4: DETAIL AKAD NIKAH            -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-moon-stars text-gold me-2"></i>Akad Nikah
                        </span>
                        <span class="font-body text-gray-400 text-xs">Opsional</span>
                    </div>
                    <div class="admin-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                            <div class="form-group">
                                <label class="form-label">Tanggal Akad</label>
                                <input type="date" name="akad_date"
                                       value="<?= clean($form['akad_date'] ?? '') ?>"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jam Akad</label>
                                <input type="time" name="akad_time"
                                       value="<?= clean($form['akad_time'] ?? '') ?>"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Tempat</label>
                                <input type="text" name="akad_venue"
                                       value="<?= clean($form['akad_venue'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Masjid Al-Ikhlas">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Link Google Maps</label>
                                <input type="url" name="akad_maps_url"
                                       value="<?= clean($form['akad_maps_url'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="https://maps.google.com/...">
                            </div>

                            <div class="form-group md:col-span-2">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="akad_address" rows="2" class="form-control"
                                          placeholder="Jl. Merdeka No. 10, Bangil, Pasuruan"><?= clean($form['akad_address'] ?? '') ?></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 5: DETAIL RESEPSI               -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-cup-hot text-gold me-2"></i>Resepsi
                        </span>
                        <span class="font-body text-red-400 text-xs">Wajib diisi</span>
                    </div>
                    <div class="admin-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                            <div class="form-group">
                                <label class="form-label">
                                    Tanggal Resepsi <span class="text-red-400">*</span>
                                </label>
                                <input type="date" name="reception_date"
                                       value="<?= clean($form['reception_date'] ?? '') ?>"
                                       class="form-control" required>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Gedung / Tempat</label>
                                <input type="text" name="reception_venue"
                                       value="<?= clean($form['reception_venue'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Gedung Serbaguna Bangil">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jam Mulai</label>
                                <input type="time" name="reception_time_start"
                                       value="<?= clean($form['reception_time_start'] ?? '') ?>"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Jam Selesai</label>
                                <input type="time" name="reception_time_end"
                                       value="<?= clean($form['reception_time_end'] ?? '') ?>"
                                       class="form-control">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Link Google Maps</label>
                                <input type="url" name="reception_maps_url"
                                       value="<?= clean($form['reception_maps_url'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="https://maps.google.com/...">
                            </div>

                            <div class="form-group md:col-span-2">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="reception_address" rows="2" class="form-control"
                                          placeholder="Jl. Raya Bangil No. 45, Pasuruan, Jawa Timur"><?= clean($form['reception_address'] ?? '') ?></textarea>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 6: LOVE STORY & REKENING       -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-heart text-gold me-2"></i>Love Story & Gift
                        </span>
                        <span class="font-body text-gray-400 text-xs">Opsional</span>
                    </div>
                    <div class="admin-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

                            <div class="form-group md:col-span-2">
                                <label class="form-label">Kisah Cinta (Love Story)</label>
                                <textarea name="love_story" rows="4" class="form-control"
                                          placeholder="Ceritakan bagaimana kalian bertemu dan jatuh cinta..."><?= clean($form['love_story'] ?? '') ?></textarea>
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Bank</label>
                                <input type="text" name="bank_name"
                                       value="<?= clean($form['bank_name'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: BCA / BRI / Mandiri">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" name="bank_account_number"
                                       value="<?= clean($form['bank_account_number'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: 1234567890">
                            </div>

                            <div class="form-group">
                                <label class="form-label">Nama Pemilik Rekening</label>
                                <input type="text" name="bank_account_name"
                                       value="<?= clean($form['bank_account_name'] ?? '') ?>"
                                       class="form-control"
                                       placeholder="Contoh: Muhammad Rizky Pratama">
                            </div>

                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 7: PENGATURAN PUBLIKASI         -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-8">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-globe text-gold me-2"></i>Pengaturan
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_published" value="1"
                                   <?= ($form['is_published'] ?? 0) ? 'checked' : '' ?>
                                   class="w-4 h-4 accent-[#C9A84C]">
                            <div>
                                <div class="font-body text-charcoal text-sm font-medium">
                                    Publikasikan undangan sekarang
                                </div>
                                <div class="font-body text-gray-400 text-xs">
                                    Jika dicentang, undangan langsung bisa diakses tamu.
                                    Jika tidak, tersimpan sebagai draft.
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- ── Tombol Submit ── -->
                <div class="flex items-center gap-4">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-save"></i>
                        SIMPAN UNDANGAN
                    </button>
                    <a href="weddings.php" class="btn-secondary">
                        BATAL
                    </a>
                </div>

            </form>
        </div><!-- /p-8 -->
    </main>
</div>

<script>
// ── Highlight template yang dipilih ──────────────────────────
// Ketika radio button dipilih, beri border gold pada kotaknya
document.querySelectorAll('.template-radio').forEach(function(radio) {
    // Cek saat halaman load (untuk sticky form setelah error)
    if (radio.checked) {
        radio.nextElementSibling.style.borderColor = '#C9A84C';
        radio.nextElementSibling.style.background  = 'rgba(201,168,76,0.05)';
    }

    radio.addEventListener('change', function() {
        // Reset semua
        document.querySelectorAll('.template-option').forEach(function(opt) {
            opt.style.borderColor = '#E5E7EB';
            opt.style.background  = 'white';
        });
        // Aktifkan yang dipilih
        this.nextElementSibling.style.borderColor = '#C9A84C';
        this.nextElementSibling.style.background  = 'rgba(201,168,76,0.05)';
    });
});

// ── Preview slug otomatis ────────────────────────────────────
// Tampilkan slug yang akan di-generate berdasarkan nama yang diisi
function updateSlugPreview() {
    const groom = document.querySelector('[name="groom_name"]').value;
    const bride = document.querySelector('[name="bride_name"]').value;
    if (groom && bride) {
        const slug = (groom + '-' + bride)
            .toLowerCase()
            .replace(/[^a-z0-9]+/g, '-')
            .replace(/^-|-$/g, '');
        document.getElementById('slug-preview').textContent = '/invitation/' + slug;
        document.getElementById('slug-box').classList.remove('hidden');
    } else {
        document.getElementById('slug-box').classList.add('hidden');
    }
}

// Pasang event listener pada kedua input nama
document.querySelector('[name="groom_name"]').addEventListener('input', updateSlugPreview);
document.querySelector('[name="bride_name"]').addEventListener('input', updateSlugPreview);
</script>

<!-- Preview slug (muncul setelah nama diisi) -->
<div id="slug-box"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 bg-charcoal text-white
            font-body text-xs px-4 py-2 shadow-lg hidden z-50">
    🔗 URL Undangan: <span id="slug-preview" class="text-gold font-medium"></span>
</div>

</body>
</html>
