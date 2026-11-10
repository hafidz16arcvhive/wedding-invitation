<?php
// ============================================================
// FILE: admin/settings.php
// Pengaturan website — nama, WA CS, email, dll
// ============================================================
require_once '../config/database.php';
requireLogin();

$flash = $flash_type = '';
if (!empty($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// ── Proses simpan pengaturan ──────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $settings_to_save = [
        'site_name'        => trim($_POST['site_name'] ?? ''),
        'site_tagline'     => trim($_POST['site_tagline'] ?? ''),
        'whatsapp_number'  => preg_replace('/[^0-9]/', '', $_POST['whatsapp_number'] ?? ''),
        'email_cs'         => trim($_POST['email_cs'] ?? ''),
        'instagram'        => trim($_POST['instagram'] ?? ''),
        'maintenance_mode' => isset($_POST['maintenance_mode']) ? '1' : '0',
    ];

    // ── Simpan password baru jika diisi ──────────────────────
    if (!empty($_POST['new_password'])) {
        $new_pass    = $_POST['new_password'];
        $confirm_pass = $_POST['confirm_password'] ?? '';

        if (strlen($new_pass) < 6) {
            $_SESSION['flash_message'] = 'Password minimal 6 karakter.';
            $_SESSION['flash_type']    = 'error';
            redirect(BASE_URL . '/admin/settings.php');
        }
        if ($new_pass !== $confirm_pass) {
            $_SESSION['flash_message'] = 'Konfirmasi password tidak cocok.';
            $_SESSION['flash_type']    = 'error';
            redirect(BASE_URL . '/admin/settings.php');
        }
        // Hash password baru dan simpan
        $hashed = password_hash($new_pass, PASSWORD_BCRYPT);
        db_execute("UPDATE admin SET password = ? WHERE id = ?",
            [$hashed, $_SESSION['admin_id']], 'si');
    }

    // Simpan semua setting ke database
    foreach ($settings_to_save as $key => $value) {
        // Cek apakah key sudah ada
        $existing = db_row("SELECT id FROM settings WHERE setting_key = ?", [$key], 's');
        if ($existing) {
            db_execute("UPDATE settings SET setting_value = ? WHERE setting_key = ?",
                [$value, $key], 'ss');
        } else {
            db_insert("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)",
                [$key, $value], 'ss');
        }
    }

    $_SESSION['flash_message'] = 'Pengaturan berhasil disimpan!';
    $_SESSION['flash_type']    = 'success';
    redirect(BASE_URL . '/admin/settings.php');
}

// ── Ambil semua setting dari database ────────────────────────
$all_settings = db_rows("SELECT setting_key, setting_value FROM settings");
$settings = [];
foreach ($all_settings as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}

// Info admin yang login
$admin = db_row("SELECT * FROM admin WHERE id = ?", [$_SESSION['admin_id']], 'i');

$page_title = 'Pengaturan';
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50">
<div class="flex min-h-screen">
<?php include '_sidebar.php'; ?>
<main class="flex-1 overflow-auto">

  <header class="bg-white border-b border-gray-100 px-8 py-4 sticky top-0 z-10">
    <h1 class="section-title">Pengaturan Website</h1>
    <p class="font-body text-gray-400 text-xs mt-0.5">Kelola konfigurasi website Anda</p>
  </header>

  <div class="p-8 max-w-3xl">

    <?php if ($flash): ?>
    <div class="alert-<?= $flash_type ?> px-4 py-3 mb-6 flex items-center gap-2 font-body text-sm">
      <i class="bi bi-<?= $flash_type==='success'?'check-circle-fill':'exclamation-triangle-fill' ?>"></i>
      <?= clean($flash) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="">

      <!-- ── A. INFORMASI WEBSITE ── -->
      <div class="admin-card mb-6">
        <div class="admin-card-header">
          <span class="section-title text-lg">
            <i class="bi bi-globe text-gold me-2"></i>Informasi Website
          </span>
        </div>
        <div class="admin-card-body">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

            <div class="form-group">
              <label class="form-label">Nama Website</label>
              <input type="text" name="site_name" class="form-control"
                     value="<?= clean($settings['site_name'] ?? 'LoveStory Invitation') ?>"
                     placeholder="LoveStory Invitation">
            </div>

            <div class="form-group">
              <label class="form-label">Tagline</label>
              <input type="text" name="site_tagline" class="form-control"
                     value="<?= clean($settings['site_tagline'] ?? '') ?>"
                     placeholder="Undangan Pernikahan Digital Elegan">
            </div>

          </div>
        </div>
      </div>

      <!-- ── B. KONTAK CUSTOMER SERVICE ── -->
      <div class="admin-card mb-6">
        <div class="admin-card-header">
          <span class="section-title text-lg">
            <i class="bi bi-headset text-gold me-2"></i>Customer Service
          </span>
        </div>
        <div class="admin-card-body">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">

            <div class="form-group">
              <label class="form-label">Nomor WhatsApp CS</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-body text-gray-400 text-sm">+</span>
                <input type="text" name="whatsapp_number" class="form-control pl-7"
                       value="<?= clean($settings['whatsapp_number'] ?? '') ?>"
                       placeholder="6281234567890">
              </div>
              <p class="font-body text-gray-400 text-xs mt-1">
                Format internasional tanpa + (contoh: 6281234567890)
              </p>
            </div>

            <div class="form-group">
              <label class="form-label">Email CS</label>
              <input type="email" name="email_cs" class="form-control"
                     value="<?= clean($settings['email_cs'] ?? '') ?>"
                     placeholder="cs@lovestory.id">
            </div>

            <div class="form-group">
              <label class="form-label">Instagram</label>
              <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 font-body text-gray-400 text-sm">@</span>
                <input type="text" name="instagram" class="form-control pl-8"
                       value="<?= clean(ltrim($settings['instagram'] ?? '', '@')) ?>"
                       placeholder="lovestoryinvitation">
              </div>
            </div>

          </div>

          <!-- Preview tombol WA -->
          <?php if (!empty($settings['whatsapp_number'])): ?>
          <div class="bg-gray-50 p-4 border border-gray-100 mt-2">
            <p class="font-body text-xs text-gray-400 mb-2">Preview tombol WhatsApp:</p>
            <a href="https://wa.me/<?= clean($settings['whatsapp_number']) ?>"
               target="_blank"
               class="inline-flex items-center gap-2 bg-green-500 text-white font-body
                      text-xs font-medium px-4 py-2">
              <i class="bi bi-whatsapp"></i>
              Chat CS via WhatsApp
            </a>
          </div>
          <?php endif; ?>
        </div>
      </div>

      <!-- ── C. MODE MAINTENANCE ── -->
      <div class="admin-card mb-6">
        <div class="admin-card-header">
          <span class="section-title text-lg">
            <i class="bi bi-tools text-gold me-2"></i>Mode Maintenance
          </span>
        </div>
        <div class="admin-card-body">
          <label class="flex items-start gap-3 cursor-pointer">
            <input type="checkbox" name="maintenance_mode" value="1"
                   <?= ($settings['maintenance_mode'] ?? '0') === '1' ? 'checked' : '' ?>
                   class="mt-1 w-4 h-4 accent-[#C9A84C]">
            <div>
              <div class="font-body text-charcoal text-sm font-medium">
                Aktifkan Mode Maintenance
              </div>
              <div class="font-body text-gray-400 text-xs mt-1">
                Website tidak bisa diakses pengunjung. Hanya admin yang bisa masuk.
                <span class="text-red-500 font-medium">Gunakan dengan hati-hati!</span>
              </div>
            </div>
          </label>
        </div>
      </div>

      <!-- ── D. GANTI PASSWORD ADMIN ── -->
      <div class="admin-card mb-8">
        <div class="admin-card-header">
          <span class="section-title text-lg">
            <i class="bi bi-shield-lock text-gold me-2"></i>Keamanan Admin
          </span>
        </div>
        <div class="admin-card-body">
          <p class="font-body text-gray-400 text-xs mb-4">
            Username saat ini: <strong class="text-charcoal"><?= clean($admin['username'] ?? '') ?></strong>
            &nbsp;·&nbsp; Kosongkan jika tidak ingin ganti password.
          </p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6">
            <div class="form-group">
              <label class="form-label">Password Baru</label>
              <input type="password" name="new_password" class="form-control"
                     placeholder="Minimal 6 karakter" autocomplete="new-password">
            </div>
            <div class="form-group">
              <label class="form-label">Konfirmasi Password</label>
              <input type="password" name="confirm_password" class="form-control"
                     placeholder="Ulangi password baru" autocomplete="new-password">
            </div>
          </div>
        </div>
      </div>

      <!-- Tombol simpan -->
      <div class="flex gap-3">
        <button type="submit" class="btn-primary">
          <i class="bi bi-save"></i> SIMPAN PENGATURAN
        </button>
      </div>

    </form>

    <!-- ── INFO SISTEM ── -->
    <div class="admin-card mt-8">
      <div class="admin-card-header">
        <span class="section-title text-lg">
          <i class="bi bi-info-circle text-gold me-2"></i>Info Sistem
        </span>
      </div>
      <div class="admin-card-body">
        <div class="grid grid-cols-2 gap-4 font-body text-sm">
          <?php
          $info = [
            'PHP Version'  => PHP_VERSION,
            'Base URL'     => BASE_URL,
            'Database'     => DB_NAME,
            'Server'       => $_SERVER['SERVER_SOFTWARE'] ?? 'XAMPP',
            'Tanggal Server'=> date('d F Y, H:i'),
            'Total Undangan'=> db_row("SELECT COUNT(*) AS c FROM weddings")['c'],
          ];
          foreach ($info as $k => $v): ?>
          <div class="flex justify-between py-2 border-b border-gray-50">
            <span class="text-gray-400"><?= $k ?></span>
            <span class="text-charcoal font-medium text-right"><?= clean((string)$v) ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

  </div>
</main>
</div>
</body></html>