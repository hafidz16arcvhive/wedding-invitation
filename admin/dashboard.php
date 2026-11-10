<?php
// ============================================================
// FILE: admin/dashboard.php
// Panel admin — ringkasan statistik & aksi cepat
// ============================================================
require_once '../config/database.php';
requireLogin();

// Statistik utama
$stats = [
    'weddings' => db_row("SELECT COUNT(*) AS c FROM weddings")['c'] ?? 0,
    'published'=> db_row("SELECT COUNT(*) AS c FROM weddings WHERE is_published=1")['c'] ?? 0,
    'orders'   => db_row("SELECT COUNT(*) AS c FROM orders")['c'] ?? 0,
    'pending'  => db_row("SELECT COUNT(*) AS c FROM orders WHERE status='pending'")['c'] ?? 0,
    'rsvp'     => db_row("SELECT COUNT(*) AS c FROM rsvp")['c'] ?? 0,
    'hadir'    => db_row("SELECT COUNT(*) AS c FROM rsvp WHERE attendance='hadir'")['c'] ?? 0,
    'messages' => db_row("SELECT COUNT(*) AS c FROM messages")['c'] ?? 0,
    'revenue'  => db_row("SELECT COALESCE(SUM(total_price),0) AS c FROM orders WHERE payment_status='paid'")['c'] ?? 0,
];

// Data terbaru
$recent_weddings = db_rows("SELECT w.*,t.name AS tpl FROM weddings w LEFT JOIN invitation_templates t ON w.template_id=t.id ORDER BY w.created_at DESC LIMIT 5");
$recent_orders   = db_rows("SELECT * FROM orders ORDER BY created_at DESC LIMIT 5");
$recent_messages = db_rows("SELECT m.*,w.groom_name,w.bride_name FROM messages m LEFT JOIN weddings w ON m.wedding_id=w.id ORDER BY m.created_at DESC LIMIT 4");

$site_name = db_row("SELECT setting_value FROM settings WHERE setting_key='site_name'")['setting_value'] ?? 'LoveStory';

$page_title = 'Dashboard';
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50">
<div class="flex min-h-screen">
<?php include '_sidebar.php'; ?>
<main class="flex-1 overflow-auto">

  <!-- Top Bar -->
  <header class="bg-white border-b border-gray-100 px-8 py-4 sticky top-0 z-10 flex items-center justify-between">
    <div>
      <h1 class="section-title">Dashboard</h1>
      <p class="font-body text-gray-400 text-xs mt-0.5"><?= date('l, d F Y') ?></p>
    </div>
    <a href="<?= BASE_URL ?>" target="_blank"
       class="font-body text-xs text-gray-400 hover:text-gold transition-colors flex items-center gap-1">
      <i class="bi bi-box-arrow-up-right"></i> Lihat Website
    </a>
  </header>

  <div class="p-8">

    <!-- ── STATISTIK 4 KOLOM ── -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
      <?php
      $cards = [
        ['Undangan Aktif', $stats['published'].'/'.$stats['weddings'], 'bi-heart-fill',  'text-rose-400', 'bg-rose-50',   'weddings.php'],
        ['Pesanan Masuk',  $stats['pending'].' pending',               'bi-cart3',       'text-blue-400', 'bg-blue-50',   'orders.php'],
        ['Tamu Hadir',     $stats['hadir'].' / '.$stats['rsvp'],      'bi-people-fill', 'text-green-400','bg-green-50',  'rsvp.php'],
        ['Revenue',        'Rp '.number_format($stats['revenue'],0,',','.'), 'bi-cash-coin','text-gold','bg-yellow-50','orders.php'],
      ];
      foreach ($cards as [$lbl,$val,$ico,$col,$bg,$href]): ?>
      <a href="<?= $href ?>" class="bg-white border border-gray-100 p-5 hover:border-gold transition-colors group block">
        <div class="<?= $bg ?> w-10 h-10 flex items-center justify-center mb-3 group-hover:scale-105 transition-transform">
          <i class="bi <?= $ico ?> <?= $col ?>"></i>
        </div>
        <div class="font-display text-2xl text-charcoal font-normal"><?= $val ?></div>
        <div class="font-body text-gray-400 text-xs mt-0.5"><?= $lbl ?></div>
      </a>
      <?php endforeach; ?>
    </div>

    <!-- ── QUICK ACTIONS ── -->
    <div class="bg-charcoal p-6 mb-6">
      <p class="font-body text-gray-500 text-xs tracking-widest uppercase mb-3">Aksi Cepat</p>
      <div class="flex flex-wrap gap-3">
        <a href="wedding_add.php"  class="btn-primary text-xs py-2 px-5"><i class="bi bi-plus-lg"></i> TAMBAH UNDANGAN</a>
        <a href="weddings.php"     class="btn-secondary text-xs py-2 px-5"><i class="bi bi-heart"></i> KELOLA UNDANGAN</a>
        <a href="orders.php"       class="btn-secondary text-xs py-2 px-5"><i class="bi bi-cart3"></i> LIHAT PESANAN</a>
        <a href="rsvp.php?export=csv" class="btn-secondary text-xs py-2 px-5"><i class="bi bi-download"></i> EXPORT RSVP</a>
      </div>
    </div>

    <!-- ── DUA KOLOM: Undangan + Pesanan ── -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">

      <!-- Undangan terbaru -->
      <div class="admin-card">
        <div class="admin-card-header">
          <span class="section-title text-base">Undangan Terbaru</span>
          <a href="weddings.php" class="font-body text-gold text-xs hover:underline">Lihat Semua</a>
        </div>
        <div class="divide-y divide-gray-50">
          <?php if (empty($recent_weddings)): ?>
          <p class="p-6 font-body text-gray-400 text-sm text-center">Belum ada undangan</p>
          <?php else: foreach ($recent_weddings as $w): ?>
          <div class="px-5 py-3 flex items-center justify-between hover:bg-gray-50 transition-colors">
            <div>
              <div class="font-body text-sm font-medium text-charcoal">
                <?= clean($w['groom_name']) ?> &amp; <?= clean($w['bride_name']) ?>
              </div>
              <div class="font-body text-xs text-gray-400 mt-0.5">
                <?= clean($w['tpl'] ?? '—') ?> · <?= date('d M Y', strtotime($w['created_at'])) ?>
              </div>
            </div>
            <div class="flex items-center gap-2">
              <span class="badge <?= $w['is_published']?'badge-green':'badge-gray' ?>">
                <?= $w['is_published']?'Aktif':'Draft' ?>
              </span>
              <a href="<?= BASE_URL ?>/invitation/index.php?slug=<?= clean($w['slug']) ?>"
                 target="_blank" class="text-gold hover:text-gold-dark">
                <i class="bi bi-box-arrow-up-right text-xs"></i>
              </a>
            </div>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>

      <!-- Pesanan terbaru -->
      <div class="admin-card">
        <div class="admin-card-header">
          <span class="section-title text-base">Pesanan Terbaru</span>
          <a href="orders.php" class="font-body text-gold text-xs hover:underline">Lihat Semua</a>
        </div>
        <div class="divide-y divide-gray-50">
          <?php if (empty($recent_orders)): ?>
          <p class="p-6 font-body text-gray-400 text-sm text-center">Belum ada pesanan</p>
          <?php else: foreach ($recent_orders as $o): ?>
          <div class="px-5 py-3 hover:bg-gray-50 transition-colors">
            <div class="flex items-center justify-between">
              <div>
                <div class="font-body text-xs font-medium text-charcoal"><?= clean($o['order_number']) ?></div>
                <div class="font-body text-xs text-gray-400"><?= clean($o['customer_name']) ?> · <?= ucfirst($o['package_type']) ?></div>
              </div>
              <div class="flex items-center gap-2">
                <?php $sc=['pending'=>'badge-yellow','confirmed'=>'badge-blue','completed'=>'badge-green','cancelled'=>'badge-red'];?>
                <span class="badge <?= $sc[$o['status']]??'badge-gray' ?>"><?= ucfirst($o['status']) ?></span>
              </div>
            </div>
          </div>
          <?php endforeach; endif; ?>
        </div>
      </div>

    </div>

    <!-- ── UCAPAN TERBARU ── -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span class="section-title text-base">Ucapan Terbaru</span>
        <a href="messages.php" class="font-body text-gold text-xs hover:underline">Lihat Semua</a>
      </div>
      <?php if (empty($recent_messages)): ?>
      <p class="p-6 font-body text-gray-400 text-sm text-center">Belum ada ucapan</p>
      <?php else: ?>
      <div class="grid grid-cols-1 md:grid-cols-2 divide-y md:divide-y-0 md:divide-x divide-gray-50">
        <?php foreach ($recent_messages as $m): ?>
        <div class="p-5">
          <div class="flex items-center gap-2 mb-1">
            <i class="bi bi-person-circle text-gold text-sm"></i>
            <span class="font-body text-sm font-medium text-charcoal"><?= clean($m['guest_name']) ?></span>
            <span class="font-body text-xs text-gray-400">→ <?= clean($m['groom_name']) ?> &amp; <?= clean($m['bride_name']) ?></span>
          </div>
          <p class="font-display italic text-gray-500 text-sm leading-relaxed line-clamp-2">
            "<?= clean($m['message']) ?>"
          </p>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

  </div>
</main>
</div>
</body></html>