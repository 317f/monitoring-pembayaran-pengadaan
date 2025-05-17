-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('Administrator', 'Pengguna') DEFAULT 'Pengguna',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nama_vendor VARCHAR(100) NOT NULL,
    nama_pengadaan VARCHAR(150) NOT NULL,
    metode_pengadaan ENUM('Tender', 'Pemilihan Langsung', 'Penunjukan Langsung', 'Pengadaan Langsung') NOT NULL,
    nomor_spk VARCHAR(50),
    tanggal_terbit_spk DATE,
    tanggal_akhir_spk DATE,
    nilai_kontrak DECIMAL(15,2),
    status ENUM('Dalam Proses', 'Tertunda', 'Selesai') DEFAULT 'Dalam Proses',
    opini_verifikator TEXT,
    keterangan TEXT,
    user_id INT,
    pic_divisi VARCHAR(100),
    pic_vendor VARCHAR(100),
    kontak_vendor VARCHAR(50),
    pic_draft_pks VARCHAR(100),
    file_spk VARCHAR(255),
    file_pks VARCHAR(255),
    jatuh_tempo DATE,
    jumlah_termin INT,
    pembayaran_termin_ke INT,
    jumlah_pembayaran DECIMAL(15,2),
    nomor_lpt VARCHAR(50),
    tanggal_lpt_terbit DATE,
    tanggal_posting_pembayaran DATE,
    dokumen_hasil_pekerjaan VARCHAR(255),
    kekurangan_dokumen_vendor TEXT,
    kekurangan_dokumen_internal TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Insert default regular user (password: user123)
INSERT INTO users (username, password, role) VALUES 
('user', '$2y$10$mQ3YIZDaDT3HyRBqLGJ4Vu4kDyQvVlr.vqD.YITBXGKztVdqJ3YZi', 'Pengguna');
