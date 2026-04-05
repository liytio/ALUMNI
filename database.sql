-- Membuat Database
CREATE DATABASE IF NOT EXISTS pelacakan_alumni;
USE pelacakan_alumni;

-- Membuat Tabel Alumni
CREATE TABLE alumni (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Data sample (opsional)
INSERT INTO alumni (nama_lengkap, posisi_pekerjaan, tempat_bekerja, status_pelacakan) 
VALUES 
('Contoh Alumni 1', 'Software Engineer', 'PT Teknologi Indonesia', 'Teridentifikasi dari sumber publik'),
('Contoh Alumni 2', 'Data Analyst', 'PT Industri Maju', 'Perlu Verifikasi Manual'),
('Contoh Alumni 3', 'Product Manager', 'Startup Tech', 'Belum Dilacak');
