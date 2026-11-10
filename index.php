<?php
// ============================================================
// FILE: index.php  —  Halaman Utama
// Menampilkan katalog desain undangan
// ============================================================
require_once 'config/database.php';

// Ambil semua template aktif dari database
$templates = db_rows("SELECT * FROM invitation_templates WHERE is_active = 1 ORDER BY id ASC");

// Ambil pengaturan website
$site_name    = db_row("SELECT setting_value FROM settings WHERE setting_key = 'site_name'")['setting_value'] ?? 'LoveStory';
$site_tagline = db_row("SELECT setting_value FROM settings WHERE setting_key = 'site_tagline'")['setting_value'] ?? 'Undangan Pernikahan Digital';
$wa_number    = db_row("SELECT setting_value FROM settings WHERE setting_key = 'whatsapp_number'")['setting_value'] ?? '6281234567890';
$email_cs     = db_row("SELECT setting_value FROM settings WHERE setting_key = 'email_cs'")['setting_value'] ?? 'cs@lovestory.id';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= clean($site_name) ?> — Undangan Pernikahan Digital Elegan</title>
  <meta name="description" content="Buat undangan pernikahan digital yang elegan, berkesan, dan mudah dibagikan.">

  <!-- Google Fonts: Cormorant Garamond + Jost -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,500;0,600;1,300;1,400&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            gold:    { DEFAULT: '#C9A84C', light: '#E8D5A3', dark: '#9A7B2F' },
            cream:   { DEFAULT: '#FAF6F0', dark: '#F0E8DC' },
            charcoal:{ DEFAULT: '#2C2C2C', light: '#4A4A4A' },
          },
          fontFamily: {
            display: ['"Cormorant Garamond"', 'serif'],
            body:    ['Jost', 'sans-serif'],
          },
        }
      }
    }
  </script>

  <style>
    /* ── Global ── */
    * { scroll-behavior: smooth; }
    body { font-family: 'Jost', sans-serif; background: #FAF6F0; color: #2C2C2C; }

    /* ── Custom Scrollbar ── */
    ::-webkit-scrollbar { width: 6px; }
    ::-webkit-scrollbar-track { background: #FAF6F0; }
    ::-webkit-scrollbar-thumb { background: #C9A84C; border-radius: 3px; }

    /* ── Ornament garis emas ── */
    .gold-divider {
      display: flex; align-items: center; gap: 16px;
    }
    .gold-divider::before, .gold-divider::after {
      content: ''; flex: 1; height: 1px; background: linear-gradient(90deg, transparent, #C9A84C, transparent);
    }

    /* ── Card template hover ── */
    .template-card {
      transition: transform 0.4s cubic-bezier(0.25,0.46,0.45,0.94), box-shadow 0.4s ease;
    }
    .template-card:hover {
      transform: translateY(-8px);
      box-shadow: 0 24px 60px rgba(201,168,76,0.2);
    }
    .template-card:hover .card-overlay { opacity: 1; }
    .card-overlay {
      opacity: 0;
      transition: opacity 0.3s ease;
      background: linear-gradient(to top, rgba(44,44,44,0.85) 0%, transparent 60%);
    }

    /* ── Hero background pattern ── */
    .hero-bg {
      background-color: #2C2C2C;
      background-image:
        radial-gradient(ellipse at 20% 50%, rgba(201,168,76,0.15) 0%, transparent 60%),
        radial-gradient(ellipse at 80% 20%, rgba(201,168,76,0.10) 0%, transparent 50%);
    }

    /* ── Floating particles ── */
    @keyframes float-up {
      0%   { transform: translateY(0) rotate(0deg); opacity: 0; }
      10%  { opacity: 0.6; }
      90%  { opacity: 0.2; }
      100% { transform: translateY(-100vh) rotate(360deg); opacity: 0; }
    }
    .particle {
      position: absolute;
      width: 4px; height: 4px;
      border-radius: 50%;
      background: #C9A84C;
      animation: float-up linear infinite;
      pointer-events: none;
    }

    /* ── Fade-in animation ── */
    @keyframes fadeInUp {
      from { opacity:0; transform: translateY(30px); }
      to   { opacity:1; transform: translateY(0); }
    }
    .fade-in-up { animation: fadeInUp 0.8s ease forwards; opacity: 0; }
    .delay-1 { animation-delay: 0.2s; }
    .delay-2 { animation-delay: 0.4s; }
    .delay-3 { animation-delay: 0.6s; }
    .delay-4 { animation-delay: 0.8s; }

    /* ── Bottom Nav ── */
    .bottom-nav {
      backdrop-filter: blur(16px);
      background: rgba(44,44,44,0.92);
      border-top: 1px solid rgba(201,168,76,0.3);
    }

    /* ── Price badge ── */
    .price-badge {
      background: linear-gradient(135deg, #C9A84C, #9A7B2F);
    }
  </style>
</head>

<body class="min-h-screen pb-20">

<!-- ╔══════════════════════════════════════════╗ -->
<!-- ║           HERO SECTION                   ║ -->
<!-- ╚══════════════════════════════════════════╝ -->
<section class="hero-bg relative overflow-hidden min-h-screen flex items-center justify-center py-20 px-6">

  <!-- Floating particles -->
  <div id="particles-container" class="absolute inset-0 pointer-events-none overflow-hidden"></div>

  <!-- Ornamen sudut atas kiri -->
  <div class="absolute top-8 left-8 opacity-30">
    <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
      <path d="M0 0 L30 0 M0 0 L0 30" stroke="#C9A84C" stroke-width="1.5"/>
      <path d="M10 10 L25 10 M10 10 L10 25" stroke="#C9A84C" stroke-width="0.8"/>
    </svg>
  </div>
  <!-- Ornamen sudut bawah kanan -->
  <div class="absolute bottom-8 right-8 opacity-30">
    <svg width="80" height="80" viewBox="0 0 80 80" fill="none">
      <path d="M80 80 L50 80 M80 80 L80 50" stroke="#C9A84C" stroke-width="1.5"/>
      <path d="M70 70 L55 70 M70 70 L70 55" stroke="#C9A84C" stroke-width="0.8"/>
    </svg>
  </div>

  <!-- Logo / Nav -->
  <nav class="absolute top-0 left-0 right-0 flex items-center justify-between px-8 py-5 z-10">
    <div class="font-display text-2xl text-gold font-light tracking-widest">
      ♡ <?= clean($site_name) ?>
    </div>
    <a href="admin/login.php"
       class="text-gold-light border border-gold/40 px-4 py-2 text-xs font-body tracking-widest hover:bg-gold hover:text-charcoal transition-all duration-300">
      <i class="bi bi-lock-fill me-1"></i> ADMIN
    </a>
  </nav>

  <!-- Hero Content -->
  <div class="text-center z-10 max-w-3xl mx-auto">
    <p class="fade-in-up text-gold font-body text-xs tracking-[0.4em] uppercase mb-6">
      ✦ Digital Wedding Invitation ✦
    </p>
    <h1 class="fade-in-up delay-1 font-display text-6xl md:text-8xl text-white font-light leading-tight mb-4">
      Cerita Cinta<br>
      <em class="text-gold">Tak Terlupakan</em>
    </h1>
    <p class="fade-in-up delay-2 font-body text-gray-400 text-lg font-light max-w-xl mx-auto mb-10 leading-relaxed">
      Undangan pernikahan digital yang elegan, personal, dan berkesan — dibagikan dalam hitungan detik.
    </p>
    <div class="fade-in-up delay-3 flex flex-col sm:flex-row gap-4 justify-center">
      <a href="#templates"
         class="bg-gold hover:bg-gold-dark text-charcoal font-body font-medium tracking-widest text-sm px-8 py-4 transition-all duration-300 hover:shadow-lg hover:shadow-gold/30">
        LIHAT DESAIN
      </a>
      <a href="#how-it-works"
         class="border border-gold/50 text-gold hover:border-gold font-body font-light tracking-widest text-sm px-8 py-4 transition-all duration-300">
        CARA PESAN
      </a>
    </div>

    <!-- Stats -->
    <div class="fade-in-up delay-4 flex justify-center gap-12 mt-16">
      <div class="text-center">
        <div class="font-display text-3xl text-gold font-light">500+</div>
        <div class="font-body text-gray-500 text-xs tracking-widest mt-1">PASANGAN</div>
      </div>
      <div class="w-px bg-gold/20"></div>
      <div class="text-center">
        <div class="font-display text-3xl text-gold font-light">4</div>
        <div class="font-body text-gray-500 text-xs tracking-widest mt-1">DESAIN</div>
      </div>
      <div class="w-px bg-gold/20"></div>
      <div class="text-center">
        <div class="font-display text-3xl text-gold font-light">100%</div>
        <div class="font-body text-gray-500 text-xs tracking-widest mt-1">PUAS</div>
      </div>
    </div>
  </div>

  <!-- Scroll indicator -->
  <div class="absolute bottom-8 left-1/2 -translate-x-1/2 flex flex-col items-center gap-2 animate-bounce">
    <span class="font-body text-gray-600 text-xs tracking-widest">SCROLL</span>
    <i class="bi bi-chevron-down text-gold text-sm"></i>
  </div>
</section>


<!-- ╔══════════════════════════════════════════╗ -->
<!-- ║         HOW IT WORKS                     ║ -->
<!-- ╚══════════════════════════════════════════╝ -->
<section id="how-it-works" class="bg-white py-24 px-6">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-16">
      <p class="text-gold font-body text-xs tracking-[0.4em] uppercase mb-3">Mudah & Cepat</p>
      <h2 class="font-display text-5xl font-light text-charcoal">Cara Pemesanan</h2>
      <div class="gold-divider mt-4 max-w-xs mx-auto">
        <span class="text-gold text-xs">✦</span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
      <?php
      $steps = [
        ['icon'=>'bi-palette', 'num'=>'01', 'title'=>'Pilih Desain', 'desc'=>'Pilih template undangan yang sesuai dengan kepribadian dan selera kamu'],
        ['icon'=>'bi-pencil-square', 'num'=>'02', 'title'=>'Isi Data', 'desc'=>'Isi formulir dengan data mempelai, tanggal, dan lokasi acara'],
        ['icon'=>'bi-credit-card', 'num'=>'03', 'title'=>'Bayar', 'desc'=>'Lakukan pembayaran via transfer bank atau QRIS yang tersedia'],
        ['icon'=>'bi-share', 'num'=>'04', 'title'=>'Bagikan', 'desc'=>'Dapatkan link undangan dan bagikan ke seluruh tamu via WhatsApp'],
      ];
      foreach ($steps as $i => $step): ?>
      <div class="text-center group">
        <div class="w-16 h-16 mx-auto mb-4 border border-gold/30 flex items-center justify-center group-hover:bg-gold group-hover:border-gold transition-all duration-300">
          <i class="bi <?= $step['icon'] ?> text-2xl text-gold group-hover:text-charcoal transition-colors duration-300"></i>
        </div>
        <div class="font-display text-gold text-4xl font-light mb-2 opacity-30"><?= $step['num'] ?></div>
        <h3 class="font-display text-xl font-medium text-charcoal mb-2"><?= $step['title'] ?></h3>
        <p class="font-body text-gray-500 text-sm leading-relaxed"><?= $step['desc'] ?></p>
      </div>
      <?php if ($i < count($steps)-1): ?>
        <div class="hidden md:flex items-center justify-center text-gold/30">
          <i class="bi bi-arrow-right text-2xl"></i>
        </div>
      <?php endif; endforeach; ?>
    </div>
  </div>
</section>


<!-- ╔══════════════════════════════════════════╗ -->
<!-- ║          TEMPLATE GALLERY                ║ -->
<!-- ╚══════════════════════════════════════════╝ -->
<section id="templates" class="py-24 px-6 bg-cream">
  <div class="max-w-6xl mx-auto">

    <div class="text-center mb-16">
      <p class="text-gold font-body text-xs tracking-[0.4em] uppercase mb-3">Pilih Favoritmu</p>
      <h2 class="font-display text-5xl font-light text-charcoal">Desain Undangan</h2>
      <div class="gold-divider mt-4 max-w-xs mx-auto">
        <span class="text-gold text-xs">✦</span>
      </div>
      <p class="font-body text-gray-500 mt-4 max-w-xl mx-auto text-sm leading-relaxed">
        Setiap desain dibuat dengan penuh perhatian untuk mencerminkan keindahan momen pernikahan Anda.
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-2 gap-8">
      <?php
      // Warna placeholder per template jika belum ada gambar
      $placeholder_colors = ['#2C2C2C','#1a1a2e','#2d1b1b','#1b2d1b'];
      $placeholder_patterns = [
        'Royal Garden'      => '🌹',
        'Modern Minimalist' => '◼',
        'Rustic Bohemian'   => '🌿',
        'Luxury Gold'       => '✦',
      ];
      foreach ($templates as $idx => $tpl): 
        $emoji = $placeholder_patterns[$tpl['name']] ?? '♡';
        $bgColor = $placeholder_colors[$idx % count($placeholder_colors)];
      ?>
      <div class="template-card group bg-white overflow-hidden shadow-sm cursor-pointer"
           onclick="window.location.href='invitation/preview.php?template=<?= $tpl['id'] ?>'">

        <!-- Thumbnail -->
        <div class="relative overflow-hidden" style="height: 320px;">
          <?php if (!empty($tpl['thumbnail']) && file_exists(BASE_PATH . '/' . $tpl['thumbnail'])): ?>
            <img src="<?= BASE_URL ?>/<?= clean($tpl['thumbnail']) ?>"
                 alt="<?= clean($tpl['name']) ?>"
                 class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
          <?php else: ?>
            <!-- Placeholder tampilan preview template -->
            <div class="w-full h-full flex flex-col items-center justify-center"
                 style="background: <?= $bgColor ?>;">
              <div class="text-6xl mb-4 opacity-50"><?= $emoji ?></div>
              <div class="font-display text-2xl text-gold font-light"><?= clean($tpl['name']) ?></div>
              <div class="w-12 h-px bg-gold/50 my-3"></div>
              <div class="font-body text-gray-400 text-xs tracking-widest">KLIK UNTUK PREVIEW</div>
            </div>
          <?php endif; ?>

          <!-- Overlay saat hover -->
          <div class="card-overlay absolute inset-0 flex items-end p-6">
            <a href="invitation/preview.php?template=<?= $tpl['id'] ?>"
               onclick="event.stopPropagation()"
               class="w-full bg-gold hover:bg-gold-dark text-charcoal text-center font-body text-xs font-medium tracking-widest py-3 transition-colors duration-200">
               <i class="bi bi-eye me-2"></i>LIHAT PREVIEW
            </a>
          </div>

          <!-- Badge harga -->
          <div class="absolute top-4 right-4 price-badge text-white font-body text-xs font-medium px-3 py-1">
            Rp <?= number_format($tpl['price'], 0, ',', '.') ?>
          </div>
        </div>

        <!-- Card Info -->
        <div class="p-6">
          <div class="flex items-start justify-between mb-2">
            <h3 class="font-display text-2xl font-medium text-charcoal"><?= clean($tpl['name']) ?></h3>
            <span class="text-gold text-xs font-body tracking-widest bg-gold/10 px-2 py-1 mt-1">
              <?= strtoupper(clean($tpl['color_scheme'])) ?>
            </span>
          </div>
          <p class="font-body text-gray-500 text-sm leading-relaxed mb-4"><?= clean($tpl['description']) ?></p>

          <!-- Fitur -->
          <div class="flex flex-wrap gap-2">
            <?php foreach (['RSVP Online','QRIS Gift','Countdown','Galeri Foto','Ucapan'] as $feat): ?>
            <span class="text-xs font-body text-gold/70 border border-gold/20 px-2 py-0.5">
              <i class="bi bi-check2 me-1"></i><?= $feat ?>
            </span>
            <?php endforeach; ?>
          </div>

          <a href="order.php?template=<?= $tpl['id'] ?>"
             class="mt-5 block w-full text-center bg-charcoal hover:bg-gold hover:text-charcoal text-white font-body text-xs font-medium tracking-widest py-3 transition-all duration-300">
            <i class="bi bi-cart-plus me-2"></i>PESAN SEKARANG
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ╔══════════════════════════════════════════╗ -->
<!-- ║           FEATURES / KEUNGGULAN          ║ -->
<!-- ╚══════════════════════════════════════════╝ -->
<section class="bg-charcoal py-24 px-6 relative overflow-hidden">
  <!-- Background decoration -->
  <div class="absolute inset-0 opacity-5">
    <div class="absolute top-0 left-1/4 w-96 h-96 rounded-full border border-gold"></div>
    <div class="absolute bottom-0 right-1/4 w-64 h-64 rounded-full border border-gold"></div>
  </div>

  <div class="max-w-5xl mx-auto relative z-10">
    <div class="text-center mb-16">
      <p class="text-gold font-body text-xs tracking-[0.4em] uppercase mb-3">Mengapa Kami</p>
      <h2 class="font-display text-5xl font-light text-white">Keunggulan Kami</h2>
      <div class="gold-divider mt-4 max-w-xs mx-auto">
        <span class="text-gold text-xs">✦</span>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      <?php
      $features = [
        ['icon'=>'bi-qr-code-scan','title'=>'QRIS Gift','desc'=>'Tamu bisa memberi hadiah uang secara digital langsung dari halaman undangan via QRIS'],
        ['icon'=>'bi-camera2','title'=>'Galeri Foto','desc'=>'Upload foto prewedding dan tampilkan dalam galeri yang indah dan elegan'],
        ['icon'=>'bi-hourglass-split','title'=>'Countdown Timer','desc'=>'Hitung mundur menuju hari spesial yang membuat tamu semakin tidak sabar'],
        ['icon'=>'bi-people','title'=>'RSVP Online','desc'=>'Tamu konfirmasi kehadiran langsung dari undangan, tanpa perlu hubungi satu-satu'],
        ['icon'=>'bi-chat-heart','title'=>'Ucapan & Doa','desc'=>'Tamu bisa meninggalkan ucapan selamat yang akan menjadi kenangan indah'],
        ['icon'=>'bi-music-note-beamed','title'=>'Musik Latar','desc'=>'Undangan diiringi musik romantis yang menciptakan suasana yang sempurna'],
      ];
      foreach ($features as $f): ?>
      <div class="text-center p-6 border border-gold/10 hover:border-gold/40 transition-colors duration-300 group">
        <div class="w-14 h-14 mx-auto mb-4 border border-gold/30 flex items-center justify-center group-hover:bg-gold transition-all duration-300">
          <i class="bi <?= $f['icon'] ?> text-2xl text-gold group-hover:text-charcoal transition-colors duration-300"></i>
        </div>
        <h3 class="font-display text-xl text-white font-medium mb-2"><?= $f['title'] ?></h3>
        <p class="font-body text-gray-500 text-sm leading-relaxed"><?= $f['desc'] ?></p>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>


<!-- ╔══════════════════════════════════════════╗ -->
<!-- ║          CONTOH UNDANGAN AKTIF           ║ -->
<!-- ╚══════════════════════════════════════════╝ -->
<?php
$active_weddings = db_rows("
  SELECT w.*, t.name as template_name 
  FROM weddings w 
  LEFT JOIN invitation_templates t ON w.template_id = t.id
  WHERE w.is_published = 1 
  ORDER BY w.created_at DESC 
  LIMIT 3
");
if (!empty($active_weddings)): ?>
<section class="py-24 px-6 bg-white">
  <div class="max-w-5xl mx-auto">
    <div class="text-center mb-16">
      <p class="text-gold font-body text-xs tracking-[0.4em] uppercase mb-3">Sudah Memakai Kami</p>
      <h2 class="font-display text-5xl font-light text-charcoal">Undangan Terbaru</h2>
      <div class="gold-divider mt-4 max-w-xs mx-auto">
        <span class="text-gold text-xs">✦</span>
      </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
      <?php foreach ($active_weddings as $w): ?>
      <a href="invitation/<?= clean($w['slug']) ?>"
         class="block border border-gold/20 p-6 hover:border-gold/60 hover:shadow-lg transition-all duration-300 group">
        <div class="text-4xl text-center mb-4 opacity-40">♡</div>
        <h3 class="font-display text-2xl text-center font-light text-charcoal">
          <?= clean($w['groom_name']) ?> & <?= clean($w['bride_name']) ?>
        </h3>
        <div class="text-center font-body text-gold text-sm mt-2">
          <?= date('d F Y', strtotime($w['reception_date'])) ?>
        </div>
        <div class="text-center mt-4 font-body text-xs text-gray-400 tracking-widest group-hover:text-gold transition-colors">
          LIHAT UNDANGAN →
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>


<!-- ╔══════════════════════════════════════════╗ -->
<!-- ║        BOTTOM NAV — CUSTOMER SERVICE     ║ -->
<!-- ╚══════════════════════════════════════════╝ -->
<nav class="bottom-nav fixed bottom-0 left-0 right-0 z-50 px-4 py-3">
  <div class="max-w-2xl mx-auto flex items-center justify-around">

    <!-- Beranda -->
    <a href="index.php" class="flex flex-col items-center gap-1 text-gold group">
      <i class="bi bi-house-heart-fill text-xl"></i>
      <span class="font-body text-[10px] tracking-widest">BERANDA</span>
    </a>

    <!-- Desain -->
    <a href="#templates" class="flex flex-col items-center gap-1 text-gray-500 hover:text-gold transition-colors group">
      <i class="bi bi-palette text-xl"></i>
      <span class="font-body text-[10px] tracking-widest">DESAIN</span>
    </a>

    <!-- Pesan (CTA utama) -->
    <a href="#templates"
       class="flex flex-col items-center gap-1 -mt-6 bg-gold text-charcoal w-14 h-14 rounded-full shadow-lg shadow-gold/30 justify-center hover:bg-gold-dark transition-colors">
      <i class="bi bi-heart-fill text-xl"></i>
      <span class="font-body text-[9px] font-medium tracking-wider">PESAN</span>
    </a>

    <!-- WhatsApp CS -->
    <a href="https://wa.me/<?= $wa_number ?>?text=Halo,%20saya%20ingin%20tanya%20tentang%20undangan%20pernikahan"
       target="_blank"
       class="flex flex-col items-center gap-1 text-gray-500 hover:text-green-400 transition-colors group">
      <i class="bi bi-whatsapp text-xl"></i>
      <span class="font-body text-[10px] tracking-widest">WHATSAPP</span>
    </a>

    <!-- Email CS -->
    <a href="mailto:<?= $email_cs ?>"
       class="flex flex-col items-center gap-1 text-gray-500 hover:text-gold transition-colors group">
      <i class="bi bi-envelope text-xl"></i>
      <span class="font-body text-[10px] tracking-widest">EMAIL</span>
    </a>

  </div>
</nav>


<!-- ╔══════════════════════════════════════════╗ -->
<!-- ║              FOOTER                      ║ -->
<!-- ╚══════════════════════════════════════════╝ -->
<footer class="bg-charcoal py-12 px-6 text-center">
  <div class="font-display text-3xl text-gold font-light mb-2">♡ <?= clean($site_name) ?></div>
  <p class="font-body text-gray-600 text-sm"><?= clean($site_tagline) ?></p>
  <div class="gold-divider max-w-xs mx-auto my-4">
    <span class="text-gold text-xs">✦</span>
  </div>
  <p class="font-body text-gray-700 text-xs">© <?= date('Y') ?> <?= clean($site_name) ?>. All rights reserved.</p>
</footer>


<script>
// ── Generate floating particles ──
(function() {
  const container = document.getElementById('particles-container');
  if (!container) return;
  for (let i = 0; i < 20; i++) {
    const p = document.createElement('div');
    p.className = 'particle';
    p.style.cssText = `
      left: ${Math.random() * 100}%;
      bottom: -10px;
      width: ${Math.random() * 4 + 2}px;
      height: ${Math.random() * 4 + 2}px;
      animation-duration: ${Math.random() * 10 + 8}s;
      animation-delay: ${Math.random() * 8}s;
      opacity: 0;
    `;
    container.appendChild(p);
  }
})();

// ── Smooth scroll untuk anchor links ──
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
  anchor.addEventListener('click', function(e) {
    e.preventDefault();
    const target = document.querySelector(this.getAttribute('href'));
    if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
  });
});

// ── Intersection Observer untuk animasi scroll ──
const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.template-card, .grid > div').forEach(el => {
  el.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
  observer.observe(el);
});
</script>

</body>
</html>
