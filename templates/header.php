<?php
// templates/header.php
require_once __DIR__ . '/../app/config.php';

$auth = new Auth($db->pdo());
$user = $auth->user();
?>
<!doctype html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Perpustakaan Digital</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="bg-gray-900 min-h-screen text-gray-200">

<nav class="bg-gray-800/90 backdrop-blur shadow-lg border-b border-gray-700">
  <div class="container mx-auto px-4 py-4 flex justify-between items-center">

    <!-- LOGO -->
    <a href="index.php" class="font-bold text-2xl tracking-wide text-white hover:text-purple-400 transition">
      Perpustakaan Digital
    </a>

    <div class="flex items-center space-x-6">

      <?php if($user): ?>

        <!-- USER INFO -->
        <span class="font-semibold text-base bg-gray-700 py-1 px-3 rounded-full shadow">
          Halo, <?= htmlspecialchars($user['nama'] ?: $user['username']) ?>
          (<span class="italic text-purple-300"><?= htmlspecialchars($user['role']) ?></span>)
        </span>

        <!-- NAV LINK FUNCTION -->
        <?php
          function navlink($href, $text) {
            return "<a href='$href' class='text-gray-300 hover:text-purple-400 transition font-medium text-base'>$text</a>";
          }
        ?>

        <?= navlink('index.php', 'Home'); ?>

        <?php if (in_array($user['role'], ['administrator', 'petugas'])): ?>
          <?= navlink('books.php', 'Pendataan Buku'); ?>
        <?php endif; ?>

        <?php if ($user['role'] === 'peminjam'): ?>
          <?= navlink('borrow.php', 'Peminjaman'); ?>
        <?php endif; ?>

        <?php if ($user['role'] === 'administrator'): ?>
          <?= navlink('admin_users.php', 'Manajemen User'); ?>
          <?= navlink('reports.php', 'Laporan'); ?>

        <?php elseif ($user['role'] === 'petugas'): ?>
          <?= navlink('reports.php', 'Laporan'); ?>
        <?php endif; ?>

        <!-- LOGOUT -->
        <a href="logout.php" 
           class="text-red-400 hover:text-red-300 font-semibold text-base transition">
          Logout
        </a>

      <?php endif; ?>
    </div>

  </div>
</nav>

<main class="container mx-auto p-4">
