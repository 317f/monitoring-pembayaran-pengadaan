-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL,
    role TEXT CHECK(role IN ('Administrator', 'Pengguna')) DEFAULT 'Pengguna',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create payments table
CREATE TABLE IF NOT EXISTS payments (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    nama_vendor TEXT NOT NULL,
    nama_pengadaan TEXT NOT NULL,
    metode_pengadaan TEXT CHECK(metode_pengadaan IN ('Tender', 'Pemilihan Langsung', 'Penunjukan Langsung', 'Pengadaan Langsung')) NOT NULL,
    nomor_spk TEXT,
    tanggal_terbit_spk DATE,
    tanggal_akhir_spk DATE,
    nilai_kontrak DECIMAL(15,2),
    status TEXT CHECK(status IN ('Dalam Proses', 'Tertunda', 'Selesai')) DEFAULT 'Dalam Proses',
    opini_verifikator TEXT,
    keterangan TEXT,
    user_id INTEGER,
    pic_divisi TEXT,
    pic_vendor TEXT,
    kontak_vendor TEXT,
    pic_draft_pks TEXT,
    file_spk TEXT,
    file_pks TEXT,
    jatuh_tempo DATE,
    jumlah_termin INTEGER,
    pembayaran_termin_ke INTEGER,
    jumlah_pembayaran DECIMAL(15,2),
    nomor_lpt TEXT,
    tanggal_lpt_terbit DATE,
    tanggal_posting_pembayaran DATE,
    dokumen_hasil_pekerjaan TEXT,
    kekurangan_dokumen_vendor TEXT,
    kekurangan_dokumen_internal TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- Insert default admin user (password: admin123)
INSERT INTO users (username, password, role) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator');

-- Insert default regular user (password: user123)
INSERT INTO users (username, password, role) VALUES 
('user', '$2y$10$mQ3YIZDaDT3HyRBqLGJ4Vu4kDyQvVlr.vqD.YITBXGKztVdqJ3YZi', 'Pengguna');
