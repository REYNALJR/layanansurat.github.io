<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit;
}

require_once 'includes/db.php';

// Ambil informasi pengguna
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];

// Ambil permohonan milik user
$stmt = $conn->prepare("SELECT * FROM permohonan WHERE user_id = ? ORDER BY id DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Pengguna</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<header class="flex items-center gap-3 bg-green-700 text-white p-4">
  <img src="assets/icons/logo.png" alt="Logo Kalurahan" class="w-10 h-10 rounded-full shadow">
  <h1 class="text-xl font-bold">Kalurahan Banguntapan</h1>
</header>
<body class="bg-gradient-to-br from-green-50 to-white dark:from-gray-800 dark:to-gray-900 text-gray-900 dark:text-white min-h-screen">

  <header class="bg-green-700 text-white py-4 shadow">
    <div class="max-w-6xl mx-auto px-4 flex justify-between items-center">
      <h1 class="text-xl font-bold">Dashboard Pengguna</h1>
      <div class="flex items-center gap-4">
        <span class="text-sm">👋 Halo, <strong><?= htmlspecialchars($username) ?></strong></span>
        <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-sm">Logout</a>
      </div>
    </div>
  </header>

  <main class="max-w-6xl mx-auto py-10 px-4">
    <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
      <div>
        <h2 class="text-2xl font-semibold">Riwayat Permohonan Surat</h2>
        <p class="text-sm text-gray-600 dark:text-gray-400">Lihat status dan riwayat permohonan yang pernah Anda ajukan.</p>
      </div>
      <a href="buat_permohonan.php" class="bg-green-600 hover:bg-green-700 text-white px-5 py-2 rounded shadow-md">+ Buat Permohonan</a>
    </div>

    <?php if ($result->num_rows > 0): ?>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 border rounded shadow-md">
          <thead>
            <tr class="bg-gray-200 dark:bg-gray-700 text-sm uppercase text-gray-700 dark:text-gray-200">
              <th class="p-3">No</th>
              <th class="p-3">Jenis Surat</th>
              <th class="p-3">Keperluan</th>
              <th class="p-3">Status</th>
              <th class="p-3">ID Surat</th>
              <th class="p-3">Pesan</th>
            </tr>
          </thead>
          <tbody class="text-sm text-gray-800 dark:text-gray-100">
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
              <tr class="border-b dark:border-gray-600 hover:bg-green-50 dark:hover:bg-gray-700">
                <td class="p-3 text-center"><?= $no++ ?></td>
                <td class="p-3"><?= htmlspecialchars($row['jenis_surat']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['keperluan']) ?></td>
                <td class="p-3 font-semibold">
                  <?php if ($row['status'] == 'diterima'): ?>
                    <span class="text-green-600">✔ Diterima</span>
                  <?php elseif ($row['status'] == 'ditolak'): ?>
                    <span class="text-red-600">✖ Ditolak</span>
                  <?php else: ?>
                    <span class="text-yellow-600">⏳ Diproses</span>
                  <?php endif; ?>
                </td>
                <td class="p-3 text-center"><?= htmlspecialchars($row['id_surat'] ?? '-') ?></td>
                <td class="p-3"><?= htmlspecialchars($row['pesan_admin'] ?? '-') ?></td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-center text-gray-600 dark:text-gray-300 mt-6">Belum ada permohonan surat yang diajukan.</p>
    <?php endif; ?>

    <section class="mt-12 grid md:grid-cols-2 gap-6">
      <div class="bg-white dark:bg-gray-800 rounded shadow-md p-6">
        <h3 class="text-lg font-bold mb-2">🔍 Status Pengajuan</h3>
        <p class="text-sm text-gray-600 dark:text-gray-300">
          Anda dapat memantau status permohonan secara berkala untuk melihat apakah permohonan sudah diproses, diterima, atau ditolak.
        </p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded shadow-md p-6">
        <h3 class="text-lg font-bold mb-2">📄 Jenis Surat Populer</h3>
        <ul class="list-disc ml-5 text-sm text-gray-600 dark:text-gray-300">
          <li>Surat Keterangan Domisili</li>
          <li>Surat Pengantar SKCK</li>
          <li>Surat Keterangan Usaha</li>
          <li>Surat Keterangan Tidak Mampu</li>
        </ul>
      </div>
    </section>
  </main>

</body>
</html>
