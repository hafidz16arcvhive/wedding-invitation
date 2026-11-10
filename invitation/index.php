<?php
// ============================================================
// FILE: invitation/index.php
// Self-contained: semua CSS inline, tidak butuh file eksternal
// Font: Google Fonts CDN (butuh internet)
// ============================================================
require_once '../config/database.php';

$slug = clean($_GET['slug'] ?? '');
if (empty($slug)) redirect(BASE_URL);

// Ambil data wedding
$wedding = db_row("
    SELECT w.*, t.name AS template_name, t.slug AS template_slug
    FROM weddings w
    LEFT JOIN invitation_templates t ON w.template_id = t.id
    WHERE w.slug = ? AND w.is_active = 1
", [$slug], 's');

if (!$wedding) {
    // ── Tampilkan halaman 404 yang informatif ──
    ?><!DOCTYPE html>
    <html lang="id"><head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <title>Undangan Tidak Ditemukan</title>
    <style>
      body{font-family:sans-serif;display:flex;align-items:center;justify-content:center;
           min-height:100vh;margin:0;background:#fafafa;}
      .box{text-align:center;padding:40px;max-width:400px;}
      .icon{font-size:56px;margin-bottom:20px;}
      h2{font-weight:400;font-size:24px;margin-bottom:8px;color:#1a1a1a;}
      p{color:#888;font-size:14px;line-height:1.6;margin-bottom:6px;}
      .slug{background:#f0f0f0;padding:4px 10px;font-family:monospace;font-size:13px;border-radius:4px;}
      a{display:inline-block;margin-top:20px;font-size:13px;color:#000;border-bottom:1px solid #000;padding-bottom:2px;text-decoration:none;}
      .debug{margin-top:24px;background:#fff8e1;border:1px solid #ffd54f;padding:12px 16px;text-align:left;font-size:12px;color:#795548;border-radius:4px;}
    </style>
    </head>
    <body><div class="box">
      <div class="icon">💌</div>
      <h2>Undangan Tidak Ditemukan</h2>
      <p>Slug yang dicari: <span class="slug"><?= clean($slug) ?></span></p>
      <p>Kemungkinan penyebab:</p>
      <div class="debug">
        ① Undangan belum ada di database<br>
        ② Kolom <strong>is_active</strong> = 0 (belum diaktifkan)<br>
        ③ Slug tidak cocok (huruf besar/kecil berbeda)<br><br>
        <strong>Solusi:</strong> Buka phpMyAdmin → jalankan:<br>
        <code>UPDATE weddings SET is_active=1 WHERE slug='<?= clean($slug) ?>';</code>
      </div>
      <a href="<?= BASE_URL ?>">← Kembali ke Beranda</a>
    </div></body></html>
    <?php exit;
}

// Tambah view counter
db_execute("UPDATE weddings SET views = views + 1 WHERE id = ?", [$wedding['id']], 'i');

// Ambil ucapan tamu
$messages = db_rows("
    SELECT * FROM messages
    WHERE wedding_id = ? AND is_approved = 1
    ORDER BY created_at DESC LIMIT 30
", [$wedding['id']], 'i');

// Statistik RSVP
$rsvp_stat = db_row("
    SELECT COUNT(*) AS total_response, COALESCE(SUM(total_guests),0) AS total_guests
    FROM rsvp WHERE wedding_id = ? AND attendance = 'hadir'
", [$wedding['id']], 'i');

// Status notifikasi
$form_status = clean($_GET['status'] ?? '');

// Proses form POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    if ($action === 'rsvp') {
        $name  = clean(trim($_POST['guest_name'] ?? ''));
        $phone = clean(trim($_POST['guest_phone'] ?? ''));
        $att   = in_array($_POST['attendance']??'',['hadir','tidak_hadir','mungkin'])
                 ? $_POST['attendance'] : 'hadir';
        $guests = max(1, min(10, (int)($_POST['total_guests'] ?? 1)));
        $msg    = clean(trim($_POST['message'] ?? ''));
        if (!empty($name)) {
            db_insert("INSERT INTO rsvp (wedding_id,guest_name,guest_phone,attendance,total_guests,message) VALUES (?,?,?,?,?,?)",
                [$wedding['id'],$name,$phone,$att,$guests,$msg]);
            if (!empty($msg))
                db_insert("INSERT INTO messages (wedding_id,guest_name,message) VALUES (?,?,?)",
                    [$wedding['id'],$name,$msg]);
            header("Location: ".$_SERVER['PHP_SELF']."?slug={$slug}&status=rsvp_ok#rsvp");
            exit;
        }
    }
    if ($action === 'message') {
        $name = clean(trim($_POST['msg_name'] ?? ''));
        $msg  = clean(trim($_POST['msg_text'] ?? ''));
        if (!empty($name) && !empty($msg)) {
            db_insert("INSERT INTO messages (wedding_id,guest_name,message) VALUES (?,?,?)",
                [$wedding['id'],$name,$msg]);
            header("Location: ".$_SERVER['PHP_SELF']."?slug={$slug}&status=msg_ok#ucapan");
            exit;
        }
    }
}

$gallery    = [];
if (!empty($wedding['gallery_photos']))
    $gallery = json_decode($wedding['gallery_photos'], true) ?? [];

$couple     = clean($wedding['groom_name']).' & '.clean($wedding['bride_name']);
$event_date = $wedding['reception_date'] ?? $wedding['akad_date'] ?? date('Y-m-d');
$event_time = $wedding['reception_time_start'] ?? '08:00:00';
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Undangan Pernikahan <?= $couple ?></title>
  <meta name="description" content="Undangan pernikahan <?= $couple ?>">

  <!--
    FONT GOOGLE: Playfair Display (serif elegan) + DM Sans (sans-serif bersih)
    Butuh koneksi internet. Kalau offline, browser pakai fallback font.
  -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,300;0,400;0,500;1,300;1,400&family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500&display=swap" rel="stylesheet">

  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <style>
    /* ======================================================
       SEMUA CSS INLINE DI SINI — tidak butuh file eksternal
       ====================================================== */

    /* ── Reset ── */
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html { scroll-behavior: smooth; }

    /* ── Variabel warna & font ── */
    :root {
      --black:   #0A0A0A;
      --white:   #FFFFFF;
      --gray:    #F5F5F5;
      --gray2:   #E5E5E5;
      --mid:     #A3A3A3;
      --dark2:   #262626;
      /* Font dengan fallback — kalau Google Fonts gagal load, pakai Georgia/Arial */
      --font-serif: 'Playfair Display', Georgia, 'Times New Roman', serif;
      --font-sans:  'DM Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Arial, sans-serif;
    }

    body {
      font-family: var(--font-sans);
      background: var(--white);
      color: var(--black);
      line-height: 1.6;
      overflow-x: hidden;
    }

    img { display: block; max-width: 100%; }
    a   { text-decoration: none; color: inherit; }

    /* ── Scrollbar ── */
    ::-webkit-scrollbar { width: 3px; }
    ::-webkit-scrollbar-thumb { background: var(--black); }

    /* ======================================================
       COVER SCREEN
       ====================================================== */
    #cover-screen {
      position: fixed;
      inset: 0;
      z-index: 1000;
      background: var(--black);
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      transition: opacity 0.8s ease, transform 0.8s ease;
    }
    #cover-screen.closing {
      opacity: 0;
      transform: scale(1.04);
      pointer-events: none;
    }

    /* Bingkai dekoratif */
    .cover-frame {
      border: 1px solid rgba(255,255,255,0.12);
      padding: 48px 40px;
      text-align: center;
      max-width: 380px;
      width: 100%;
      position: relative;
    }
    /* Sudut-sudut bingkai */
    .cover-frame::before {
      content: '';
      position: absolute;
      top: -1px; left: -1px;
      width: 20px; height: 20px;
      border-top: 2px solid rgba(255,255,255,0.4);
      border-left: 2px solid rgba(255,255,255,0.4);
    }
    .cover-frame::after {
      content: '';
      position: absolute;
      bottom: -1px; right: -1px;
      width: 20px; height: 20px;
      border-bottom: 2px solid rgba(255,255,255,0.4);
      border-right: 2px solid rgba(255,255,255,0.4);
    }
    .corner-tr {
      position: absolute;
      top: -1px; right: -1px;
      width: 20px; height: 20px;
      border-top: 2px solid rgba(255,255,255,0.4);
      border-right: 2px solid rgba(255,255,255,0.4);
    }
    .corner-bl {
      position: absolute;
      bottom: -1px; left: -1px;
      width: 20px; height: 20px;
      border-bottom: 2px solid rgba(255,255,255,0.4);
      border-left: 2px solid rgba(255,255,255,0.4);
    }

    /* Nama di cover */
    .cover-name {
      font-family: var(--font-serif);
      font-size: clamp(36px, 9vw, 58px);
      font-weight: 400;
      color: white;
      line-height: 1.05;
      letter-spacing: -0.02em;
    }
    .cover-sep {
      font-family: var(--font-serif);
      font-size: clamp(18px, 4vw, 26px);
      font-weight: 300;
      font-style: italic;
      color: rgba(255,255,255,0.3);
      display: block;
      margin: 8px 0;
    }

    /* ======================================================
       SECTION LAYOUTS
       ====================================================== */
    .section {
      padding: 88px 24px;
    }
    .section-dark {
      background: var(--black);
      color: var(--white);
    }
    .section-gray {
      background: var(--gray);
    }
    .section-white {
      background: var(--white);
    }

    /* Container */
    .container {
      max-width: 680px;
      margin-left: auto;
      margin-right: auto;
    }
    .container-wide {
      max-width: 960px;
      margin-left: auto;
      margin-right: auto;
    }

    /* ── Typography helpers ── */
    .section-tag {
      display: block;
      font-family: var(--font-sans);
      font-size: 10px;
      font-weight: 500;
      letter-spacing: 0.28em;
      text-transform: uppercase;
      color: var(--mid);
      margin-bottom: 14px;
    }
    .section-dark .section-tag { color: rgba(255,255,255,0.35); }

    .section-title {
      font-family: var(--font-serif);
      font-size: clamp(30px, 5vw, 46px);
      font-weight: 400;
      line-height: 1.15;
      letter-spacing: -0.01em;
    }
    .section-title em {
      font-style: italic;
      font-weight: 300;
    }

    .divider {
      width: 40px;
      height: 1px;
      background: var(--black);
      margin-top: 18px;
      margin-bottom: 18px;
    }
    .section-dark .divider { background: rgba(255,255,255,0.2); }

    /* Nama besar pasangan */
    .couple-name-big {
      font-family: var(--font-serif);
      font-size: clamp(44px, 9vw, 82px);
      font-weight: 400;
      line-height: 1.05;
      letter-spacing: -0.025em;
    }
    .couple-amp {
      font-family: var(--font-serif);
      font-style: italic;
      font-weight: 300;
      font-size: 0.5em;
      color: rgba(255,255,255,0.3);
      display: block;
      margin: 6px 0;
    }

    /* ======================================================
       MEMPELAI
       ====================================================== */
    .couple-grid {
      display: grid;
      grid-template-columns: 1fr auto 1fr;
      gap: 32px;
      align-items: center;
      margin-top: 44px;
    }
    .person { text-align: center; }
    .person-photo {
      width: 130px;
      height: 165px;
      object-fit: cover;
      margin: 0 auto 18px;
      filter: grayscale(15%);
      transition: filter 0.4s, transform 0.4s;
    }
    .person-photo:hover { filter: grayscale(0%); transform: scale(1.02); }
    .person-empty {
      width: 130px;
      height: 165px;
      background: var(--gray);
      margin: 0 auto 18px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 44px;
      color: var(--mid);
    }
    .person-role {
      font-family: var(--font-sans);
      font-size: 10px;
      font-weight: 500;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      color: var(--mid);
      margin-bottom: 8px;
    }
    .person-name {
      font-family: var(--font-serif);
      font-size: 26px;
      font-weight: 400;
    }
    .person-fullname {
      font-family: var(--font-sans);
      font-size: 13px;
      color: #525252;
      margin-top: 4px;
    }
    .person-parents {
      font-family: var(--font-sans);
      font-size: 12px;
      color: var(--mid);
      margin-top: 12px;
      line-height: 1.8;
    }
    .couple-sep {
      font-family: var(--font-serif);
      font-size: 48px;
      font-weight: 300;
      font-style: italic;
      color: var(--gray2);
      text-align: center;
    }

    /* ======================================================
       COUNTDOWN
       ====================================================== */
    .countdown-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 1px;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.1);
      margin-top: 36px;
    }
    .countdown-item {
      background: rgba(255,255,255,0.05);
      padding: 26px 12px;
      text-align: center;
    }
    .countdown-num {
      display: block;
      font-family: var(--font-serif);
      font-size: clamp(32px, 6vw, 52px);
      font-weight: 300;
      line-height: 1;
      color: var(--white);
      letter-spacing: -0.02em;
    }
    .countdown-lbl {
      display: block;
      font-family: var(--font-sans);
      font-size: 10px;
      font-weight: 500;
      letter-spacing: 0.2em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.35);
      margin-top: 8px;
    }

    /* ======================================================
       EVENT CARDS
       ====================================================== */
    .event-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1px;
      background: var(--gray2);
      border: 1px solid var(--gray2);
      margin-top: 44px;
    }
    .event-card { background: var(--white); padding: 36px 32px; }
    .event-type {
      font-family: var(--font-sans);
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.28em;
      text-transform: uppercase;
      color: var(--mid);
      margin-bottom: 14px;
    }
    .event-name {
      font-family: var(--font-serif);
      font-size: 24px;
      font-weight: 400;
      margin-bottom: 20px;
    }
    .event-row {
      display: flex;
      align-items: flex-start;
      gap: 10px;
      margin-bottom: 10px;
      font-family: var(--font-sans);
      font-size: 13px;
      color: #525252;
    }
    .event-row i { color: var(--black); margin-top: 2px; flex-shrink: 0; }
    .event-map-btn {
      display: inline-flex;
      align-items: center;
      gap: 6px;
      margin-top: 16px;
      padding: 8px 16px;
      border: 1px solid var(--black);
      font-family: var(--font-sans);
      font-size: 11px;
      font-weight: 500;
      letter-spacing: 0.1em;
      text-transform: uppercase;
      color: var(--black);
      transition: all 0.2s ease;
    }
    .event-map-btn:hover { background: var(--black); color: var(--white); }

    /* ======================================================
       GALERI FOTO
       ====================================================== */
    .gallery-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 4px;
      margin-top: 40px;
    }
    .gallery-item {
      position: relative;
      overflow: hidden;
      cursor: pointer;
      background: var(--gray);
    }
    .gallery-item::after {
      content: '';
      display: block;
      padding-bottom: 100%;
    }
    .gallery-item:first-child { grid-column: span 2; }
    .gallery-item:first-child::after { padding-bottom: 50%; }
    .gallery-item img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: cover;
      transition: transform 0.5s ease;
      filter: grayscale(20%);
    }
    .gallery-item:hover img {
      transform: scale(1.06);
      filter: grayscale(0%);
    }

    /* ======================================================
       RSVP & FORM
       ====================================================== */
    .form-group { margin-bottom: 18px; }
    .form-label {
      display: block;
      font-family: var(--font-sans);
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.12em;
      text-transform: uppercase;
      color: #6B7280;
      margin-bottom: 7px;
    }
    .form-input {
      width: 100%;
      padding: 11px 14px;
      border: 1px solid var(--gray2);
      background: var(--white);
      font-family: var(--font-sans);
      font-size: 14px;
      color: var(--black);
      outline: none;
      border-radius: 0;
      -webkit-appearance: none;
      transition: border-color 0.2s ease;
    }
    .form-input:focus { border-color: var(--black); }
    .form-input::placeholder { color: var(--mid); }
    textarea.form-input { resize: vertical; min-height: 88px; }

    /* Attendance radio */
    .att-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 8px;
    }
    .att-option input[type="radio"] { display: none; }
    .att-btn {
      display: block;
      padding: 10px 8px;
      text-align: center;
      border: 1px solid var(--gray2);
      font-family: var(--font-sans);
      font-size: 12px;
      font-weight: 500;
      color: #6B7280;
      cursor: pointer;
      transition: all 0.2s;
    }
    .att-option input:checked + .att-btn {
      background: var(--black);
      color: var(--white);
      border-color: var(--black);
    }
    .att-btn:hover { border-color: var(--black); }

    /* ======================================================
       UCAPAN / MESSAGES
       ====================================================== */
    .messages-wall {
      display: flex;
      flex-direction: column;
      gap: 1px;
      background: var(--gray2);
      border: 1px solid var(--gray2);
    }
    .message-item { background: var(--white); padding: 20px 24px; }
    .message-name {
      font-family: var(--font-sans);
      font-size: 13px;
      font-weight: 600;
      color: var(--black);
      margin-bottom: 3px;
    }
    .message-time {
      font-family: var(--font-sans);
      font-size: 11px;
      color: var(--mid);
      margin-bottom: 8px;
    }
    .message-text {
      font-family: var(--font-serif);
      font-size: 15px;
      font-style: italic;
      color: #525252;
      line-height: 1.65;
    }

    /* ======================================================
       WEDDING GIFT
       ====================================================== */
    .gift-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1px;
      background: rgba(255,255,255,0.1);
      border: 1px solid rgba(255,255,255,0.1);
      margin-top: 32px;
    }
    .gift-card { padding: 32px; text-align: center; }
    .gift-label {
      font-family: var(--font-sans);
      font-size: 10px;
      font-weight: 600;
      letter-spacing: 0.22em;
      text-transform: uppercase;
      color: rgba(255,255,255,0.35);
      margin-bottom: 16px;
    }
    .gift-qris {
      width: 148px;
      height: 148px;
      object-fit: contain;
      margin: 0 auto 12px;
      background: white;
      padding: 8px;
    }
    .gift-bank { font-family: var(--font-serif); font-size: 20px; font-weight: 400; color: white; margin-bottom: 4px; }
    .gift-number { font-family: var(--font-sans); font-size: 22px; font-weight: 300; letter-spacing: 0.1em; color: white; margin-bottom: 4px; }
    .gift-owner { font-family: var(--font-sans); font-size: 12px; color: rgba(255,255,255,0.35); margin-bottom: 14px; }

    /* ======================================================
       TOMBOL
       ====================================================== */
    .btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      padding: 13px 24px;
      font-family: var(--font-sans);
      font-size: 11px;
      font-weight: 600;
      letter-spacing: 0.15em;
      text-transform: uppercase;
      cursor: pointer;
      border: none;
      width: 100%;
      transition: all 0.2s ease;
    }
    .btn-dark  { background: var(--black); color: var(--white); }
    .btn-dark:hover { background: var(--dark2); }
    .btn-copy  {
      display: inline-flex;
      background: transparent;
      color: rgba(255,255,255,0.5);
      border: 1px solid rgba(255,255,255,0.15);
      padding: 8px 16px;
      font-size: 11px;
      width: auto;
      cursor: pointer;
      transition: all 0.2s;
    }
    .btn-copy:hover { color: white; border-color: rgba(255,255,255,0.5); }

    /* ======================================================
       MUSIK
       ====================================================== */
    #music-btn {
      position: fixed;
      bottom: 24px; right: 24px;
      z-index: 99;
      width: 44px; height: 44px;
      background: var(--black);
      color: var(--white);
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      box-shadow: 0 4px 16px rgba(0,0,0,0.2);
      transition: transform 0.2s;
    }
    #music-btn:hover { transform: scale(1.05); }
    @keyframes spin { to { transform: rotate(360deg); } }
    .spinning { animation: spin 4s linear infinite; }

    /* ======================================================
       LIGHTBOX
       ====================================================== */
    #lightbox {
      position: fixed; inset: 0; z-index: 500;
      background: rgba(0,0,0,0.95);
      display: none;
      align-items: center;
      justify-content: center;
    }
    #lightbox.active { display: flex; }
    #lightbox img { max-width: 90vw; max-height: 90vh; object-fit: contain; }
    #lb-close {
      position: absolute; top: 20px; right: 24px;
      color: white; font-size: 28px; cursor: pointer;
      background: none; border: none; opacity: 0.7;
    }
    #lb-close:hover { opacity: 1; }

    /* ======================================================
       ALERT NOTIFIKASI
       ====================================================== */
    .alert-success {
      background: #f0fdf4;
      border-left: 3px solid #16a34a;
      padding: 12px 16px;
      font-family: var(--font-sans);
      font-size: 13px;
      color: #166534;
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 20px;
    }

    /* ======================================================
       SCROLL REVEAL ANIMATION
       ====================================================== */
    .reveal {
      opacity: 0;
      transform: translateY(22px);
      transition: opacity 0.7s ease, transform 0.7s ease;
    }
    .reveal.visible { opacity: 1; transform: translateY(0); }
    .d1 { transition-delay: 0.1s; }
    .d2 { transition-delay: 0.2s; }
    .d3 { transition-delay: 0.3s; }
    .d4 { transition-delay: 0.4s; }

    /* ======================================================
       RESPONSIVE MOBILE
       ====================================================== */
    @media (max-width: 600px) {
      .section { padding: 64px 20px; }
      .cover-frame { padding: 36px 24px; }
      .couple-grid { grid-template-columns: 1fr; gap: 20px; }
      .couple-sep { font-size: 32px; }
      .event-grid { grid-template-columns: 1fr; }
      .gallery-grid { grid-template-columns: repeat(2, 1fr); }
      .gallery-item:first-child { grid-column: span 2; }
      .gift-grid { grid-template-columns: 1fr; }
      .att-grid { grid-template-columns: 1fr; }
      .countdown-grid { grid-template-columns: repeat(2, 1fr); }
    }
  </style>
</head>
<body>

<!-- ════════════════════════════
     COVER SCREEN
     ════════════════════════════ -->
<div id="cover-screen">
  <div class="cover-frame">
    <span class="corner-tr"></span>
    <span class="corner-bl"></span>

    <p style="font-family:var(--font-sans);font-size:10px;letter-spacing:.28em;text-transform:uppercase;color:rgba(255,255,255,.35);margin-bottom:32px;">
      Undangan Pernikahan
    </p>

    <div class="cover-name"><?= clean($wedding['groom_name']) ?></div>
    <span class="cover-sep">dan</span>
    <div class="cover-name" style="margin-bottom:28px;"><?= clean($wedding['bride_name']) ?></div>

    <div style="width:28px;height:1px;background:rgba(255,255,255,.15);margin:0 auto 20px;"></div>

    <p style="font-family:var(--font-sans);font-size:12px;color:rgba(255,255,255,.35);margin-bottom:32px;letter-spacing:.05em;">
      <?= date('d F Y', strtotime($event_date)) ?>
    </p>

    <button onclick="openInvitation()"
            style="width:100%;padding:14px;background:white;color:#0A0A0A;
                   font-family:var(--font-sans);font-size:11px;font-weight:600;
                   letter-spacing:.15em;text-transform:uppercase;border:none;cursor:pointer;
                   display:flex;align-items:center;justify-content:center;gap:8px;">
      <i class="bi bi-envelope-open"></i> Buka Undangan
    </button>
  </div>
</div>

<!-- ════════════════════════════
     MAIN CONTENT
     ════════════════════════════ -->
<div id="main-content" style="display:none;">

  <?php if (!empty($wedding['music_url'])): ?>
  <audio id="bg-music" loop>
    <source src="<?= BASE_URL ?>/<?= clean($wedding['music_url']) ?>" type="audio/mpeg">
  </audio>
  <?php endif; ?>

  <button id="music-btn" onclick="toggleMusic()" title="Musik">
    <i class="bi bi-music-note" id="music-icon"></i>
  </button>

  <!-- ─── 1. HERO ─── -->
  <section class="section section-dark"
           style="min-height:100vh;display:flex;align-items:center;
                  justify-content:center;text-align:center;
                  position:relative;overflow:hidden;">

    <?php if (!empty($wedding['couple_photo'])): ?>
    <div style="position:absolute;inset:0;z-index:0;">
      <img src="<?= BASE_URL ?>/<?= clean($wedding['couple_photo']) ?>"
           alt="<?= $couple ?>"
           style="width:100%;height:100%;object-fit:cover;
                  opacity:0.15;filter:grayscale(50%);">
    </div>
    <?php endif; ?>

    <div class="container reveal" style="position:relative;z-index:1;">
      <span class="section-tag d1 reveal">Bismillahirrahmanirrahim</span>

      <div class="couple-name-big reveal d2" style="color:white;margin:16px 0;">
        <?= clean($wedding['groom_name']) ?>
        <span class="couple-amp">&amp;</span>
        <?= clean($wedding['bride_name']) ?>
      </div>

      <div class="divider reveal d3" style="margin:20px auto;background:rgba(255,255,255,.15);"></div>

      <p class="reveal d3"
         style="font-family:var(--font-sans);font-size:12px;
                color:rgba(255,255,255,.4);letter-spacing:.1em;">
        <?= date('d F Y', strtotime($event_date)) ?>
        <?php if (!empty($wedding['reception_venue'])): ?>
          &nbsp;&middot;&nbsp;<?= clean($wedding['reception_venue']) ?>
        <?php endif; ?>
      </p>
    </div>

    <!-- Scroll indicator -->
    <div style="position:absolute;bottom:28px;left:50%;transform:translateX(-50%);
                color:rgba(255,255,255,.2);font-size:24px;">
      <i class="bi bi-chevron-compact-down"></i>
    </div>
  </section>

  <!-- ─── 2. PEMBUKAAN ─── -->
  <section class="section section-white" style="text-align:center;">
    <div class="container">
      <span class="section-tag reveal">Dengan Rahmat Allah SWT</span>
      <h2 class="section-title reveal d1">Kami mengundang<br>kehadiran Anda</h2>
      <div class="divider reveal d2" style="margin-left:auto;margin-right:auto;"></div>
      <p class="reveal d3"
         style="font-family:var(--font-sans);font-size:14px;color:#525252;
                line-height:1.9;max-width:460px;margin:0 auto;">
        Merupakan suatu kebahagiaan apabila Bapak / Ibu / Saudara / i
        berkenan hadir dan memberikan doa restu kepada kedua mempelai.
      </p>
    </div>
  </section>

  <!-- ─── 3. MEMPELAI ─── -->
  <section class="section section-gray">
    <div class="container-wide">
      <div style="text-align:center;">
        <span class="section-tag reveal">Kedua Mempelai</span>
        <h2 class="section-title reveal d1">Yang Berbahagia</h2>
      </div>

      <div class="couple-grid">

        <!-- Mempelai Pria -->
        <div class="person reveal">
          <?php if (!empty($wedding['groom_photo'])): ?>
          <img class="person-photo"
               src="<?= BASE_URL ?>/<?= clean($wedding['groom_photo']) ?>"
               alt="<?= clean($wedding['groom_name']) ?>">
          <?php else: ?>
          <div class="person-empty"><i class="bi bi-person"></i></div>
          <?php endif; ?>

          <p class="person-role">Mempelai Pria</p>
          <h3 class="person-name"><?= clean($wedding['groom_name']) ?></h3>
          <?php if (!empty($wedding['groom_full_name'])): ?>
          <p class="person-fullname"><?= clean($wedding['groom_full_name']) ?></p>
          <?php endif; ?>
          <?php if (!empty($wedding['groom_father'])): ?>
          <p class="person-parents">
            Putra dari<br>
            <?= clean($wedding['groom_father']) ?>
            <?php if (!empty($wedding['groom_mother'])): ?>
            &amp; <?= clean($wedding['groom_mother']) ?>
            <?php endif; ?>
          </p>
          <?php endif; ?>
        </div>

        <!-- Simbol & -->
        <div class="couple-sep reveal d1">&amp;</div>

        <!-- Mempelai Wanita -->
        <div class="person reveal d2">
          <?php if (!empty($wedding['bride_photo'])): ?>
          <img class="person-photo"
               src="<?= BASE_URL ?>/<?= clean($wedding['bride_photo']) ?>"
               alt="<?= clean($wedding['bride_name']) ?>">
          <?php else: ?>
          <div class="person-empty"><i class="bi bi-person"></i></div>
          <?php endif; ?>

          <p class="person-role">Mempelai Wanita</p>
          <h3 class="person-name"><?= clean($wedding['bride_name']) ?></h3>
          <?php if (!empty($wedding['bride_full_name'])): ?>
          <p class="person-fullname"><?= clean($wedding['bride_full_name']) ?></p>
          <?php endif; ?>
          <?php if (!empty($wedding['bride_father'])): ?>
          <p class="person-parents">
            Putri dari<br>
            <?= clean($wedding['bride_father']) ?>
            <?php if (!empty($wedding['bride_mother'])): ?>
            &amp; <?= clean($wedding['bride_mother']) ?>
            <?php endif; ?>
          </p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </section>

  <!-- ─── 4. COUNTDOWN ─── -->
  <section class="section section-dark" style="text-align:center;">
    <div class="container">
      <span class="section-tag reveal">Menuju Hari Istimewa</span>
      <h2 class="section-title reveal d1" style="color:white;">
        <?= date('d F Y', strtotime($event_date)) ?>
      </h2>

      <div class="countdown-grid reveal d2"
           id="countdown-grid"
           data-date="<?= $event_date ?>"
           data-time="<?= $event_time ?>">
        <?php foreach (['cd-days'=>'Hari','cd-hours'=>'Jam','cd-minutes'=>'Menit','cd-seconds'=>'Detik'] as $id=>$lbl): ?>
        <div class="countdown-item">
          <span class="countdown-num" id="<?= $id ?>">00</span>
          <span class="countdown-lbl"><?= $lbl ?></span>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ─── 5. DETAIL ACARA ─── -->
  <section class="section section-white">
    <div class="container-wide">
      <div style="text-align:center;">
        <span class="section-tag reveal">Rangkaian Acara</span>
        <h2 class="section-title reveal d1">Waktu &amp; Tempat</h2>
        <div class="divider reveal d2" style="margin-left:auto;margin-right:auto;"></div>
      </div>

      <div class="event-grid">
        <?php foreach ([
          ['akad',      'Akad Nikah',  'Ijab Qabul'],
          ['reception', 'Resepsi',     'Walimatul Ursy'],
        ] as [$key,$type,$name]):
          if (empty($wedding[$key.'_date'])) continue; ?>
        <div class="event-card reveal">
          <p class="event-type"><?= $type ?></p>
          <h3 class="event-name"><?= $name ?></h3>

          <div class="event-row">
            <i class="bi bi-calendar3"></i>
            <span><?= date('l, d F Y', strtotime($wedding[$key.'_date'])) ?></span>
          </div>
          <?php if (!empty($wedding[$key.'_time'])): ?>
          <div class="event-row">
            <i class="bi bi-clock"></i>
            <span><?= date('H.i', strtotime($wedding[$key.'_time'])) ?> WIB</span>
          </div>
          <?php endif; ?>
          <?php if (!empty($wedding[$key.'_venue'])): ?>
          <div class="event-row">
            <i class="bi bi-geo-alt"></i>
            <span><?= clean($wedding[$key.'_venue']) ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($wedding[$key.'_address'])): ?>
          <div class="event-row">
            <i class="bi bi-pin-map"></i>
            <span style="color:var(--mid);font-size:12px;"><?= clean($wedding[$key.'_address']) ?></span>
          </div>
          <?php endif; ?>
          <?php if (!empty($wedding[$key.'_maps_url'])): ?>
          <a href="<?= $wedding[$key.'_maps_url'] ?>" target="_blank" class="event-map-btn">
            <i class="bi bi-map"></i> Lihat Peta
          </a>
          <?php endif; ?>
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>

  <!-- ─── 6. LOVE STORY ─── -->
  <?php if (!empty($wedding['love_story'])): ?>
  <section class="section section-gray">
    <div class="container">
      <span class="section-tag reveal">Kisah Kami</span>
      <h2 class="section-title reveal d1">Our <em>Love Story</em></h2>
      <div class="divider reveal d2"></div>
      <p class="reveal d3"
         style="font-family:var(--font-serif);font-size:clamp(17px,2.5vw,22px);
                font-weight:300;font-style:italic;line-height:1.8;color:#525252;
                border-left:2px solid var(--black);padding-left:22px;margin-top:24px;">
        <?= nl2br(clean($wedding['love_story'])) ?>
      </p>
    </div>
  </section>
  <?php endif; ?>

  <!-- ─── 7. GALERI ─── -->
  <?php if (!empty($gallery)): ?>
  <section class="section section-white">
    <div class="container-wide">
      <div style="text-align:center;">
        <span class="section-tag reveal">Momen Kami</span>
        <h2 class="section-title reveal d1">Galeri Foto</h2>
        <div class="divider reveal d2" style="margin:16px auto 0;"></div>
      </div>
      <div class="gallery-grid reveal">
        <?php foreach (array_slice($gallery, 0, 7) as $i => $photo): ?>
        <div class="gallery-item"
             onclick="openLightbox('<?= BASE_URL ?>/<?= clean($photo) ?>')">
          <img src="<?= BASE_URL ?>/<?= clean($photo) ?>"
               alt="Foto <?= $i+1 ?>" loading="lazy">
        </div>
        <?php endforeach; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ─── 8. RSVP ─── -->
  <section class="section section-gray" id="rsvp">
    <div class="container">
      <span class="section-tag reveal">Konfirmasi Kehadiran</span>
      <h2 class="section-title reveal d1">RSVP</h2>
      <div class="divider reveal d2"></div>

      <?php if ((int)$rsvp_stat['total_response'] > 0): ?>
      <p class="reveal"
         style="font-family:var(--font-sans);font-size:12px;
                color:var(--mid);margin-top:12px;">
        <i class="bi bi-people" style="margin-right:4px;"></i>
        <strong style="color:var(--black);"><?= $rsvp_stat['total_response'] ?></strong>
        tamu (<?= (int)$rsvp_stat['total_guests'] ?> orang) sudah konfirmasi hadir
      </p>
      <?php endif; ?>

      <?php if ($form_status === 'rsvp_ok'): ?>
      <div class="alert-success">
        <i class="bi bi-check-circle-fill"></i>
        Terima kasih! Konfirmasi kehadiran Anda sudah tercatat.
      </div>
      <?php endif; ?>

      <form method="POST" action="?slug=<?= $slug ?>#rsvp"
            style="margin-top:24px;" class="reveal">
        <input type="hidden" name="action" value="rsvp">

        <div class="form-group">
          <label class="form-label">Nama Lengkap *</label>
          <input type="text" name="guest_name" class="form-input"
                 placeholder="Tulis nama Anda" required>
        </div>
        <div class="form-group">
          <label class="form-label">Nomor WhatsApp</label>
          <input type="tel" name="guest_phone" class="form-input"
                 placeholder="08xxxxxxxxxx">
        </div>
        <div class="form-group">
          <label class="form-label">Konfirmasi Kehadiran *</label>
          <div class="att-grid">
            <?php foreach (['hadir'=>'Hadir','tidak_hadir'=>'Tidak Hadir','mungkin'=>'Mungkin'] as $v=>$l): ?>
            <label class="att-option">
              <input type="radio" name="attendance" value="<?= $v ?>"
                     <?= $v==='hadir'?'checked':'' ?>>
              <span class="att-btn"><?= $l ?></span>
            </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Jumlah Tamu</label>
          <select name="total_guests" class="form-input">
            <?php for ($i=1;$i<=10;$i++): ?>
            <option value="<?= $i ?>"><?= $i ?> orang</option>
            <?php endfor; ?>
          </select>
        </div>
        <div class="form-group">
          <label class="form-label">Ucapan &amp; Doa (opsional)</label>
          <textarea name="message" class="form-input"
                    placeholder="Tulis ucapan dan doa..."></textarea>
        </div>
        <button type="submit" class="btn btn-dark">
          <i class="bi bi-send"></i> Kirim Konfirmasi
        </button>
      </form>
    </div>
  </section>

  <!-- ─── 9. UCAPAN ─── -->
  <section class="section section-white" id="ucapan">
    <div class="container">
      <span class="section-tag reveal">Dari Para Tamu</span>
      <h2 class="section-title reveal d1">Ucapan &amp; Doa</h2>
      <div class="divider reveal d2"></div>

      <?php if ($form_status === 'msg_ok'): ?>
      <div class="alert-success">
        <i class="bi bi-check-circle-fill"></i> Ucapan Anda sudah terkirim!
      </div>
      <?php endif; ?>

      <form method="POST" action="?slug=<?= $slug ?>#ucapan"
            style="margin-top:24px;margin-bottom:32px;" class="reveal">
        <input type="hidden" name="action" value="message">
        <div class="form-group">
          <input type="text" name="msg_name" class="form-input"
                 placeholder="Nama Anda" required>
        </div>
        <div class="form-group">
          <textarea name="msg_text" class="form-input"
                    placeholder="Tulis ucapan terbaik untuk kedua mempelai..."
                    required></textarea>
        </div>
        <button type="submit" class="btn btn-dark">
          <i class="bi bi-heart"></i> Kirim Ucapan
        </button>
      </form>

      <?php if (!empty($messages)): ?>
      <div class="messages-wall reveal">
        <?php foreach ($messages as $msg): ?>
        <div class="message-item">
          <p class="message-name"><?= clean($msg['guest_name']) ?></p>
          <p class="message-time">
            <i class="bi bi-clock" style="margin-right:3px;"></i>
            <?= date('d M Y, H:i', strtotime($msg['created_at'])) ?>
          </p>
          <p class="message-text">"<?= clean($msg['message']) ?>"</p>
        </div>
        <?php endforeach; ?>
      </div>
      <?php else: ?>
      <p style="text-align:center;font-family:var(--font-sans);font-size:13px;
                color:var(--mid);padding:28px 0;" class="reveal">
        Jadilah yang pertama memberikan ucapan ✉️
      </p>
      <?php endif; ?>
    </div>
  </section>

  <!-- ─── 10. WEDDING GIFT ─── -->
  <?php if (!empty($wedding['qris_image']) || !empty($wedding['bank_account_number'])): ?>
  <section class="section section-dark">
    <div class="container" style="text-align:center;">
      <span class="section-tag reveal">Hadiah Pernikahan</span>
      <h2 class="section-title reveal d1" style="color:white;">Wedding Gift</h2>
      <div class="divider reveal d2" style="margin:16px auto;background:rgba(255,255,255,.15);"></div>
      <p class="reveal"
         style="font-family:var(--font-sans);font-size:13px;
                color:rgba(255,255,255,.35);margin-bottom:28px;">
        Doa restu Anda adalah hadiah terbaik bagi kami.
      </p>
      <div class="gift-grid reveal">
        <?php if (!empty($wedding['qris_image'])): ?>
        <div class="gift-card">
          <p class="gift-label">Scan QRIS</p>
          <img src="<?= BASE_URL ?>/<?= clean($wedding['qris_image']) ?>"
               class="gift-qris" alt="QRIS">
          <p style="font-family:var(--font-sans);font-size:11px;
                    color:rgba(255,255,255,.25);">
            Scan dengan aplikasi bank / e-wallet apapun
          </p>
        </div>
        <?php endif; ?>

        <?php if (!empty($wedding['bank_account_number'])): ?>
        <div class="gift-card">
          <p class="gift-label">Transfer Bank</p>
          <i class="bi bi-bank2"
             style="font-size:28px;color:rgba(255,255,255,.15);
                    display:block;margin-bottom:12px;"></i>
          <p class="gift-bank"><?= clean($wedding['bank_name'] ?? '') ?></p>
          <p class="gift-number"><?= clean($wedding['bank_account_number']) ?></p>
          <p class="gift-owner">a.n <?= clean($wedding['bank_account_name'] ?? '') ?></p>
          <button class="btn-copy"
                  onclick="copyText('<?= clean($wedding['bank_account_number']) ?>', this)">
            <i class="bi bi-clipboard" style="margin-right:4px;"></i>Salin Nomor
          </button>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </section>
  <?php endif; ?>

  <!-- ─── 11. PENUTUP ─── -->
  <section class="section section-white" style="text-align:center;">
    <div class="container">
      <span class="section-tag reveal">Penutup</span>
      <h2 class="section-title reveal d1">Terima Kasih</h2>
      <div class="divider reveal d2" style="margin-left:auto;margin-right:auto;"></div>
      <p class="reveal d3"
         style="font-family:var(--font-sans);font-size:14px;color:#525252;
                max-width:440px;margin:0 auto 44px;line-height:1.9;">
        Atas kehadiran dan doa restu Bapak / Ibu / Saudara / i,
        kami ucapkan terima kasih yang sebesar-besarnya.
      </p>
      <div class="couple-name-big reveal d4"
           style="font-size:clamp(30px,6vw,50px);margin-bottom:32px;">
        <?= clean($wedding['groom_name']) ?>
        <span class="couple-amp" style="color:var(--mid);">&amp;</span>
        <?= clean($wedding['bride_name']) ?>
      </div>
      <a href="<?= BASE_URL ?>"
         style="font-family:var(--font-sans);font-size:11px;font-weight:500;
                letter-spacing:.12em;text-transform:uppercase;color:var(--mid);
                border-bottom:1px solid var(--gray2);padding-bottom:2px;"
         class="reveal">
        ← Kembali ke Beranda
      </a>
    </div>
  </section>

</div><!-- /main-content -->

<!-- Lightbox -->
<div id="lightbox" onclick="closeLightbox()">
  <button id="lb-close" onclick="closeLightbox()"><i class="bi bi-x-lg"></i></button>
  <img id="lightbox-img" src="" alt="Foto">
</div>

<script>
/* ── Buka undangan (tutup cover) ── */
function openInvitation() {
  const cover = document.getElementById('cover-screen');
  cover.classList.add('closing');
  setTimeout(() => {
    cover.style.display = 'none';
    document.getElementById('main-content').style.display = 'block';
    // Tunggu sebentar baru init agar DOM sudah render
    setTimeout(() => { initReveal(); startCountdown(); tryAutoplay(); }, 150);
  }, 800);
}

/* ── Countdown ── */
function startCountdown() {
  const el = document.getElementById('countdown-grid');
  if (!el) return;
  const target = new Date(el.dataset.date + 'T' + (el.dataset.time || '08:00:00'));
  function tick() {
    const diff = target - new Date();
    if (diff <= 0) {
      el.innerHTML = '<div style="grid-column:span 4;padding:24px;font-family:var(--font-serif);font-size:22px;color:white;text-align:center;">🎉 Selamat Menempuh Hidup Baru!</div>';
      return;
    }
    const d = Math.floor(diff / 864e5);
    const h = Math.floor((diff % 864e5) / 36e5);
    const m = Math.floor((diff % 36e5) / 6e4);
    const s = Math.floor((diff % 6e4) / 1e3);
    document.getElementById('cd-days').textContent    = String(d).padStart(2,'0');
    document.getElementById('cd-hours').textContent   = String(h).padStart(2,'0');
    document.getElementById('cd-minutes').textContent = String(m).padStart(2,'0');
    document.getElementById('cd-seconds').textContent = String(s).padStart(2,'0');
  }
  tick();
  setInterval(tick, 1000);
}

/* ── Scroll reveal ── */
function initReveal() {
  const obs = new IntersectionObserver((entries) => {
    entries.forEach(e => {
      if (e.isIntersecting) {
        e.target.classList.add('visible');
        obs.unobserve(e.target);
      }
    });
  }, { threshold: 0.08, rootMargin: '0px 0px -30px 0px' });
  document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
}

/* ── Musik ── */
let isPlaying = false;
function tryAutoplay() {
  const audio = document.getElementById('bg-music');
  if (!audio) return;
  audio.volume = 0.35;
  audio.play()
    .then(() => { isPlaying = true; document.getElementById('music-icon').classList.add('spinning'); })
    .catch(() => {});
}
function toggleMusic() {
  const audio = document.getElementById('bg-music');
  const icon  = document.getElementById('music-icon');
  if (!audio) return;
  if (isPlaying) {
    audio.pause();
    icon.classList.remove('spinning');
    isPlaying = false;
  } else {
    audio.play();
    icon.classList.add('spinning');
    isPlaying = true;
  }
}

/* ── Lightbox ── */
function openLightbox(src) {
  document.getElementById('lightbox-img').src = src;
  document.getElementById('lightbox').classList.add('active');
  document.body.style.overflow = 'hidden';
}
function closeLightbox() {
  document.getElementById('lightbox').classList.remove('active');
  document.body.style.overflow = '';
}
document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });

/* ── Copy rekening ── */
function copyText(text, btn) {
  navigator.clipboard.writeText(text).then(() => {
    const orig = btn.innerHTML;
    btn.innerHTML = '<i class="bi bi-check2" style="margin-right:4px;"></i>Tersalin!';
    setTimeout(() => { btn.innerHTML = orig; }, 2000);
  }).catch(() => {
    // Fallback browser lama
    const el = document.createElement('textarea');
    el.value = text;
    document.body.appendChild(el);
    el.select();
    document.execCommand('copy');
    document.body.removeChild(el);
    alert('Tersalin: ' + text);
  });
}
</script>

</body>
</html>