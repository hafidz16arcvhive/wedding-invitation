<?php
// ============================================================
// FILE: admin/weddings.php
// Halaman daftar semua undangan (bagian READ dari CRUD)
// ============================================================

require_once '../config/database.php';
requireLogin(); // Cek login — jika belum, redirect ke login.php

// ── Ambil flash message dari session ─────────────────────────
// Flash message = pesan sementara yang muncul sekali setelah redirect
// Contoh: setelah hapus berhasil, kita simpan pesan ke session,
// lalu redirect ke halaman ini, dan tampilkan pesannya.
$flash = '';
$flash_type = '';
if (!empty($_SESSION['flash_message'])) {
    $flash      = $_SESSION['flash_message'];
    $flash_type = $_SESSION['flash_type'] ?? 'success';
    // Hapus dari session agar tidak muncul lagi saat refresh
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// ── Ambil semua data undangan dari database ───────────────────
// LEFT JOIN: gabungkan tabel weddings dengan invitation_templates
// Kenapa LEFT JOIN? Agar undangan yang belum punya template tetap muncul
$weddings = db_rows("
    SELECT
        w.id,
        w.slug,
        w.groom_name,
        w.bride_name,
        w.reception_date,
        w.views,
        w.is_published,
        w.is_active,
        w.created_at,
        t.name AS template_name
    FROM weddings w
    LEFT JOIN invitation_templates t ON w.template_id = t.id
    ORDER BY w.created_at DESC
");

// ── Hitung total untuk ringkasan ──────────────────────────────
$total_all       = count($weddings);
$total_published = count(array_filter($weddings, fn($w) => $w['is_published'] == 1));
$total_draft     = $total_all - $total_published;

// Set judul halaman (dipakai oleh _head.php)
$page_title = 'Kelola Undangan';
?>

<?php include '_head.php'; ?>
<body class="bg-gray-50">
<div class="flex min-h-screen">

    <?php include '_sidebar.php'; ?>

    <!-- ── KONTEN UTAMA ── -->
    <main class="flex-1 overflow-auto">

        <!-- Top Bar -->
        <header class="bg-white border-b border-gray-100 px-8 py-4 sticky top-0 z-10
                        flex items-center justify-between">
            <div>
                <h1 class="section-title">Kelola Undangan</h1>
                <p class="font-body text-gray-400 text-xs mt-0.5">
                    Total <?= $total_all ?> undangan
                    · <?= $total_published ?> aktif
                    · <?= $total_draft ?> draft
                </p>
            </div>
            <!-- Tombol tambah undangan baru -->
            <a href="wedding_add.php" class="btn-primary">
                <i class="bi bi-plus-lg"></i>
                TAMBAH UNDANGAN
            </a>
        </header>

        <div class="p-8">

            <!-- ── Flash Message ── -->
            <!-- Tampil jika ada pesan dari aksi sebelumnya (tambah/edit/hapus) -->
            <?php if ($flash): ?>
            <div class="alert-<?= $flash_type ?> px-4 py-3 mb-6 flex items-center gap-2 font-body text-sm">
                <i class="bi <?= $flash_type === 'success' ? 'bi-check-circle-fill' : 'bi-exclamation-triangle-fill' ?>"></i>
                <?= clean($flash) ?>
            </div>
            <?php endif; ?>

            <!-- ── Tabel Undangan ── -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <span class="section-title text-lg">Daftar Semua Undangan</span>
                    <!-- Filter/Search bisa ditambahkan di sini nanti -->
                </div>

                <?php if (empty($weddings)): ?>
                <!-- Tampilkan pesan jika belum ada data -->
                <div class="p-16 text-center">
                    <i class="bi bi-heart text-4xl text-gray-200 block mb-3"></i>
                    <p class="font-body text-gray-400 text-sm">Belum ada undangan.</p>
                    <a href="wedding_add.php" class="btn-primary mt-4 inline-flex">
                        <i class="bi bi-plus-lg"></i> TAMBAH SEKARANG
                    </a>
                </div>

                <?php else: ?>
                <!-- Tabel data -->
                <div class="overflow-x-auto">
                    <table class="admin-table w-full">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Pasangan</th>
                                <th>Slug / URL</th>
                                <th>Template</th>
                                <th>Tanggal Resepsi</th>
                                <th>Views</th>
                                <th>Status</th>
                                <th>Dibuat</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($weddings as $i => $w): ?>
                            <tr>
                                <!-- Nomor urut -->
                                <td class="text-gray-400 text-xs"><?= $i + 1 ?></td>

                                <!-- Nama pasangan -->
                                <td>
                                    <div class="font-body font-medium text-charcoal">
                                        <?= clean($w['groom_name']) ?>
                                        <span class="text-gold mx-1">♡</span>
                                        <?= clean($w['bride_name']) ?>
                                    </div>
                                </td>

                                <!-- Slug / link undangan -->
                                <td>
                                    <code class="text-xs bg-gray-100 px-2 py-0.5 rounded text-gray-600">
                                        /invitation/<?= clean($w['slug']) ?>
                                    </code>
                                </td>

                                <!-- Nama template -->
                                <td class="font-body text-sm text-gray-500">
                                    <?= clean($w['template_name'] ?? '—') ?>
                                </td>

                                <!-- Tanggal resepsi -->
                                <td class="font-body text-sm text-gray-600">
                                    <?php if ($w['reception_date']): ?>
                                        <?= date('d M Y', strtotime($w['reception_date'])) ?>
                                    <?php else: ?>
                                        <span class="text-gray-300">—</span>
                                    <?php endif; ?>
                                </td>

                                <!-- Jumlah views -->
                                <td class="text-center">
                                    <span class="font-body text-sm text-gray-500">
                                        <?= number_format($w['views']) ?>
                                        <i class="bi bi-eye text-gray-300 text-xs ms-1"></i>
                                    </span>
                                </td>

                                <!-- Badge status (Published / Draft) -->
                                <td>
                                    <?php if ($w['is_published']): ?>
                                        <span class="badge badge-green">
                                            <i class="bi bi-circle-fill text-[6px] me-1"></i> Aktif
                                        </span>
                                    <?php else: ?>
                                        <span class="badge badge-gray">
                                            <i class="bi bi-circle text-[6px] me-1"></i> Draft
                                        </span>
                                    <?php endif; ?>
                                </td>

                                <!-- Tanggal dibuat -->
                                <td class="font-body text-xs text-gray-400">
                                    <?= date('d M Y', strtotime($w['created_at'])) ?>
                                </td>

                                <!-- Tombol Aksi -->
                                <td>
                                    <div class="flex items-center gap-1">
                                        <!-- Preview undangan di tab baru -->
                                        <a href="<?= BASE_URL ?>/invitation/<?= clean($w['slug']) ?>"
                                           target="_blank"
                                           class="btn-action btn-view"
                                           title="Preview Undangan">
                                            <i class="bi bi-eye"></i>
                                        </a>

                                        <!-- Edit undangan -->
                                        <a href="wedding_edit.php?id=<?= $w['id'] ?>"
                                           class="btn-action btn-edit"
                                           title="Edit Undangan">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <!-- Hapus undangan (dengan konfirmasi) -->
                                        <button onclick="confirmDelete(<?= $w['id'] ?>, '<?= clean($w['groom_name']) ?> & <?= clean($w['bride_name']) ?>')"
                                                class="btn-action btn-delete"
                                                title="Hapus Undangan">
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

<!-- ── Modal Konfirmasi Hapus ── -->
<!-- Muncul ketika tombol hapus diklik, meminta konfirmasi sebelum benar-benar hapus -->
<div id="delete-modal"
     class="fixed inset-0 z-50 hidden items-center justify-center"
     style="background:rgba(0,0,0,0.5);">
    <div class="bg-white p-8 max-w-sm w-full mx-4 shadow-xl">
        <div class="text-center mb-6">
            <div class="w-14 h-14 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="bi bi-trash3-fill text-red-500 text-2xl"></i>
            </div>
            <h3 class="font-display text-xl text-charcoal mb-2">Hapus Undangan?</h3>
            <p class="font-body text-gray-500 text-sm" id="delete-modal-name"></p>
            <p class="font-body text-red-400 text-xs mt-2">
                ⚠️ Data RSVP dan ucapan juga akan ikut terhapus!
            </p>
        </div>
        <div class="flex gap-3">
            <!-- Tombol batal -->
            <button onclick="closeDeleteModal()"
                    class="btn-secondary flex-1 justify-center">
                BATAL
            </button>
            <!-- Form hapus — submit ke wedding_delete.php -->
            <form method="POST" action="wedding_delete.php" class="flex-1">
                <input type="hidden" name="id" id="delete-id">
                <button type="submit"
                        class="w-full bg-red-500 hover:bg-red-600 text-white py-2.5 font-body
                               font-medium text-xs tracking-widest transition-colors flex items-center
                               justify-center gap-2">
                    <i class="bi bi-trash3"></i> YA, HAPUS
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// ── Fungsi untuk tampilkan modal konfirmasi hapus ──
function confirmDelete(id, name) {
    // Isi ID dan nama ke dalam modal
    document.getElementById('delete-id').value     = id;
    document.getElementById('delete-modal-name').textContent =
        'Undangan "' + name + '" akan dihapus permanen.';

    // Tampilkan modal
    const modal = document.getElementById('delete-modal');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

// ── Fungsi untuk tutup modal ──
function closeDeleteModal() {
    const modal = document.getElementById('delete-modal');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

// Tutup modal jika klik di luar kotak
document.getElementById('delete-modal').addEventListener('click', function(e) {
    if (e.target === this) closeDeleteModal();
});
</script>

</body>
</html>
