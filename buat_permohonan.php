<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
  header("Location: login.php");
  exit;
}

require_once 'includes/db.php';

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $jenis_surat   = trim($_POST['jenis_surat']);
  $nama_lengkap  = trim($_POST['nama_lengkap']);
  $rt_rw         = trim($_POST['rt_rw']);
  $dusun         = trim($_POST['dusun']);
  $keperluan     = trim($_POST['keperluan']);
  $user_id       = $_SESSION['user_id'];

  $surat_pengantar = $_FILES['surat_pengantar']['name'];
  $ktp             = $_FILES['fotokopi_ktp']['name'];
  $kk              = $_FILES['fotokopi_kk']['name'];

  $tmp_pengantar = $_FILES['surat_pengantar']['tmp_name'];
  $tmp_ktp       = $_FILES['fotokopi_ktp']['tmp_name'];
  $tmp_kk        = $_FILES['fotokopi_kk']['tmp_name'];

  $path_pengantar = 'uploads/' . basename($surat_pengantar);
  $path_ktp       = 'uploads/' . basename($ktp);
  $path_kk        = 'uploads/' . basename($kk);

  if (
    empty($jenis_surat) || empty($keperluan) || empty($surat_pengantar) || empty($ktp) || empty($kk) ||
    empty($nama_lengkap) || empty($rt_rw) || empty($dusun)
  ) {
    $errors[] = "Semua field dan dokumen wajib diisi.";
  } elseif (
    move_uploaded_file($tmp_pengantar, $path_pengantar) &&
    move_uploaded_file($tmp_ktp, $path_ktp) &&
    move_uploaded_file($tmp_kk, $path_kk)
  ) {
    $stmt = $conn->prepare("INSERT INTO permohonan 
      (user_id, jenis_surat, nama_lengkap, rt_rw, dusun, keperluan, surat_pengantar, fotokopi_ktp, fotokopi_kk, status, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'Menunggu', NOW())");

    $stmt->bind_param("issssssss", $user_id, $jenis_surat, $nama_lengkap, $rt_rw, $dusun, $keperluan, $surat_pengantar, $ktp, $kk);

    if ($stmt->execute()) {
      $success = "Permohonan berhasil dikirim.";
    } else {
      $errors[] = "Gagal mengirim permohonan.";
    }
  } else {
    $errors[] = "Gagal mengunggah dokumen.";
  }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Permohonan Surat</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-gray-100 dark:bg-gray-900 text-gray-900 dark:text-white">

  <div class="max-w-xl mx-auto py-10 px-4">
    <h2 class="text-2xl font-bold mb-6 text-center">Ajukan Permohonan Surat</h2>

    <?php if ($errors): ?>
      <div class="bg-red-100 text-red-700 p-3 rounded mb-4">
        <ul class="list-disc ml-5">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <?php if ($success): ?>
      <div class="bg-green-100 text-green-700 p-3 rounded mb-4 text-center">
        <?= htmlspecialchars($success) ?> <br>
        <a href="dashboard_user.php" class="underline text-green-800">Kembali ke Dashboard</a>
      </div>
    <?php endif; ?>

    <form action="buat_permohonan.php" method="POST" enctype="multipart/form-data" class="bg-white p-6 rounded shadow">
      <div class="mb-4">
        <label class="block mb-1 font-medium">Jenis Surat</label>
        <select name="jenis_surat" required class="w-full px-3 py-2 rounded border">
          <option value="">-- Pilih Jenis Surat --</option>
          <option value="Surat Permohonan Nikah">Surat Permohonan Nikah</option>
          <option value="Surat Permohonan Domisili Tempat Tinggal">Surat Permohonan Domisili Tempat Tinggal</option>
          <option value="Surat Permohonan Domisili Perusahaan/Usaha">Surat Permohonan Domisili Perusahaan/Usaha</option>
          <option value="Surat Permohonan SKCK">Surat Permohonan SKCK</option>
          <option value="Surat Keterangan Usaha">Surat Keterangan Usaha</option>
          <option value="Surat Keterangan Tidak Mampu">Surat Keterangan Tidak Mampu</option>
          <option value="Surat Keterangan Kependudukan">Surat Keterangan Kependudukan</option>
          <option value="Surat Akta Kelahiran">Surat Akta Kelahiran</option>
          <option value="Surat Akta Kematian">Surat Akta Kematian</option>
          <option value="Surat KTP">Surat KTP</option>
          <option value="Surat Permohonan Status Pekerjaan">Surat Permohonan Status Pekerjaan</option>
          <option value="Surat Permohonan Perubahan Status Perkawinan">Surat Permohonan Perubahan Status Perkawinan</option>
          <option value="Surat Permohonan Status Pendidikan">Surat Permohonan Status Pendidikan</option>
          <option value="Surat Permohonan Keterangan Pensiun">Surat Permohonan Keterangan Pensiun</option>
          <option value="Surat Permohonan Cerai">Surat Permohonan Cerai</option>
          <option value="Surat Keterangan Penghasilan Orang Tua">Surat Keterangan Penghasilan Orang Tua</option>
          <option value="Surat Izin Perjalanan">Surat Izin Perjalanan</option>
          <option value="Surat Permohonan Pindah Kependudukan">Surat Permohonan Pindah Kependudukan</option>
          <option value="Surat Permohonan Masuk Kependudukan">Surat Permohonan Masuk Kependudukan</option>
        </select>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Nama Lengkap</label>
        <input type="text" name="nama_lengkap" required class="w-full px-3 py-2 rounded border" placeholder="Nama sesuai KTP" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Alamat RT/RW</label>
        <input type="text" name="rt_rw" required class="w-full px-3 py-2 rounded border" placeholder="Contoh: 01/05" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Dusun</label>
        <input type="text" name="dusun" required class="w-full px-3 py-2 rounded border" placeholder="Nama Dusun" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Keperluan</label>
        <textarea name="keperluan" rows="3" required class="w-full px-3 py-2 rounded border"></textarea>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Upload Surat Pengantar</label>
        <input type="file" name="surat_pengantar" required class="w-full px-3 py-2 rounded border" />
        <p class="text-sm text-gray-600 mt-1 italic">* Nama file tidak boleh mengandung spasi atau karakter aneh.</p>
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Upload Fotokopi KTP</label>
        <input type="file" name="fotokopi_ktp" required class="w-full px-3 py-2 rounded border" />
      </div>

      <div class="mb-4">
        <label class="block mb-1 font-medium">Upload Fotokopi KK</label>
        <input type="file" name="fotokopi_kk" required class="w-full px-3 py-2 rounded border" />
      </div>

      <div class="text-right">
        <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
          Kirim Permohonan
        </button>
      </div>
    </form>

    <p class="mt-4 text-center text-sm">
      <a href="dashboard_user.php" class="text-green-600 hover:underline">← Kembali ke Dashboard</a>
    </p>
  </div>
</body>
</html>
