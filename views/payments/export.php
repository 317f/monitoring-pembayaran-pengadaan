<?php
// Disable caching
header("Cache-Control: no-cache, must-revalidate");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");

// Set headers for Excel download
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=\"daftar_pembayaran_" . date('Y-m-d') . ".xls\"");
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid #000000;
            padding: 5px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
    </style>
</head>
<body>
    <h2>Daftar Pembayaran Pengadaan</h2>
    <p>Tanggal Export: <?php echo date('d/m/Y H:i:s'); ?></p>
    
    <table>
        <thead>
            <tr>
                <th>No</th>
                <th>Nama Vendor</th>
                <th>Nama Pengadaan</th>
                <th>Metode Pengadaan</th>
                <th>Nomor SPK</th>
                <th>Tanggal Terbit SPK</th>
                <th>Tanggal Akhir SPK</th>
                <th>Nilai Kontrak</th>
                <th>Status</th>
                <th>PIC Divisi</th>
                <th>PIC Vendor</th>
                <th>Kontak Vendor</th>
                <th>Jatuh Tempo</th>
                <th>Jumlah Termin</th>
                <th>Termin Ke</th>
                <th>Jumlah Pembayaran</th>
                <th>Nomor LPT</th>
                <th>Tanggal LPT</th>
                <th>Tanggal Posting</th>
                <th>Opini Verifikator</th>
                <th>Keterangan</th>
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
                <td class="text-right"><?php echo number_format($payment['nilai_kontrak'], 0, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($payment['status']); ?></td>
                <td><?php echo htmlspecialchars($payment['pic_divisi']); ?></td>
                <td><?php echo htmlspecialchars($payment['pic_vendor']); ?></td>
                <td><?php echo htmlspecialchars($payment['kontak_vendor']); ?></td>
                <td><?php echo date('d/m/Y', strtotime($payment['jatuh_tempo'])); ?></td>
                <td class="text-right"><?php echo $payment['jumlah_termin']; ?></td>
                <td class="text-right"><?php echo $payment['pembayaran_termin_ke']; ?></td>
                <td class="text-right"><?php echo number_format($payment['jumlah_pembayaran'], 0, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($payment['nomor_lpt']); ?></td>
                <td><?php echo $payment['tanggal_lpt_terbit'] ? date('d/m/Y', strtotime($payment['tanggal_lpt_terbit'])) : ''; ?></td>
                <td><?php echo $payment['tanggal_posting_pembayaran'] ? date('d/m/Y', strtotime($payment['tanggal_posting_pembayaran'])) : ''; ?></td>
                <td><?php echo htmlspecialchars($payment['opini_verifikator']); ?></td>
                <td><?php echo htmlspecialchars($payment['keterangan']); ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
