<?php
require_once __DIR__.'/../templates/header.php';

$auth = new Auth($db->pdo());
$auth->requireRole(['administrator','petugas']);
// Hanya admin dan petugas yang bisa membuka laporan

$peminjamanModel = new PeminjamanModel($db->pdo());
$bookModel       = new BookModel($db->pdo());


$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

$from_dt = $from ? $from . " 00:00:00" : null;
$to_dt   = $to   ? $to   . " 23:59:59" : null;

$borrow_list = $peminjamanModel->reportAll($from_dt, $to_dt);
$book_list   = $bookModel->reportAll($from_dt, $to_dt);
?>

<style>
.shini-card {
    background: linear-gradient(160deg, #0c0c0d, #121215 60%, #0c0c0d);
    border: 1px solid rgba(168,85,247,0.20);
    border-radius: 14px;
    padding: 28px;
    box-shadow: 0 0 18px rgba(168,85,247,0.22);
    transition: .28s;
}
.shini-card:hover {
    border-color: rgba(168,85,247,0.35);
    box-shadow: 0 0 24px rgba(168,85,247,0.30);
    transform: translateY(-1.5px);
}

.shini-title {
    font-size: 1.9rem;
    font-weight: 800;
    color: #d8b4fe;
    text-shadow: 0 0 12px rgba(168,85,247,.45);
}

.shini-input {
    background: #141417;
    border: 1px solid rgba(168,85,247,0.28);
    padding: 8px 10px;
    border-radius: 10px;
    color: #eee;
    width: 100%;
}
.shini-input:focus {
    border-color: #c084fc;
    box-shadow: 0 0 8px rgba(168,85,247,.35);
    outline: none;
}

table.shini-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 16px;
    font-size: 0.95rem;
    color: #dfd9ff;
}
.shini-table thead tr {
    background: #1e1e24;
    border-bottom: 1px solid rgba(168,85,247,0.25);
}
.shini-table th {
    padding: 10px;
    color: #c7b3ff;
    font-weight: 700;
}
.shini-table tbody tr {
    background: #141417;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    transition: .2s;
}
.shini-table tbody tr:hover {
    background: #1c1c21;
    box-shadow: inset 0 0 8px rgba(168,85,247,0.18);
}
.shini-table td {
    padding: 10px;
}

.shini-btn {
    background: #7e22ce;
    padding: 9px 18px;
    border-radius: 10px;
    color: white;
    font-weight: 600;
    transition: .25s;
}
.shini-btn:hover {
    background: #6b13c0;
    box-shadow: 0 0 12px rgba(168,85,247,.35);
}

.shini-btn-green {
    background: #0f8d2f;
    padding: 8px 14px;
    border-radius: 8px;
    color: white;
    font-weight: 600;
    transition: .25s;
}
.shini-btn-green:hover {
    background: #16a93e;
    box-shadow: 0 0 10px rgba(0,255,120,0.25);
}

.shini-reset {
    color: #b8a8d9;
    font-weight: 600;
    margin-left: 6px;
    margin-top: 9px !important;
    display: inline-block;
    transition: .25s;
}
.shini-reset:hover {
    color: #e5d2ff;
    text-shadow: 0 0 6px rgba(168,85,247,.35);
}
</style>


<div class="max-w-6xl mx-auto shini-card">

    <h2 class="shini-title mb-6">Laporan Perpustakaan</h2>

    <!-- ============================= -->
    <!--      FILTER TANGGAL           -->
    <!-- ============================= -->
    <form method="get" class="mb-6 flex items-end gap-5">

        <div class="w-44">
            <label class="text-purple-200 text-sm font-semibold">Dari Tanggal</label>
            <input type="date" name="from" value="<?= htmlspecialchars($from) ?>" class="shini-input mt-1">
        </div>

        <div class="w-44">
            <label class="text-purple-200 text-sm font-semibold">Sampai Tanggal</label>
            <input type="date" name="to" value="<?= htmlspecialchars($to) ?>" class="shini-input mt-1">
        </div>

        <!-- ============ EDITED BAGIAN INI SAJA ============ -->
        <div class="flex items-center gap-3 mb-[2px]">
            <button class="shini-btn">Filter</button>
            <a href="reports.php" class="shini-reset !mt-0">Reset</a>
        </div>
        <!-- ================================================= -->

    </form>

    <!-- LAPORAN PEMINJAMAN -->
    <div class="mt-8 shini-card">

        <div class="flex justify-between items-center mb-3">
            <h3 class="text-xl font-bold text-purple-200">Laporan Peminjaman</h3>

            <a href="reports_print.php?type=peminjaman&from=<?= $from ?>&to=<?= $to ?>"
               class="shini-btn-green" target="_blank">
               Cetak
            </a>
        </div>

        <table class="shini-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Buku</th>
                    <th>Tanggal Pinjam</th>
                    <th>Tanggal Kembali</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
                <?php if (empty($borrow_list)): ?>
                    <tr><td colspan="6" class="text-center p-4 text-gray-400">Tidak ada data</td></tr>
                <?php endif; ?>

                <?php foreach($borrow_list as $r): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= htmlspecialchars($r['username']) ?></td>
                        <td><?= htmlspecialchars($r['judul']) ?></td>
                        <td><?= $r['tanggal_pinjam'] ?></td>
                        <td><?= $r['tanggal_kembali'] ?: '-' ?></td>
                        <td><?= $r['status'] ?></td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>
    </div>

    <!-- LAPORAN DETAIL BUKU -->
    <div class="mt-10 shini-card">

        <div class="flex justify-between items-center mb-3">
            <h3 class="text-xl font-bold text-purple-200">Laporan Detail Buku</h3>

            <a href="reports_print.php?type=buku&from=<?= $from ?>&to=<?= $to ?>"
               class="shini-btn-green" target="_blank">
               Cetak
            </a>
        </div>

        <table class="shini-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Judul</th>
                    <th>Penulis</th>
                    <th>Penerbit</th>
                    <th>Tahun Terbit</th>
                    <th>Stok</th>
                </tr>
            </thead>

            <tbody>

                <?php if (empty($book_list)): ?>
                    <tr><td colspan="6" class="text-center p-4 text-gray-400">Tidak ada data</td></tr>
                <?php endif; ?>

                <?php foreach($book_list as $b): ?>
                    <tr>
                        <td><?= $b['id'] ?></td>
                        <td><?= htmlspecialchars($b['judul']) ?></td>
                        <td><?= htmlspecialchars($b['penulis']) ?></td>
                        <td><?= htmlspecialchars($b['penerbit']) ?></td>
                        <td><?= $b['tahun_terbit'] ?></td>
                        <td><?= $b['stok'] ?></td>
                    </tr>
                <?php endforeach; ?>

            </tbody>
        </table>

    </div>

</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
