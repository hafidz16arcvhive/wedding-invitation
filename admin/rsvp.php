<?php
// ============================================================
// FILE: admin/rsvp.php
// Kelola data RSVP — lihat siapa yang hadir, export, dll
// ============================================================
require_once '../config/database.php';
requireLogin();

// ── Flash message ─────────────────────────────────────────────
$flash = $flash_type = '';
if (!empty($_SESSION['flash_message'])) {
    $flash      = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// ── Proses hapus ──────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    $id = (int)($_POST['id'] ?? 0);
    if ($id > 0) {
        db_execute("DELETE FROM rsvp WHERE id = ?", [$id], 'i');
        $_SESSION['flash_message'] = 'Data RSVP berhasil dihapus.';
        $_SESSION['flash_type']    = 'success';
    }
    redirect(BASE_URL . '/admin/rsvp.php' . ($_GET['wedding_id'] ? '?wedding_id='.(int)$_GET['wedding_id'] : ''));
}

// ── Filter per undangan ───────────────────────────────────────
$wedding_id     = (int)($_GET['wedding_id'] ?? 0);
$filter_attend  = clean($_GET['attendance'] ?? '');

$where  = []; $params = []; $types = '';
if ($wedding_id > 0) { $where[] = 'r.wedding_id = ?'; $params[] = $wedding_id; $types .= 'i'; }
if ($filter_attend)  { $where[] = 'r.attendance = ?';  $params[] = $filter_attend; $types .= 's'; }
$wsql = $where ? 'WHERE '.implode(' AND ', $where) : '';

// Ambil data RSVP
$rsvp_list = db_rows("
    SELECT r.*, w.groom_name, w.bride_name
    FROM rsvp r
    LEFT JOIN weddings w ON r.wedding_id = w.id
    {$wsql}
    ORDER BY r.created_at DESC
", $params, $types);

// Statistik
$stats = db_row("
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN attendance='hadir'       THEN 1 ELSE 0 END) AS hadir,
        SUM(CASE WHEN attendance='tidak_hadir' THEN 1 ELSE 0 END) AS tidak_hadir,
        SUM(CASE WHEN attendance='mungkin'     THEN 1 ELSE 0 END) AS mungkin,
        SUM(CASE WHEN attendance='hadir'       THEN total_guests ELSE 0 END) AS total_tamu
    FROM rsvp
    " . ($wedding_id ? "WHERE wedding_id = $wedding_id" : '')
);

// Daftar undangan untuk dropdown filter
$weddings = db_rows("SELECT id, groom_name, bride_name FROM weddings ORDER BY created_at DESC");

$page_title = 'Data RSVP';
?>
<?php include '_head.php'; ?>
<body class="bg-gray-50">
<div class="flex min-h-screen">
<?php include '_sidebar.php'; ?>
<main class="flex-1 overflow-auto">

  <!-- Top Bar -->
  <header class="bg-white border-b border-gray-100 px-8 py-4 sticky top-0 z-10 flex items-center justify-between">
    <div>
      <h1 class="section-title">Data RSVP</h1>
      <p class="font-body text-gray-400 text-xs mt-0.5"><?= count($rsvp_list) ?> konfirmasi ditemukan</p>
    </div>
    <!-- Export CSV -->
    <a href="?<?= http_build_query(array_merge($_GET, ['export'=>'csv'])) ?>"
       class="btn-secondary text-xs">
      <i class="bi bi-download"></i> EXPORT CSV
    </a>
  </header>

  <?php
  // ── Export CSV ───────────────────────────────────────────────
  if (isset($_GET['export']) && $_GET['export'] === 'csv') {
      header('Content-Type: text/csv; charset=utf-8');
      header('Content-Disposition: attachment; filename="rsvp-'.date('Ymd').'.csv"');
      $out = fopen('php://output', 'w');
      fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8
      fputcsv($out, ['No','Nama Tamu','WhatsApp','Undangan','Kehadiran','Jumlah Tamu','Ucapan','Waktu']);
      foreach ($rsvp_list as $i => $r) {
          fputcsv($out, [
              $i+1,
              $r['guest_name'],
              $r['guest_phone'],
              $r['groom_name'].' & '.$r['bride_name'],
              $r['attendance'],
              $r['total_guests'],
              $r['message'],
              date('d/m/Y H:i', strtotime($r['created_at'])),
          ]);
      }
      fclose($out); exit;
  }
  ?>

  <div class="p-8">

    <?php if ($flash): ?>
    <div class="alert-<?= $flash_type ?> px-4 py-3 mb-6 flex items-center gap-2 font-body text-sm">
      <i class="bi bi-check-circle-fill"></i> <?= clean($flash) ?>
    </div>
    <?php endif; ?>

    <!-- ── STATISTIK ── -->
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4 mb-8">
      <?php
      $stat_items = [
        ['label'=>'Total RSVP',  'value'=>$stats['total'],       'color'=>'text-charcoal', 'bg'=>'bg-gray-50'],
        ['label'=>'Hadir',       'value'=>$stats['hadir'],       'color'=>'text-green-600','bg'=>'bg-green-50'],
        ['label'=>'Tidak Hadir', 'value'=>$stats['tidak_hadir'], 'color'=>'text-red-500',  'bg'=>'bg-red-50'],
        ['label'=>'Mungkin',     'value'=>$stats['mungkin'],     'color'=>'text-yellow-600','bg'=>'bg-yellow-50'],
        ['label'=>'Total Orang', 'value'=>(int)$stats['total_tamu'].' orang', 'color'=>'text-blue-600','bg'=>'bg-blue-50'],
      ];
      foreach ($stat_items as $s): ?>
      <div class="bg-white border border-gray-100 p-5 <?= $s['bg'] ?>">
        <div class="font-display text-3xl <?= $s['color'] ?> font-normal"><?= $s['value'] ?></div>
        <div class="font-body text-gray-400 text-xs mt-1"><?= $s['label'] ?></div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- ── FILTER ── -->
    <div class="admin-card mb-6">
      <div class="admin-card-body">
        <form method="GET" class="flex flex-wrap gap-3 items-end">
          <div>
            <label class="form-label">Filter Undangan</label>
            <select name="wedding_id" class="form-control">
              <option value="">Semua Undangan</option>
              <?php foreach ($weddings as $w): ?>
              <option value="<?= $w['id'] ?>" <?= $wedding_id==$w['id']?'selected':'' ?>>
                <?= clean($w['groom_name']) ?> & <?= clean($w['bride_name']) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div>
            <label class="form-label">Kehadiran</label>
            <select name="attendance" class="form-control">
              <option value="">Semua</option>
              <option value="hadir"       <?= $filter_attend==='hadir'      ?'selected':''?>>Hadir</option>
              <option value="tidak_hadir" <?= $filter_attend==='tidak_hadir'?'selected':''?>>Tidak Hadir</option>
              <option value="mungkin"     <?= $filter_attend==='mungkin'    ?'selected':''?>>Mungkin</option>
            </select>
          </div>
          <button type="submit" class="btn-primary"><i class="bi bi-search"></i> FILTER</button>
          <?php if ($wedding_id || $filter_attend): ?>
          <a href="rsvp.php" class="btn-secondary"><i class="bi bi-x"></i> RESET</a>
          <?php endif; ?>
        </form>
      </div>
    </div>

    <!-- ── TABEL RSVP ── -->
    <div class="admin-card">
      <div class="admin-card-header">
        <span class="section-title text-lg">Daftar Konfirmasi Tamu</span>
      </div>

      <?php if (empty($rsvp_list)): ?>
      <div class="p-16 text-center">
        <i class="bi bi-people text-4xl text-gray-200 block mb-3"></i>
        <p class="font-body text-gray-400 text-sm">Belum ada data RSVP.</p>
      </div>
      <?php else: ?>
      <div class="overflow-x-auto">
        <table class="admin-table w-full">
          <thead>
            <tr>
              <th>#</th><th>Nama Tamu</th><th>WhatsApp</th>
              <th>Undangan</th><th>Kehadiran</th>
              <th>Jml Tamu</th><th>Ucapan</th><th>Waktu</th><th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($rsvp_list as $i => $r): ?>
            <tr>
              <td class="text-gray-400 text-xs"><?= $i+1 ?></td>
              <td class="font-body font-medium text-sm text-charcoal"><?= clean($r['guest_name']) ?></td>
              <td>
                <?php if (!empty($r['guest_phone'])): ?>
                <a href="https://wa.me/<?= preg_replace('/[^0-9]/','', $r['guest_phone']) ?>"
                   target="_blank" class="font-body text-xs text-green-600 hover:underline">
                  <i class="bi bi-whatsapp me-1"></i><?= clean($r['guest_phone']) ?>
                </a>
                <?php else: ?><span class="text-gray-300 text-xs">—</span><?php endif; ?>
              </td>
              <td class="font-body text-xs text-gray-500">
                <?= clean($r['groom_name']) ?> &amp; <?= clean($r['bride_name']) ?>
              </td>
              <td>
                <?php
                $att = ['hadir'=>['badge-green','bi-check-circle','Hadir'],
                        'tidak_hadir'=>['badge-red','bi-x-circle','Tidak Hadir'],
                        'mungkin'=>['badge-yellow','bi-question-circle','Mungkin']];
                [$bc,$ic,$lbl] = $att[$r['attendance']] ?? ['badge-gray','bi-dash','—'];
                ?>
                <span class="badge <?= $bc ?>">
                  <i class="bi <?= $ic ?> me-1 text-[10px]"></i><?= $lbl ?>
                </span>
              </td>
              <td class="text-center font-body text-sm font-medium text-charcoal">
                <?= $r['total_guests'] ?>
              </td>
              <td class="font-body text-xs text-gray-500 max-w-[200px]">
                <?php if (!empty($r['message'])): ?>
                <span class="line-clamp-2"><?= clean($r['message']) ?></span>
                <?php else: ?><span class="text-gray-300">—</span><?php endif; ?>
              </td>
              <td class="font-body text-xs text-gray-400">
                <?= date('d M Y', strtotime($r['created_at'])) ?><br>
                <span class="text-gray-300"><?= date('H:i', strtotime($r['created_at'])) ?></span>
              </td>
              <td>
                <form method="POST">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id"     value="<?= $r['id'] ?>">
                  <?php if ($wedding_id): ?>
                  <input type="hidden" name="wedding_id" value="<?= $wedding_id ?>">
                  <?php endif; ?>
                  <button type="submit" class="btn-action btn-delete"
                          onclick="return confirm('Hapus data RSVP ini?')"
                          title="Hapus">
                    <i class="bi bi-trash3"></i>
                  </button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>

  </div>
</main>
</div>
</body></html>