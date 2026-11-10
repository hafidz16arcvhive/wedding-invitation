<?php
// ============================================================
// FILE: invitation/preview.php
// Preview template undangan dengan data dummy
// URL: /invitation/preview.php?template=1
// ============================================================
require_once '../config/database.php';

$template_id = (int)($_GET['template'] ?? 1);

// Ambil info template dari database
$template = db_row("SELECT * FROM invitation_templates WHERE id = ? AND is_active = 1",
    [$template_id], 'i');

// Jika template tidak ditemukan, pakai default
if (!$template) {
    $template = [
        'id'           => 1,
        'name'         => 'Royal Garden',
        'slug'         => 'royal-garden',
        'color_scheme' => 'gold',
        'price'        => 150000,
    ];
}

// ── Data dummy untuk preview ──────────────────────────────────
// Ini data contoh yang ditampilkan saat customer lihat preview
$wedding = [
    'id'                   => 0,
    'slug'                 => 'preview',
    'template_id'          => $template_id,
    'template_name'        => $template['name'],
    'groom_name'           => 'Rizky',
    'groom_full_name'      => 'Muhammad Rizky Pratama',
    'groom_father'         => 'Bapak Ahmad Fauzi',
    'groom_mother'         => 'Ibu Siti Aminah',
    'groom_photo'          => '',
    'bride_name'           => 'Aulia',
    'bride_full_name'      => 'Aulia Rahmawati Putri',
    'bride_father'         => 'Bapak Hendra Wijaya',
    'bride_mother'         => 'Ibu Dewi Lestari',
    'bride_photo'          => '',
    'couple_photo'         => '',
    'akad_date'            => date('Y-m-d', strtotime('+60 days')),
    'akad_time'            => '08:00:00',
    'akad_venue'           => 'Masjid Al-Ikhlas Bangil',
    'akad_address'         => 'Jl. Merdeka No. 10, Bangil, Pasuruan, Jawa Timur',
    'akad_maps_url'        => '#',
    'reception_date'       => date('Y-m-d', strtotime('+60 days')),
    'reception_time_start' => '11:00:00',
    'reception_time_end'   => '14:00:00',
    'reception_venue'      => 'Gedung Serbaguna Bangil',
    'reception_address'    => 'Jl. Raya Bangil No. 45, Pasuruan, Jawa Timur',
    'reception_maps_url'   => '#',
    'love_story'           => 'Kami bertemu di bangku kuliah pada tahun 2019. Pertemuan sederhana di perpustakaan kampus menjadi awal kisah cinta yang indah ini. Tiga tahun bersama melewati suka dan duka, akhirnya kami memutuskan untuk melangkah ke jenjang yang lebih serius.',
    'gallery_photos'       => '[]',
    'qris_image'           => '',
    'bank_name'            => 'BCA',
    'bank_account_number'  => '1234567890',
    'bank_account_name'    => 'Muhammad Rizky Pratama',
    'music_url'            => '',
    'views'                => 0,
    'is_active'            => 1,
    'is_published'         => 1,
];

// Data dummy RSVP & ucapan
$messages = [
    ['guest_name'=>'Budi Santoso',    'message'=>'Selamat menempuh hidup baru! Semoga menjadi keluarga yang sakinah, mawaddah, warahmah. Bahagia selalu!', 'created_at'=>date('Y-m-d H:i:s', strtotime('-2 hours'))],
    ['guest_name'=>'Siti Nurhaliza',  'message'=>'Barakallahu lakuma wa baraka alaikuma wa jamaa bainakuma fi khair. Semoga langgeng sampai kakek nenek! 💕', 'created_at'=>date('Y-m-d H:i:s', strtotime('-5 hours'))],
    ['guest_name'=>'Ahmad Firdaus',   'message'=>'Congrats! Wish you both a lifetime of love and happiness. Ditunggu traktiran nasi tumpengnya!', 'created_at'=>date('Y-m-d H:i:s', strtotime('-1 day'))],
];

$rsvp_stat = ['total_response' => 24, 'total_guests' => 48];
$gallery   = [];
$form_status = '';
$couple    = clean($wedding['groom_name']) . ' & ' . clean($wedding['bride_name']);
$event_date = $wedding['reception_date'];
$event_time = $wedding['reception_time_start'];
$slug       = 'preview';

// ── Warna tema per template ───────────────────────────────────
$themes = [
    1  => ['bg'=>'#1A1200', 'accent'=>'#C9A84C', 'name'=>'Royal Garden'],
    2  => ['bg'=>'#0A0A0A', 'accent'=>'#FFFFFF', 'name'=>'Modern Minimalist'],
    3  => ['bg'=>'#2C1810', 'accent'=>'#A67C5B', 'name'=>'Rustic Bohemian'],
    4  => ['bg'=>'#0D0D0D', 'accent'=>'#D4AF37', 'name'=>'Luxury Gold'],
    5  => ['bg'=>'#2D1B1B', 'accent'=>'#C9746A', 'name'=>'Soft Floral'],
    6  => ['bg'=>'#1A2E1A', 'accent'=>'#4A8C5C', 'name'=>'Tropical Paradise'],
    7  => ['bg'=>'#2C1810', 'accent'=>'#8B5E3C', 'name'=>'Vintage Classic'],
    8  => ['bg'=>'#3D3453', 'accent'=>'#B07FD4', 'name'=>'Pastel Dream'],
    9  => ['bg'=>'#0D1B3E', 'accent'=>'#4169C8', 'name'=>'Royal Blue'],
    10 => ['bg'=>'#071210', 'accent'=>'#4DB87A', 'name'=>'Midnight Garden'],
    11 => ['bg'=>'#2D1B2D', 'accent'=>'#E8759A', 'name'=>'Sakura Blossom'],
    12 => ['bg'=>'#2C1A10', 'accent'=>'#C4623A', 'name'=>'Desert Rose'],
    13 => ['bg'=>'#061A10', 'accent'=>'#B8975A', 'name'=>'Emerald Luxury'],
];
$theme = $themes[$template_id] ?? $themes[2];

// Ambil semua template untuk navigasi preview
$all_templates = db_rows("SELECT id, name, price FROM invitation_templates WHERE is_active = 1 ORDER BY id");
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Preview: <?= clean($template['name']) ?> — LoveStory Invitation</title>

  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,300;0,400;0,500;1,300;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        colors: { gold: '#C9A84C', charcoal: '#1C1C1E' },
        fontFamily: {
          display: ['"Playfair Display"','serif'],
          body: ['"DM Sans"','sans-serif'],
        }
      }}
    }
  </script>

  <style>
    /* ── CSS Undangan (inline, tidak butuh file eksternal) ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    :root {
      --accent:    <?= $theme['accent'] ?>;
      --cover-bg:  <?= $theme['bg'] ?>;
      --font-serif: 'Playfair Display', Georgia, serif;
      --font-sans:  'DM Sans', Arial, sans-serif;
    }

    /* Preview wrapper styles */
    body { font-family: var(--font-sans); background: #F5F5F5; }
    #invitation-frame {
      background: white;
      color: #0A0A0A;
      overflow-x: hidden;
    }
    .section { padding: 80px 24px; }
    .section-dark  { background: #0A0A0A; color: white; }
    .section-gray  { background: #F5F5F5; }
    .section-white { background: white; }
    .con   { max-width: 680px; margin: 0 auto; }
    .con-w { max-width: 960px; margin: 0 auto; }
    .section-tag {
      display: block;
      font-family: var(--font-sans);
      font-size: 10px; font-weight: 500;
      letter-spacing: .28em; text-transform: uppercase;
      color: #A3A3A3; margin-bottom: 14px;
    }
    .section-dark .section-tag { color: rgba(255,255,255,.35); }
    .section-title {
      font-family: var(--font-serif);
      font-size: clamp(28px, 4.5vw, 44px);
      font-weight: 400; line-height: 1.15;
    }
    .divider { width: 40px; height: 1px; background: #0A0A0A; margin: 16px 0; }
    .section-dark .divider { background: rgba(255,255,255,.2); }
    .big-name {
      font-family: var(--font-serif);
      font-size: clamp(40px, 8vw, 76px);
      font-weight: 400; line-height: 1.05; letter-spacing: -.02em;
    }

    /* Mempelai */
    .couple-grid { display: grid; grid-template-columns: 1fr auto 1fr; gap: 24px; align-items: center; margin-top: 40px; }
    .person { text-align: center; }
    .person-empty { width: 120px; height: 155px; background: #F0F0F0; margin: 0 auto 16px; display: flex; align-items: center; justify-content: center; font-size: 40px; color: #D0D0D0; }
    .person-photo { width: 120px; height: 155px; object-fit: cover; margin: 0 auto 16px; }

    /* Event */
    .event-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: #E5E5E5; border: 1px solid #E5E5E5; margin-top: 40px; }
    .event-card { background: white; padding: 28px 24px; }
    .event-row { display: flex; align-items: flex-start; gap: 9px; margin-bottom: 9px; font-size: 13px; color: #525252; }
    .event-row i { color: #0A0A0A; margin-top: 1px; flex-shrink: 0; }

    /* Countdown */
    .cd-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 1px; background: rgba(255,255,255,.1); border: 1px solid rgba(255,255,255,.1); margin-top: 28px; }
    .cd-item { background: rgba(255,255,255,.05); padding: 22px 12px; text-align: center; }
    .cd-num { display: block; font-family: var(--font-serif); font-size: clamp(28px,5vw,48px); font-weight: 300; line-height: 1; color: white; }
    .cd-lbl { display: block; font-size: 10px; font-weight: 500; letter-spacing: .2em; text-transform: uppercase; color: rgba(255,255,255,.35); margin-top: 6px; }

    /* Messages */
    .msg-wall { display: flex; flex-direction: column; gap: 1px; background: #E5E5E5; border: 1px solid #E5E5E5; }
    .msg-item { background: white; padding: 18px 20px; }

    /* Gift */
    .gift-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: rgba(255,255,255,.1); margin-top: 28px; border: 1px solid rgba(255,255,255,.1); }
    .gift-card { padding: 28px; text-align: center; }
    .gift-lbl { font-size: 10px; font-weight: 600; letter-spacing: .2em; text-transform: uppercase; color: rgba(255,255,255,.35); margin-bottom: 14px; }

    /* Form */
    .form-inp { width: 100%; padding: 10px 12px; border: 1px solid #E5E5E5; background: white; font-family: var(--font-sans); font-size: 13px; outline: none; }
    .form-inp:focus { border-color: #0A0A0A; }
    .btn-dark { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 12px; background: #0A0A0A; color: white; font-family: var(--font-sans); font-size: 11px; font-weight: 600; letter-spacing: .15em; text-transform: uppercase; cursor: pointer; border: none; }

    /* Cover */
    #cover-screen { position: fixed; inset: 0; z-index: 1000; background: var(--cover-bg); display: flex; align-items: center; justify-content: center; padding: 20px; transition: opacity .8s, transform .8s; }
    #cover-screen.closing { opacity: 0; transform: scale(1.04); pointer-events: none; }
    .cover-frame { border: 1px solid rgba(255,255,255,.12); padding: 44px 36px; text-align: center; max-width: 360px; width: 100%; position: relative; }
    .cover-frame::before { content:''; position:absolute; top:-1px; left:-1px; width:18px; height:18px; border-top:2px solid rgba(255,255,255,.35); border-left:2px solid rgba(255,255,255,.35); }
    .cover-frame::after  { content:''; position:absolute; bottom:-1px; right:-1px; width:18px; height:18px; border-bottom:2px solid rgba(255,255,255,.35); border-right:2px solid rgba(255,255,255,.35); }
    .cover-name { font-family: var(--font-serif); font-size: clamp(32px,8vw,54px); font-weight: 400; color: white; line-height: 1.05; }
    .cover-sep  { font-family: var(--font-serif); font-style: italic; font-weight: 300; font-size: clamp(16px,3.5vw,22px); color: rgba(255,255,255,.3); display: block; margin: 7px 0; }

    /* Reveal */
    .reveal { opacity: 0; transform: translateY(18px); transition: opacity .6s ease, transform .6s ease; }
    .reveal.visible { opacity: 1; transform: translateY(0); }
    .d1{transition-delay:.1s} .d2{transition-delay:.2s} .d3{transition-delay:.3s}

    @media(max-width:600px) {
      .section { padding: 56px 20px; }
      .couple-grid { grid-template-columns: 1fr; }
      .event-grid  { grid-template-columns: 1fr; }
      .gift-grid   { grid-template-columns: 1fr; }
      .cd-grid     { grid-template-columns: repeat(2,1fr); }
    }
  </style>
</head>
<body>

<!-- ════════════════════════════════════════
     TOP BAR — navigasi preview & aksi pesan
     ════════════════════════════════════════ -->
<div class="sticky top-0 z-50 bg-white border-b border-gray-200 px-4 py-3 flex items-center justify-between shadow-sm">
  <div class="flex items-center gap-3">
    <!-- Kembali ke beranda -->
    <a href="<?= BASE_URL ?>"
       class="text-gray-400 hover:text-gray-700 transition-colors font-body text-sm flex items-center gap-1">
      <i class="bi bi-arrow-left text-base"></i>
      <span class="hidden sm:inline">Kembali</span>
    </a>
    <div class="w-px h-4 bg-gray-200"></div>
    <!-- Info template aktif -->
    <div>
      <span class="font-body font-medium text-gray-800 text-sm">
        <?= clean($template['name']) ?>
      </span>
      <span class="font-body text-gray-400 text-xs ml-2">
        Rp <?= number_format($template['price'],0,',','.') ?>
      </span>
    </div>
  </div>

  <div class="flex items-center gap-2">
    <!-- Dropdown pilih template lain -->
    <select onchange="window.location.href='preview.php?template='+this.value"
            class="font-body text-xs text-gray-600 border border-gray-200 px-3 py-1.5 outline-none hidden sm:block">
      <?php foreach ($all_templates as $t): ?>
      <option value="<?= $t['id'] ?>" <?= $t['id']==$template_id?'selected':''?>>
        <?= clean($t['name']) ?>
      </option>
      <?php endforeach; ?>
    </select>

    <!-- Tombol pesan -->
    <a href="<?= BASE_URL ?>/order.php?template=<?= $template_id ?>"
       class="bg-gray-900 hover:bg-gray-700 text-white font-body font-medium text-xs
              tracking-widest px-5 py-2 transition-colors flex items-center gap-2">
      <i class="bi bi-cart-plus text-sm"></i>
      <span>PESAN INI</span>
    </a>
  </div>
</div>

<!-- ── Banner "Mode Preview" ── -->
<div class="bg-amber-50 border-b border-amber-200 px-4 py-2 text-center">
  <p class="font-body text-xs text-amber-700">
    <i class="bi bi-eye me-1"></i>
    <strong>Mode Preview</strong> — Ini adalah tampilan contoh dengan data dummy.
    Undangan asli Anda akan menggunakan data dan foto yang Anda kirimkan.
  </p>
</div>

<!-- ════════════════════════════════════════
     UNDANGAN (sama persis dengan aslinya)
     ════════════════════════════════════════ -->
<div id="invitation-frame">

  <!-- COVER -->
  <div id="cover-screen">
    <div class="cover-frame">
      <p style="font-family:var(--font-sans);font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:28px;">
        Undangan Pernikahan
      </p>
      <div class="cover-name"><?= clean($wedding['groom_name']) ?></div>
      <span class="cover-sep">dan</span>
      <div class="cover-name" style="margin-bottom:24px;"><?= clean($wedding['bride_name']) ?></div>
      <div style="width:28px;height:1px;background:rgba(255,255,255,.15);margin:0 auto 18px;"></div>
      <p style="font-family:var(--font-sans);font-size:12px;color:rgba(255,255,255,.35);margin-bottom:28px;letter-spacing:.05em;">
        <?= date('d F Y', strtotime($event_date)) ?>
      </p>
      <button onclick="openInvitation()"
              style="width:100%;padding:13px;background:white;color:#0A0A0A;font-family:var(--font-sans);font-size:11px;font-weight:600;letter-spacing:.15em;text-transform:uppercase;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:8px;">
        <i class="bi bi-envelope-open"></i> Lihat Preview
      </button>
    </div>
  </div>

  <div id="main-content" style="display:none;">

    <!-- 1. HERO -->
    <section class="section section-dark"
             style="min-height:100vh;display:flex;align-items:center;justify-content:center;text-align:center;position:relative;overflow:hidden;background:var(--cover-bg);">
      <div class="con reveal" style="position:relative;z-index:1;">
        <span class="section-tag d1 reveal">Bismillahirrahmanirrahim</span>
        <div class="big-name reveal d2" style="color:white;margin:16px 0;">
          <?= clean($wedding['groom_name']) ?>
          <div style="font-family:var(--font-serif);font-style:italic;font-weight:300;font-size:.5em;color:rgba(255,255,255,.3);display:block;margin:6px 0;">&amp;</div>
          <?= clean($wedding['bride_name']) ?>
        </div>
        <div class="divider reveal d3" style="margin:18px auto;background:rgba(255,255,255,.15);"></div>
        <p class="reveal d3" style="font-family:var(--font-sans);font-size:12px;color:rgba(255,255,255,.4);letter-spacing:.1em;">
          <?= date('d F Y', strtotime($event_date)) ?> &middot; <?= clean($wedding['reception_venue']) ?>
        </p>
      </div>
      <div style="position:absolute;bottom:24px;left:50%;transform:translateX(-50%);color:rgba(255,255,255,.2);font-size:22px;">
        <i class="bi bi-chevron-compact-down"></i>
      </div>
    </section>

    <!-- 2. PEMBUKAAN -->
    <section class="section section-white" style="text-align:center;">
      <div class="con">
        <span class="section-tag reveal">Dengan Rahmat Allah SWT</span>
        <h2 class="section-title reveal d1">Kami mengundang<br>kehadiran Anda</h2>
        <div class="divider reveal d2" style="margin-left:auto;margin-right:auto;"></div>
        <p class="reveal d3" style="font-size:14px;color:#525252;line-height:1.9;max-width:460px;margin:0 auto;">
          Merupakan suatu kebahagiaan apabila Bapak / Ibu / Saudara / i berkenan hadir dan memberikan doa restu kepada kedua mempelai.
        </p>
      </div>
    </section>

    <!-- 3. MEMPELAI -->
    <section class="section section-gray">
      <div class="con-w">
        <div style="text-align:center;">
          <span class="section-tag reveal">Kedua Mempelai</span>
          <h2 class="section-title reveal d1">Yang Berbahagia</h2>
        </div>
        <div class="couple-grid">
          <?php foreach ([['groom','Pria'],['bride','Wanita']] as [$k,$role]): ?>
          <div class="person reveal">
            <div class="person-empty"><i class="bi bi-person"></i></div>
            <p style="font-size:10px;font-weight:500;letter-spacing:.2em;text-transform:uppercase;color:#A3A3A3;margin-bottom:6px;">Mempelai <?= $role ?></p>
            <h3 style="font-family:var(--font-serif);font-size:24px;font-weight:400;"><?= clean($wedding[$k.'_name']) ?></h3>
            <p style="font-size:13px;color:#525252;margin-top:4px;"><?= clean($wedding[$k.'_full_name']) ?></p>
            <p style="font-size:12px;color:#A3A3A3;margin-top:10px;line-height:1.8;">
              Putr<?= $role==='Pria'?'a':'i' ?> dari<br>
              <?= clean($wedding[$k.'_father']) ?> &amp; <?= clean($wedding[$k.'_mother']) ?>
            </p>
          </div>
          <?php if($k==='groom'): ?>
          <div style="font-family:var(--font-serif);font-size:44px;font-weight:300;font-style:italic;color:#E5E5E5;text-align:center;" class="reveal d1">&amp;</div>
          <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- 4. COUNTDOWN -->
    <section class="section section-dark" style="text-align:center;background:var(--cover-bg);">
      <div class="con">
        <span class="section-tag reveal">Menuju Hari Istimewa</span>
        <h2 class="section-title reveal d1" style="color:white;"><?= date('d F Y', strtotime($event_date)) ?></h2>
        <div class="cd-grid reveal d2" id="countdown-grid"
             data-date="<?= $event_date ?>" data-time="<?= $event_time ?>">
          <?php foreach(['cd-days'=>'Hari','cd-hours'=>'Jam','cd-minutes'=>'Menit','cd-seconds'=>'Detik'] as $id=>$lbl): ?>
          <div class="cd-item">
            <span class="cd-num" id="<?= $id ?>">00</span>
            <span class="cd-lbl"><?= $lbl ?></span>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- 5. ACARA -->
    <section class="section section-white">
      <div class="con-w">
        <div style="text-align:center;">
          <span class="section-tag reveal">Rangkaian Acara</span>
          <h2 class="section-title reveal d1">Waktu &amp; Tempat</h2>
          <div class="divider reveal d2" style="margin-left:auto;margin-right:auto;"></div>
        </div>
        <div class="event-grid">
          <?php foreach([['akad','Akad Nikah','Ijab Qabul'],['reception','Resepsi','Walimatul Ursy']] as [$k,$type,$name]): ?>
          <div class="event-card reveal">
            <p style="font-size:10px;font-weight:600;letter-spacing:.25em;text-transform:uppercase;color:#A3A3A3;margin-bottom:12px;"><?= $type ?></p>
            <h3 style="font-family:var(--font-serif);font-size:22px;font-weight:400;margin-bottom:16px;"><?= $name ?></h3>
            <div class="event-row"><i class="bi bi-calendar3"></i><span><?= date('l, d F Y', strtotime($wedding[$k.'_date'])) ?></span></div>
            <div class="event-row"><i class="bi bi-clock"></i><span><?= date('H.i', strtotime($wedding[$k.'_time'])) ?> WIB</span></div>
            <div class="event-row"><i class="bi bi-geo-alt"></i><span><?= clean($wedding[$k.'_venue']) ?></span></div>
            <div class="event-row"><i class="bi bi-pin-map"></i><span style="color:#A3A3A3;font-size:12px;"><?= clean($wedding[$k.'_address']) ?></span></div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- 6. LOVE STORY -->
    <section class="section section-gray">
      <div class="con">
        <span class="section-tag reveal">Kisah Kami</span>
        <h2 class="section-title reveal d1">Our <em style="font-style:italic;font-weight:300;">Love Story</em></h2>
        <div class="divider reveal d2"></div>
        <p class="reveal d3" style="font-family:var(--font-serif);font-size:clamp(17px,2.5vw,21px);font-weight:300;font-style:italic;line-height:1.8;color:#525252;border-left:2px solid #0A0A0A;padding-left:22px;margin-top:24px;">
          <?= clean($wedding['love_story']) ?>
        </p>
      </div>
    </section>

    <!-- 7. RSVP -->
    <section class="section section-white" id="rsvp">
      <div class="con">
        <span class="section-tag reveal">Konfirmasi Kehadiran</span>
        <h2 class="section-title reveal d1">RSVP</h2>
        <div class="divider reveal d2"></div>
        <p class="reveal" style="font-size:12px;color:#A3A3A3;margin-top:12px;margin-bottom:20px;">
          <i class="bi bi-people me-1"></i>
          <strong style="color:#0A0A0A;">24</strong> tamu (48 orang) sudah konfirmasi hadir
        </p>
        <!-- Form dummy — tidak bisa disubmit di preview -->
        <div class="reveal" style="display:flex;flex-direction:column;gap:14px;pointer-events:none;opacity:.7;">
          <div><label style="display:block;font-size:10px;font-weight:600;letter-spacing:.1em;text-transform:uppercase;color:#6B7280;margin-bottom:6px;">Nama Lengkap</label>
          <input type="text" class="form-inp" placeholder="Contoh: Budi Santoso" disabled></div>
          <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:8px;">
            <?php foreach(['Hadir','Tidak Hadir','Mungkin'] as $i=>$l): ?>
            <div style="padding:9px 8px;text-align:center;border:1px solid <?= $i===0?'#0A0A0A':'#E5E5E5' ?>;font-size:12px;font-weight:500;color:<?= $i===0?'white':'#6B7280' ?>;background:<?= $i===0?'#0A0A0A':'white' ?>;"><?= $l ?></div>
            <?php endforeach; ?>
          </div>
          <button class="btn-dark" style="opacity:.5;cursor:not-allowed;">
            <i class="bi bi-send"></i> Kirim Konfirmasi (Preview Only)
          </button>
        </div>
      </div>
    </section>

    <!-- 8. UCAPAN -->
    <section class="section section-gray" id="ucapan">
      <div class="con">
        <span class="section-tag reveal">Dari Para Tamu</span>
        <h2 class="section-title reveal d1">Ucapan &amp; Doa</h2>
        <div class="divider reveal d2"></div>
        <div class="msg-wall reveal" style="margin-top:28px;">
          <?php foreach ($messages as $msg): ?>
          <div class="msg-item">
            <p style="font-family:var(--font-sans);font-size:13px;font-weight:600;margin-bottom:3px;"><?= clean($msg['guest_name']) ?></p>
            <p style="font-family:var(--font-sans);font-size:11px;color:#A3A3A3;margin-bottom:8px;">
              <i class="bi bi-clock me-1"></i><?= date('d M Y, H:i', strtotime($msg['created_at'])) ?>
            </p>
            <p style="font-family:var(--font-serif);font-size:15px;font-style:italic;color:#525252;line-height:1.65;">
              "<?= clean($msg['message']) ?>"
            </p>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </section>

    <!-- 9. GIFT -->
    <section class="section section-dark" style="background:var(--cover-bg);">
      <div class="con" style="text-align:center;">
        <span class="section-tag reveal">Hadiah Pernikahan</span>
        <h2 class="section-title reveal d1" style="color:white;">Wedding Gift</h2>
        <div class="divider reveal d2" style="margin:16px auto;background:rgba(255,255,255,.15);"></div>
        <p class="reveal" style="font-size:13px;color:rgba(255,255,255,.35);margin-bottom:28px;">Doa restu Anda adalah hadiah terbaik bagi kami.</p>
        <div class="gift-grid reveal">
          <div class="gift-card">
            <p class="gift-lbl">Scan QRIS</p>
            <div style="width:140px;height:140px;background:rgba(255,255,255,.08);margin:0 auto 12px;display:flex;align-items:center;justify-content:center;">
              <i class="bi bi-qr-code-scan" style="font-size:56px;color:rgba(255,255,255,.2);"></i>
            </div>
            <p style="font-size:11px;color:rgba(255,255,255,.25);">Scan dengan aplikasi bank / e-wallet apapun</p>
          </div>
          <div class="gift-card">
            <p class="gift-lbl">Transfer Bank</p>
            <i class="bi bi-bank2" style="font-size:32px;color:rgba(255,255,255,.15);display:block;margin-bottom:12px;"></i>
            <p style="font-family:var(--font-serif);font-size:20px;font-weight:400;color:white;margin-bottom:4px;">BCA</p>
            <p style="font-family:var(--font-sans);font-size:20px;font-weight:300;letter-spacing:.1em;color:white;margin-bottom:4px;">1234 5678 90</p>
            <p style="font-size:12px;color:rgba(255,255,255,.35);">a.n <?= clean($wedding['groom_name']) ?></p>
          </div>
        </div>
      </div>
    </section>

    <!-- 10. PENUTUP -->
    <section class="section section-white" style="text-align:center;">
      <div class="con">
        <span class="section-tag reveal">Penutup</span>
        <h2 class="section-title reveal d1">Terima Kasih</h2>
        <div class="divider reveal d2" style="margin-left:auto;margin-right:auto;"></div>
        <p class="reveal d3" style="font-size:14px;color:#525252;max-width:420px;margin:0 auto 40px;line-height:1.9;">
          Atas kehadiran dan doa restu Bapak / Ibu / Saudara / i, kami ucapkan terima kasih yang sebesar-besarnya.
        </p>
        <div class="big-name reveal" style="font-size:clamp(28px,6vw,48px);margin-bottom:32px;">
          <?= clean($wedding['groom_name']) ?>
          <div style="font-family:var(--font-serif);font-style:italic;font-weight:300;font-size:.5em;color:#A3A3A3;display:block;margin:4px 0;">&amp;</div>
          <?= clean($wedding['bride_name']) ?>
        </div>
        <!-- CTA pesan di penutup -->
        <a href="<?= BASE_URL ?>/order.php?template=<?= $template_id ?>"
           style="display:inline-flex;align-items:center;gap:8px;padding:14px 32px;background:#0A0A0A;color:white;font-family:var(--font-sans);font-size:11px;font-weight:600;letter-spacing:.15em;text-transform:uppercase;">
          <i class="bi bi-cart-plus"></i> PESAN TEMPLATE INI
        </a>
      </div>
    </section>

  </div><!-- /main-content -->
</div><!-- /invitation-frame -->

<!-- ════════════════════════════════════════
     BOTTOM BAR — navigasi template
     ════════════════════════════════════════ -->
<div class="fixed bottom-0 left-0 right-0 z-40 bg-white border-t border-gray-200 px-4 py-3">
  <div class="max-w-3xl mx-auto flex items-center justify-between gap-3">
    <div class="flex items-center gap-2 overflow-x-auto pb-1 flex-1">
      <?php foreach ($all_templates as $t): ?>
      <a href="preview.php?template=<?= $t['id'] ?>"
         class="flex-shrink-0 px-3 py-1.5 font-body text-xs whitespace-nowrap transition-all
                <?= $t['id']==$template_id
                    ? 'bg-gray-900 text-white'
                    : 'bg-gray-100 text-gray-600 hover:bg-gray-200' ?>">
        <?= clean($t['name']) ?>
      </a>
      <?php endforeach; ?>
    </div>
    <a href="<?= BASE_URL ?>/order.php?template=<?= $template_id ?>"
       class="flex-shrink-0 bg-gray-900 text-white font-body font-medium text-xs
              tracking-widest px-5 py-2.5 hover:bg-gray-700 transition-colors
              flex items-center gap-2">
      <i class="bi bi-cart-plus"></i> PESAN
    </a>
  </div>
</div>
<!-- Spacer untuk bottom bar -->
<div style="height:64px;"></div>

<script>
// Buka undangan (tutup cover)
function openInvitation() {
  const cover = document.getElementById('cover-screen');
  cover.classList.add('closing');
  setTimeout(() => {
    cover.style.display = 'none';
    document.getElementById('main-content').style.display = 'block';
    setTimeout(() => { initReveal(); startCountdown(); }, 150);
  }, 800);
}

// Countdown
function startCountdown() {
  const el = document.getElementById('countdown-grid');
  if (!el) return;
  const target = new Date(el.dataset.date + 'T' + (el.dataset.time || '08:00:00'));
  function tick() {
    const diff = target - new Date();
    if (diff <= 0) return;
    const d=Math.floor(diff/864e5), h=Math.floor(diff%864e5/36e5),
          m=Math.floor(diff%36e5/6e4), s=Math.floor(diff%6e4/1e3);
    document.getElementById('cd-days').textContent    = String(d).padStart(2,'0');
    document.getElementById('cd-hours').textContent   = String(h).padStart(2,'0');
    document.getElementById('cd-minutes').textContent = String(m).padStart(2,'0');
    document.getElementById('cd-seconds').textContent = String(s).padStart(2,'0');
  }
  tick(); setInterval(tick, 1000);
}

// Scroll reveal
function initReveal() {
  const obs = new IntersectionObserver(entries => {
    entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('visible'); obs.unobserve(e.target); }});
  }, { threshold: 0.08 });
  document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
}
</script>
</body>
</html>
