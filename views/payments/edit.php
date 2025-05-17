<?php 
include __DIR__ . '/../layouts/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}

// Ensure we have payment data
if (!isset($payment) || !$payment) {
    header('Location: index.php?page=dashboard');
    exit;
}
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Edit Pembayaran</h2>
        <a href="index.php?page=dashboard" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i> Kembali
        </a>
    </div>

    <?php if (isset($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <form action="index.php?page=payments&action=edit&id=<?php echo $payment['id']; ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                <!-- Informasi Vendor -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi Vendor</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_vendor" class="form-label">Nama Vendor <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_vendor" name="nama_vendor" value="<?php echo htmlspecialchars($payment['nama_vendor']); ?>" required>
                            <div class="invalid-feedback">Nama vendor harus diisi</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="kontak_vendor" class="form-label">Kontak Vendor</label>
                            <input type="text" class="form-control" id="kontak_vendor" name="kontak_vendor" value="<?php echo htmlspecialchars($payment['kontak_vendor']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Informasi Pengadaan -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi Pengadaan</h5>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="nama_pengadaan" class="form-label">Nama Pengadaan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nama_pengadaan" name="nama_pengadaan" value="<?php echo htmlspecialchars($payment['nama_pengadaan']); ?>" required>
                            <div class="invalid-feedback">Nama pengadaan harus diisi</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="metode_pengadaan" class="form-label">Metode Pengadaan <span class="text-danger">*</span></label>
                            <select class="form-select" id="metode_pengadaan" name="metode_pengadaan" required>
                                <option value="">Pilih Metode</option>
                                <option value="Tender" <?php echo $payment['metode_pengadaan'] == 'Tender' ? 'selected' : ''; ?>>Tender</option>
                                <option value="Pemilihan Langsung" <?php echo $payment['metode_pengadaan'] == 'Pemilihan Langsung' ? 'selected' : ''; ?>>Pemilihan Langsung</option>
                                <option value="Penunjukan Langsung" <?php echo $payment['metode_pengadaan'] == 'Penunjukan Langsung' ? 'selected' : ''; ?>>Penunjukan Langsung</option>
                                <option value="Pengadaan Langsung" <?php echo $payment['metode_pengadaan'] == 'Pengadaan Langsung' ? 'selected' : ''; ?>>Pengadaan Langsung</option>
                            </select>
                            <div class="invalid-feedback">Metode pengadaan harus dipilih</div>
                        </div>
                    </div>
                </div>

                <!-- Informasi SPK -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi SPK</h5>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="nomor_spk" class="form-label">Nomor SPK <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="nomor_spk" name="nomor_spk" value="<?php echo htmlspecialchars($payment['nomor_spk']); ?>" required>
                            <div class="invalid-feedback">Nomor SPK harus diisi</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="tanggal_terbit_spk" class="form-label">Tanggal Terbit SPK <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_terbit_spk" name="tanggal_terbit_spk" value="<?php echo $payment['tanggal_terbit_spk']; ?>" required>
                            <div class="invalid-feedback">Tanggal terbit SPK harus diisi</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="tanggal_akhir_spk" class="form-label">Tanggal Akhir SPK <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="tanggal_akhir_spk" name="tanggal_akhir_spk" value="<?php echo $payment['tanggal_akhir_spk']; ?>" required>
                            <div class="invalid-feedback">Tanggal akhir SPK harus diisi</div>
                        </div>
                    </div>
                </div>

                <!-- Informasi Kontrak -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi Kontrak</h5>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="nilai_kontrak" class="form-label">Nilai Kontrak (Rp) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control currency-input" id="nilai_kontrak" name="nilai_kontrak" value="<?php echo number_format($payment['nilai_kontrak'], 0, ',', '.'); ?>" required>
                            <div class="invalid-feedback">Nilai kontrak harus diisi</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="jumlah_termin" class="form-label">Jumlah Termin</label>
                            <input type="number" class="form-control" id="jumlah_termin" name="jumlah_termin" value="<?php echo $payment['jumlah_termin']; ?>" min="1">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="pembayaran_termin_ke" class="form-label">Pembayaran Termin Ke-</label>
                            <input type="number" class="form-control" id="pembayaran_termin_ke" name="pembayaran_termin_ke" value="<?php echo $payment['pembayaran_termin_ke']; ?>" min="1">
                        </div>
                    </div>
                </div>

                <!-- Informasi Status dan Dokumen -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Status dan Dokumen</h5>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="status" class="form-label">Status <span class="text-danger">*</span></label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="">Pilih Status</option>
                                <option value="Dalam Proses" <?php echo $payment['status'] == 'Dalam Proses' ? 'selected' : ''; ?>>Dalam Proses</option>
                                <option value="Tertunda" <?php echo $payment['status'] == 'Tertunda' ? 'selected' : ''; ?>>Tertunda</option>
                                <option value="Selesai" <?php echo $payment['status'] == 'Selesai' ? 'selected' : ''; ?>>Selesai</option>
                            </select>
                            <div class="invalid-feedback">Status harus dipilih</div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="file_spk" class="form-label">File SPK</label>
                            <input type="file" class="form-control" id="file_spk" name="file_spk">
                            <?php if ($payment['file_spk']): ?>
                                <small class="text-muted">File saat ini: <?php echo htmlspecialchars($payment['file_spk']); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="file_pks" class="form-label">File PKS</label>
                            <input type="file" class="form-control" id="file_pks" name="file_pks">
                            <?php if ($payment['file_pks']): ?>
                                <small class="text-muted">File saat ini: <?php echo htmlspecialchars($payment['file_pks']); ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Informasi PIC -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi PIC</h5>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="pic_divisi" class="form-label">PIC Divisi</label>
                            <input type="text" class="form-control" id="pic_divisi" name="pic_divisi" value="<?php echo htmlspecialchars($payment['pic_divisi']); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="pic_vendor" class="form-label">PIC Vendor</label>
                            <input type="text" class="form-control" id="pic_vendor" name="pic_vendor" value="<?php echo htmlspecialchars($payment['pic_vendor']); ?>">
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label for="pic_draft_pks" class="form-label">PIC Draft PKS</label>
                            <input type="text" class="form-control" id="pic_draft_pks" name="pic_draft_pks" value="<?php echo htmlspecialchars($payment['pic_draft_pks']); ?>">
                        </div>
                    </div>
                </div>

                <!-- Informasi Tambahan -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="border-bottom pb-2">Informasi Tambahan</h5>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="opini_verifikator" class="form-label">Opini Verifikator</label>
                            <textarea class="form-control" id="opini_verifikator" name="opini_verifikator" rows="3"><?php echo htmlspecialchars($payment['opini_verifikator']); ?></textarea>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="mb-3">
                            <label for="keterangan" class="form-label">Keterangan</label>
                            <textarea class="form-control" id="keterangan" name="keterangan" rows="3"><?php echo htmlspecialchars($payment['keterangan']); ?></textarea>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12 text-end">
                        <button type="reset" class="btn btn-secondary me-2">Reset</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i> Simpan Perubahan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()

// Currency input formatting
document.querySelectorAll('.currency-input').forEach(function(input) {
    input.addEventListener('input', function(e) {
        let value = e.target.value.replace(/\D/g, "")
        value = new Intl.NumberFormat('id-ID').format(value)
        e.target.value = value
    })
})
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
