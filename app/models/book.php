<?php
// app/Models/Book.php

class BookModel {
    private $db; // koneksi database (PDO)

    public function __construct($db){
        $this->db = $db;
    }

    /* ===============================
       AMBIL SEMUA BUKU TANPA FILTER
    =============================== */
    public function all(){
        $stmt = $this->db->query("SELECT * FROM buku ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    /* ===============================
       GET SEMUA BUKU + FILTER TANGGAL
    =============================== */
    public function getAll($from = null, $to = null){
        $sql = "SELECT * FROM buku WHERE 1";
        $params = [];

        if ($from) {
            $sql .= " AND created_at >= ?";
            $params[] = $from;
        }

        if ($to) {
            $sql .= " AND created_at <= ?";
            $params[] = $to;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /* ===============================
       LAPORAN BUKU
    =============================== */
    public function reportAll($from = null, $to = null){
        $sql = "SELECT * FROM buku WHERE 1";
        $params = [];

        if ($from) {
            $sql .= " AND created_at >= ?";
            $params[] = $from;
        }

        if ($to) {
            $sql .= " AND created_at <= ?";
            $params[] = $to;
        }

        $sql .= " ORDER BY id DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }

    /* ===============================
       FIND BUKU BY ID
    =============================== */
    public function find($id){
        $stmt = $this->db->prepare("SELECT * FROM buku WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /* ===============================
       CREATE BUKU + COVER
    =============================== */
    public function create($d){
        $stmt = $this->db->prepare("
            INSERT INTO buku (judul, penulis, penerbit, tahun_terbit, stok, cover)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $d['judul'],
            $d['penulis'],
            $d['penerbit'],
            $d['tahun_terbit'],
            $d['stok'],
            $d['cover'] ?? null  // cover optional
        ]);
    }

    /* ===============================
       UPDATE BUKU + COVER
    =============================== */
    public function update($id, $d){
        $stmt = $this->db->prepare("
            UPDATE buku 
            SET judul = ?, penulis = ?, penerbit = ?, tahun_terbit = ?, stok = ?, cover = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $d['judul'],
            $d['penulis'],
            $d['penerbit'],
            $d['tahun_terbit'],
            $d['stok'],
            $d['cover'] ?? null,
            $id
        ]);
    }

    /* ===============================
       DELETE BUKU
    =============================== */
    public function delete($id){
        $stmt = $this->db->prepare("DELETE FROM buku WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /* ===============================
       HITUNG TOTAL BUKU
    =============================== */
    public function countBooks($from = null, $to = null){
        $sql = "SELECT COUNT(*) FROM buku WHERE 1";
        $params = [];

        if ($from) {
            $sql .= " AND created_at >= ?";
            $params[] = $from;
        }

        if ($to) {
            $sql .= " AND created_at <= ?";
            $params[] = $to;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchColumn();
    }

    /* ===============================
        FUNGSI TAMBAHAN COVER
    =============================== */

    /* UPDATE COVER SAJA */
    public function updateCover($id, $cover){
        $stmt = $this->db->prepare("
            UPDATE buku SET cover = ? WHERE id = ?
        ");
        return $stmt->execute([$cover, $id]);
    }

    /* HAPUS COVER (SET NULL) */
    public function deleteCover($id){
        $stmt = $this->db->prepare("
            UPDATE buku SET cover = NULL WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
}
