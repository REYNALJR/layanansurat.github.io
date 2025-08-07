<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

require_once 'includes/db.php';

// Update status jika dikirim melalui form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $id         = intval($_POST['id']);
  $status     = $_POST['status'];
  $pesan      = trim($_POST['pesan_admin']);
  $id_surat   = trim($_POST['id_surat']);

  $stmt = $conn->prepare("UPDATE permohonan SET status = ?, pesan_admin = ?, id_surat = ? WHERE id = ?");
  $stmt->bind_param("sssi", $status, $pesan, $id_surat, $id);
  $stmt->execute();
  $notif = "Permohonan berhasil diperbarui.";
}

// Ambil seluruh permohonan (tanpa join ke users)
$result = $conn->query("SELECT * FROM permohonan ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<header class="flex items-center gap-3 bg-green-700 text-white p-4">
  <img src="assets/icons/logo.png" alt="Logo Kalurahan" class="w-10 h-10 rounded-full shadow">
  <h1 class="text-xl font-bold">Kalurahan Banguntapan</h1>
</header>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">

  <header class="p-4 bg-green-700 text-white dark:bg-green-800">
    <div class="max-w-6xl mx-auto flex justify-between items-center">
      <h1 class="text-xl font-bold">Dashboard</h1>
      <a href="logout.php" class="text-sm bg-red-500 hover:bg-red-600 px-3 py-1 rounded">Logout</a>
    </div>
  </header>

  <main class="max-w-6xl mx-auto py-10 px-4">
    <h2 class="text-2xl font-bold mb-6">Daftar Permohonan Warga</h2>

    <?php if (!empty($notif)): ?>
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">✅ <?= htmlspecialchars($notif) ?></div>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
      <div class="overflow-x-auto">
        <table class="min-w-full bg-white dark:bg-gray-800 border rounded shadow">
          <thead>
            <tr class="bg-gray-200 dark:bg-gray-700 text-sm uppercase text-gray-700 dark:text-gray-200">
              <th class="p-3">No</th>
              <th class="p-3">Nama Lengkap</th>
              <th class="p-3">Jenis Surat</th>
              <th class="p-3">Keperluan</th>
              <th class="p-3">Dokumen</th>
              <th class="p-3">Proses</th>
              <th class="p-3">Ubah Status</th>
            </tr>
          </thead>
          <tbody class="text-sm text-gray-900 dark:text-gray-100">
            <?php $no = 1; while ($row = $result->fetch_assoc()): ?>
              <tr class="border-b dark:border-gray-600 hover:bg-green-50 dark:hover:bg-gray-700">
                <td class="p-3 text-center"><?= $no++ ?></td>
                <td class="p-3"><?= htmlspecialchars($row['nama_lengkap']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['jenis_surat']) ?></td>
                <td class="p-3"><?= htmlspecialchars($row['keperluan']) ?></td>
                <td class="p-3 space-y-1">
                  <a href="uploads/<?= urlencode($row['surat_pengantar']) ?>" target="_blank" class="text-blue-600 underline block">Surat Pengantar</a>
                  <a href="uploads/<?= urlencode($row['fotokopi_ktp']) ?>" target="_blank" class="text-blue-600 underline block">Fotokopi KTP</a>
                  <a href="uploads/<?= urlencode($row['fotokopi_kk']) ?>" target="_blank" class="text-blue-600 underline block">Fotokopi KK</a>
                </td>
                <td class="p-3 text-center">
                  <a href="proses_permohonan.php?id=<?= $row['id'] ?>" class="text-blue-600 hover:underline">Proses</a>
                </td>
                <td class="p-3">
                  <form method="post" class="space-y-2">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <select name="status" class="w-full px-2 py-1 border rounded dark:bg-gray-700 dark:border-gray-600">
                      <option value="diproses" <?= $row['status'] === 'diproses' ? 'selected' : '' ?>>Diproses</option>
                      <option value="diterima" <?= $row['status'] === 'diterima' ? 'selected' : '' ?>>Diterima</option>
                      <option value="ditolak" <?= $row['status'] === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
                    </select>
                    <input type="text" name="id_surat" placeholder="ID Surat (jika diterima)"
                           value="<?= htmlspecialchars($row['id_surat']) ?>"
                           class="w-full px-2 py-1 border rounded dark:bg-gray-700 dark:border-gray-600" />
                    <textarea name="pesan_admin" placeholder="Pesan untuk warga"
                              class="w-full px-2 py-1 border rounded dark:bg-gray-700 dark:border-gray-600"><?= htmlspecialchars($row['pesan_admin']) ?></textarea>
                    <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white py-1 rounded">
                      Simpan
                    </button>
                  </form>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      </div>
    <?php else: ?>
      <p class="text-gray-600 dark:text-gray-300 mt-6">Belum ada permohonan surat masuk.</p>
    <?php endif; ?>
  </main>
</body>
</html>
