<?php
// ============================================================
// FILE: admin/messages.php
// Moderasi ucapan tamu — approve / hapus
// ============================================================
require_once '../config/database.php';
requireLogin();

$flash = $flash_type = '';
if (!empty($_SESSION['flash_message'])) {
    $flash = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// ── Proses aksi (approve / reject / delete) ───────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id     = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        match($action) {
            'approve' => db_execute("UPDATE messages SET is_approved = 1 WHERE id = ?", [$id], 'i'),
            'reject'  => db_execute("UPDATE messages SET is_approved = 0 WHERE id = ?", [$id], 'i'),
            'delete'  => db_execute("DELETE FROM messages WHERE id = ?", [$id], 'i'),
            default   => null,
        };
        $_SESSION['flash_message'] = match($action) {
            'approve' => 'Ucapan berhasil ditampilkan.',
            'reject'  => 'Ucapan berhasil disembunyikan.',
            'delete'  => 'Ucapan berhasil dihapus.',
            default   => 'Aksi berhasil.',
        };
        $_SESSION['flash_type'] = 'success';
    }
    redirect(BASE_URL . '/admin/messages.php');
}

// ── Filter ────────────────────────────────────────────────────
$wedding_id    = (int)($_GET['wedding_id'] ?? 0);
$filter_status = clean($_GET['status'] ?? '');

$where = []; $params = []; $types = '';
if ($wedding_id)     { $where[] = 'm.wedding_id = ?';   $params[] = $wedding_id;    $types .= 'i'; }
if ($filter_status === 'approved')  { $where[] = 'm.is_approved = 1'; }
if ($filter_status === 'hidden')    { $where[] = 'm.is_approved = 0'; }
$wsql = $where ? 'WHERE '.implode(' AND ', $where) : '';

$messages = db_rows("
    SELECT m.*, w.groom_name, w.bride_name
    FROM messages m
    LEFT JOIN weddings w ON m.wedding_id = w.id
    {$wsql}
    ORDER BY m.created_at DESC
", $params, $types);

$stats = db_row("SELECT COUNT(*) AS total,
    SUM(is_approved=1) AS approved,
    SUM(is_approved=0) AS hidden
    FROM messages");

$weddings = db_rows("SELECT id, groom_name, bride_name FROM weddings ORDER BY created_at DESC");

$page_title = 'Ucapan Tamu';
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50">
<div class="flex min-h-screen">
<?php include '_sidebar.php'; ?>
<main class="flex-1 overflow-auto">

  <header class="bg-white border-b border-gray-100 px-8 py-4 sticky top-0 z-10 flex items-center justify-between">
    <div>
      <h1 class="section-title">Ucapan Tamu</h1>
      <p class="font-body text-gray-400 text-xs mt-0.5"><?= count($messages) ?> ucapan ditemukan</p>
    </div>
  </header>

  <div class="p-8">

    <?php if ($flash): ?>
    <div class="alert-<?= $flash_type ?> px-4 py-3 mb-6 flex items-center gap-2 font-body text-sm">
      <i class="bi bi-check-circle-fill"></i> <?= clean($flash) ?>
    </div>
    <?php endif; ?>

    <!-- Statistik -->
    <div class="grid grid-cols-3 gap-4 mb-8">
      <?php foreach ([
        ['Total Ucapan', $stats['total'],    'text-charcoal', 'bi-chat-dots'],
        ['Ditampilkan',  $stats['approved'], 'text-green-600','bi-eye'],
        ['Disembunyikan',$stats['hidden'],   'text-red-500',  'bi-eye-slash'],
      ] as [$lbl,$val,$col,$ico]): ?>
      <div class="bg-white border border-gray-100 p-5">
        <i class="bi <?= $ico ?> <?= $col ?> text-xl block mb-2"></i>
        <div class="font-display text-3xl <?= $col ?> font-normal"><?= $val ?></div>
        <div class="font-body text-gray-400 text-xs mt-1"><?= $lbl ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Filter -->
    <div class="admin-card mb-6">
      <div class="admin-card-body">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
          <div>
            <label class="form-label">Filter Undangan</label>
            <select name="wedding_id" class="form-control">
              <option value="">Semua Undangan</option>
              <?php foreach ($weddings as $w): ?>
              <option value="<?= $w['id'] ?>" <?= $wedding_id==$w['id']?'selected':''?>>
                <?= clean($w['groom_name']) ?> & <?= clean($w['bride_name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
              <option value="">Semua</option>
              <option value="approved" <?= $filter_status==='approved'?'selected':''?>>Ditampilkan</option>
              <option value="hidden"   <?= $filter_status==='hidden'  ?'selected':''?>>Disembunyikan</option>
            </select>
          </div>
          <button type="submit" class="btn-primary"><i class="bi bi-search"></i> FILTER</button>
          <?php if ($wedding_id || $filter_status): ?>
          <a href="messages.php" class="btn-secondary"><i class="bi bi-x"></i> RESET</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- Tabel ucapan -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span class="section-title text-lg">Daftar Ucapan</span>
      </div>

      <?php if (empty($messages)): ?>
      <div class="p-16 text-center">
        <i class="bi bi-chat-dots text-4xl text-gray-200 block mb-3"></i>
        <p class="font-body text-gray-400 text-sm">Belum ada ucapan.</p>
      </div>
      <?php else: ?>
      <div class="divide-y divide-gray-50">
        <?php foreach ($messages as $msg): ?>
        <div class="p-5 hover:bg-gray-50 transition-colors">
          <div class="flex items-start justify-between gap-4">
            <!-- Konten ucapan -->
            <div class="flex-1">
              <div class="flex items-center gap-2 mb-1">
                <i class="bi bi-person-circle text-gold"></i>
                <span class="font-body font-medium text-sm text-charcoal">
                  <?= clean($msg['guest_name']) ?>
                </span>
                <span class="font-body text-xs text-gray-400">untuk</span>
                <span class="font-body text-xs text-gray-500">
                  <?= clean($msg['groom_name']) ?> &amp; <?= clean($msg['bride_name']) ?>
                </span>
                <!-- Badge status -->
                <?php if ($msg['is_approved']): ?>
                <span class="badge badge-green"><i class="bi bi-eye text-[10px] me-1"></i>Tampil</span>
                <?php else: ?>
                <span class="badge badge-gray"><i class="bi bi-eye-slash text-[10px] me-1"></i>Tersembunyi</span>
                <?php endif; ?>
              </div>
              <!-- Teks ucapan -->
              <p class="font-display font-light italic text-gray-600 text-sm leading-relaxed my-2 pl-6">
                "<?= clean($msg['message']) ?>"
              </p>
              <p class="font-body text-xs text-gray-400 pl-6">
                <i class="bi bi-clock me-1"></i>
                <?= date('d M Y, H:i', strtotime($msg['created_at'])) ?>
              </p>
            </div>

            <!-- Tombol aksi -->
            <div class="flex gap-1 flex-shrink-0">
              <?php if ($msg['is_approved']): ?>
              <form method="POST">
                <input type="hidden" name="action" value="reject">
                <input type="hidden" name="id"     value="<?= $msg['id'] ?>">
                <button type="submit" class="btn-action btn-delete" title="Sembunyikan">
                  <i class="bi bi-eye-slash"></i>
                </button>
              </form>
              <?php else: ?>
              <form method="POST">
                <input type="hidden" name="action" value="approve">
                <input type="hidden" name="id"     value="<?= $msg['id'] ?>">
                <button type="submit" class="btn-action btn-view" title="Tampilkan">
                  <i class="bi bi-eye"></i>
                </button>
              </form>
              <?php endif; ?>
              <form method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id"     value="<?= $msg['id'] ?>">
                <button type="submit" class="btn-action btn-delete"
                        onclick="return confirm('Hapus ucapan ini permanen?')" title="Hapus">
                  <i class="bi bi-trash3"></i>
                </button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>

  </div>
</main>
</div>
</body></html>