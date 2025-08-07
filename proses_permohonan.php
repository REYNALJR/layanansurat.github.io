<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  header("Location: login.php");
  exit;
}

require_once 'includes/db.php';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
  echo "Permohonan tidak ditemukan.";
  exit;
}

$id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM permohonan WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows < 1) {
  echo "Permohonan tidak ditemukan.";
  exit;
}

$data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $status = $_POST['status'];
  $pesan = trim($_POST['pesan_admin']);
  $id_surat = trim($_POST['id_surat']);

  $update = $conn->prepare("UPDATE permohonan SET status = ?, pesan_admin = ?, id_surat = ? WHERE id = ?");
  $update->bind_param("sssi", $status, $pesan, $id_surat, $id);
  if ($update->execute()) {
    $berhasil = "Data permohonan berhasil diperbarui.";
    header("Refresh:1");
  } else {
    $error = "Gagal menyimpan perubahan.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Proses Permohonan</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">

  <div class="max-w-3xl mx-auto py-10 px-4">
    <h2 class="text-2xl font-bold mb-4">Proses Permohonan Surat</h2>
    <a href="dashboard_admin.php" class="text-green-600 underline mb-4 inline-block">← Kembali ke Dashboard</a>

    <?php if (!empty($berhasil)): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4"><?= htmlspecialchars($berhasil) ?></div>
    <?php elseif (!empty($error)): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <div class="bg-white dark:bg-gray-800 p-6 rounded shadow mb-6">
      <p><strong>Nama Lengkap:</strong> <?= htmlspecialchars($data['nama_lengkap']) ?></p>
      <p><strong>Alamat (RT/RW):</strong> <?= htmlspecialchars($data['rt_rw']) ?></p>
      <p><strong>Dusun:</strong> <?= htmlspecialchars($data['dusun']) ?></p>
      <p><strong>Jenis Surat:</strong> <?= htmlspecialchars($data['jenis_surat']) ?></p>
      <p><strong>Keperluan:</strong> <?= htmlspecialchars($data['keperluan']) ?></p>
      <p><strong>Dokumen:</strong></p>
      <ul class="list-disc ml-6 text-blue-600">
        <li><a href="uploads/<?= urlencode($data['surat_pengantar']) ?>" target="_blank" class="underline">Surat Pengantar</a></li>
        <li><a href="uploads/<?= urlencode($data['fotokopi_ktp']) ?>" target="_blank" class="underline">Fotokopi KTP</a></li>
        <li><a href="uploads/<?= urlencode($data['fotokopi_kk']) ?>" target="_blank" class="underline">Fotokopi KK</a></li>
      </ul>
    </div>

    <form method="post" class="space-y-4">
      <div>
        <label class="block mb-1 font-medium">Status Permohonan</label>
        <select name="status" required class="w-full border px-3 py-2 rounded dark:bg-gray-700 dark:border-gray-600">
          <option value="diproses" <?= $data['status'] === 'diproses' ? 'selected' : '' ?>>Diproses</option>
          <option value="diterima" <?= $data['status'] === 'diterima' ? 'selected' : '' ?>>Diterima</option>
          <option value="ditolak" <?= $data['status'] === 'ditolak' ? 'selected' : '' ?>>Ditolak</option>
        </select>
      </div>

      <div>
        <label class="block mb-1 font-medium">ID Surat</label>
        <input type="text" name="id_surat" value="<?= htmlspecialchars($data['id_surat']) ?>"
               class="w-full border px-3 py-2 rounded dark:bg-gray-700 dark:border-gray-600">
      </div>

      <div>
        <label class="block mb-1 font-medium">Pesan untuk Pemohon</label>
        <textarea name="pesan_admin" rows="3"
                  class="w-full border px-3 py-2 rounded dark:bg-gray-700 dark:border-gray-600"><?= htmlspecialchars($data['pesan_admin']) ?></textarea>
      </div>

      <button type="submit"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
        Simpan Perubahan
      </button>
    </form>
  </div>
</body>
</html>
