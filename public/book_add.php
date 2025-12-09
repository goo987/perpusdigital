<?php
require_once __DIR__.'/../templates/header.php'; 

$auth = new Auth($db->pdo());
// Instansiasi class Auth

$auth->requireRole(['administrator','petugas']); 
// Hanya admin/petugas yang boleh menambah buku

$bookModel = new BookModel($db->pdo());

// HANDLE FORM SUBMIT
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $coverName = null; // default tanpa cover

      // HANDLE FILE UPLOAD
    if (!empty($_FILES['cover']['name'])) {

        $folder = __DIR__ . "/uploads/cover/"; 
        // lokasi penyimpanan cover

        if (!is_dir($folder)) { 
            mkdir($folder, 0777, true); 
            // buat folder jika belum ada
        }

        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $allowed)) {

            $coverName = uniqid('cover_') . "." . $ext;
            // nama file acak biar aman

            $target = $folder . $coverName;

            move_uploaded_file($_FILES['cover']['tmp_name'], $target);
            // simpan file ke folder

        } else {
            $err = "Format file tidak valid. Hanya jpg/jpeg/png/webp.";
        }
    }

    // Jika tidak ada error maka insert ke DB
    if (empty($err)) {
        $_POST['cover'] = $coverName;
        $bookModel->create($_POST);
        header("Location: books.php"); 
        exit;
    }
}
?>

<style>
/* CARD TAMBAH BUKU */
.edit-card {
    background: linear-gradient(160deg, #0c0c0d, #111113 60%, #0c0c0d);
    border: 1px solid rgba(168,85,247,0.15);
    border-radius: 14px;
    padding: 30px;
    box-shadow: 0 0 25px rgba(168,85,247,0.18);
    transition: .35s;
}
.edit-card:hover {
    border-color: rgba(168,85,247,0.35);
    box-shadow: 0 0 30px rgba(168,85,247,0.35);
    transform: translateY(-3px);
}

/* TITLE */
.edit-title {
    font-size: 1.9rem;
    font-weight: 800;
    color: #d8b4fe;
    text-shadow: 0 0 20px rgba(168,85,247,.55);
}

/* INPUT */
input {
    background: #1a1a1c;
    border: 1px solid rgba(255,255,255,0.13);
    color: #e5e5e5;
    border-radius: 10px;
    padding: 10px;
    width: 100%;
    transition: .3s;
}
input:focus {
    border-color: #a855f7;
    box-shadow: 0 0 12px rgba(168,85,247,.45);
    outline: none;
}

/* BUTTON SIMPAN */
.btn-purple {
    background: #7e22ce;
    padding: 10px 26px;
    border-radius: 10px;
    font-weight: 600;
    transition: .3s;
    color: white;
}
.btn-purple:hover {
    background: #6b13c0;
    box-shadow: 0 0 16px rgba(168,85,247,.45);
    transform: translateY(-2px);
}

/* BUTTON BATAL */
.btn-cancel {
    background: #3a3a3d;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 500;
    color: #e5e5e5;
    transition: .25s;
}
.btn-cancel:hover {
    background: #525257;
    transform: translateY(-2px);
}

label {
    color: #dcdcdc;
    font-weight: 500;
    margin-bottom: 4px;
    display: block;
}
</style>

<div class="max-w-xl mx-auto mt-14 edit-card">

    <!-- TITLE -->
    <h2 class="edit-title mb-6">Tambah Buku</h2>

    <!-- ERROR MESSAGE -->
    <?php if (!empty($err)): ?>
        <div class="text-red-400 mb-4"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <!-- FORM TAMBAH BUKU -->
    <form method="post" enctype="multipart/form-data" class="space-y-5">

        <!-- JUDUL -->
        <div>
            <label>Judul</label>
            <input name="judul" required>
        </div>

        <!-- PENULIS -->
        <div>
            <label>Penulis</label>
            <input name="penulis">
        </div>

        <!-- PENERBIT -->
        <div>
            <label>Penerbit</label>
            <input name="penerbit">
        </div>

        <!-- TAHUN TERBIT -->
        <div>
            <label>Tahun Terbit</label>
            <input type="number" name="tahun_terbit">
        </div>

        <!-- STOK -->
        <div>
            <label>Stok</label>
            <input type="number" name="stok" value="1">
        </div>

        <!-- COVER -->
        <div>
            <label>Cover Buku</label>
            <input type="file" name="cover" accept="image/*">
        </div>

        <!-- BUTTON -->
        <div class="flex justify-between mt-6">
            <a href="books.php" class="btn-cancel">Batal</a>
            <button class="btn-purple">Simpan</button>
        </div>

    </form>
</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
