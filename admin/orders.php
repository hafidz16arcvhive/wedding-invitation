<?php
// ============================================================
// FILE: admin/orders.php
// Kelola pesanan undangan masuk dari pengunjung
// ============================================================
require_once '../config/database.php';
requireLogin();

// ── Flash message ─────────────────────────────────────────────
$flash = '';
$flash_type = '';
if (!empty($_SESSION['flash_message'])) {
    $flash      = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'] ?? 'success';
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// ── Proses update status order (via POST) ─────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action   = $_POST['action'] ?? '';
    $order_id = (int)($_POST['order_id'] ?? 0);

    // Update status pesanan
    if ($action === 'update_status' && $order_id > 0) {
        $new_status = $_POST['status'] ?? '';
        $allowed    = ['pending','confirmed','processing','completed','cancelled'];

        if (in_array($new_status, $allowed)) {
            db_execute("UPDATE orders SET status = ? WHERE id = ?", [$new_status, $order_id], 'si');
            $_SESSION['flash_message'] = 'Status pesanan berhasil diperbarui.';
            $_SESSION['flash_type']    = 'success';
        }
    }

    // Update status pembayaran
    if ($action === 'update_payment' && $order_id > 0) {
        $new_payment = $_POST['payment_status'] ?? '';
        $allowed     = ['unpaid','paid','refunded'];

        if (in_array($new_payment, $allowed)) {
            db_execute("UPDATE orders SET payment_status = ? WHERE id = ?", [$new_payment, $order_id], 'si');
            $_SESSION['flash_message'] = 'Status pembayaran berhasil diperbarui.';
            $_SESSION['flash_type']    = 'success';
        }
    }

    // Hapus pesanan
    if ($action === 'delete' && $order_id > 0) {
        db_execute("DELETE FROM orders WHERE id = ?", [$order_id], 'i');
        $_SESSION['flash_message'] = 'Pesanan berhasil dihapus.';
        $_SESSION['flash_type']    = 'success';
    }

    redirect(BASE_URL . '/admin/orders.php');
}

// ── Filter & Search ───────────────────────────────────────────
$filter_status  = clean($_GET['status'] ?? '');
$filter_payment = clean($_GET['payment'] ?? '');
$search         = clean($_GET['q'] ?? '');

// Bangun query dinamis berdasarkan filter
$where  = [];
$params = [];
$types  = '';

if (!empty($filter_status)) {
    $where[]  = "o.status = ?";
    $params[] = $filter_status;
    $types   .= 's';
}
if (!empty($filter_payment)) {
    $where[]  = "o.payment_status = ?";
    $params[] = $filter_payment;
    $types   .= 's';
}
if (!empty($search)) {
    $where[]  = "(o.customer_name LIKE ? OR o.order_number LIKE ? OR o.customer_phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types   .= 'sss';
}

$where_sql = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

// Ambil semua order
$orders = db_rows("
    SELECT o.*, t.name AS template_name
    FROM orders o
    LEFT JOIN invitation_templates t ON o.template_id = t.id
    {$where_sql}
    ORDER BY o.created_at DESC
", $params, $types);

// Hitung statistik ringkasan
$stats = db_row("
    SELECT
        COUNT(*) AS total,
        SUM(CASE WHEN status='pending'    THEN 1 ELSE 0 END) AS pending,
        SUM(CASE WHEN status='completed'  THEN 1 ELSE 0 END) AS completed,
        SUM(CASE WHEN payment_status='paid' THEN total_price ELSE 0 END) AS revenue
    FROM orders
");

$page_title = 'Kelola Pesanan';
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
        <h1 class="section-title">Kelola Pesanan</h1>
        <p class="font-body text-gray-400 text-xs mt-0.5">
          <?= count($orders) ?> pesanan ditemukan
        </p>
      </div>
    </header>

    <div class="p-8">

      <!-- Flash message -->
      <?php if ($flash): ?>
      <div class="alert-<?= $flash_type ?> px-4 py-3 mb-6 flex items-center gap-2 font-body text-sm">
        <i class="bi <?= $flash_type==='success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?>"></i>
        <?= clean($flash) ?>
      </div>
      <?php endif; ?>

      <!-- ── STATISTIK CARDS ── -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
        <?php
        $stat_cards = [
          ['label'=>'Total Pesanan',  'value'=> $stats['total'],     'icon'=>'bi-cart3',      'color'=>'text-blue-500',  'bg'=>'bg-blue-50'],
          ['label'=>'Menunggu',       'value'=> $stats['pending'],   'icon'=>'bi-clock',      'color'=>'text-yellow-500','bg'=>'bg-yellow-50'],
          ['label'=>'Selesai',        'value'=> $stats['completed'], 'icon'=>'bi-check-circle','color'=>'text-green-500', 'bg'=>'bg-green-50'],
          ['label'=>'Total Revenue',  'value'=>'Rp '.number_format($stats['revenue'],0,',','.'), 'icon'=>'bi-cash-coin','color'=>'text-gold','bg'=>'bg-yellow-50'],
        ];
        foreach ($stat_cards as $sc):
        ?>
        <div class="bg-white border border-gray-100 p-5">
          <div class="<?= $sc['bg'] ?> w-10 h-10 flex items-center justify-center mb-3">
            <i class="bi <?= $sc['icon'] ?> <?= $sc['color'] ?>"></i>
          </div>
          <div class="font-display text-2xl text-charcoal font-normal"><?= $sc['value'] ?></div>
          <div class="font-body text-gray-400 text-xs mt-0.5"><?= $sc['label'] ?></div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- ── FILTER & SEARCH ── -->
      <div class="admin-card mb-6">
        <div class="admin-card-body">
          <form method="GET" action="" class="flex flex-wrap gap-3 items-end">

            <!-- Search -->
            <div class="flex-1 min-w-48">
              <label class="form-label">Cari</label>
              <input type="text" name="q" value="<?= $search ?>"
                     class="form-control" placeholder="Nama / Nomor Order / WA...">
            </div>

            <!-- Filter status -->
            <div>
              <label class="form-label">Status Pesanan</label>
              <select name="status" class="form-control">
                <option value="">Semua Status</option>
                <?php foreach (['pending'=>'Menunggu','confirmed'=>'Dikonfirmasi',
                                'processing'=>'Diproses','completed'=>'Selesai',
                                'cancelled'=>'Dibatalkan'] as $val=>$lbl): ?>
                <option value="<?= $val ?>" <?= $filter_status===$val?'selected':'' ?>>
                  <?= $lbl ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

            <!-- Filter pembayaran -->
            <div>
              <label class="form-label">Pembayaran</label>
              <select name="payment" class="form-control">
                <option value="">Semua</option>
                <option value="unpaid"   <?= $filter_payment==='unpaid'  ?'selected':'' ?>>Belum Bayar</option>
                <option value="paid"     <?= $filter_payment==='paid'    ?'selected':'' ?>>Sudah Bayar</option>
                <option value="refunded" <?= $filter_payment==='refunded'?'selected':'' ?>>Refund</option>
              </select>
            </div>

            <button type="submit" class="btn-primary">
              <i class="bi bi-search"></i> CARI
            </button>

            <?php if ($search || $filter_status || $filter_payment): ?>
            <a href="orders.php" class="btn-secondary">
              <i class="bi bi-x"></i> RESET
            </a>
            <?php endif; ?>

          </form>
        </div>
      </div>

      <!-- ── TABEL PESANAN ── -->
      <div class="admin-card">
        <div class="admin-card-header">
          <span class="section-title text-lg">Daftar Pesanan</span>
        </div>

        <?php if (empty($orders)): ?>
        <div class="p-16 text-center">
          <i class="bi bi-cart-x text-4xl text-gray-200 block mb-3"></i>
          <p class="font-body text-gray-400 text-sm">Belum ada pesanan.</p>
        </div>

        <?php else: ?>
        <div class="overflow-x-auto">
          <table class="admin-table w-full">
            <thead>
              <tr>
                <th>No. Order</th>
                <th>Pemesan</th>
                <th>Paket</th>
                <th>Harga</th>
                <th>Status</th>
                <th>Pembayaran</th>
                <th>Tanggal</th>
                <th>Aksi</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orders as $o): ?>
              <tr>

                <!-- Nomor Order -->
                <td>
                  <div class="font-body font-medium text-xs text-charcoal">
                    <?= clean($o['order_number']) ?>
                  </div>
                  <div class="font-body text-[11px] text-gray-400 mt-0.5">
                    <?= clean($o['template_name'] ?? '—') ?>
                    · <?= ucfirst($o['package_type']) ?>
                  </div>
                </td>

                <!-- Pemesan -->
                <td>
                  <div class="font-body text-sm font-medium text-charcoal">
                    <?= clean($o['customer_name']) ?>
                  </div>
                  <div class="font-body text-xs text-gray-400 mt-0.5">
                    <?= clean($o['customer_phone']) ?>
                  </div>
                  <a href="mailto:<?= clean($o['customer_email']) ?>"
                     class="font-body text-xs text-gold hover:underline">
                    <?= clean($o['customer_email']) ?>
                  </a>
                </td>

                <!-- Paket -->
                <td>
                  <?php
                  $pkg_colors = ['basic'=>'badge-gray','premium'=>'badge-blue','luxury'=>'badge-yellow'];
                  $pkg_color  = $pkg_colors[$o['package_type']] ?? 'badge-gray';
                  ?>
                  <span class="badge <?= $pkg_color ?>">
                    <?= ucfirst($o['package_type']) ?>
                  </span>
                </td>

                <!-- Harga -->
                <td class="font-body text-sm font-medium text-charcoal">
                  Rp <?= number_format($o['total_price'],0,',','.') ?>
                </td>

                <!-- Status pesanan — bisa diubah langsung via dropdown -->
                <td>
                  <form method="POST" action="">
                    <input type="hidden" name="action"   value="update_status">
                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                    <?php
                    $status_colors = [
                      'pending'    => 'text-yellow-600 bg-yellow-50 border-yellow-200',
                      'confirmed'  => 'text-blue-600   bg-blue-50   border-blue-200',
                      'processing' => 'text-purple-600 bg-purple-50 border-purple-200',
                      'completed'  => 'text-green-600  bg-green-50  border-green-200',
                      'cancelled'  => 'text-red-600    bg-red-50    border-red-200',
                    ];
                    $sc = $status_colors[$o['status']] ?? 'text-gray-600 bg-gray-50 border-gray-200';
                    ?>
                    <select name="status" onchange="this.form.submit()"
                            class="font-body text-xs border px-2 py-1 <?= $sc ?> cursor-pointer">
                      <option value="pending"    <?= $o['status']==='pending'    ?'selected':'' ?>>Menunggu</option>
                      <option value="confirmed"  <?= $o['status']==='confirmed'  ?'selected':'' ?>>Dikonfirmasi</option>
                      <option value="processing" <?= $o['status']==='processing' ?'selected':'' ?>>Diproses</option>
                      <option value="completed"  <?= $o['status']==='completed'  ?'selected':'' ?>>Selesai</option>
                      <option value="cancelled"  <?= $o['status']==='cancelled'  ?'selected':'' ?>>Dibatalkan</option>
                    </select>
                  </form>
                </td>

                <!-- Status pembayaran -->
                <td>
                  <form method="POST" action="">
                    <input type="hidden" name="action"   value="update_payment">
                    <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                    <?php
                    $pay_colors = [
                      'unpaid'   => 'text-red-600    bg-red-50    border-red-200',
                      'paid'     => 'text-green-600  bg-green-50  border-green-200',
                      'refunded' => 'text-gray-600   bg-gray-50   border-gray-200',
                    ];
                    $pc = $pay_colors[$o['payment_status']] ?? 'text-gray-600 bg-gray-50 border-gray-200';
                    ?>
                    <select name="payment_status" onchange="this.form.submit()"
                            class="font-body text-xs border px-2 py-1 <?= $pc ?> cursor-pointer">
                      <option value="unpaid"   <?= $o['payment_status']==='unpaid'   ?'selected':'' ?>>Belum Bayar</option>
                      <option value="paid"     <?= $o['payment_status']==='paid'     ?'selected':'' ?>>Sudah Bayar</option>
                      <option value="refunded" <?= $o['payment_status']==='refunded' ?'selected':'' ?>>Refund</option>
                    </select>
                  </form>
                </td>

                <!-- Tanggal order -->
                <td class="font-body text-xs text-gray-400">
                  <?= date('d M Y', strtotime($o['created_at'])) ?><br>
                  <span class="text-gray-300"><?= date('H:i', strtotime($o['created_at'])) ?></span>
                </td>

                <!-- Aksi -->
                <td>
                  <div class="flex items-center gap-1">
                    <!-- Lihat detail / notes -->
                    <?php if (!empty($o['notes'])): ?>
                    <button onclick="showNotes(<?= $o['id'] ?>, `<?= addslashes(nl2br(clean($o['notes']))) ?>`)"
                            class="btn-action btn-view" title="Lihat Catatan">
                      <i class="bi bi-eye"></i>
                    </button>
                    <?php endif; ?>

                    <!-- WA pemesan -->
                    <a href="https://wa.me/<?= preg_replace('/[^0-9]/', '', $o['customer_phone']) ?>"
                       target="_blank"
                       class="btn-action" style="background:#f0fdf4;color:#16a34a;border:1px solid #bbf7d0;"
                       title="WhatsApp Pemesan">
                      <i class="bi bi-whatsapp"></i>
                    </a>

                    <!-- Hapus -->
                    <button onclick="confirmDeleteOrder(<?= $o['id'] ?>, '<?= clean($o['order_number']) ?>')"
                            class="btn-action btn-delete" title="Hapus">
                      <i class="bi bi-trash3"></i>
                    </button>
                  </div>
                </td>

              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </div>

    </div><!-- /p-8 -->
  </main>
</div>

<!-- ── Modal Catatan ── -->
<div id="notes-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background:rgba(0,0,0,0.5);">
  <div class="bg-white p-6 max-w-md w-full mx-4 shadow-xl">
    <div class="flex items-center justify-between mb-4">
      <h3 class="font-display text-xl text-charcoal">Catatan Pesanan</h3>
      <button onclick="closeNotesModal()" class="text-gray-400 hover:text-charcoal">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>
    <div id="notes-content"
         class="font-body text-sm text-gray-600 leading-relaxed bg-gray-50 p-4">
    </div>
    <div class="mt-4 text-right">
      <button onclick="closeNotesModal()" class="btn-secondary">TUTUP</button>
    </div>
  </div>
</div>

<!-- ── Modal Hapus ── -->
<div id="delete-order-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background:rgba(0,0,0,0.5);">
  <div class="bg-white p-8 max-w-sm w-full mx-4 shadow-xl text-center">
    <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
      <i class="bi bi-trash3-fill text-red-500 text-xl"></i>
    </div>
    <h3 class="font-display text-xl text-charcoal mb-2">Hapus Pesanan?</h3>
    <p class="font-body text-gray-500 text-sm mb-6" id="delete-order-num"></p>
    <div class="flex gap-3">
      <button onclick="closeDeleteOrder()" class="btn-secondary flex-1 justify-center">BATAL</button>
      <form method="POST" action="" class="flex-1">
        <input type="hidden" name="action"   value="delete">
        <input type="hidden" name="order_id" id="delete-order-id">
        <button type="submit"
                class="w-full bg-red-500 hover:bg-red-600 text-white py-2.5 font-body
                       text-xs tracking-widest transition-colors flex items-center
                       justify-center gap-2">
          <i class="bi bi-trash3"></i> HAPUS
        </button>
      </form>
    </div>
  </div>
</div>

<script>
function showNotes(id, notes) {
  document.getElementById('notes-content').innerHTML = notes;
  const m = document.getElementById('notes-modal');
  m.classList.remove('hidden');
  m.classList.add('flex');
}
function closeNotesModal() {
  const m = document.getElementById('notes-modal');
  m.classList.add('hidden');
  m.classList.remove('flex');
}

function confirmDeleteOrder(id, num) {
  document.getElementById('delete-order-id').value        = id;
  document.getElementById('delete-order-num').textContent = 'Pesanan ' + num + ' akan dihapus permanen.';
  const m = document.getElementById('delete-order-modal');
  m.classList.remove('hidden');
  m.classList.add('flex');
}
function closeDeleteOrder() {
  const m = document.getElementById('delete-order-modal');
  m.classList.add('hidden');
  m.classList.remove('flex');
}
</script>

</body>
</html>