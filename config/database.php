<?php
// ============================================================
// FILE: config/database.php
// Koneksi database MySQL menggunakan MySQLi
// Kompatibel 100% dengan XAMPP (PHP 7.x / 8.x)
// ============================================================

// ── Konfigurasi ──────────────────────────────────────────────
define('DB_HOST',    'localhost');
define('DB_USER',    'root');       // default XAMPP: root
define('DB_PASS',    '');           // default XAMPP: kosong (tidak ada password)
define('DB_NAME',    'wedding_invitation');
define('DB_CHARSET', 'utf8mb4');

// Sesuaikan BASE_URL dengan nama folder kamu di htdocs
define('BASE_URL',  'http://localhost/wedding-invitation');
define('BASE_PATH', dirname(__DIR__)); // path absolut root project

// ── Koneksi MySQLi ────────────────────────────────────────────
/**
 * Membuat dan mengembalikan koneksi MySQLi (singleton)
 * Dipanggil setiap kali butuh query ke database
 */
function getDB(): mysqli {
    static $conn = null;

    if ($conn === null) {
        // Buat koneksi baru
        $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        // Cek koneksi berhasil
        if ($conn->connect_error) {
            die('
                <div style="font-family:sans-serif;padding:30px;background:#fff3cd;border:1px solid #ffc107;margin:20px;border-radius:4px;">
                    <h3 style="color:#856404;">&#10060; Koneksi Database Gagal</h3>
                    <p style="color:#533f03;">Error: ' . $conn->connect_error . '</p>
                    <p style="color:#533f03;">Pastikan:</p>
                    <ul style="color:#533f03;">
                        <li>XAMPP sudah dibuka dan MySQL sudah <strong>Start</strong></li>
                        <li>Nama database <strong>' . DB_NAME . '</strong> sudah dibuat di phpMyAdmin</li>
                        <li>Username: <strong>' . DB_USER . '</strong> dan password sesuai</li>
                    </ul>
                </div>
            ');
        }

        // Set charset agar mendukung emoji & karakter unicode
        $conn->set_charset(DB_CHARSET);
    }

    return $conn;
}

// ── Fungsi Query Helper ───────────────────────────────────────

/**
 * Ambil SATU baris data dari database
 *
 * Contoh penggunaan:
 *   $admin = db_row("SELECT * FROM admin WHERE username = ?", [$username], 's');
 *   echo $admin['username'];
 *
 * @param string $sql    Query SQL dengan placeholder ?
 * @param array  $params Array nilai untuk mengganti placeholder
 * @param string $types  Tipe data: s=string, i=integer, d=double, b=blob
 * @return array|null    Satu baris data, atau null jika tidak ditemukan
 */
function db_row(string $sql, array $params = [], string $types = ''): ?array {
    $db   = getDB();
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        error_log("db_row prepare error: " . $db->error . " | SQL: $sql");
        return null;
    }

    // Bind parameter jika ada
    if (!empty($params)) {
        if (empty($types)) {
            $types = _auto_types($params);
        }
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result ? $result->fetch_assoc() : null;
    $stmt->close();

    return $row ?: null;
}

/**
 * Ambil SEMUA baris data dari database
 *
 * Contoh penggunaan:
 *   $all = db_rows("SELECT * FROM weddings WHERE is_published = ?", [1], 'i');
 *   foreach ($all as $row) { echo $row['groom_name']; }
 *
 * @return array Array dari semua baris, atau array kosong []
 */
function db_rows(string $sql, array $params = [], string $types = ''): array {
    $db   = getDB();
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        error_log("db_rows prepare error: " . $db->error . " | SQL: $sql");
        return [];
    }

    if (!empty($params)) {
        if (empty($types)) {
            $types = _auto_types($params);
        }
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $rows   = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
    $stmt->close();

    return $rows;
}

/**
 * Jalankan query INSERT / UPDATE / DELETE
 * Mengembalikan jumlah baris yang terpengaruh
 *
 * Contoh:
 *   db_execute("UPDATE weddings SET views = views + 1 WHERE id = ?", [5], 'i');
 *
 * @return int Jumlah baris yang berubah
 */
function db_execute(string $sql, array $params = [], string $types = ''): int {
    $db   = getDB();
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        error_log("db_execute prepare error: " . $db->error . " | SQL: $sql");
        return 0;
    }

    if (!empty($params)) {
        if (empty($types)) {
            $types = _auto_types($params);
        }
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $affected = $stmt->affected_rows;
    $stmt->close();

    return $affected;
}

/**
 * Jalankan INSERT dan kembalikan ID baris baru (auto_increment)
 *
 * Contoh:
 *   $id = db_insert("INSERT INTO rsvp (wedding_id, guest_name) VALUES (?, ?)", [1, 'Budi'], 'is');
 *
 * @return int ID baris baru, 0 jika gagal
 */
function db_insert(string $sql, array $params = [], string $types = ''): int {
    $db   = getDB();
    $stmt = $db->prepare($sql);

    if (!$stmt) {
        error_log("db_insert prepare error: " . $db->error . " | SQL: $sql");
        return 0;
    }

    if (!empty($params)) {
        if (empty($types)) {
            $types = _auto_types($params);
        }
        $stmt->bind_param($types, ...$params);
    }

    $stmt->execute();
    $new_id = (int) $db->insert_id;
    $stmt->close();

    return $new_id;
}

/**
 * Helper internal: auto-detect tipe parameter untuk bind_param
 * i = integer, d = float/double, s = string (default)
 */
function _auto_types(array $params): string {
    $types = '';
    foreach ($params as $p) {
        if (is_int($p))        $types .= 'i';
        elseif (is_float($p))  $types .= 'd';
        else                   $types .= 's';
    }
    return $types;
}

// ── Fungsi Umum / Utilitas ────────────────────────────────────

/**
 * Bersihkan input dari user untuk mencegah XSS
 * Selalu gunakan ini sebelum menampilkan data ke HTML!
 *
 * Contoh: echo clean($nama_tamu);
 */
function clean(string $input): string {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Redirect ke halaman lain
 * Contoh: redirect(BASE_URL . '/admin/login.php');
 */
function redirect(string $url): void {
    header("Location: " . $url);
    exit;
}

/**
 * Cek apakah admin sudah login
 */
function isAdminLoggedIn(): bool {
    return isset($_SESSION['admin_id']) && !empty($_SESSION['admin_id']);
}

/**
 * Paksa login — jika belum masuk, redirect ke halaman login
 * Taruh di baris pertama halaman admin
 */
function requireLogin(): void {
    if (!isAdminLoggedIn()) {
        redirect(BASE_URL . '/admin/login.php');
    }
}

// ── Start Session ─────────────────────────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}