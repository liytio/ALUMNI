<?php
session_start();

// Pengecekan Keamanan: Jika belum login, tendang kembali ke halaman login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    header("Location: login.php");
    exit;
}

// Koneksi ke Database
$host = 'localhost';
$dbname = 'alumni_db'; 
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // --- FITUR PENGURUTAN (SORTING) ---
    // Menangkap nilai 'sort' dari URL, default-nya adalah 'terbaru' (berdasarkan ID)
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'terbaru';
    
    // Menentukan query SQL berdasarkan pilihan pengurutan
    if ($sort === 'asc') {
        $order_sql = "ORDER BY nama_lengkap ASC"; // A ke Z
    } elseif ($sort === 'desc') {
        $order_sql = "ORDER BY nama_lengkap DESC"; // Z ke A
    } else {
        $order_sql = "ORDER BY id DESC"; // Data terbaru di atas
    }

    // Mengambil data alumni dengan urutan yang sudah ditentukan
    $stmt = $pdo->query("SELECT * FROM alumni $order_sql");
    $alumni_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Pelacakan Alumni</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 p-8">
    <div class="max-w-6xl mx-auto bg-white p-6 rounded-lg shadow-md">
        
        <div class="flex justify-between items-center mb-6 border-b pb-4">
            <h1 class="text-2xl font-bold text-gray-800">Dashboard Pelacakan Alumni</h1>
            <div class="flex items-center gap-4">
                <span class="text-sm text-gray-600">Halo, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong></span>
                <a href="import_excel.php" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm transition shadow">📥 Impor Data Excel</a>
                <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded text-sm transition shadow">Keluar</a>
            </div>
        </div>
        
        <div class="flex justify-between items-center mb-4 bg-gray-50 p-3 rounded border border-gray-200">
            <div class="text-sm text-gray-600">
                Total Data: <strong><?= count($alumni_list) ?></strong> Alumni
            </div>
            
            <div class="flex items-center gap-2">
                <span class="text-sm font-semibold text-gray-700">Urutkan Nama:</span>
                
                <a href="?sort=asc" class="px-3 py-1.5 rounded text-sm font-medium transition <?= $sort == 'asc' ? 'bg-blue-600 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                    A - Z ↓
                </a>
                
                <a href="?sort=desc" class="px-3 py-1.5 rounded text-sm font-medium transition <?= $sort == 'desc' ? 'bg-blue-600 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                    Z - A ↑
                </a>
                
                <a href="?sort=terbaru" class="px-3 py-1.5 rounded text-sm font-medium transition <?= $sort == 'terbaru' ? 'bg-blue-600 text-white shadow' : 'bg-gray-200 text-gray-700 hover:bg-gray-300' ?>">
                    Waktu Impor
                </a>
            </div>
        </div>

        <div class="overflow-x-auto rounded-lg shadow-sm">
            <table class="min-w-full table-auto border-collapse border border-gray-200">
                <thead class="bg-gray-200">
                    <tr>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border border-gray-300">Nama Lengkap</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border border-gray-300">Pekerjaan</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border border-gray-300">Instansi / Tempat Kerja</th>
                        <th class="px-4 py-3 text-left text-sm font-semibold text-gray-700 border border-gray-300">Status Pelacakan</th>
                        <th class="px-4 py-3 text-center text-sm font-semibold text-gray-700 border border-gray-300">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($alumni_list) > 0): ?>
                        <?php foreach ($alumni_list as $alumni): ?>
                        <tr class="hover:bg-gray-50 transition" id="row-<?= $alumni['id'] ?>">
                            <td class="px-4 py-3 border border-gray-300 text-sm font-medium text-gray-900">
                                <?= htmlspecialchars($alumni['nama_lengkap']) ?>
                            </td>
                            <td class="px-4 py-3 border border-gray-300 text-sm text-gray-600">
                                <?= htmlspecialchars($alumni['posisi_pekerjaan'] ?? '-') ?>
                            </td>
                            <td class="px-4 py-3 border border-gray-300 text-sm text-gray-600">
                                <?= htmlspecialchars($alumni['tempat_bekerja'] ?? '-') ?>
                            </td>
                            <td class="px-4 py-3 border border-gray-300 status-cell">
                                <?php 
                                    $bg_color = 'bg-gray-500';
                                    if ($alumni['status_pelacakan'] == 'Belum Dilacak') $bg_color = 'bg-red-500';
                                    elseif ($alumni['status_pelacakan'] == 'Perlu Verifikasi Manual') $bg_color = 'bg-yellow-500';
                                    elseif ($alumni['status_pelacakan'] == 'Teridentifikasi dari sumber publik') $bg_color = 'bg-green-500';
                                ?>
                                <span class="px-2 py-1 rounded-full text-xs font-semibold text-white <?= $bg_color ?>">
                                    <?= htmlspecialchars($alumni['status_pelacakan']) ?>
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center border border-gray-300">
                                <button type="button" onclick="lacakAlumni(<?= $alumni['id'] ?>)" class="bg-blue-600 text-white px-3 py-1.5 rounded hover:bg-blue-700 text-xs font-semibold shadow transition">
                                    🔍 Lacak
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="px-4 py-10 text-center text-gray-500 border border-gray-300">
                                Belum ada data alumni di database. Silakan klik tombol <strong>"Impor Data Excel"</strong> di atas.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function lacakAlumni(id) {
            const baris = document.getElementById('row-' + id);
            if (!baris) return;

            const statusCell = baris.querySelector('.status-cell');
            if (!statusCell) return;

            statusCell.innerHTML = '<span class="px-2 py-1 rounded-full text-xs font-semibold text-white bg-blue-400 animate-pulse">Sedang mencari...</span>';

            let formData = new FormData();
            formData.append('id', id);

            fetch('proses_lacak.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                if(data.status === 'success') {
                    statusCell.innerHTML = '<span class="px-2 py-1 rounded-full text-xs font-semibold text-white bg-green-500">Teridentifikasi (' + data.data_ditemukan.confidence_score + '%)</span>';
                    alert(data.pesan + "\n\nSumber Temuan: " + data.data_ditemukan.sumber + "\nJabatan Terbaru: " + data.data_ditemukan.jabatan);
                } else {
                    statusCell.innerHTML = '<span class="px-2 py-1 rounded-full text-xs font-semibold text-white bg-red-500">Gagal Dilacak</span>';
                    alert("Gagal: " + data.pesan);
                }
            })
            .catch(error => {
                console.error('Fetch Error:', error);
                statusCell.innerHTML = '<span class="px-2 py-1 rounded-full text-xs font-semibold text-white bg-red-500">Error System</span>';
                alert("Terjadi kesalahan pada sistem.");
            });
        }
    </script>
</body>
</html>