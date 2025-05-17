<?php 
include __DIR__ . '/../layouts/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Dashboard</h2>
        <div>
            <a href="index.php?page=payments&action=create" class="btn btn-primary me-2">
                <i class="fas fa-plus me-1"></i> Tambah Pembayaran
            </a>
            <div class="btn-group">
                <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                        <a class="dropdown-item" href="index.php?page=payments&action=export">
                            <i class="fas fa-file-excel me-1"></i> Export Excel
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="index.php?page=payments&action=print" target="_blank">
                            <i class="fas fa-print me-1"></i> Print
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-primary text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Total Pembayaran</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['total']); ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-file-invoice fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-warning text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Dalam Proses</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['dalam_proses']); ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-clock fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-danger text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Tertunda</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['tertunda']); ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-pause-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card bg-success text-white mb-4">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-uppercase mb-1">Selesai</h6>
                            <h3 class="mb-0"><?php echo number_format($stats['selesai']); ?></h3>
                        </div>
                        <div class="stats-icon">
                            <i class="fas fa-check-circle fa-2x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Due Payments Alert -->
    <?php if (!empty($duePayments)): ?>
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <h5 class="alert-heading mb-2">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Pembayaran Jatuh Tempo
        </h5>
        <p class="mb-0">Terdapat <?php echo count($duePayments); ?> pembayaran yang akan jatuh tempo dalam 7 hari ke depan:</p>
        <ul class="list-unstyled mt-2 mb-0">
            <?php foreach ($duePayments as $payment): ?>
            <li>
                <i class="fas fa-arrow-right me-2"></i>
                <?php echo htmlspecialchars($payment['nama_pengadaan']); ?> - 
                Jatuh tempo: <strong><?php echo date('d/m/Y', strtotime($payment['jatuh_tempo'])); ?></strong>
            </li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    <?php endif; ?>

    <!-- Payments Table -->
    <div class="card shadow-sm">
        <div class="card-header">
            <h5 class="card-title mb-0">Daftar Pembayaran</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Vendor</th>
                            <th>Nama Pengadaan</th>
                            <th>Metode</th>
                            <th>Nomor SPK</th>
                            <th>Nilai Kontrak</th>
                            <th>Status</th>
                            <th>Jatuh Tempo</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $index => $payment): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($payment['nama_vendor']); ?></td>
                            <td><?php echo htmlspecialchars($payment['nama_pengadaan']); ?></td>
                            <td><?php echo htmlspecialchars($payment['metode_pengadaan']); ?></td>
                            <td><?php echo htmlspecialchars($payment['nomor_spk']); ?></td>
                            <td class="text-end">
                                <?php echo 'Rp ' . number_format($payment['nilai_kontrak'], 0, ',', '.'); ?>
                            </td>
                            <td>
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
                                <span class="badge bg-<?php echo $statusClass; ?>">
                                    <?php echo htmlspecialchars($payment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <span class="due-date" data-date="<?php echo $payment['jatuh_tempo']; ?>">
                                    <?php echo date('d/m/Y', strtotime($payment['jatuh_tempo'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="index.php?page=payments&action=view&id=<?php echo $payment['id']; ?>" 
                                       class="btn btn-sm btn-info" 
                                       data-bs-toggle="tooltip" 
                                       title="Lihat Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="index.php?page=payments&action=edit&id=<?php echo $payment['id']; ?>" 
                                       class="btn btn-sm btn-warning" 
                                       data-bs-toggle="tooltip" 
                                       title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete(<?php echo $payment['id']; ?>, 'pembayaran')"
                                            data-bs-toggle="tooltip" 
                                            title="Hapus">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                <form id="delete-form-<?php echo $payment['id']; ?>" 
                                      action="index.php?page=payments&action=delete&id=<?php echo $payment['id']; ?>" 
                                      method="POST" 
                                      style="display: none;">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
