<?php
// ============================================================
// FILE: admin/login.php
// Halaman login untuk admin
// ============================================================
require_once '../config/database.php';

$error = '';

// Jika sudah login, langsung ke dashboard
if (isAdminLoggedIn()) {
    redirect(BASE_URL . '/admin/dashboard.php');
}

// Proses form login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = clean($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($username) || empty($password)) {
        $error = 'Username dan password wajib diisi.';
    } else {
        // Cari admin berdasarkan username
        $admin = db_row("SELECT * FROM admin WHERE username = ?", [$username]);

        // Verifikasi password dengan bcrypt
        if ($admin && password_verify($password, $admin['password'])) {
            // Login berhasil — simpan ke session
            $_SESSION['admin_id']   = $admin['id'];
            $_SESSION['admin_name'] = $admin['username'];
            redirect(BASE_URL . '/admin/dashboard.php');
        } else {
            $error = 'Username atau password salah.';
            // Tambah sedikit delay untuk mencegah brute-force
            sleep(1);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Admin Login — LoveStory Invitation</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: { extend: {
        colors: { gold: { DEFAULT:'#C9A84C', dark:'#9A7B2F' }, charcoal:'#2C2C2C' },
        fontFamily: { display:['"Cormorant Garamond"','serif'], body:['Jost','sans-serif'] }
      }}
    }
  </script>
  <style>
    body { background: #2C2C2C; }
    @keyframes fadeIn { from{opacity:0;transform:translateY(20px)} to{opacity:1;transform:translateY(0)} }
    .login-box { animation: fadeIn 0.8s ease forwards; }
    .input-field { background: rgba(255,255,255,0.05); border: 1px solid rgba(201,168,76,0.2); transition: border-color 0.3s; }
    .input-field:focus { border-color: #C9A84C; outline: none; }
  </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

  <!-- Background decoration -->
  <div class="fixed inset-0 pointer-events-none overflow-hidden opacity-10">
    <div class="absolute top-1/4 left-1/4 w-96 h-96 rounded-full border border-gold"></div>
    <div class="absolute bottom-1/4 right-1/4 w-64 h-64 rounded-full border border-gold"></div>
  </div>

  <div class="login-box w-full max-w-md relative z-10">

    <!-- Header -->
    <div class="text-center mb-10">
      <a href="../index.php" class="font-display text-4xl text-gold font-light block mb-2">♡ LoveStory</a>
      <p class="font-body text-gray-500 text-xs tracking-[0.3em] uppercase">Admin Panel</p>
      <div class="w-16 h-px bg-gold/40 mx-auto mt-4"></div>
    </div>

    <!-- Login Form -->
    <div class="border border-gold/20 p-8">
      <h2 class="font-display text-2xl text-white font-light mb-6 text-center">Masuk ke Dashboard</h2>

      <?php if ($error): ?>
      <div class="bg-red-900/30 border border-red-500/30 text-red-300 font-body text-sm px-4 py-3 mb-5 flex items-center gap-2">
        <i class="bi bi-exclamation-triangle"></i>
        <?= $error ?>
      </div>
      <?php endif; ?>

      <form method="POST" action="">
        <!-- Username -->
        <div class="mb-5">
          <label class="font-body text-gray-400 text-xs tracking-widest uppercase block mb-2">
            Username
          </label>
          <div class="relative">
            <i class="bi bi-person absolute left-3 top-1/2 -translate-y-1/2 text-gold/50 text-sm"></i>
            <input type="text" name="username"
                   value="<?= clean($_POST['username'] ?? '') ?>"
                   placeholder="Masukkan username"
                   class="input-field w-full pl-10 pr-4 py-3 text-white font-body text-sm"
                   required autocomplete="username">
          </div>
        </div>

        <!-- Password -->
        <div class="mb-6">
          <label class="font-body text-gray-400 text-xs tracking-widest uppercase block mb-2">
            Password
          </label>
          <div class="relative">
            <i class="bi bi-lock absolute left-3 top-1/2 -translate-y-1/2 text-gold/50 text-sm"></i>
            <input type="password" id="passwordInput" name="password"
                   placeholder="Masukkan password"
                   class="input-field w-full pl-10 pr-10 py-3 text-white font-body text-sm"
                   required autocomplete="current-password">
            <button type="button" onclick="togglePassword()"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gold/50 hover:text-gold transition-colors">
              <i class="bi bi-eye-slash" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <!-- Submit -->
        <button type="submit"
                class="w-full bg-gold hover:bg-gold-dark text-charcoal font-body font-medium tracking-widest text-sm py-3 transition-all duration-300 flex items-center justify-center gap-2">
          <i class="bi bi-box-arrow-in-right"></i>
          MASUK
        </button>
      </form>
    </div>

    <!-- Back to home -->
    <div class="text-center mt-6">
      <a href="../index.php" class="font-body text-gray-600 hover:text-gold text-xs tracking-widest transition-colors">
        <i class="bi bi-arrow-left me-1"></i>KEMBALI KE WEBSITE
      </a>
    </div>

    <!-- Info default credentials (hapus di production!) -->
    <div class="mt-4 border border-yellow-900/30 bg-yellow-900/10 p-3 text-center">
      <p class="text-yellow-600 font-body text-xs">
        <i class="bi bi-info-circle me-1"></i>
        Default: username <strong>admin</strong> / password <strong>password</strong><br>
        <span class="opacity-70">Hapus pesan ini setelah ganti password!</span>
      </p>
    </div>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('passwordInput');
      const icon  = document.getElementById('eyeIcon');
      if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye';
      } else {
        input.type = 'password';
        icon.className = 'bi bi-eye-slash';
      }
    }
  </script>
</body>
</html>
