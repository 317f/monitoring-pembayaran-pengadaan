<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../models/Payment.php';

class PaymentController {
    private $paymentModel;
    private $uploadDir;

    public function __construct($pdo) {
        $this->paymentModel = new Payment($pdo);
        $this->uploadDir = __DIR__ . '/../public/uploads/';
        
        // Create upload directory if it doesn't exist
        if (!file_exists($this->uploadDir)) {
            mkdir($this->uploadDir, 0777, true);
        }
    }

    public function index() {
        // Get statistics for dashboard
        $stats = $this->paymentModel->getPaymentStatistics();
        
        // Get due payments for reminders
        $duePayments = $this->paymentModel->getDuePayments();
        
        // Get all payments
        $payments = $this->paymentModel->getAllPayments();
        
        require_once __DIR__ . '/../views/payments/index.php';
    }

    public function create() {
        if (!isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Handle file uploads
                $fileSpk = $this->handleFileUpload('file_spk');
                $filePks = $this->handleFileUpload('file_pks');
                $dokumenHasil = $this->handleFileUpload('dokumen_hasil_pekerjaan');

                $data = [
                    'nama_vendor' => sanitize($_POST['nama_vendor']),
                    'nama_pengadaan' => sanitize($_POST['nama_pengadaan']),
                    'metode_pengadaan' => sanitize($_POST['metode_pengadaan']),
                    'nomor_spk' => sanitize($_POST['nomor_spk']),
                    'tanggal_terbit_spk' => $_POST['tanggal_terbit_spk'],
                    'tanggal_akhir_spk' => $_POST['tanggal_akhir_spk'],
                    'nilai_kontrak' => str_replace([',', '.'], '', $_POST['nilai_kontrak']),
                    'status' => sanitize($_POST['status']),
                    'opini_verifikator' => sanitize($_POST['opini_verifikator']),
                    'keterangan' => sanitize($_POST['keterangan']),
                    'user_id' => $_SESSION['user_id'],
                    'pic_divisi' => sanitize($_POST['pic_divisi']),
                    'pic_vendor' => sanitize($_POST['pic_vendor']),
                    'kontak_vendor' => sanitize($_POST['kontak_vendor']),
                    'pic_draft_pks' => sanitize($_POST['pic_draft_pks']),
                    'file_spk' => $fileSpk,
                    'file_pks' => $filePks,
                    'jatuh_tempo' => $_POST['jatuh_tempo'],
                    'jumlah_termin' => (int)$_POST['jumlah_termin'],
                    'pembayaran_termin_ke' => (int)$_POST['pembayaran_termin_ke'],
                    'jumlah_pembayaran' => str_replace([',', '.'], '', $_POST['jumlah_pembayaran']),
                    'nomor_lpt' => sanitize($_POST['nomor_lpt']),
                    'tanggal_lpt_terbit' => $_POST['tanggal_lpt_terbit'],
                    'tanggal_posting_pembayaran' => $_POST['tanggal_posting_pembayaran'],
                    'dokumen_hasil_pekerjaan' => $dokumenHasil,
                    'kekurangan_dokumen_vendor' => sanitize($_POST['kekurangan_dokumen_vendor']),
                    'kekurangan_dokumen_internal' => sanitize($_POST['kekurangan_dokumen_internal'])
                ];

                if ($this->paymentModel->createPayment($data)) {
                    $_SESSION['success'] = 'Data pembayaran berhasil ditambahkan';
                    header('Location: index.php?page=payments');
                    exit;
                } else {
                    $error = 'Gagal menambahkan data pembayaran';
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $error = 'Terjadi kesalahan sistem';
            }
        }

        require_once __DIR__ . '/../views/payments/create.php';
    }

    public function edit($id) {
        if (!isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        $payment = $this->paymentModel->getPaymentById($id);
        if (!$payment) {
            header('Location: index.php?page=payments');
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'nama_vendor' => sanitize($_POST['nama_vendor']),
                    'nama_pengadaan' => sanitize($_POST['nama_pengadaan']),
                    'metode_pengadaan' => sanitize($_POST['metode_pengadaan']),
                    'nomor_spk' => sanitize($_POST['nomor_spk']),
                    'tanggal_terbit_spk' => $_POST['tanggal_terbit_spk'],
                    'tanggal_akhir_spk' => $_POST['tanggal_akhir_spk'],
                    'nilai_kontrak' => str_replace([',', '.'], '', $_POST['nilai_kontrak']),
                    'status' => sanitize($_POST['status']),
                    'opini_verifikator' => sanitize($_POST['opini_verifikator']),
                    'keterangan' => sanitize($_POST['keterangan']),
                    'pic_divisi' => sanitize($_POST['pic_divisi']),
                    'pic_vendor' => sanitize($_POST['pic_vendor']),
                    'kontak_vendor' => sanitize($_POST['kontak_vendor']),
                    'pic_draft_pks' => sanitize($_POST['pic_draft_pks']),
                    'jatuh_tempo' => $_POST['jatuh_tempo'],
                    'jumlah_termin' => (int)$_POST['jumlah_termin'],
                    'pembayaran_termin_ke' => (int)$_POST['pembayaran_termin_ke'],
                    'jumlah_pembayaran' => str_replace([',', '.'], '', $_POST['jumlah_pembayaran']),
                    'nomor_lpt' => sanitize($_POST['nomor_lpt']),
                    'tanggal_lpt_terbit' => $_POST['tanggal_lpt_terbit'],
                    'tanggal_posting_pembayaran' => $_POST['tanggal_posting_pembayaran'],
                    'kekurangan_dokumen_vendor' => sanitize($_POST['kekurangan_dokumen_vendor']),
                    'kekurangan_dokumen_internal' => sanitize($_POST['kekurangan_dokumen_internal'])
                ];

                // Handle file uploads if new files are provided
                if (!empty($_FILES['file_spk']['name'])) {
                    $data['file_spk'] = $this->handleFileUpload('file_spk');
                }
                if (!empty($_FILES['file_pks']['name'])) {
                    $data['file_pks'] = $this->handleFileUpload('file_pks');
                }
                if (!empty($_FILES['dokumen_hasil_pekerjaan']['name'])) {
                    $data['dokumen_hasil_pekerjaan'] = $this->handleFileUpload('dokumen_hasil_pekerjaan');
                }

                if ($this->paymentModel->updatePayment($id, $data)) {
                    $_SESSION['success'] = 'Data pembayaran berhasil diperbarui';
                    header('Location: index.php?page=payments');
                    exit;
                } else {
                    $error = 'Gagal memperbarui data pembayaran';
                }
            } catch (Exception $e) {
                error_log($e->getMessage());
                $error = 'Terjadi kesalahan sistem';
            }
        }

        require_once __DIR__ . '/../views/payments/edit.php';
    }

    public function delete($id) {
        if (!isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if ($this->paymentModel->deletePayment($id)) {
                $_SESSION['success'] = 'Data pembayaran berhasil dihapus';
            } else {
                $_SESSION['error'] = 'Gagal menghapus data pembayaran';
            }
        }

        header('Location: index.php?page=payments');
        exit;
    }

    public function view($id) {
        if (!isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        $payment = $this->paymentModel->getPaymentById($id);
        if (!$payment) {
            header('Location: index.php?page=payments');
            exit;
        }

        require_once __DIR__ . '/../views/payments/view.php';
    }

    public function export() {
        if (!isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        $payments = $this->paymentModel->getAllPayments();
        
        // Set headers for Excel download
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="daftar_pembayaran.xls"');
        header('Cache-Control: max-age=0');
        
        require_once __DIR__ . '/../views/payments/export.php';
    }

    public function print() {
        if (!isLoggedIn()) {
            header('Location: index.php?page=login');
            exit;
        }

        $payments = $this->paymentModel->getAllPayments();
        require_once __DIR__ . '/../views/payments/print.php';
    }

    private function handleFileUpload($fieldName) {
        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$fieldName];
            $fileName = uniqid() . '_' . basename($file['name']);
            $targetPath = $this->uploadDir . $fileName;

            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return $fileName;
            }
        }
        return null;
    }
}
