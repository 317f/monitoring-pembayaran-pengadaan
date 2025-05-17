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
        <h2 class="mb-0">Detail Pembayaran</h2>
        <div>
            <a href="index.php?page=payments&action=edit&id=<?php echo $payment['id']; ?>" class="btn btn-warning me-2">
                <i class="fas fa-edit me-1"></i> Edit
            </a>
            <a href="index.php?page=dashboard" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Status Badge -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card bg-light">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-1">Status Pembayaran</h6>
                            <?php
                            $statusClass = '';
                            switch ($payment['status']) {
                                case 'Dalam Proses':
                                    $statusClass = 'warning';
                                    break;
                                case 'Tertunda':
                                    $statusClass = 'danger';
                                    break;
                                case 'Selesai':
                                    $statusClass = 'success';
                                    break;
                            }
                            ?>
                            <span class="badge bg-<?php echo $statusClass; ?> fs-6">
                                <?php echo htmlspecialchars($payment['status']); ?>
                            </span>
                        </div>
                        <div class="text-end">
                            <h6 class="text-muted mb-1">Nilai Kontrak</h6>
                            <h4 class="mb-0">Rp <?php echo number_format($payment['nilai_kontrak'], 0, ',', '.'); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Informasi Utama -->
        <div class="col-md-8">
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Utama</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h6 class="text-muted">Nama Pengadaan</h6>
                            <p class="mb-4"><?php echo htmlspecialchars($payment['nama_pengadaan']); ?></p>

                            <h6 class="text-muted">Metode Pengadaan</h6>
                            <p class="mb-4"><?php echo htmlspecialchars($payment['metode_pengadaan']); ?></p>

                            <h6 class="text-muted">Nomor SPK</h6>
                            <p class="mb-0"><?php echo htmlspecialchars($payment['nomor_spk']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Tanggal Terbit SPK</h6>
                            <p class="mb-4"><?php echo date('d/m/Y', strtotime($payment['tanggal_terbit_spk'])); ?></p>

                            <h6 class="text-muted">Tanggal Akhir SPK</h6>
                            <p class="mb-4"><?php echo date('d/m/Y', strtotime($payment['tanggal_akhir_spk'])); ?></p>

                            <h6 class="text-muted">Jatuh Tempo</h6>
                            <p class="mb-0 due-date" data-date="<?php echo $payment['jatuh_tempo']; ?>">
                                <?php echo date('d/m/Y', strtotime($payment['jatuh_tempo'])); ?>
                            </p>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted">Jumlah Termin</h6>
                            <p class="mb-4"><?php echo $payment['jumlah_termin']; ?></p>

                            <h6 class="text-muted">Pembayaran Termin Ke-</h6>
                            <p class="mb-0"><?php echo $payment['pembayaran_termin_ke']; ?></p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted">Jumlah Pembayaran</h6>
                            <p class="mb-4">Rp <?php echo number_format($payment['jumlah_pembayaran'], 0, ',', '.'); ?></p>

                            <h6 class="text-muted">Tanggal Posting Pembayaran</h6>
                            <p class="mb-0">
                                <?php echo $payment['tanggal_posting_pembayaran'] ? date('d/m/Y', strtotime($payment['tanggal_posting_pembayaran'])) : '-'; ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Opini dan Keterangan -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Opini & Keterangan</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted">Opini Verifikator</h6>
                    <p class="mb-4"><?php echo nl2br(htmlspecialchars($payment['opini_verifikator'] ?: '-')); ?></p>

                    <h6 class="text-muted">Keterangan</h6>
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($payment['keterangan'] ?: '-')); ?></p>
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-md-4">
            <!-- Informasi Vendor -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi Vendor</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted">Nama Vendor</h6>
                    <p class="mb-4"><?php echo htmlspecialchars($payment['nama_vendor']); ?></p>

                    <h6 class="text-muted">Kontak Vendor</h6>
                    <p class="mb-0"><?php echo htmlspecialchars($payment['kontak_vendor'] ?: '-'); ?></p>
                </div>
            </div>

            <!-- Informasi PIC -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Informasi PIC</h5>
                </div>
                <div class="card-body">
                    <h6 class="text-muted">PIC Divisi</h6>
                    <p class="mb-4"><?php echo htmlspecialchars($payment['pic_divisi'] ?: '-'); ?></p>

                    <h6 class="text-muted">PIC Vendor</h6>
                    <p class="mb-4"><?php echo htmlspecialchars($payment['pic_vendor'] ?: '-'); ?></p>

                    <h6 class="text-muted">PIC Draft PKS</h6>
                    <p class="mb-0"><?php echo htmlspecialchars($payment['pic_draft_pks'] ?: '-'); ?></p>
                </div>
            </div>

            <!-- Dokumen -->
            <div class="card shadow-sm mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">Dokumen</h5>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h6 class="text-muted">File SPK</h6>
                        <?php if ($payment['file_spk']): ?>
                            <a href="public/uploads/<?php echo htmlspecialchars($payment['file_spk']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-download me-1"></i> Download SPK
                            </a>
                        <?php else: ?>
                            <p class="text-muted mb-0">Tidak ada file</p>
                        <?php endif; ?>
                    </div>

                    <div class="mb-4">
                        <h6 class="text-muted">File PKS</h6>
                        <?php if ($payment['file_pks']): ?>
                            <a href="public/uploads/<?php echo htmlspecialchars($payment['file_pks']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-download me-1"></i> Download PKS
                            </a>
                        <?php else: ?>
                            <p class="text-muted mb-0">Tidak ada file</p>
                        <?php endif; ?>
                    </div>

                    <div>
                        <h6 class="text-muted">Dokumen Hasil Pekerjaan</h6>
                        <?php if ($payment['dokumen_hasil_pekerjaan']): ?>
                            <a href="public/uploads/<?php echo htmlspecialchars($payment['dokumen_hasil_pekerjaan']); ?>" class="btn btn-sm btn-outline-primary" target="_blank">
                                <i class="fas fa-download me-1"></i> Download Dokumen
                            </a>
                        <?php else: ?>
                            <p class="text-muted mb-0">Tidak ada file</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
