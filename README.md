# Monitoring Pembayaran Pengadaan

Aplikasi web untuk monitoring proses pembayaran pengadaan dengan fitur multi-role authentication, pengelolaan data pembayaran, ekspor data, dan pengingat otomatis.

## Fitur

- Multi-role authentication (Administrator & Pengguna)
- Dashboard dengan statistik pembayaran
- Pengelolaan data proses pembayaran
- Upload dan manajemen dokumen (SPK, PKS, dll)
- Ekspor data ke Excel
- Cetak daftar pembayaran
- Pengingat otomatis untuk jatuh tempo pembayaran
- Tema light/dark mode
- Responsive design

## Teknologi

- PHP 7.4+
- MySQL 5.7+
- Bootstrap 5
- Font Awesome 6
- DataTables
- JavaScript/jQuery

## Persyaratan Sistem

- Web Server (Apache/Nginx)
- PHP 7.4 atau lebih tinggi
- MySQL 5.7 atau lebih tinggi
- mod_rewrite enabled (untuk Apache)
- PHP Extensions:
  - PDO
  - PDO_MYSQL
  - GD
  - fileinfo

## Instalasi

1. Clone repository ini atau download source code
2. Import database schema dari `database/schema.sql`
3. Konfigurasi database di `config/database.php`:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'username_database');
   define('DB_PASS', 'password_database');
   define('DB_NAME', 'procurement_db');
   ```
4. Pastikan folder `public/uploads` memiliki permission yang tepat (755)
5. Konfigurasi virtual host atau gunakan built-in PHP server

## Struktur Folder

```
├── config/             # Konfigurasi aplikasi
├── controllers/        # Controller files
├── models/            # Model files
├── views/             # View files
│   ├── auth/          # Authentication views
│   ├── layouts/       # Layout templates
│   ├── payments/      # Payment views
│   └── errors/        # Error pages
├── public/            # Public assets
│   └── uploads/       # Upload directory
└── database/          # Database schema
```

## Penggunaan

### Login Default

1. Administrator:
   - Username: admin
   - Password: admin123

2. Pengguna:
   - Username: user
   - Password: user123

### Fitur Utama

1. **Dashboard**
   - Statistik pembayaran
   - Daftar pembayaran dengan filter dan pencarian
   - Notifikasi jatuh tempo

2. **Manajemen Pembayaran**
   - Tambah pembayaran baru
   - Edit pembayaran
   - Hapus pembayaran
   - Upload dokumen

3. **Ekspor Data**
   - Export ke Excel
   - Print daftar pembayaran

4. **Manajemen User** (Admin only)
   - Tambah user baru
   - Edit user
   - Hapus user
   - Reset password

## Keamanan

- Password di-hash menggunakan algoritma bcrypt
- Proteksi terhadap SQL injection menggunakan PDO prepared statements
- XSS protection dengan HTML escaping
- CSRF protection pada form
- Validasi input
- Session handling yang aman
- File upload validation

## Maintenance

### Backup Database

Lakukan backup database secara berkala:
```bash
mysqldump -u [username] -p procurement_db > backup_[tanggal].sql
```

### Update Sistem

1. Backup database dan files
2. Pull updates dari repository
3. Import perubahan database jika ada
4. Clear cache browser

## Troubleshooting

### Common Issues

1. **Error 500**
   - Periksa permission folder uploads
   - Periksa konfigurasi database
   - Cek error log PHP

2. **Upload Gagal**
   - Periksa upload_max_filesize di php.ini
   - Periksa permission folder uploads

3. **Halaman Tidak Ditemukan**
   - Pastikan mod_rewrite aktif
   - Periksa konfigurasi .htaccess

## Kontribusi

1. Fork repository
2. Buat branch baru
3. Commit perubahan
4. Push ke branch
5. Buat Pull Request

## Lisensi

MIT License. Lihat file `LICENSE` untuk detail.
