<?php
// ============================================================
// FILE: order_success.php
// Halaman sukses setelah pengunjung berhasil kirim pesanan
//
// Alur:
// 1. Terima ?order=WED-20250817-XXXX dari redirect order.php
// 2. Ambil data order dari database
// 3. Tampilkan ringkasan + langkah selanjutnya + tombol WA
// ============================================================
require_once 'config/database.php';

// Ambil nomor order dari URL
$order_number = clean($_GET['order'] ?? '');

// Jika tidak ada nomor order → redirect ke beranda
if (empty($order_number)) {
    redirect(BASE_URL);
}

// Ambil data order dari database
$order = db_row("
    SELECT o.*, t.name AS template_name
    FROM orders o
    LEFT JOIN invitation_templates t ON o.template_id = t.id
    WHERE o.order_number = ?
", [$order_number], 's');

// Jika order tidak ditemukan
if (!$order) {
    redirect(BASE_URL);
}

// Ambil nomor WhatsApp CS dari settings
$wa_number = db_row("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'");
$wa_number = $wa_number['setting_value'] ?? '6281234567890';

$site_name = db_row("SELECT setting_value FROM settings WHERE setting_key = 'site_name'");
$site_name = $site_name['setting_value'] ?? 'LoveStory';

// Buat teks pesan WhatsApp otomatis
$wa_message = urlencode(
    "Halo Admin {$site_name}! 😊\n\n" .
    "Saya baru saja melakukan pemesanan undangan dengan detail:\n" .
    "• Nomor Order: *{$order['order_number']}*\n" .
    "• Nama: {$order['customer_name']}\n" .
    "• Paket: " . ucfirst($order['package_type']) . "\n" .
    "• Template: {$order['template_name']}\n\n" .
    "Mohon konfirmasinya. Terima kasih!"
);
$wa_url = "https://wa.me/{$wa_number}?text={$wa_message}";

// Format harga
$formatted_price = 'Rp ' . number_format($order['total_price'], 0, ',', '.');

// Parse catatan dari field notes untuk tampilkan nama pasangan
$notes_lines = explode("\n", $order['notes'] ?? '');
$mempelai = '';
$tgl_acara = '';
foreach ($notes_lines as $line) {
    if (str_starts_with($line, 'Mempelai:')) $mempelai = trim(str_replace('Mempelai:', '', $line));
    if (str_starts_with($line, 'Tanggal:'))  $tgl_acara = trim(str_replace('Tanggal:', '', $line));
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pesanan Berhasil — <?= clean($site_name) ?></title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        colors: {
          gold:    { DEFAULT:'#C9A84C', dark:'#9A7B2F' },
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
    @keyframes fadeUp {
      from { opacity:0; transform:translateY(20px); }
      to   { opacity:1; transform:translateY(0); }
    }
    .fade-up { animation: fadeUp 0.6s ease forwards; }
    .fade-up-2 { animation: fadeUp 0.6s 0.2s ease both; }
    .fade-up-3 { animation: fadeUp 0.6s 0.4s ease both; }

    @keyframes checkPop {
      0%   { transform: scale(0); opacity:0; }
      60%  { transform: scale(1.15); }
      100% { transform: scale(1); opacity:1; }
    }
    .check-pop { animation: checkPop 0.5s 0.3s cubic-bezier(0.34,1.56,0.64,1) both; }

    .step-item::before {
      content: '';
      position: absolute;
      left: 15px;
      top: 32px;
      bottom: -8px;
      width: 1px;
      background: #E5E7EB;
    }
    .step-item:last-child::before { display: none; }
  </style>
</head>

<body class="min-h-screen">

  <!-- NAV -->
  <nav class="bg-white border-b border-gray-100 px-6 py-4 flex items-center justify-between">
    <a href="index.php" class="font-display text-2xl text-charcoal font-light">
      ♡ <?= clean($site_name) ?>
    </a>
  </nav>

  <div class="max-w-2xl mx-auto px-4 py-12">

    <!-- ── ICON SUKSES ── -->
    <div class="text-center mb-8 fade-up">
      <div class="check-pop w-20 h-20 bg-charcoal rounded-full flex items-center
                  justify-center mx-auto mb-6">
        <i class="bi bi-check2 text-white text-3xl"></i>
      </div>
      <p class="font-body text-xs text-gray-400 tracking-[0.3em] uppercase mb-2">
        Pesanan Diterima
      </p>
      <h1 class="font-display text-4xl md:text-5xl font-light text-charcoal">
        Terima Kasih!
      </h1>
      <p class="font-body text-gray-500 text-sm mt-3 leading-relaxed">
        Pesanan Anda telah kami terima. Tim kami akan segera<br>
        menghubungi Anda dalam <strong>1×24 jam</strong>.
      </p>
    </div>

    <!-- ── NOMOR ORDER (highlight) ── -->
    <div class="bg-charcoal text-white p-6 text-center mb-6 fade-up-2">
      <p class="font-body text-xs text-gray-400 tracking-[0.25em] uppercase mb-2">
        Nomor Pesanan Anda
      </p>
      <div class="font-display text-3xl font-light tracking-wider">
        <?= clean($order['order_number']) ?>
      </div>
      <p class="font-body text-gray-500 text-xs mt-2">
        Simpan nomor ini untuk konfirmasi pembayaran
      </p>
      <!-- Tombol copy nomor order -->
      <button onclick="copyOrder()"
              id="copy-btn"
              class="mt-4 border border-white/20 text-white/70 hover:border-white/50
                     hover:text-white font-body text-xs tracking-widest px-4 py-2
                     transition-all duration-200">
        <i class="bi bi-clipboard me-1"></i> SALIN NOMOR
      </button>
    </div>

    <!-- ── DETAIL PESANAN ── -->
    <div class="bg-white border border-gray-100 p-6 mb-6 fade-up-2">
      <h2 class="font-display text-xl font-medium text-charcoal mb-4 pb-3 border-b border-gray-100">
        Detail Pesanan
      </h2>
      <div class="space-y-3">
        <?php
        $details = [
          ['label'=>'Nama Pemesan',  'value'=> $order['customer_name']],
          ['label'=>'Email',         'value'=> $order['customer_email']],
          ['label'=>'WhatsApp',      'value'=> $order['customer_phone']],
          ['label'=>'Template',      'value'=> $order['template_name'] ?? '—'],
          ['label'=>'Paket',         'value'=> ucfirst($order['package_type'])],
          ['label'=>'Mempelai',      'value'=> $mempelai ?: '—'],
          ['label'=>'Tanggal Acara', 'value'=> $tgl_acara ? date('d F Y', strtotime($tgl_acara)) : '—'],
          ['label'=>'Total',         'value'=> $formatted_price],
          ['label'=>'Status',        'value'=> 'Menunggu Konfirmasi'],
        ];
        foreach ($details as $d):
        ?>
        <div class="flex justify-between items-start gap-4">
          <span class="font-body text-xs text-gray-400 tracking-wide uppercase flex-shrink-0 w-32">
            <?= $d['label'] ?>
          </span>
          <span class="font-body text-sm text-charcoal text-right font-medium">
            <?php if ($d['label'] === 'Status'): ?>
            <span class="bg-yellow-50 text-yellow-700 border border-yellow-200
                         text-xs px-2 py-0.5 font-medium">
              <i class="bi bi-clock me-1"></i><?= $d['value'] ?>
            </span>
            <?php elseif ($d['label'] === 'Total'): ?>
            <span class="font-display text-xl font-light"><?= $d['value'] ?></span>
            <?php else: ?>
            <?= clean($d['value']) ?>
            <?php endif; ?>
          </span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ── LANGKAH SELANJUTNYA ── -->
    <div class="bg-white border border-gray-100 p-6 mb-6 fade-up-3">
      <h2 class="font-display text-xl font-medium text-charcoal mb-5">
        Langkah Selanjutnya
      </h2>
      <div class="space-y-5">
        <?php
        $steps = [
          [
            'num'  => '01',
            'icon' => 'bi-whatsapp',
            'title'=> 'Konfirmasi via WhatsApp',
            'desc' => 'Tim kami akan menghubungi Anda via WhatsApp untuk konfirmasi pesanan dan detail undangan.',
            'done' => false,
          ],
          [
            'num'  => '02',
            'icon' => 'bi-credit-card',
            'title'=> 'Lakukan Pembayaran',
            'desc' => 'Setelah konfirmasi, lakukan pembayaran sesuai paket yang dipilih.',
            'done' => false,
          ],
          [
            'num'  => '03',
            'icon' => 'bi-pencil-square',
            'title'=> 'Pengisian Data Lengkap',
            'desc' => 'Isi data lengkap pasangan, foto, dan detail acara bersama tim kami.',
            'done' => false,
          ],
          [
            'num'  => '04',
            'icon' => 'bi-share',
            'title'=> 'Undangan Siap Dibagikan',
            'desc' => 'Undangan digital Anda selesai dan siap dibagikan ke semua tamu!',
            'done' => false,
          ],
        ];
        foreach ($steps as $i => $s):
        ?>
        <div class="flex gap-4 step-item relative pb-2">
          <!-- Icon nomor -->
          <div class="w-8 h-8 rounded-full border-2 <?= $s['done'] ? 'bg-charcoal border-charcoal' : 'border-gray-200' ?>
                      flex items-center justify-center flex-shrink-0 z-10 bg-white">
            <?php if ($s['done']): ?>
            <i class="bi bi-check2 text-white text-xs"></i>
            <?php else: ?>
            <span class="font-body text-[10px] font-medium text-gray-400"><?= $s['num'] ?></span>
            <?php endif; ?>
          </div>
          <!-- Konten -->
          <div>
            <h4 class="font-body text-sm font-medium text-charcoal mb-0.5">
              <i class="bi <?= $s['icon'] ?> me-1 text-gray-400"></i>
              <?= $s['title'] ?>
            </h4>
            <p class="font-body text-xs text-gray-400 leading-relaxed"><?= $s['desc'] ?></p>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- ── TOMBOL AKSI ── -->
    <div class="space-y-3 fade-up-3">

      <!-- Tombol WhatsApp (utama) -->
      <a href="<?= $wa_url ?>" target="_blank"
         class="flex items-center justify-center gap-3 w-full bg-green-500
                hover:bg-green-600 text-white font-body font-medium text-sm
                tracking-widest py-4 transition-colors duration-200">
        <i class="bi bi-whatsapp text-xl"></i>
        KONFIRMASI VIA WHATSAPP
      </a>

      <!-- Tombol kembali ke beranda -->
      <a href="index.php"
         class="flex items-center justify-center gap-2 w-full bg-white border
                border-gray-200 hover:border-charcoal text-gray-500 hover:text-charcoal
                font-body font-medium text-xs tracking-widest py-4
                transition-all duration-200">
        <i class="bi bi-house"></i> KEMBALI KE BERANDA
      </a>

    </div>

    <!-- Info tambahan -->
    <div class="mt-6 p-4 bg-blue-50 border border-blue-100 fade-up-3">
      <p class="font-body text-xs text-blue-600 leading-relaxed">
        <i class="bi bi-info-circle me-1"></i>
        <strong>Belum menerima konfirmasi?</strong> Jika dalam 1×24 jam belum ada kabar,
        silakan hubungi kami via WhatsApp dengan menyebutkan nomor pesanan
        <strong><?= clean($order['order_number']) ?></strong>.
      </p>
    </div>

  </div><!-- /container -->

  <script>
  function copyOrder() {
    const num = '<?= clean($order['order_number']) ?>';
    const btn = document.getElementById('copy-btn');
    navigator.clipboard.writeText(num).then(() => {
      btn.innerHTML = '<i class="bi bi-check2 me-1"></i> TERSALIN!';
      btn.style.borderColor = 'rgba(255,255,255,0.6)';
      btn.style.color = 'white';
      setTimeout(() => {
        btn.innerHTML = '<i class="bi bi-clipboard me-1"></i> SALIN NOMOR';
        btn.style.borderColor = '';
        btn.style.color = '';
      }, 2500);
    });
  }
  </script>

</body>
</html>