<?php
require_once __DIR__.'/../templates/header.php';

$auth = new Auth($db->pdo());
$auth->requireRole(['administrator','petugas']);

$bookModel = new BookModel($db->pdo());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $coverName = null;

    /* ======================
       HANDLE FILE UPLOAD
       ====================== */
    if (!empty($_FILES['cover']['name'])) {

        $folder = __DIR__ . "/uploads/cover/";

        // create folder jika blm ada
        if (!is_dir($folder)) {
            mkdir($folder, 0777, true);
        }

        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $allowed)) {

            $coverName = uniqid('cover_') . "." . $ext;

            $target = $folder . $coverName;

            move_uploaded_file($_FILES['cover']['tmp_name'], $target);

        } else {
            $err = "Format file tidak valid. Hanya jpg/jpeg/png/webp.";
        }
    }

    if (empty($err)) {
        $_POST['cover'] = $coverName;
        $bookModel->create($_POST);
        header("Location: books.php");
        exit;
    }
}
?>

<style>

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

    .edit-title {
        font-size: 1.9rem;
        font-weight: 800;
        color: #d8b4fe;
        text-shadow: 0 0 20px rgba(168,85,247,.55);
    }

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

    <h2 class="edit-title mb-6">Tambah Buku</h2>

    <?php if (!empty($err)): ?>
        <div class="text-red-400 mb-4"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <form method="post" enctype="multipart/form-data" class="space-y-5">

        <div>
            <label>Judul</label>
            <input name="judul" required>
        </div>

        <div>
            <label>Penulis</label>
            <input name="penulis">
        </div>

        <div>
            <label>Penerbit</label>
            <input name="penerbit">
        </div>

        <div>
            <label>Tahun Terbit</label>
            <input type="number" name="tahun_terbit">
        </div>

        <div>
            <label>Stok</label>
            <input type="number" name="stok" value="1">
        </div>

        <!-- COVER UPLOAD -->
        <div>
            <label>Cover Buku</label>
            <input type="file" name="cover" accept="image/*">
        </div>

        <div class="flex justify-between mt-6">
            <a href="books.php" class="btn-cancel">Batal</a>
            <button class="btn-purple">Simpan</button>
        </div>

    </form>
</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
