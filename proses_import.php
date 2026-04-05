<?php
// WAJIB: Mendefinisikan Namespace dari library
use Shuchkin\SimpleXLSX;

// Mengabaikan batas waktu dan memori pada level skrip (sebagai cadangan)
ini_set('max_execution_time', '0');
ini_set('memory_limit', '1024M');

// Memanggil Library SimpleXLSX
require_once __DIR__ . '/SimpleXLSX.php';

// Konfigurasi Database (Gunakan PDO untuk performa keamanan yang lebih baik)
$host = 'localhost';
$dbname = 'alumni_db';
$username = 'root'; // Sesuaikan dengan database Anda
$password = '';     // Sesuaikan dengan database Anda

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}

// Mengecek apakah ada file yang diunggah
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file_excel'])) {
    $file = $_FILES['file_excel'];
    
    // Validasi Ekstensi dan Ukuran (10MB = 10 * 1024 * 1024 bytes)
    $ekstensi = pathinfo($file['name'], PATHINFO_EXTENSION);
    if (strtolower($ekstensi) !== 'xlsx') {
        die("Format file harus .xlsx");
    }
    if ($file['size'] > 10485760) {
        die("Ukuran file melebihi 10MB");
    }

    // Mengurai file Excel menggunakan namespace yang benar
    if ($xlsx = SimpleXLSX::parse($file['tmp_name'])) {
        $baris_data = $xlsx->rows();
        
        // Memulai Transaksi Database (SANGAT PENTING untuk mempercepat insert ribuan baris)
        $pdo->beginTransaction();
        
        // Menyiapkan perintah SQL
        $sql = "INSERT INTO alumni (
                    nama_lengkap, linkedin, instagram, facebook, tiktok, 
                    email, no_hp, tempat_bekerja, alamat_bekerja, 
                    posisi_pekerjaan, status_pekerjaan, medsos_tempat_bekerja
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);

        $jumlah_sukses = 0;

        foreach ($baris_data as $indeks => $baris) {
            // Melewati baris pertama (indeks 0) jika itu adalah header judul kolom
            if ($indeks === 0) continue; 

            // Memetakan data kolom (Mencegah error jika kolom kosong/kurang)
            $nama_lengkap    = isset($baris[0]) ? $baris[0] : '';
            if (empty(trim($nama_lengkap))) continue; // Abaikan jika nama kosong

            $linkedin        = isset($baris[1]) ? $baris[1] : null;
            $instagram       = isset($baris[2]) ? $baris[2] : null;
            $facebook        = isset($baris[3]) ? $baris[3] : null;
            $tiktok          = isset($baris[4]) ? $baris[4] : null;
            $email           = isset($baris[5]) ? $baris[5] : null;
            $no_hp           = isset($baris[6]) ? $baris[6] : null;
            $tempat_kerja    = isset($baris[7]) ? $baris[7] : null;
            $alamat_kerja    = isset($baris[8]) ? $baris[8] : null;
            $posisi          = isset($baris[9]) ? $baris[9] : null;
            
            // Standarisasi status pekerjaan sesuai ENUM di database
            $status_raw      = isset($baris[10]) ? trim($baris[10]) : 'Belum Bekerja';
            $status_pekerjaan = in_array($status_raw, ['PNS', 'Swasta', 'Wirausaha']) ? $status_raw : 'Belum Bekerja';
            
            $medsos_kerja    = isset($baris[11]) ? $baris[11] : null;

            // Mengeksekusi penyimpanan baris
            $stmt->execute([
                $nama_lengkap, $linkedin, $instagram, $facebook, $tiktok,
                $email, $no_hp, $tempat_kerja, $alamat_kerja,
                $posisi, $status_pekerjaan, $medsos_kerja
            ]);
            
            $jumlah_sukses++;
        }

        // Menerapkan semua perubahan ke database
        $pdo->commit();
        
        echo "<script>
                alert('Sukses! Sebanyak $jumlah_sukses data alumni berhasil diimpor.');
                window.location.href = 'index.php';
              </script>";
    } else {
        // Pemanggilan error juga disesuaikan dengan namespace
        echo "Gagal mengurai Excel: " . SimpleXLSX::parseError();
    }
}
?>