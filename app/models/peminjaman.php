<?php

class PeminjamanModel {
    private $db;

    public function __construct($db){
        $this->db = $db;
    }

    /**
     * Ambil semua data peminjaman untuk laporan (dengan filter)
     */
    public function reportAll($from = null, $to = null){
        $sql = "
            SELECT p.*, u.username, b.judul
            FROM peminjaman p
            JOIN users u ON p.user_id = u.id
            JOIN buku b ON p.buku_id = b.id
            WHERE 1
        ";

        $params = [];

        if ($from) {
            $sql .= " AND p.created_at >= ?";
            $params[] = $from;
        }

        if ($to) {
            $sql .= " AND p.created_at <= ?";
            $params[] = $to;
        }

        $sql .= " ORDER BY p.id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /**
     * Ambil semua peminjaman (untuk halaman borrow.php)
     */
    public function all(){
        $stmt = $this->db->prepare("
            SELECT p.*, u.username, b.judul, b.cover
            FROM peminjaman p
            JOIN users u ON p.user_id = u.id
            JOIN buku b ON p.buku_id = b.id
            ORDER BY p.id DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Cari peminjaman berdasarkan ID
     */
    public function find($id){
        $stmt = $this->db->prepare("
            SELECT p.*, u.username, b.judul, b.cover
            FROM peminjaman p
            JOIN users u ON p.user_id = u.id
            JOIN buku b ON p.buku_id = b.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /**
     * Tambah peminjaman
     * Return:
     *   - "OK"
     *   - "STOK_HABIS"
     *   - "SUDAH_DIPINJAM"
     */
    public function borrow($user_id, $buku_id, $tanggal_pinjam){

        // CEK apakah user sudah meminjam buku ini dan BELUM mengembalikan
        $stmt = $this->db->prepare("
            SELECT COUNT(*) FROM peminjaman
            WHERE user_id = ? AND buku_id = ? AND status = 'dipinjam'
        ");
        $stmt->execute([$user_id, $buku_id]);
        $sudah = $stmt->fetchColumn();

        if ($sudah > 0) {
            return "SUDAH_DIPINJAM";
        }

        // CEK STOK
        $stmt = $this->db->prepare("SELECT stok FROM buku WHERE id = ?");
        $stmt->execute([$buku_id]);
        $stok = $stmt->fetchColumn();

        if ($stok <= 0) {
            return "STOK_HABIS";
        }

        // INSERT PEMINJAMAN
        $stmt = $this->db->prepare("
            INSERT INTO peminjaman (user_id, buku_id, tanggal_pinjam, status)
            VALUES (?, ?, ?, 'dipinjam')
        ");
        $stmt->execute([$user_id, $buku_id, $tanggal_pinjam]);

        // KURANGI STOK
        $stmt = $this->db->prepare("UPDATE buku SET stok = stok - 1 WHERE id = ?");
        $stmt->execute([$buku_id]);

        return "OK";
    }

    /**
     * Kembalikan buku
     */
    public function returnBook($id, $tanggal_kembali){

        // Ambil info buku yang dikembalikan
        $stmt = $this->db->prepare("
            SELECT buku_id FROM peminjaman WHERE id = ?
        ");
        $stmt->execute([$id]);
        $buku_id = $stmt->fetchColumn();

        // Update status peminjaman
        $stmt = $this->db->prepare("
            UPDATE peminjaman
            SET status = 'dikembalikan', tanggal_kembali = ?
            WHERE id = ?
        ");
        $stmt->execute([$tanggal_kembali, $id]);

        // Tambah stok kembali
        if ($buku_id) {
            $stmt = $this->db->prepare("UPDATE buku SET stok = stok + 1 WHERE id = ?");
            $stmt->execute([$buku_id]);
        }

        return true;
    }
}
