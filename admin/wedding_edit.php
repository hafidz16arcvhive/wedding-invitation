<?php
// ============================================================
// FILE: admin/wedding_edit.php
// Form edit undangan yang sudah ada (bagian UPDATE dari CRUD)
//
// Perbedaan dengan wedding_add.php:
// - Perlu ambil data lama dari DB (berdasarkan ID di URL)
// - Form sudah terisi dengan data lama
// - Pakai UPDATE bukan INSERT
// - Slug tidak diubah (agar link tamu tidak rusak)
// - Bisa upload foto couple & QRIS
// ============================================================

require_once '../config/database.php';
requireLogin();

// ── Ambil ID dari URL ─────────────────────────────────────────
// Contoh URL: wedding_edit.php?id=5
$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    redirect(BASE_URL . '/admin/weddings.php');
}

// ── Ambil data undangan yang akan diedit ─────────────────────
$wedding = db_row("SELECT * FROM weddings WHERE id = ?", [$id], 'i');

if (!$wedding) {
    $_SESSION['flash_message'] = 'Undangan tidak ditemukan.';
    $_SESSION['flash_type']    = 'error';
    redirect(BASE_URL . '/admin/weddings.php');
}

// ── Ambil daftar template ─────────────────────────────────────
$templates = db_rows("SELECT id, name FROM invitation_templates WHERE is_active = 1 ORDER BY id");

// ── Inisialisasi errors & form data ──────────────────────────
$errors = [];
// Gunakan data dari DB sebagai nilai awal form
// Kalau POST (ada error validasi), pakai data POST agar sticky
$form = $wedding;

// ── Proses POST request ───────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil semua input form
    $form = [
        'id'                   => $id,  // tetap
        'slug'                 => $wedding['slug'], // slug tidak berubah
        'template_id'          => (int) ($_POST['template_id'] ?? $wedding['template_id']),
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
        // Pertahankan foto lama dulu
        'couple_photo'         => $wedding['couple_photo'],
        'qris_image'           => $wedding['qris_image'],
    ];

    // ── Validasi ──────────────────────────────────────────────
    if (empty($form['groom_name']))     $errors[] = 'Nama panggilan mempelai pria wajib diisi.';
    if (empty($form['bride_name']))     $errors[] = 'Nama panggilan mempelai wanita wajib diisi.';
    if (empty($form['reception_date'])) $errors[] = 'Tanggal resepsi wajib diisi.';

    // ── Upload foto couple ─────────────────────────────────────
    // Cek apakah ada file yang diupload
    if (!empty($_FILES['couple_photo']['name'])) {
        $upload_result = uploadImage('couple_photo', 'assets/images/couples/');
        if ($upload_result['success']) {
            // Hapus foto lama jika ada
            if (!empty($wedding['couple_photo']) && file_exists('../' . $wedding['couple_photo'])) {
                unlink('../' . $wedding['couple_photo']);
            }
            $form['couple_photo'] = $upload_result['path'];
        } else {
            $errors[] = 'Foto couple: ' . $upload_result['error'];
        }
    }

    // ── Upload gambar QRIS ─────────────────────────────────────
    if (!empty($_FILES['qris_image']['name'])) {
        $upload_result = uploadImage('qris_image', 'assets/images/qris/');
        if ($upload_result['success']) {
            if (!empty($wedding['qris_image']) && file_exists('../' . $wedding['qris_image'])) {
                unlink('../' . $wedding['qris_image']);
            }
            $form['qris_image'] = $upload_result['path'];
        } else {
            $errors[] = 'Gambar QRIS: ' . $upload_result['error'];
        }
    }

    // ── Update ke database ────────────────────────────────────
    if (empty($errors)) {
        $affected = db_execute("
            UPDATE weddings SET
                template_id          = ?,
                groom_name           = ?,
                groom_full_name      = ?,
                groom_father         = ?,
                groom_mother         = ?,
                bride_name           = ?,
                bride_full_name      = ?,
                bride_father         = ?,
                bride_mother         = ?,
                akad_date            = ?,
                akad_time            = ?,
                akad_venue           = ?,
                akad_address         = ?,
                akad_maps_url        = ?,
                reception_date       = ?,
                reception_time_start = ?,
                reception_time_end   = ?,
                reception_venue      = ?,
                reception_address    = ?,
                reception_maps_url   = ?,
                love_story           = ?,
                bank_name            = ?,
                bank_account_number  = ?,
                bank_account_name    = ?,
                couple_photo         = ?,
                qris_image           = ?,
                is_published         = ?
            WHERE id = ?
        ", [
            $form['template_id'],
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
            $form['couple_photo'],      $form['qris_image'],
            $form['is_published'],
            $id,
        ]);

        $_SESSION['flash_message'] = 'Undangan berhasil diperbarui!';
        $_SESSION['flash_type']    = 'success';
        redirect(BASE_URL . '/admin/weddings.php');
    }
}

// ── Helper: Upload gambar ─────────────────────────────────────
/**
 * Upload file gambar ke folder tertentu
 * @param string $field_name  Nama input file di form
 * @param string $folder      Folder tujuan, relatif dari root project
 * @return array              ['success'=>bool, 'path'=>string, 'error'=>string]
 */
function uploadImage(string $field_name, string $folder): array {
    $file     = $_FILES[$field_name];
    $max_size = 2 * 1024 * 1024; // 2MB

    // Cek ada file yang diupload
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'error' => 'Gagal upload file.'];
    }

    // Cek ukuran file
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'Ukuran file maksimal 2MB.'];
    }

    // Cek tipe file (hanya gambar)
    $allowed_types = ['image/jpeg', 'image/png', 'image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    if (!in_array($mime, $allowed_types)) {
        return ['success' => false, 'error' => 'Format file harus JPG, PNG, atau WebP.'];
    }

    // Buat nama file unik agar tidak tertimpa
    $ext      = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . strtolower($ext);

    // Buat folder jika belum ada
    $full_folder = BASE_PATH . '/' . $folder;
    if (!is_dir($full_folder)) {
        mkdir($full_folder, 0755, true);
    }

    // Pindahkan file dari temporary ke folder tujuan
    $dest = $full_folder . $filename;
    if (move_uploaded_file($file['tmp_name'], $dest)) {
        return ['success' => true, 'path' => $folder . $filename];
    }

    return ['success' => false, 'error' => 'Gagal memindahkan file.'];
}

$page_title = 'Edit Undangan — ' . $form['groom_name'] . ' & ' . $form['bride_name'];
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
                <h1 class="section-title">
                    Edit: <?= clean($form['groom_name']) ?> ♡ <?= clean($form['bride_name']) ?>
                </h1>
                <p class="font-body text-gray-400 text-xs mt-0.5">
                    URL: <code class="bg-gray-100 px-1">/invitation/<?= clean($form['slug']) ?></code>
                    · <?= $form['views'] ?> views
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="<?= BASE_URL ?>/invitation/<?= clean($form['slug']) ?>"
                   target="_blank"
                   class="btn-secondary text-xs">
                    <i class="bi bi-eye"></i> PREVIEW
                </a>
                <a href="weddings.php" class="btn-secondary">
                    <i class="bi bi-arrow-left"></i> KEMBALI
                </a>
            </div>
        </header>

        <div class="p-8">

            <!-- Error validasi -->
            <?php if (!empty($errors)): ?>
            <div class="alert-error px-4 py-3 mb-6 font-body text-sm">
                <div class="flex items-center gap-2 mb-2 font-medium">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    Mohon perbaiki:
                </div>
                <ul class="list-disc list-inside space-y-1">
                    <?php foreach ($errors as $err): ?>
                    <li><?= clean($err) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <!-- Form edit — enctype multipart untuk upload file -->
            <form method="POST" action="?id=<?= $id ?>" enctype="multipart/form-data">

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 1: TEMPLATE                     -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-palette text-gold me-2"></i>Template Undangan
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                            <?php foreach ($templates as $tpl): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="template_id"
                                       value="<?= $tpl['id'] ?>"
                                       class="hidden template-radio"
                                       <?= $form['template_id'] == $tpl['id'] ? 'checked' : '' ?>>
                                <div class="template-option border-2 p-4 text-center transition-all duration-200
                                            <?= $form['template_id'] == $tpl['id'] ? 'border-gold bg-gold/5' : 'border-gray-200 hover:border-gold' ?>">
                                    <div class="text-3xl mb-2">💌</div>
                                    <div class="font-body text-sm font-medium text-charcoal">
                                        <?= clean($tpl['name']) ?>
                                    </div>
                                </div>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 2 & 3: MEMPELAI (digabung)     -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-people text-gold me-2"></i>Data Mempelai
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <!-- Tab Pria / Wanita -->
                        <div class="flex gap-1 mb-6 border-b border-gray-100">
                            <button type="button" onclick="showTab('pria')"
                                    id="tab-pria"
                                    class="tab-btn px-4 py-2 font-body text-sm border-b-2 border-gold text-gold -mb-px">
                                <i class="bi bi-person me-1"></i> Mempelai Pria
                            </button>
                            <button type="button" onclick="showTab('wanita')"
                                    id="tab-wanita"
                                    class="tab-btn px-4 py-2 font-body text-sm border-b-2 border-transparent text-gray-400 -mb-px">
                                <i class="bi bi-person me-1"></i> Mempelai Wanita
                            </button>
                        </div>

                        <!-- Panel Pria -->
                        <div id="panel-pria">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                                <div class="form-group">
                                    <label class="form-label">Nama Panggilan *</label>
                                    <input type="text" name="groom_name"
                                           value="<?= clean($form['groom_name']) ?>"
                                           class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="groom_full_name"
                                           value="<?= clean($form['groom_full_name'] ?? '') ?>"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nama Ayah</label>
                                    <input type="text" name="groom_father"
                                           value="<?= clean($form['groom_father'] ?? '') ?>"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nama Ibu</label>
                                    <input type="text" name="groom_mother"
                                           value="<?= clean($form['groom_mother'] ?? '') ?>"
                                           class="form-control">
                                </div>
                            </div>
                        </div>

                        <!-- Panel Wanita -->
                        <div id="panel-wanita" class="hidden">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                                <div class="form-group">
                                    <label class="form-label">Nama Panggilan *</label>
                                    <input type="text" name="bride_name"
                                           value="<?= clean($form['bride_name']) ?>"
                                           class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nama Lengkap</label>
                                    <input type="text" name="bride_full_name"
                                           value="<?= clean($form['bride_full_name'] ?? '') ?>"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nama Ayah</label>
                                    <input type="text" name="bride_father"
                                           value="<?= clean($form['bride_father'] ?? '') ?>"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nama Ibu</label>
                                    <input type="text" name="bride_mother"
                                           value="<?= clean($form['bride_mother'] ?? '') ?>"
                                           class="form-control">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 4 & 5: AKAD + RESEPSI          -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-calendar3 text-gold me-2"></i>Detail Acara
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">

                            <!-- AKAD -->
                            <div>
                                <h4 class="font-body font-medium text-charcoal text-sm mb-4 pb-2 border-b border-gray-100">
                                    <i class="bi bi-moon-stars text-gold me-1"></i>Akad Nikah
                                </h4>
                                <div class="form-group">
                                    <label class="form-label">Tanggal</label>
                                    <input type="date" name="akad_date"
                                           value="<?= clean($form['akad_date'] ?? '') ?>"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Jam</label>
                                    <input type="time" name="akad_time"
                                           value="<?= clean($form['akad_time'] ?? '') ?>"
                                           class="form-control">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nama Tempat</label>
                                    <input type="text" name="akad_venue"
                                           value="<?= clean($form['akad_venue'] ?? '') ?>"
                                           class="form-control" placeholder="Masjid Al-Ikhlas">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="akad_address" rows="2"
                                              class="form-control"><?= clean($form['akad_address'] ?? '') ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Link Google Maps</label>
                                    <input type="url" name="akad_maps_url"
                                           value="<?= clean($form['akad_maps_url'] ?? '') ?>"
                                           class="form-control" placeholder="https://maps.google.com/...">
                                </div>
                            </div>

                            <!-- RESEPSI -->
                            <div>
                                <h4 class="font-body font-medium text-charcoal text-sm mb-4 pb-2 border-b border-gray-100">
                                    <i class="bi bi-cup-hot text-gold me-1"></i>Resepsi <span class="text-red-400">*</span>
                                </h4>
                                <div class="form-group">
                                    <label class="form-label">Tanggal *</label>
                                    <input type="date" name="reception_date"
                                           value="<?= clean($form['reception_date'] ?? '') ?>"
                                           class="form-control" required>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
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
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Nama Tempat</label>
                                    <input type="text" name="reception_venue"
                                           value="<?= clean($form['reception_venue'] ?? '') ?>"
                                           class="form-control" placeholder="Gedung Serbaguna">
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Alamat</label>
                                    <textarea name="reception_address" rows="2"
                                              class="form-control"><?= clean($form['reception_address'] ?? '') ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Link Google Maps</label>
                                    <input type="url" name="reception_maps_url"
                                           value="<?= clean($form['reception_maps_url'] ?? '') ?>"
                                           class="form-control" placeholder="https://maps.google.com/...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 6: FOTO & QRIS (upload)        -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-image text-gold me-2"></i>Foto & QRIS
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">

                            <!-- Upload foto couple -->
                            <div class="form-group">
                                <label class="form-label">Foto Bersama (Couple)</label>
                                <?php if (!empty($form['couple_photo'])): ?>
                                <div class="mb-3">
                                    <img src="<?= BASE_URL ?>/<?= clean($form['couple_photo']) ?>"
                                         alt="Foto Couple"
                                         class="w-full h-40 object-cover border border-gray-200">
                                    <p class="font-body text-gray-400 text-xs mt-1">Foto saat ini</p>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="couple_photo" class="form-control"
                                       accept="image/jpeg,image/png,image/webp">
                                <p class="font-body text-gray-400 text-xs mt-1">
                                    JPG/PNG/WebP, maks. 2MB.
                                    <?= empty($form['couple_photo']) ? '' : 'Kosongkan jika tidak ingin mengubah.' ?>
                                </p>
                            </div>

                            <!-- Upload QRIS -->
                            <div class="form-group">
                                <label class="form-label">Gambar QRIS</label>
                                <?php if (!empty($form['qris_image'])): ?>
                                <div class="mb-3">
                                    <img src="<?= BASE_URL ?>/<?= clean($form['qris_image']) ?>"
                                         alt="QRIS"
                                         class="w-32 h-32 object-contain border border-gray-200 p-2">
                                    <p class="font-body text-gray-400 text-xs mt-1">QRIS saat ini</p>
                                </div>
                                <?php endif; ?>
                                <input type="file" name="qris_image" class="form-control"
                                       accept="image/jpeg,image/png,image/webp">
                                <p class="font-body text-gray-400 text-xs mt-1">
                                    Screenshot QRIS dari aplikasi bank. Maks. 2MB.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 7: LOVE STORY & REKENING       -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-6">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-heart text-gold me-2"></i>Love Story & Gift
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
                            <div class="form-group md:col-span-2">
                                <label class="form-label">Kisah Cinta</label>
                                <textarea name="love_story" rows="4"
                                          class="form-control"><?= clean($form['love_story'] ?? '') ?></textarea>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Bank</label>
                                <input type="text" name="bank_name"
                                       value="<?= clean($form['bank_name'] ?? '') ?>"
                                       class="form-control" placeholder="BCA / BRI / Mandiri">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nomor Rekening</label>
                                <input type="text" name="bank_account_number"
                                       value="<?= clean($form['bank_account_number'] ?? '') ?>"
                                       class="form-control">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Pemilik Rekening</label>
                                <input type="text" name="bank_account_name"
                                       value="<?= clean($form['bank_account_name'] ?? '') ?>"
                                       class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ══════════════════════════════════════ -->
                <!-- BAGIAN 8: PENGATURAN                  -->
                <!-- ══════════════════════════════════════ -->
                <div class="admin-card mb-8">
                    <div class="admin-card-header">
                        <span class="section-title text-lg">
                            <i class="bi bi-gear text-gold me-2"></i>Pengaturan
                        </span>
                    </div>
                    <div class="admin-card-body">
                        <label class="flex items-center gap-3 cursor-pointer">
                            <input type="checkbox" name="is_published" value="1"
                                   <?= $form['is_published'] ? 'checked' : '' ?>
                                   class="w-4 h-4 accent-[#C9A84C]">
                            <div>
                                <div class="font-body text-charcoal text-sm font-medium">
                                    Undangan dipublikasikan
                                </div>
                                <div class="font-body text-gray-400 text-xs">
                                    Tamu dapat mengakses undangan jika ini dicentang.
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Tombol aksi -->
                <div class="flex items-center gap-4">
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-save"></i> SIMPAN PERUBAHAN
                    </button>
                    <a href="weddings.php" class="btn-secondary">BATAL</a>
                </div>

            </form>
        </div>
    </main>
</div>

<script>
// ── Template selector ──
document.querySelectorAll('.template-radio').forEach(function(radio) {
    radio.addEventListener('change', function() {
        document.querySelectorAll('.template-option').forEach(function(opt) {
            opt.className = opt.className
                .replace('border-gold bg-gold/5', '')
                .replace('border-gray-200', '')
                .trim();
            opt.classList.add('border-gray-200');
        });
        this.nextElementSibling.classList.remove('border-gray-200');
        this.nextElementSibling.classList.add('border-gold', 'bg-gold/5');
    });
});

// ── Tab mempelai ──
function showTab(name) {
    // Sembunyikan semua panel
    document.getElementById('panel-pria').classList.add('hidden');
    document.getElementById('panel-wanita').classList.add('hidden');

    // Tampilkan panel yang dipilih
    document.getElementById('panel-' + name).classList.remove('hidden');

    // Update style tombol tab
    document.querySelectorAll('.tab-btn').forEach(function(btn) {
        btn.classList.remove('border-gold', 'text-gold');
        btn.classList.add('border-transparent', 'text-gray-400');
    });
    const activeTab = document.getElementById('tab-' + name);
    activeTab.classList.remove('border-transparent', 'text-gray-400');
    activeTab.classList.add('border-gold', 'text-gold');
}

// ── Preview gambar sebelum upload ──
function previewImage(input, previewId) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const prev = document.getElementById(previewId);
            if (prev) prev.src = e.target.result;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>

</body>
</html>
