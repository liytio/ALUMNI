<?php
/**
 * Setup Database - Buat database dan tabel otomatis
 * Akses: http://localhost/adalahpokoknya/setup.php
 */

$host = 'localhost';
$username = 'root';
$password = '';
$database = 'pelacakan_alumni';

try {
    // 1. Koneksi ke MySQL tanpa spesifikasi database
    $pdo = new PDO("mysql:host=$host", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 2. Membuat database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS $database CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "<p>✓ Database '$database' berhasil dibuat/sudah ada</p>";
    
    // 3. Memilih database
    $pdo->exec("USE $database");
    
    // 4. Membuat tabel alumni
    $createTableSQL = "CREATE TABLE IF NOT EXISTS alumni (
        id INT AUTO_INCREMENT PRIMARY KEY,
        nama_lengkap VARCHAR(255) NOT NULL,
        linkedin VARCHAR(255) NULL,
        instagram VARCHAR(255) NULL,
        facebook VARCHAR(255) NULL,
        tiktok VARCHAR(255) NULL,
        email VARCHAR(255) NULL,
        no_hp VARCHAR(20) NULL,
        tempat_bekerja VARCHAR(255) NULL,
        alamat_bekerja VARCHAR(255) NULL,
        posisi_pekerjaan VARCHAR(255) NULL,
        status_pekerjaan ENUM('PNS', 'Swasta', 'Wirausaha', 'Belum Bekerja') DEFAULT 'Belum Bekerja',
        medsos_tempat_bekerja VARCHAR(255) NULL,
        status_pelacakan VARCHAR(50) DEFAULT 'Belum Dilacak',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_nama (nama_lengkap),
        INDEX idx_status (status_pelacakan)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($createTableSQL);
    echo "<p>✓ Tabel 'alumni' berhasil dibuat/sudah ada</p>";
    
    // 5. Sebagai opsional, masukkan data sample jika tabel masih kosong
    $stmt = $pdo->query("SELECT COUNT(*) FROM alumni");
    $count = $stmt->fetchColumn();
    
    if ($count == 0) {
        $sampleDataSQL = "INSERT INTO alumni (nama_lengkap, posisi_pekerjaan, tempat_bekerja, status_pelacakan) 
        VALUES 
        ('Contoh Alumni 1', 'Software Engineer', 'PT Teknologi Indonesia', 'Teridentifikasi dari sumber publik'),
        ('Contoh Alumni 2', 'Data Analyst', 'PT Industri Maju', 'Perlu Verifikasi Manual'),
        ('Contoh Alumni 3', 'Product Manager', 'Startup Tech', 'Belum Dilacak')";
        
        $pdo->exec($sampleDataSQL);
        echo "<p>✓ Data sample berhasil dimasukkan (3 alumni)</p>";
    } else {
        echo "<p>ℹ Tabel sudah berisi data ($count alumni)</p>";
    }
    
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Setup Database - Sukses</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-green-600 mb-4">✓ Setup Berhasil!</h1>
                <p class="text-gray-600 mb-6">Database dan tabel telah berhasil disiapkan.</p>
                <div class="bg-green-50 border border-green-200 rounded p-4 text-sm text-left mb-6 space-y-2">
                    <p>✓ Database 'pelacakan_alumni' dibuat</p>
                    <p>✓ Tabel 'alumni' dibuat dengan struktur lengkap</p>
                    <p>✓ Indeks database optimized</p>
                </div>
                <a href="login.php" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded inline-block transition">
                    Ke Login
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    
} catch (PDOException $e) {
    ?>
    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Setup Database - Error</title>
        <script src="https://cdn.tailwindcss.com"></script>
    </head>
    <body class="bg-gray-100 h-screen flex items-center justify-center p-4">
        <div class="bg-white p-8 rounded-lg shadow-lg max-w-md w-full">
            <div class="text-center">
                <h1 class="text-3xl font-bold text-red-600 mb-4">✗ Setup Gagal!</h1>
                <p class="text-gray-600 mb-4">Terjadi kesalahan saat membuat database:</p>
                <div class="bg-red-50 border border-red-200 rounded p-4 text-sm text-left mb-6">
                    <p class="text-red-700 font-mono"><?= htmlspecialchars($e->getMessage()) ?></p>
                </div>
                <details class="text-left text-xs text-gray-500">
                    <summary class="cursor-pointer mb-2">Detail error:</summary>
                    <pre class="bg-gray-100 p-2 rounded overflow-auto"><?= htmlspecialchars($e->getTraceAsString()) ?></pre>
                </details>
                <a href="javascript:history.back()" class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-6 rounded inline-block transition">
                    Kembali
                </a>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit;
}
?>
