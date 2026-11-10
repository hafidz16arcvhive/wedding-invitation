<?php
// ============================================================
// FILE: admin/_sidebar.php
// Sidebar navigasi admin — di-include oleh semua halaman admin
// Cara pakai: include '_sidebar.php'; di dalam layout flex
// ============================================================

// Deteksi halaman aktif berdasarkan nama file saat ini
$current_page = basename($_SERVER['PHP_SELF']);

// Daftar menu navigasi sidebar
$nav_items = [
    ['href' => 'dashboard.php',   'icon' => 'bi-grid-1x2',   'label' => 'Dashboard'],
    ['href' => 'weddings.php',    'icon' => 'bi-heart',       'label' => 'Undangan'],
    ['href' => 'orders.php',      'icon' => 'bi-cart3',       'label' => 'Pesanan'],
    ['href' => 'rsvp.php',        'icon' => 'bi-people',      'label' => 'RSVP'],
    ['href' => 'messages.php',    'icon' => 'bi-chat-dots',   'label' => 'Ucapan'],
    ['href' => 'templates.php',   'icon' => 'bi-palette',     'label' => 'Template'],
    ['href' => 'settings.php',    'icon' => 'bi-gear',        'label' => 'Pengaturan'],
];
?>

<aside class="w-64 bg-[#1C1C1E] flex-shrink-0 flex flex-col min-h-screen sticky top-0">

    <!-- ── Logo ── -->
    <div class="p-6 border-b border-white/5">
        <a href="dashboard.php" class="font-display text-2xl text-gold font-light block">
            ♡ LoveStory
        </a>
        <p class="font-body text-gray-600 text-xs mt-1 tracking-wider">Admin Panel</p>
    </div>

    <!-- ── Menu Navigasi ── -->
    <nav class="p-4 flex-1 space-y-1">
        <?php foreach ($nav_items as $item):
            // Tandai menu aktif jika nama file cocok
            $is_active = ($current_page === $item['href']);
            $active_class = $is_active
                ? 'bg-gold/10 border-l-2 border-gold text-gold'
                : 'text-gray-400 hover:bg-white/5 hover:text-gold border-l-2 border-transparent';
        ?>
        <a href="<?= $item['href'] ?>"
           class="flex items-center gap-3 px-4 py-3 text-sm font-body transition-all duration-200 <?= $active_class ?>">
            <i class="bi <?= $item['icon'] ?> text-base w-4 text-center"></i>
            <span><?= $item['label'] ?></span>
        </a>
        <?php endforeach; ?>
    </nav>

    <!-- ── Info Admin & Logout ── -->
    <div class="p-4 border-t border-white/5">
        <!-- Nama admin yang login -->
        <div class="flex items-center gap-3 px-4 py-2 mb-1">
            <div class="w-8 h-8 rounded-full bg-gold/20 flex items-center justify-center">
                <i class="bi bi-person-fill text-gold text-sm"></i>
            </div>
            <div>
                <div class="font-body text-white text-sm">
                    <?= clean($_SESSION['admin_name'] ?? 'Admin') ?>
                </div>
                <div class="font-body text-gray-600 text-xs">Administrator</div>
            </div>
        </div>

        <!-- Tombol logout -->
        <a href="logout.php"
           class="flex items-center gap-3 px-4 py-2 text-gray-600 hover:text-red-400 font-body text-sm transition-colors duration-200 mt-1">
            <i class="bi bi-box-arrow-right text-base"></i>
            <span>Logout</span>
        </a>
    </div>
</aside>
