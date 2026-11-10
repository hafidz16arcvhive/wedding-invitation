<?php
// ============================================================
// FILE: order.php
// Halaman pemesanan undangan untuk pengunjung
//
// Alur:
// 1. GET  → tampilkan form (bisa pre-select template dari ?template=ID)
// 2. POST → validasi → simpan ke DB → redirect ke order_success.php
// ============================================================
require_once 'config/database.php';

// ── Data Paket (disimpan sebagai array, bukan DB) ─────────────
// Kenapa bukan DB? Karena paket jarang berubah dan lebih mudah dikelola di kode
$packages = [
    'basic' => [
        'label'   => 'Basic',
        'price'   => 99000,
        'color'   => 'gray',
        'desc'    => 'Cocok untuk undangan sederhana dan personal',
        'features'=> [
            '1 Desain Template',
            'Countdown Timer',
            'Detail Acara',
            'Form RSVP',
            'Wall Ucapan',
            'Aktif 3 Bulan',
        ],
        'not_included' => ['Galeri Foto', 'Musik Background', 'QRIS Gift', 'Custom Domain'],
    ],
    'premium' => [
        'label'   => 'Premium',
        'price'   => 199000,
        'color'   => 'black',
        'desc'    => 'Paling populer — lengkap dengan galeri & musik',
        'features'=> [
            '1 Desain Template',
            'Countdown Timer',
            'Detail Acara',
            'Form RSVP',
            'Wall Ucapan',
            'Galeri Foto (maks 9)',
            'Musik Background',
            'QRIS & Rekening Gift',
            'Love Story',
            'Aktif 6 Bulan',
        ],
        'not_included' => ['Custom Domain'],
        'popular' => true,
    ],
    'luxury' => [
        'label'   => 'Luxury',
        'price'   => 349000,
        'color'   => 'gold',
        'desc'    => 'Semua fitur lengkap + prioritas support',
        'features'=> [
            'Semua fitur Premium',
            'Galeri Foto tak terbatas',
            'Video Background',
            'Custom Domain (.com)',
            'Prioritas Revisi',
            'WhatsApp Support',
            'Aktif 12 Bulan',
            'Custom Design (opsional)',
        ],
        'not_included' => [],
    ],
];

// ── Ambil semua template aktif ────────────────────────────────
$templates = db_rows("SELECT * FROM invitation_templates WHERE is_active = 1 ORDER BY id");

// ── Pre-select template dari URL (?template=1) ────────────────
$selected_template = (int)($_GET['template'] ?? 0);
$selected_package  = $_GET['package'] ?? 'premium'; // default premium
if (!isset($packages[$selected_package])) $selected_package = 'premium';

// ── Inisialisasi variabel ─────────────────────────────────────
$errors = [];
$form   = [];
$step   = (int)($_GET['step'] ?? 1); // Step 1: Pilih paket, Step 2: Isi form

// ── Proses POST (submit form) ─────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Ambil semua input
    $form = [
        'template_id'    => (int)($_POST['template_id'] ?? 0),
        'package_type'   => $_POST['package_type'] ?? 'premium',
        'customer_name'  => trim($_POST['customer_name'] ?? ''),
        'customer_email' => trim($_POST['customer_email'] ?? ''),
        'customer_phone' => trim($_POST['customer_phone'] ?? ''),
        'groom_name'     => trim($_POST['groom_name'] ?? ''),
        'bride_name'     => trim($_POST['bride_name'] ?? ''),
        'event_date'     => trim($_POST['event_date'] ?? ''),
        'notes'          => trim($_POST['notes'] ?? ''),
    ];

    // Validasi paket
    if (!isset($packages[$form['package_type']])) $form['package_type'] = 'premium';

    // ── Validasi input ────────────────────────────────────────
    if (empty($form['customer_name']))
        $errors[] = 'Nama pemesan wajib diisi.';

    if (empty($form['customer_email']) || !filter_var($form['customer_email'], FILTER_VALIDATE_EMAIL))
        $errors[] = 'Email tidak valid.';

    if (empty($form['customer_phone']))
        $errors[] = 'Nomor WhatsApp wajib diisi.';

    if ($form['template_id'] <= 0)
        $errors[] = 'Pilih template undangan terlebih dahulu.';

    if (empty($form['groom_name']))
        $errors[] = 'Nama mempelai pria wajib diisi.';

    if (empty($form['bride_name']))
        $errors[] = 'Nama mempelai wanita wajib diisi.';

    if (empty($form['event_date']))
        $errors[] = 'Tanggal acara wajib diisi.';

    // ── Simpan ke database jika tidak ada error ───────────────
    if (empty($errors)) {
        // Generate nomor order unik
        // Format: WED-YYYYMMDD-XXXX (contoh: WED-20250817-4821)
        $order_number = 'WED-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));

        // Hitung harga dari paket yang dipilih
        $total_price = $packages[$form['package_type']]['price'];

        $order_id = db_insert("
            INSERT INTO orders (
                order_number, template_id, package_type,
                customer_name, customer_email, customer_phone,
                total_price, notes, status, payment_status
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'unpaid')
        ", [
            $order_number,
            $form['template_id'],
            $form['package_type'],
            $form['customer_name'],
            $form['customer_email'],
            $form['customer_phone'],
            $total_price,
            // Gabungkan info pasangan ke notes
            "Mempelai: {$form['groom_name']} & {$form['bride_name']}\n" .
            "Tanggal: {$form['event_date']}\n" .
            ($form['notes'] ? "Catatan: {$form['notes']}" : ''),
        ]);

        if ($order_id > 0) {
            // Berhasil → redirect ke halaman sukses
            redirect(BASE_URL . '/order_success.php?order=' . urlencode($order_number));
        } else {
            $errors[] = 'Gagal menyimpan pesanan. Silakan coba lagi.';
        }
    }

    // Kalau ada error, tetap di step 2
    $step = 2;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pesan Undangan — LoveStory Invitation</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        colors: {
          gold:    { DEFAULT:'#C9A84C', light:'#E8D5A3', dark:'#9A7B2F' },
          cream:   '#FAF6F0',
          charcoal:'#1C1C1E',
        },
        fontFamily: {
          display: ['"Cormorant Garamond"','serif'],
          body:    ['Jost','sans-serif'],
        }
      }}
    }
  </script>
  <style>
    body { font-family:'Jost',sans-serif; background:#FAF6F0; }

    /* Step indicator */
    .step-line { height: 1px; flex: 1; background: #E5E7EB; }
    .step-line.active { background: #1C1C1E; }

    /* Package card */
    .pkg-card {
      border: 2px solid #E5E7EB;
      transition: all 0.3s ease;
      cursor: pointer;
      position: relative;
    }
    .pkg-card:hover { border-color: #1C1C1E; }
    .pkg-card.selected { border-color: #1C1C1E; background: #FAFAFA; }
    .pkg-card.selected-gold { border-color: #C9A84C; background: #FFFDF5; }

    /* Form input */
    .inp {
      width:100%; padding:11px 14px;
      border:1px solid #E5E7EB;
      font-family:'Jost',sans-serif; font-size:14px;
      color:#1C1C1E; background:white;
      transition:border-color 0.2s;
      outline:none; border-radius:0;
    }
    .inp:focus { border-color:#1C1C1E; }
    .inp::placeholder { color:#A3A3A3; }
    textarea.inp { resize:vertical; min-height:88px; }
    label.lbl {
      display:block; font-size:11px; font-weight:500;
      letter-spacing:0.08em; text-transform:uppercase;
      color:#6B7280; margin-bottom:6px;
    }

    /* Fade in */
    @keyframes fadeUp { from{opacity:0;transform:translateY(16px)} to{opacity:1;transform:translateY(0)} }
    .fade-up { animation: fadeUp 0.5s ease forwards; }
  </style>
</head>

<body class="min-h-screen pb-20">

  <!-- ── NAV ── -->
  <nav class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
    <a href="index.php" class="font-display text-2xl text-charcoal font-light">♡ LoveStory</a>
    <a href="index.php" class="font-body text-gray-400 hover:text-charcoal text-xs tracking-widest transition-colors">
      <i class="bi bi-arrow-left me-1"></i>KEMBALI
    </a>
  </nav>

  <div class="max-w-4xl mx-auto px-4 py-10">

    <!-- ── STEP INDICATOR ── -->
    <div class="flex items-center gap-3 mb-10 max-w-xs mx-auto">
      <!-- Step 1 -->
      <div class="flex flex-col items-center gap-1">
        <div class="w-8 h-8 rounded-full flex items-center justify-center font-body text-xs font-medium
                    <?= $step >= 1 ? 'bg-charcoal text-white' : 'bg-gray-100 text-gray-400' ?>">
          <?= $step > 1 ? '<i class="bi bi-check2"></i>' : '1' ?>
        </div>
        <span class="font-body text-[10px] tracking-widest uppercase
                     <?= $step >= 1 ? 'text-charcoal' : 'text-gray-400' ?>">Paket</span>
      </div>
      <div class="step-line <?= $step >= 2 ? 'active' : '' ?> mb-4"></div>
      <!-- Step 2 -->
      <div class="flex flex-col items-center gap-1">
        <div class="w-8 h-8 rounded-full flex items-center justify-center font-body text-xs font-medium
                    <?= $step >= 2 ? 'bg-charcoal text-white' : 'bg-gray-100 text-gray-400' ?>">
          2
        </div>
        <span class="font-body text-[10px] tracking-widest uppercase
                     <?= $step >= 2 ? 'text-charcoal' : 'text-gray-400' ?>">Data</span>
      </div>
    </div>

    <!-- ════════════════════════════════════════
         STEP 1: PILIH PAKET & TEMPLATE
         ════════════════════════════════════════ -->
    <?php if ($step === 1): ?>
    <div class="fade-up">

      <div class="text-center mb-10">
        <p class="font-body text-xs text-gray-400 tracking-[0.3em] uppercase mb-2">Langkah 1</p>
        <h1 class="font-display text-4xl md:text-5xl font-light text-charcoal">Pilih Paket</h1>
        <div class="w-8 h-px bg-charcoal mx-auto mt-4"></div>
      </div>

      <!-- Kartu 3 Paket -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-12">
        <?php foreach ($packages as $key => $pkg): ?>
        <?php
          $is_popular = !empty($pkg['popular']);
          $card_class = $key === $selected_package
            ? ($key === 'luxury' ? 'pkg-card selected-gold' : 'pkg-card selected')
            : 'pkg-card';
        ?>
        <div class="<?= $card_class ?> bg-white p-6"
             id="pkg-<?= $key ?>"
             onclick="selectPackage('<?= $key ?>')">

          <?php if ($is_popular): ?>
          <div class="absolute -top-3 left-1/2 -translate-x-1/2
                      bg-charcoal text-white font-body text-[10px] font-medium
                      tracking-widest px-3 py-1">
            POPULER
          </div>
          <?php endif; ?>

          <?php if ($key === 'luxury'): ?>
          <div class="absolute -top-3 left-1/2 -translate-x-1/2
                      bg-gold text-charcoal font-body text-[10px] font-medium
                      tracking-widest px-3 py-1">
            TERLENGKAP
          </div>
          <?php endif; ?>

          <!-- Nama & harga paket -->
          <p class="font-body text-xs tracking-widest text-gray-400 uppercase mb-1"><?= $pkg['label'] ?></p>
          <div class="font-display text-3xl font-light text-charcoal mb-1">
            Rp <?= number_format($pkg['price'], 0, ',', '.') ?>
          </div>
          <p class="font-body text-gray-400 text-xs mb-5 leading-relaxed"><?= $pkg['desc'] ?></p>

          <div class="w-full h-px bg-gray-100 mb-4"></div>

          <!-- Fitur yang ada -->
          <ul class="space-y-2 mb-4">
            <?php foreach ($pkg['features'] as $feat): ?>
            <li class="flex items-start gap-2 font-body text-xs text-gray-600 
                       <?= $key === 'luxury' ? 'text-charcoal font-medium' : '' ?>">
              <i class="bi bi-check2 text-charcoal mt-0.5 flex-shrink-0"></i>
              <?= $feat ?>
            </li>
            <?php endforeach; ?>
          </ul>

          <!-- Fitur yang tidak ada -->
          <?php if (!empty($pkg['not_included'])): ?>
          <ul class="space-y-2">
            <?php foreach ($pkg['not_included'] as $feat): ?>
            <li class="flex items-start gap-2 font-body text-xs text-gray-300 line-through">
              <i class="bi bi-x text-gray-200 mt-0.5 flex-shrink-0"></i>
              <?= $feat ?>
            </li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>

        </div>
        <?php endforeach; ?>
      </div>

      <!-- Pilih Template -->
      <div class="mb-10">
        <div class="text-center mb-6">
          <h2 class="font-display text-3xl font-light text-charcoal">Pilih Template</h2>
          <p class="font-body text-gray-400 text-xs mt-2">Template bisa diubah setelah pemesanan</p>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <?php foreach ($templates as $tpl): ?>
          <?php $is_sel = ($tpl['id'] == $selected_template); ?>
          <div class="cursor-pointer group"
               id="tpl-<?= $tpl['id'] ?>"
               onclick="selectTemplate(<?= $tpl['id'] ?>)">
            <!-- Preview placeholder -->
            <div class="aspect-[3/4] bg-gray-100 mb-3 overflow-hidden border-2
                        <?= $is_sel ? 'border-charcoal' : 'border-transparent group-hover:border-gray-300' ?>
                        transition-all duration-200 relative">
              <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-4">
                <div class="text-3xl mb-2 opacity-30">💌</div>
                <div class="font-display text-sm text-gray-500"><?= clean($tpl['name']) ?></div>
                <div class="font-body text-[10px] text-gray-400 mt-1">
                  Rp <?= number_format($tpl['price'],0,',','.') ?>
                </div>
              </div>
              <?php if ($is_sel): ?>
              <div class="absolute top-2 right-2 w-6 h-6 bg-charcoal rounded-full
                          flex items-center justify-center">
                <i class="bi bi-check2 text-white text-xs"></i>
              </div>
              <?php endif; ?>
            </div>
            <p class="font-body text-xs text-center font-medium
                      <?= $is_sel ? 'text-charcoal' : 'text-gray-500' ?>">
              <?= clean($tpl['name']) ?>
            </p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>

      <!-- Tombol lanjut ke Step 2 -->
      <div class="text-center">
        <button onclick="goToStep2()"
                class="bg-charcoal hover:bg-gray-800 text-white font-body font-medium
                       text-xs tracking-widest px-12 py-4 transition-colors duration-200
                       inline-flex items-center gap-2">
          LANJUT ISI DATA <i class="bi bi-arrow-right"></i>
        </button>
        <p class="font-body text-gray-400 text-xs mt-3">
          Pilih paket dan template dulu sebelum melanjutkan
        </p>
      </div>
    </div>

    <!-- ════════════════════════════════════════
         STEP 2: FORM DATA PEMESAN
         ════════════════════════════════════════ -->
    <?php else: ?>
    <div class="fade-up">

      <div class="text-center mb-10">
        <p class="font-body text-xs text-gray-400 tracking-[0.3em] uppercase mb-2">Langkah 2</p>
        <h1 class="font-display text-4xl md:text-5xl font-light text-charcoal">Isi Data</h1>
        <div class="w-8 h-px bg-charcoal mx-auto mt-4"></div>
      </div>

      <!-- Ringkasan pesanan di atas form -->
      <?php
        $sel_pkg = $packages[$form['package_type'] ?? $selected_package] ?? $packages['premium'];
        $sel_tpl = null;
        foreach ($templates as $t) {
          if ($t['id'] == ($form['template_id'] ?? $selected_template)) { $sel_tpl = $t; break; }
        }
      ?>
      <div class="bg-white border border-gray-100 p-4 mb-8 flex items-center justify-between">
        <div class="flex items-center gap-4">
          <div class="w-10 h-10 bg-gray-100 flex items-center justify-center text-lg">💌</div>
          <div>
            <div class="font-body text-sm font-medium text-charcoal">
              Paket <?= $sel_pkg['label'] ?>
              <?php if ($sel_tpl): ?> · <?= clean($sel_tpl['name']) ?><?php endif; ?>
            </div>
            <div class="font-body text-xs text-gray-400">
              Rp <?= number_format($sel_pkg['price'],0,',','.') ?>
            </div>
          </div>
        </div>
        <a href="order.php?step=1&package=<?= $form['package_type'] ?? $selected_package ?>&template=<?= $form['template_id'] ?? $selected_template ?>"
           class="font-body text-xs text-gray-400 hover:text-charcoal transition-colors">
          <i class="bi bi-pencil me-1"></i>Ubah
        </a>
      </div>

      <!-- Error validasi -->
      <?php if (!empty($errors)): ?>
      <div class="bg-red-50 border-l-4 border-red-400 px-4 py-3 mb-6 font-body text-sm text-red-700">
        <div class="flex items-center gap-2 font-medium mb-1">
          <i class="bi bi-exclamation-triangle-fill"></i> Mohon perbaiki:
        </div>
        <ul class="list-disc list-inside space-y-0.5 text-xs">
          <?php foreach ($errors as $e): ?>
          <li><?= clean($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
      <?php endif; ?>

      <!-- FORM -->
      <form method="POST" action="order.php">
        <!-- Hidden fields: simpan pilihan dari step 1 -->
        <input type="hidden" name="template_id"
               value="<?= (int)($form['template_id'] ?? $selected_template) ?>">
        <input type="hidden" name="package_type"
               value="<?= clean($form['package_type'] ?? $selected_package) ?>">

        <!-- ── A. DATA PEMESAN ── -->
        <div class="bg-white border border-gray-100 p-6 mb-4">
          <h3 class="font-display text-xl font-medium text-charcoal mb-5 pb-3 border-b border-gray-100">
            <i class="bi bi-person text-gold me-2"></i>Data Pemesan
          </h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
              <label class="lbl">Nama Lengkap *</label>
              <input type="text" name="customer_name" class="inp"
                     value="<?= clean($form['customer_name'] ?? '') ?>"
                     placeholder="Nama lengkap Anda" required>
            </div>

            <div>
              <label class="lbl">Email *</label>
              <input type="email" name="customer_email" class="inp"
                     value="<?= clean($form['customer_email'] ?? '') ?>"
                     placeholder="email@contoh.com" required>
              <p class="font-body text-gray-400 text-[11px] mt-1">
                Konfirmasi pesanan akan dikirim ke email ini
              </p>
            </div>

            <div>
              <label class="lbl">Nomor WhatsApp *</label>
              <input type="tel" name="customer_phone" class="inp"
                     value="<?= clean($form['customer_phone'] ?? '') ?>"
                     placeholder="08xxxxxxxxxx" required>
              <p class="font-body text-gray-400 text-[11px] mt-1">
                Kami akan menghubungi via WhatsApp
              </p>
            </div>

          </div>
        </div>

        <!-- ── B. DATA PASANGAN ── -->
        <div class="bg-white border border-gray-100 p-6 mb-4">
          <h3 class="font-display text-xl font-medium text-charcoal mb-5 pb-3 border-b border-gray-100">
            <i class="bi bi-heart text-gold me-2"></i>Data Pasangan
          </h3>
          <p class="font-body text-gray-400 text-xs mb-4">
            Data ini untuk keperluan pembuatan undangan. Detail lengkap akan diisi bersama tim kami.
          </p>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            <div>
              <label class="lbl">Nama Mempelai Pria *</label>
              <input type="text" name="groom_name" class="inp"
                     value="<?= clean($form['groom_name'] ?? '') ?>"
                     placeholder="Contoh: Rizky" required>
            </div>

            <div>
              <label class="lbl">Nama Mempelai Wanita *</label>
              <input type="text" name="bride_name" class="inp"
                     value="<?= clean($form['bride_name'] ?? '') ?>"
                     placeholder="Contoh: Aulia" required>
            </div>

            <div>
              <label class="lbl">Rencana Tanggal Acara *</label>
              <input type="date" name="event_date" class="inp"
                     value="<?= clean($form['event_date'] ?? '') ?>"
                     min="<?= date('Y-m-d', strtotime('+7 days')) ?>"
                     required>
              <p class="font-body text-gray-400 text-[11px] mt-1">
                Minimal 7 hari dari sekarang untuk persiapan
              </p>
            </div>

          </div>
        </div>

        <!-- ── C. CATATAN TAMBAHAN ── -->
        <div class="bg-white border border-gray-100 p-6 mb-6">
          <h3 class="font-display text-xl font-medium text-charcoal mb-5 pb-3 border-b border-gray-100">
            <i class="bi bi-chat-dots text-gold me-2"></i>Catatan Tambahan
          </h3>
          <div>
            <label class="lbl">Catatan / Permintaan Khusus</label>
            <textarea name="notes" class="inp"
                      placeholder="Contoh: saya ingin menggunakan foto prewedding, ada warna khusus yang diinginkan, dll..."><?= clean($form['notes'] ?? '') ?></textarea>
          </div>
        </div>

        <!-- ── D. RINGKASAN + SUBMIT ── -->
        <div class="bg-charcoal text-white p-6 mb-6">
          <h3 class="font-display text-xl font-light mb-4">Ringkasan Pesanan</h3>
          <div class="space-y-2 mb-4">
            <div class="flex justify-between font-body text-sm">
              <span class="text-gray-400">Paket <?= $sel_pkg['label'] ?></span>
              <span>Rp <?= number_format($sel_pkg['price'],0,',','.') ?></span>
            </div>
            <?php if ($sel_tpl): ?>
            <div class="flex justify-between font-body text-sm">
              <span class="text-gray-400">Template</span>
              <span><?= clean($sel_tpl['name']) ?></span>
            </div>
            <?php endif; ?>
          </div>
          <div class="border-t border-white/10 pt-3 flex justify-between items-center">
            <span class="font-body text-sm text-gray-400">Total</span>
            <span class="font-display text-2xl font-light">
              Rp <?= number_format($sel_pkg['price'],0,',','.') ?>
            </span>
          </div>
          <p class="font-body text-gray-500 text-xs mt-3">
            * Pembayaran dilakukan setelah konfirmasi dari tim kami via WhatsApp
          </p>
        </div>

        <div class="flex flex-col sm:flex-row gap-3">
          <a href="order.php?step=1&package=<?= $form['package_type'] ?? $selected_package ?>&template=<?= $form['template_id'] ?? $selected_template ?>"
             class="flex-1 text-center border border-gray-200 text-gray-500
                    hover:border-charcoal hover:text-charcoal font-body text-xs
                    font-medium tracking-widest py-4 transition-all duration-200">
            <i class="bi bi-arrow-left me-2"></i>KEMBALI
          </a>
          <button type="submit"
                  class="flex-1 bg-charcoal hover:bg-gray-800 text-white font-body
                         font-medium text-xs tracking-widest py-4 transition-colors
                         duration-200 flex items-center justify-center gap-2">
            <i class="bi bi-check-circle"></i>KIRIM PESANAN
          </button>
        </div>

        <!-- Info setelah order -->
        <div class="mt-4 p-4 bg-gray-50 border border-gray-100">
          <p class="font-body text-xs text-gray-500 leading-relaxed">
            <i class="bi bi-info-circle text-charcoal me-1"></i>
            Setelah mengirim pesanan, tim kami akan menghubungi Anda via WhatsApp dalam
            <strong>1×24 jam</strong> untuk konfirmasi dan proses pembayaran.
          </p>
        </div>

      </form>
    </div>
    <?php endif; ?>

  </div><!-- /container -->

</body>

<script>
// ── Pilih paket ─────────────────────────────────────────────
let selectedPackage  = '<?= $selected_package ?>';
let selectedTemplate = <?= $selected_template ?>;

function selectPackage(key) {
  selectedPackage = key;

  // Reset semua kartu
  document.querySelectorAll('[id^="pkg-"]').forEach(el => {
    el.classList.remove('selected', 'selected-gold');
  });

  // Tandai yang dipilih
  const card = document.getElementById('pkg-' + key);
  if (card) {
    card.classList.add(key === 'luxury' ? 'selected-gold' : 'selected');
  }
}

// ── Pilih template ───────────────────────────────────────────
function selectTemplate(id) {
  selectedTemplate = id;

  // Reset semua
  document.querySelectorAll('[id^="tpl-"]').forEach(el => {
    const inner = el.querySelector('.aspect-\\[3\\/4\\]');
    if (inner) {
      inner.classList.remove('border-charcoal');
      inner.classList.add('border-transparent');
      // Hapus checkmark
      const check = inner.querySelector('.bg-charcoal.rounded-full');
      if (check) check.remove();
    }
    const label = el.querySelector('p');
    if (label) { label.classList.remove('text-charcoal'); label.classList.add('text-gray-500'); }
  });

  // Aktifkan yang dipilih
  const tplEl = document.getElementById('tpl-' + id);
  if (tplEl) {
    const inner = tplEl.querySelector('div[class*="aspect"]');
    if (inner) {
      inner.classList.add('border-charcoal');
      inner.classList.remove('border-transparent');
      // Tambah checkmark
      if (!inner.querySelector('.bg-charcoal.rounded-full')) {
        inner.insertAdjacentHTML('beforeend',
          '<div class="absolute top-2 right-2 w-6 h-6 bg-charcoal rounded-full flex items-center justify-center">' +
          '<i class="bi bi-check2 text-white text-xs"></i></div>');
      }
    }
    const label = tplEl.querySelector('p');
    if (label) { label.classList.add('text-charcoal'); label.classList.remove('text-gray-500'); }
  }
}

// ── Lanjut ke step 2 ─────────────────────────────────────────
function goToStep2() {
  if (!selectedTemplate) {
    alert('Pilih template undangan dulu ya! 😊');
    return;
  }
  // Redirect ke step 2 dengan parameter yang dipilih
  window.location.href = 'order.php?step=2&package=' + selectedPackage + '&template=' + selectedTemplate;
}
</script>
</html>