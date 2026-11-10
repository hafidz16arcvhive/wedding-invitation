<?php
// ============================================================
// FILE: admin/_head.php
// Tag <head> HTML yang dipakai semua halaman admin
// Cara pakai: include '_head.php'; setelah <!DOCTYPE html>
//
// Variabel opsional yang bisa diset SEBELUM include:
//   $page_title = 'Judul Halaman';  → ditampilkan di <title>
// ============================================================

$page_title = $page_title ?? 'Admin Panel';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title><?= clean($page_title) ?> — LoveStory Admin</title>

    <!-- Google Fonts: Cormorant Garamond (display) + Jost (body) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@300;400;500&family=Jost:wght@300;400;500&display=swap" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        gold:     { DEFAULT: '#C9A84C', light: '#E8D5A3', dark: '#9A7B2F' },
                        charcoal: { DEFAULT: '#1C1C1E', light: '#2C2C2C' },
                    },
                    fontFamily: {
                        display: ['"Cormorant Garamond"', 'serif'],
                        body:    ['Jost', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <style>
        /* ── Reset & Base ── */
        * { box-sizing: border-box; }
        body { font-family: 'Jost', sans-serif; background: #F9FAFB; }

        /* ── Sidebar nav item ── */
        .nav-item-active {
            background: rgba(201,168,76,0.1);
            border-left: 2px solid #C9A84C;
            color: #C9A84C;
        }

        /* ── Alert/Notifikasi ── */
        .alert-success { background:#f0fdf4; border:1px solid #86efac; color:#166534; }
        .alert-error   { background:#fef2f2; border:1px solid #fca5a5; color:#991b1b; }
        .alert-warning { background:#fffbeb; border:1px solid #fcd34d; color:#92400e; }

        /* ── Tabel admin ── */
        .admin-table th {
            font-family: 'Jost', sans-serif;
            font-size: 11px;
            font-weight: 500;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #6B7280;
            padding: 12px 16px;
            border-bottom: 1px solid #F3F4F6;
            background: #FAFAFA;
            text-align: left;
        }
        .admin-table td {
            padding: 14px 16px;
            border-bottom: 1px solid #F9FAFB;
            font-size: 14px;
            color: #374151;
            vertical-align: middle;
        }
        .admin-table tr:hover td { background: #FAFAFA; }

        /* ── Badge status ── */
        .badge {
            display: inline-flex; align-items: center;
            padding: 2px 10px; border-radius: 2px;
            font-size: 11px; font-weight: 500;
            font-family: 'Jost', sans-serif;
            letter-spacing: 0.05em;
        }
        .badge-green  { background:#f0fdf4; color:#16a34a; border:1px solid #bbf7d0; }
        .badge-gray   { background:#f3f4f6; color:#6b7280; border:1px solid #e5e7eb; }
        .badge-yellow { background:#fffbeb; color:#b45309; border:1px solid #fcd34d; }
        .badge-red    { background:#fef2f2; color:#dc2626; border:1px solid #fecaca; }
        .badge-blue   { background:#eff6ff; color:#2563eb; border:1px solid #bfdbfe; }

        /* ── Tombol aksi tabel ── */
        .btn-action {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 5px 10px; font-size: 12px;
            font-family: 'Jost', sans-serif;
            border-radius: 2px; cursor: pointer;
            transition: all 0.2s ease; border: 1px solid transparent;
            text-decoration: none;
        }
        .btn-edit    { background:#eff6ff; color:#2563eb; border-color:#bfdbfe; }
        .btn-edit:hover { background:#dbeafe; }
        .btn-delete  { background:#fef2f2; color:#dc2626; border-color:#fecaca; }
        .btn-delete:hover { background:#fee2e2; }
        .btn-view    { background:#f0fdf4; color:#16a34a; border-color:#bbf7d0; }
        .btn-view:hover { background:#dcfce7; }

        /* ── Form input admin ── */
        .form-control {
            width: 100%; padding: 9px 12px;
            border: 1px solid #E5E7EB; border-radius: 2px;
            font-family: 'Jost', sans-serif; font-size: 14px;
            color: #1C1C1E; background: white;
            transition: border-color 0.2s ease;
        }
        .form-control:focus {
            outline: none; border-color: #C9A84C;
            box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
        }
        .form-label {
            display: block; font-family: 'Jost', sans-serif;
            font-size: 12px; font-weight: 500;
            letter-spacing: 0.05em; color: #6B7280;
            margin-bottom: 6px; text-transform: uppercase;
        }
        .form-group { margin-bottom: 20px; }

        /* ── Tombol utama ── */
        .btn-primary {
            background: #1C1C1E; color: white;
            padding: 10px 24px; font-family: 'Jost', sans-serif;
            font-size: 12px; font-weight: 500;
            letter-spacing: 0.1em; text-transform: uppercase;
            border: none; cursor: pointer;
            transition: all 0.3s ease;
            display: inline-flex; align-items: center; gap: 8px;
        }
        .btn-primary:hover { background: #C9A84C; color: #1C1C1E; }

        .btn-secondary {
            background: white; color: #6B7280;
            padding: 10px 24px; font-family: 'Jost', sans-serif;
            font-size: 12px; font-weight: 500;
            letter-spacing: 0.1em; text-transform: uppercase;
            border: 1px solid #E5E7EB; cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex; align-items: center; gap: 8px;
            text-decoration: none;
        }
        .btn-secondary:hover { border-color: #C9A84C; color: #C9A84C; }

        /* ── Card ── */
        .admin-card {
            background: white; border: 1px solid #F3F4F6;
            border-radius: 2px; overflow: hidden;
        }
        .admin-card-header {
            padding: 18px 24px;
            border-bottom: 1px solid #F3F4F6;
            display: flex; align-items: center; justify-content: space-between;
        }
        .admin-card-body { padding: 24px; }

        /* ── Section title ── */
        .section-title {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px; font-weight: 400;
            color: #1C1C1E;
        }
    </style>
</head>
