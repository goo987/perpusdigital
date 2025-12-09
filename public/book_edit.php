<?php
require_once __DIR__.'/../templates/header.php';

$auth = new Auth($db->pdo());
// Instansiasi class Auth

$auth->requireRole(['administrator','petugas']);
// Hanya admin/petugas yang boleh edit buku

$bookModel = new BookModel($db->pdo());
$id = intval($_GET['id'] ?? 0);
$book = $bookModel->find($id);

// jika id tidak ditemukan
if (!$book) {
    echo "Buku tidak ditemukan.";
    exit;
}

// HANDLE FORM UPDATE
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $coverName = $book['cover']; 
    // default: pakai cover lama

    // jika upload cover baru
    if (!empty($_FILES['cover']['name'])) {

        $folder = __DIR__ . "/uploads/cover/";

        // create folder jika belum ada
        if (!is_dir($folder)) mkdir($folder, 0777, true);

        $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg','jpeg','png','webp'];

        if (in_array($ext, $allowed)) {

            $coverName = uniqid('cover_') . "." . $ext;
            // nama file acak

            move_uploaded_file($_FILES['cover']['tmp_name'], $folder . $coverName);
            // simpan file baru

        } else {
            $err = "Format file tidak valid.";
        }
    }

    // jika tidak ada error, update DB
    if (empty($err)) {
        $_POST['cover'] = $coverName;
        $bookModel->update($id, $_POST);
        header("Location: books.php");
        exit;
    }
}
?>

<style>
/* CARD EDIT BUKU */
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
input, select {
    background: #1a1a1c;
    border: 1px solid rgba(255,255,255,0.13);
    color: #e5e5e5;
    border-radius: 10px;
    padding: 10px;
    transition: .3s;
}
input:focus {
    border-color: #a855f7;
    box-shadow: 0 0 12px rgba(168,85,247,.45);
    outline: none;
}

/* BUTTON UPDATE */
.btn-update {
    background: #7e22ce;
    padding: 10px 26px;
    border-radius: 10px;
    font-weight: 600;
    transition: .3s;
    color: white;
}
.btn-update:hover {
    background: #6b13c0;
    box-shadow: 0 0 16px rgba(168,85,247,.45);
    transform: translateY(-2px);
}

/* BUTTON CANCEL */
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
</style>

<div class="max-w-xl mx-auto mt-14 edit-card">

    <!-- TITLE -->
    <h2 class="edit-title mb-6">Edit Buku</h2>

    <!-- ERROR MESSAGE -->
    <?php if (!empty($err)): ?>
        <div class="text-red-400 mb-4"><?= htmlspecialchars($err) ?></div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="post" enctype="multipart/form-data" class="space-y-5">

        <!-- JUDUL -->
        <div>
            <label class="block text-gray-300 mb-1 font-medium">Judul</label>
            <input name="judul" value="<?= htmlspecialchars($book['judul']) ?>" class="w-full">
        </div>

        <!-- PENULIS -->
        <div>
            <label class="block text-gray-300 mb-1 font-medium">Penulis</label>
            <input name="penulis" value="<?= htmlspecialchars($book['penulis']) ?>" class="w-full">
        </div>

        <!-- PENERBIT -->
        <div>
            <label class="block text-gray-300 mb-1 font-medium">Penerbit</label>
            <input name="penerbit" value="<?= htmlspecialchars($book['penerbit']) ?>" class="w-full">
        </div>

        <!-- TAHUN TERBIT -->
        <div>
            <label class="block text-gray-300 mb-1 font-medium">Tahun Terbit</label>
            <input type="number" name="tahun_terbit" value="<?= $book['tahun_terbit'] ?>" class="w-full">
        </div>

        <!-- STOK -->
        <div>
            <label class="block text-gray-300 mb-1 font-medium">Stok</label>
            <input type="number" name="stok" value="<?= $book['stok'] ?>" class="w-full">
        </div>

        <!-- GANTI COVER -->
        <div>
            <label class="block text-gray-300 mb-1 font-medium">Ganti Cover (Opsional)</label>
            <input type="file" name="cover" accept="image/*" class="w-full">

            <!-- TAMPILKAN COVER LAMA -->
            <?php if ($book['cover']): ?>
                <img src="uploads/cover/<?= $book['cover'] ?>" class="w-32 mt-3 rounded-lg shadow">
            <?php endif; ?>
        </div>

        <!-- BUTTON -->
        <div class="flex justify-between mt-6">
            <a href="books.php" class="btn-cancel">Batal</a>

            <button class="btn-update">
                Update
            </button>
        </div>

    </form>
</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
