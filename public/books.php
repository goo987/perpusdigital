<?php
require_once __DIR__.'/../templates/header.php';

$auth = new Auth($db->pdo());
// Instansiasi class Auth

$auth->requireRole(['administrator','petugas']);
// Hanya administrator & petugas yang boleh masuk

$bookModel = new BookModel($db->pdo());

// Jika tombol hapus ditekan maka delete
if (isset($_GET['delete'])) {
    $bookModel->delete(intval($_GET['delete']));
    header('Location: books.php'); // reload halaman
    exit;
}

// Ambil semua data buku
$books = $bookModel->all();

// Hitung berapa buku sedang dipinjam
$stmt = $db->pdo()->query("
    SELECT buku_id, COUNT(*) AS jml 
    FROM peminjaman 
    WHERE status = 'dipinjam'
    GROUP BY buku_id
");

// Simpan hasil per buku_id
$dipped = [];
foreach ($stmt->fetchAll() as $row) {
    $dipped[$row['buku_id']] = $row['jml'];
}
?>

<style>
    /* Kartu utama daftar buku */
    .card-books {
        background: linear-gradient(160deg, #0c0c0d, #111113 55%, #0c0c0d);
        border: 1px solid rgba(168,85,247,0.15);
        border-radius: 14px;
        padding: 28px;
        box-shadow: 0 0 20px rgba(168,85,247,0.15);
        transition: .3s ease;
    }

    .card-books:hover {
        box-shadow: 0 0 30px rgba(168,85,247,0.25);
        border-color: rgba(168,85,247,0.35);
        transform: translateY(-3px);
    }

    /* Judul halaman */
    .books-title {
        font-size: 1.8rem;
        font-weight: 800;
        color: #d8b4fe;
        text-shadow: 0 0 18px rgba(168,85,247,0.45);
    }

    /* Tabel */
    table { background: #1a1a1c; }
    thead tr { background: #2a2a2d; }

    tbody tr:hover {
        background: #2b2a31;
        box-shadow: 0 0 6px rgba(168,85,247,0.25);
        transition: 0.18s ease-in-out;
    }

    td, th {
        padding: 14px;
        font-size: 0.97rem;
    }

    /* Tombol tambah buku */
    .btn-add {
        background: #7e22ce;
        color: white;
        padding: 9px 18px;
        border-radius: 10px;
        font-weight: 600;
        transition: .3s;
    }

    .btn-add:hover {
        background: #6b13c0;
        box-shadow: 0 0 16px rgba(168,85,247,.45);
        transform: translateY(-2px);
    }

    /* Link edit */
    .aksi-edit {
        color: #a855f7;
        font-weight: 600;
    }
    .aksi-edit:hover {
        color: #c084fc;
        text-shadow: 0 0 10px rgba(168,85,247,.55);
    }

    /* Link hapus */
    .aksi-hapus {
        color: #f87171;
        font-weight: 600;
    }
    .aksi-hapus:hover {
        color: #fca5a5;
        text-shadow: 0 0 10px rgba(248,113,113,.45);
    }
</style>

<div class="card-books mt-10">

    <!-- Header halaman -->
    <div class="flex justify-between items-center mb-6">
        <h2 class="books-title">Daftar Buku</h2>

        <!-- Tombol menuju tambah buku -->
        <a href="book_add.php" class="btn-add">
            + Tambah Buku
        </a>
    </div>

    <!-- Wrapper tabel -->
    <div class="overflow-x-auto rounded-lg border border-gray-700/40">

        <!-- Tabel daftar buku -->
        <table class="w-full text-left text-gray-200 border-collapse">

            <!-- Header tabel kolom -->
            <thead class="border-b border-gray-600/40">
                <tr>
                    <th>Cover</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Penerbit</th>
                    <th class="text-center">Stok Asli</th>
                    <th class="text-center">Dipinjam</th>
                    <th class="text-center">Tersisa</th>
                    <th>Aksi</th>
                </tr>
            </thead>

            <tbody>

            <!-- Loop semua buku -->
            <?php foreach($books as $b):

                // Stok original
                $stok_asli = intval($b['stok']);

                // Jumlah sedang dipinjam
                $jml_dipinjam = $dipped[$b['id']] ?? 0;

                // Hitung sisa stok
                $tersisa = max($stok_asli - $jml_dipinjam, 0);
            ?>

                <tr class="border-b border-gray-700/40">

                    <!-- Cover buku -->
                    <td>
                        <?php if(!empty($b['cover'])): ?>
                            <img src="uploads/cover/<?= htmlspecialchars($b['cover']) ?>"
                                 class="h-16 w-12 object-cover rounded shadow">
                        <?php else: ?>
                            <!-- Jika cover kosong -->
                            <div class="h-16 w-12 bg-gray-700 rounded flex items-center justify-center text-gray-400 text-xs">
                                No Img
                            </div>
                        <?php endif; ?>
                    </td>

                    <!-- Judul -->
                    <td class="font-semibold">
                        <?= htmlspecialchars($b['judul']) ?>
                    </td>

                    <!-- Penulis -->
                    <td class="text-gray-300">
                        <?= htmlspecialchars($b['penulis']) ?>
                    </td>

                    <!-- Penerbit -->
                    <td class="text-gray-300">
                        <?= htmlspecialchars($b['penerbit']) ?>
                    </td>

                    <!-- Stok asli -->
                    <td class="text-center font-bold text-purple-300">
                        <?= $stok_asli ?>
                    </td>

                    <!-- Sedang dipinjam -->
                    <td class="text-center font-bold text-blue-400">
                        <?= $jml_dipinjam ?>
                    </td>

                    <!-- Stok tersisa -->
                    <td class="text-center">
                        <span class="px-3 py-1 rounded-full text-sm
                            <?= $tersisa > 0 
                                ? 'bg-green-900/40 text-green-300 border border-green-600/40' 
                                : 'bg-red-900/40 text-red-300 border border-red-600/40' ?>">
                            <?= $tersisa ?>
                        </span>
                    </td>

                    <!-- Aksi edit & hapus -->
                    <td class="space-x-4">

                        <!-- Edit -->
                        <a href="book_edit.php?id=<?= $b['id'] ?>" class="aksi-edit">Edit</a>

                        <!-- Hapus -->
                        <a href="books.php?delete=<?= $b['id'] ?>"
                           onclick="return confirm('Hapus buku ini?')"
                           class="aksi-hapus">
                            Hapus
                        </a>
                    </td>

                </tr>

            <?php endforeach; ?>

            </tbody>
        </table>

    </div>

</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
