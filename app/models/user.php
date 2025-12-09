<?php
// app/Models/User.php

class UserModel {
    private $db; // koneksi database (PDO)

    public function __construct($db){
        $this->db = $db; // simpan PDO ke variabel
    }

    /*
       GET ALL USER
       Untuk halaman admin: daftar user
    */
    public function all(){
        return $this->db->query("
            SELECT * FROM users 
            ORDER BY id DESC
        ")->fetchAll();
    }

    /*
       FIND USER BY ID
       Ambil 1 baris user berdasarkan ID
    */
    public function find($id){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    /*
       FIND USER BY USERNAME
       Digunakan untuk login
    */
    public function findByUsername($username){
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }

    /*
       FIND USER BY ID (alias)
       Alias untuk find()
    */
    public function findById($id){
        return $this->find($id);
    }

    /*
       CREATE USER
       Tambah akun baru
    */
    public function create($data){
        $stmt = $this->db->prepare("
            INSERT INTO users 
            (username, password, nama_lengkap, email, role, alamat)
            VALUES (?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $data['username'],
            $data['password'],
            $data['nama_lengkap'],
            $data['email'],
            $data['role'],
            $data['alamat']
        ]);
    }

    /*
       UPDATE USER TANPA PASSWORD
       (jika admin hanya ganti nama, role, email dll)
    */
    public function update($id, $data){
        $stmt = $this->db->prepare("
            UPDATE users SET
                username = ?,
                nama_lengkap = ?,
                email = ?,
                role = ?,
                alamat = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['username'],
            $data['nama_lengkap'],
            $data['email'],
            $data['role'],
            $data['alamat'],
            $id
        ]);
    }

    /*
       UPDATE USER + PASSWORD
       (jika password ikut diganti)
    */
    public function updateWithPassword($id, $data){
        $stmt = $this->db->prepare("
            UPDATE users SET
                username = ?,
                nama_lengkap = ?,
                email = ?,
                role = ?,
                alamat = ?,
                password = ?
            WHERE id = ?
        ");

        return $stmt->execute([
            $data['username'],
            $data['nama_lengkap'],
            $data['email'],
            $data['role'],
            $data['alamat'],
            $data['password'], // password baru
            $id
        ]);
    }

    /*
       DELETE USER
       Hapus akun dari database
    */
    public function delete($id){
        $stmt = $this->db->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /*
       COUNT REGISTRATIONS
       Hitung jumlah peminjam (role = peminjam)
       Untuk laporan pendaftaran
    */
    public function countRegistrations($from = null, $to = null){
        $sql = "SELECT COUNT(*) FROM users WHERE role='peminjam'";
        $params = [];

        if($from){
            $sql .= " AND created_at >= ?";
            $params[] = $from;
        }
        
        if($to){
            $sql .= " AND created_at <= ?";
            $params[] = $to;
        }

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchColumn(); // hanya angka
    }
}
