<?php
// Ensure user is logged in
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pembayaran Pengadaan - Print</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        @media print {
            @page {
                size: landscape;
                margin: 1cm;
            }
            
            body {
                padding: 0;
                margin: 0;
            }
            
            .no-print {
                display: none !important;
            }
            
            .table {
                font-size: 12px;
            }
            
            .table td, .table th {
                padding: 4px;
            }
            
            .container-fluid {
                width: 100% !important;
                padding: 0 !important;
                margin: 0 !important;
            }
        }
        
        .table th {
            background-color: #f8f9fa !important;
        }
        
        .print-header {
            margin-bottom: 20px;
        }
        
        .print-footer {
            margin-top: 20px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="container-fluid p-4">
        <!-- Print Header -->
        <div class="print-header">
            <div class="row">
                <div class="col-12 text-center">
                    <h3>Daftar Pembayaran Pengadaan</h3>
                    <p class="mb-0">Tanggal Cetak: <?php echo date('d/m/Y H:i:s'); ?></p>
                </div>
            </div>
        </div>

        <!-- Print Button -->
        <div class="no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <a href="index.php?page=dashboard" class="btn btn-secondary ms-2">
                <i class="fas fa-arrow-left me-1"></i> Kembali
            </a>
        </div>

        <!-- Payments Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Vendor</th>
                        <th>Nama Pengadaan</th>
                        <th>Metode</th>
                        <th>Nomor SPK</th>
                        <th>Tgl Terbit SPK</th>
                        <th>Tgl Akhir SPK</th>
                        <th>Nilai Kontrak</th>
                        <th>Status</th>
                        <th>PIC Divisi</th>
                        <th>Jatuh Tempo</th>
                        <th>Termin</th>
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
                        <td><?php echo date('d/m/Y', strtotime($payment['tanggal_terbit_spk'])); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($payment['tanggal_akhir_spk'])); ?></td>
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
                        <td><?php echo htmlspecialchars($payment['pic_divisi']); ?></td>
                        <td><?php echo date('d/m/Y', strtotime($payment['jatuh_tempo'])); ?></td>
                        <td class="text-center">
                            <?php echo $payment['pembayaran_termin_ke']; ?>/<?php echo $payment['jumlah_termin']; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Print Footer -->
        <div class="print-footer">
            <div class="row">
                <div class="col-6">
                    <p class="mb-0">Dicetak oleh: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </div>
                <div class="col-6 text-end">
                    <p class="mb-0">Monitoring Pembayaran Pengadaan</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
