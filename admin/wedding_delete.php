<?php
// ============================================================
// FILE: admin/wedding_delete.php
// Proses hapus undangan (bagian DELETE dari CRUD)
//
// Alur kerja:
// 1. Cek login admin
// 2. Terima ID dari form POST (dikirim dari modal di weddings.php)
// 3. Cek ID valid & data ada di database
// 4. Hapus dari database (CASCADE otomatis hapus RSVP & messages)
// 5. Simpan flash message ke session
// 6. Redirect ke weddings.php
// ============================================================

require_once '../config/database.php';
requireLogin();

// ── Hanya terima request POST ─────────────────────────────────
// Ini penting untuk keamanan — tidak boleh hapus via URL langsung
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect(BASE_URL . '/admin/weddings.php');
}

// ── Ambil & validasi ID ───────────────────────────────────────
$id = (int) ($_POST['id'] ?? 0); // Cast ke integer, 0 jika tidak ada

if ($id <= 0) {
    $_SESSION['flash_message'] = 'ID tidak valid.';
    $_SESSION['flash_type']    = 'error';
    redirect(BASE_URL . '/admin/weddings.php');
}

// ── Cek apakah undangan dengan ID ini ada ────────────────────
$wedding = db_row("SELECT id, groom_name, bride_name FROM weddings WHERE id = ?", [$id], 'i');

if (!$wedding) {
    $_SESSION['flash_message'] = 'Undangan tidak ditemukan.';
    $_SESSION['flash_type']    = 'error';
    redirect(BASE_URL . '/admin/weddings.php');
}

// ── Hapus dari database ───────────────────────────────────────
// Catatan: tabel rsvp & messages sudah pakai ON DELETE CASCADE di SQL,
// jadi otomatis ikut terhapus ketika wedding dihapus.
$affected = db_execute("DELETE FROM weddings WHERE id = ?", [$id], 'i');

// ── Set flash message & redirect ─────────────────────────────
if ($affected > 0) {
    $_SESSION['flash_message'] = 'Undangan ' . $wedding['groom_name'] . ' & ' . $wedding['bride_name'] . ' berhasil dihapus.';
    $_SESSION['flash_type']    = 'success';
} else {
    $_SESSION['flash_message'] = 'Gagal menghapus undangan. Coba lagi.';
    $_SESSION['flash_type']    = 'error';
}

redirect(BASE_URL . '/admin/weddings.php');
