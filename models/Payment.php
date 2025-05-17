<?php
class Payment {
    private $db;

    public function __construct($pdo) {
        $this->db = $pdo;
    }

    public function getAllPayments() {
        try {
            $stmt = $this->db->query("
                SELECT p.*, u.username as created_by 
                FROM payments p 
                LEFT JOIN users u ON p.user_id = u.id 
                ORDER BY p.created_at DESC
            ");
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get payments error: " . $e->getMessage());
            return [];
        }
    }

    public function getPaymentById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT p.*, u.username as created_by 
                FROM payments p 
                LEFT JOIN users u ON p.user_id = u.id 
                WHERE p.id = :id
            ");
            $stmt->execute(['id' => $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get payment error: " . $e->getMessage());
            return false;
        }
    }

    public function createPayment($data) {
        try {
            $sql = "INSERT INTO payments (
                nama_vendor, nama_pengadaan, metode_pengadaan, nomor_spk,
                tanggal_terbit_spk, tanggal_akhir_spk, nilai_kontrak, status,
                opini_verifikator, keterangan, user_id, pic_divisi, pic_vendor,
                kontak_vendor, pic_draft_pks, file_spk, file_pks, jatuh_tempo,
                jumlah_termin, pembayaran_termin_ke, jumlah_pembayaran, nomor_lpt,
                tanggal_lpt_terbit, tanggal_posting_pembayaran, dokumen_hasil_pekerjaan,
                kekurangan_dokumen_vendor, kekurangan_dokumen_internal
            ) VALUES (
                :nama_vendor, :nama_pengadaan, :metode_pengadaan, :nomor_spk,
                :tanggal_terbit_spk, :tanggal_akhir_spk, :nilai_kontrak, :status,
                :opini_verifikator, :keterangan, :user_id, :pic_divisi, :pic_vendor,
                :kontak_vendor, :pic_draft_pks, :file_spk, :file_pks, :jatuh_tempo,
                :jumlah_termin, :pembayaran_termin_ke, :jumlah_pembayaran, :nomor_lpt,
                :tanggal_lpt_terbit, :tanggal_posting_pembayaran, :dokumen_hasil_pekerjaan,
                :kekurangan_dokumen_vendor, :kekurangan_dokumen_internal
            )";

            $stmt = $this->db->prepare($sql);
            $stmt->execute($data);
            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            error_log("Create payment error: " . $e->getMessage());
            return false;
        }
    }

    public function updatePayment($id, $data) {
        try {
            $updates = [];
            $params = ['id' => $id];

            foreach ($data as $key => $value) {
                if ($key !== 'id') {
                    $updates[] = "$key = :$key";
                    $params[$key] = $value;
                }
            }

            $sql = "UPDATE payments SET " . implode(", ", $updates) . " WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (PDOException $e) {
            error_log("Update payment error: " . $e->getMessage());
            return false;
        }
    }

    public function deletePayment($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM payments WHERE id = :id");
            return $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log("Delete payment error: " . $e->getMessage());
            return false;
        }
    }

    public function getDuePayments($days = 7) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM payments 
                WHERE jatuh_tempo BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :days DAY)
                AND status != 'Selesai'
                ORDER BY jatuh_tempo ASC
            ");
            $stmt->execute(['days' => $days]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get due payments error: " . $e->getMessage());
            return [];
        }
    }

    public function getPaymentsByStatus($status) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM payments 
                WHERE status = :status 
                ORDER BY created_at DESC
            ");
            $stmt->execute(['status' => $status]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Get payments by status error: " . $e->getMessage());
            return [];
        }
    }

    public function getPaymentStatistics() {
        try {
            $stats = [
                'total' => 0,
                'dalam_proses' => 0,
                'tertunda' => 0,
                'selesai' => 0,
                'total_nilai_kontrak' => 0,
                'due_this_week' => 0
            ];

            // Get counts by status
            $stmt = $this->db->query("
                SELECT status, COUNT(*) as count, SUM(nilai_kontrak) as total_nilai
                FROM payments 
                GROUP BY status
            ");
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($results as $row) {
                $stats['total']++;
                $stats['total_nilai_kontrak'] += $row['total_nilai'];
                switch ($row['status']) {
                    case 'Dalam Proses':
                        $stats['dalam_proses'] = $row['count'];
                        break;
                    case 'Tertunda':
                        $stats['tertunda'] = $row['count'];
                        break;
                    case 'Selesai':
                        $stats['selesai'] = $row['count'];
                        break;
                }
            }

            // Get due this week count
            $stmt = $this->db->query("
                SELECT COUNT(*) 
                FROM payments 
                WHERE jatuh_tempo BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                AND status != 'Selesai'
            ");
            $stats['due_this_week'] = $stmt->fetchColumn();

            return $stats;
        } catch (PDOException $e) {
            error_log("Get payment statistics error: " . $e->getMessage());
            return false;
        }
    }

    public function uploadFile($file, $destination) {
        try {
            if ($file['error'] === UPLOAD_ERR_OK) {
                $tempName = $file['tmp_name'];
                $fileName = basename($file['name']);
                $uniqueName = uniqid() . '_' . $fileName;
                $uploadPath = $destination . $uniqueName;
                
                if (move_uploaded_file($tempName, $uploadPath)) {
                    return $uniqueName;
                }
            }
            return false;
        } catch (Exception $e) {
            error_log("File upload error: " . $e->getMessage());
            return false;
        }
    }
}
