<?php
session_start();
// Set header agar output dikenali sebagai JSON oleh JavaScript
header('Content-Type: application/json');

// Proteksi Endpoint: Pastikan request datang dari admin yang sudah login
if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
    echo json_encode(["status" => "error", "pesan" => "Akses ditolak. Sesi tidak valid."]);
    exit;
}

// Koneksi ke Database (Gunakan database alumni_db sesuai milik Anda)
$host = 'localhost';
$dbname = 'alumni_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "pesan" => "Koneksi database gagal: " . $e->getMessage()]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alumni_id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

    if ($alumni_id > 0) {
        // DI SINI LOGIKA PELACAKAN BERJALAN
        // Simulasi proses pencarian yang memakan waktu (jeda 2 detik)
        sleep(2);

        // Simulasi hasil data yang dikembalikan oleh sistem pencari publik
        $hasil_simulasi = [
            "status" => "success",
            "pesan" => "Pelacakan selesai.",
            "data_ditemukan" => [
                "sumber" => "LinkedIn / Google Scholar",
                "jabatan" => "Profesional / Akademisi",
                "confidence_score" => rand(80, 98) // Skor acak agar terlihat dinamis
            ]
        ];

        try {
            // Update data ke database MySQL
            $sql = "UPDATE alumni SET 
                    status_pelacakan = 'Teridentifikasi dari sumber publik',
                    posisi_pekerjaan = ?,
                    confidence_score = ?
                    WHERE id = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $hasil_simulasi['data_ditemukan']['jabatan'],
                $hasil_simulasi['data_ditemukan']['confidence_score'],
                $alumni_id
            ]);

            echo json_encode($hasil_simulasi);
        } catch (PDOException $e) {
            echo json_encode(["status" => "error", "pesan" => "Gagal update database."]);
        }

    } else {
        echo json_encode(["status" => "error", "pesan" => "ID tidak valid."]);
    }
} else {
    echo json_encode(["status" => "error", "pesan" => "Metode tidak diizinkan."]);
}
?>