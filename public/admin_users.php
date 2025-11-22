<?php
require_once __DIR__ . '/../templates/header.php';

$auth = new Auth($db->pdo());
$auth->requireRole(['administrator']);

$userModel = new UserModel($db->pdo());

// Hapus user
if (isset($_GET['delete'])) {
    $uid = intval($_GET['delete']);
    $userModel->delete($uid);
    header('Location: admin_users.php');
    exit;
}

// Ambil daftar admin & petugas
$users = $db->pdo()->query("
    SELECT * FROM users 
    WHERE role IN ('administrator','petugas')
    ORDER BY role, username
")->fetchAll();

?>

<style>

/* Container Card */
.shini-card {
    background: linear-gradient(160deg, #0c0c0d, #121215 60%, #0c0c0d);
    border: 1px solid rgba(168,85,247,0.25);
    border-radius: 14px;
    padding: 28px;
    box-shadow: 0 0 28px rgba(168,85,247,0.32);
    margin-top: 25px;
    transition: .35s;
}
.shini-card:hover {
    border-color: rgba(168,85,247,0.45);
    box-shadow: 0 0 36px rgba(168,85,247,0.45);
    transform: translateY(-3px);
}

/* Title */
.shini-title {
    font-size: 1.9rem;
    font-weight: 800;
    color: #d8b4fe;
    text-shadow: 0 0 18px rgba(168,85,247,.55);
}

/* Add Button */
.shini-btn-add {
    background: #7e22ce;
    padding: 10px 22px;
    border-radius: 10px;
    color: white;
    font-weight: 600;
    transition: .3s;
}
.shini-btn-add:hover {
    background: #6b13c0;
    box-shadow: 0 0 18px rgba(168,85,247,.45);
    transform: translateY(-2px);
}

/* Table Style */
table.shini-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 18px;
    color: #dfd9ff;
    font-size: 0.95rem;
}

.shini-table thead tr {
    background: #1e1e24;
    border-bottom: 1px solid rgba(168,85,247,0.25);
}

.shini-table th {
    padding: 10px;
    font-weight: 700;
    color: #c7b3ff;
}

.shini-table tbody tr {
    background: #141417;
    border-bottom: 1px solid rgba(255,255,255,0.05);
    transition: .25s;
}

.shini-table tbody tr:hover {
    background: #1c1c21;
    box-shadow: inset 0 0 12px rgba(168,85,247,0.25);
}

/* Table Cells */
.shini-table td {
    padding: 10px;
    color: #dedede;
}

/* Action Buttons */
.shini-edit {
    color: #8d5bff;
    font-weight: 600;
    transition: .2s;
}
.shini-edit:hover {
    color: #b794ff;
    text-shadow: 0 0 12px rgba(168,85,247,.45);
}

.shini-delete {
    color: #ff5b5b;
    font-weight: 600;
    transition: .2s;
}
.shini-delete:hover {
    color: #ff7a7a;
    text-shadow: 0 0 12px rgba(255,80,80,.55);
}

.shini-self {
    color: #888;
    font-style: italic;
}
</style>

<div class="shini-card">

    <div class="flex justify-between items-center mb-4">
        <h2 class="shini-title">Manajemen Admin & Petugas</h2>

        <a href="admin_register.php" class="shini-btn-add">+ Tambah Akun</a>
    </div>

    <table class="shini-table">
        <thead>
            <tr>
                <th>Username</th>
                <th>Nama Lengkap</th>
                <th>Role</th>
                <th>Email</th>
                <th>Alamat</th>
                <th>Aksi</th>
            </tr>
        </thead>

        <tbody>
        <?php foreach ($users as $u): ?>
            <tr>
                <td><?= htmlspecialchars($u['username']) ?></td>

                <td><?= htmlspecialchars($u['nama_lengkap']) ?></td>

                <td class="font-bold italic text-purple-300">
                    <?= htmlspecialchars($u['role']) ?>
                </td>

                <td><?= htmlspecialchars($u['email']) ?></td>

                <td><?= nl2br(htmlspecialchars($u['alamat'])) ?></td>

                <td>
                    <!-- EDIT -->
                    <a href="admin_edit.php?id=<?= $u['id'] ?>" class="shini-edit">Edit</a>

                    <!-- DELETE -->
                    <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                        &nbsp;|&nbsp;
                        <a href="admin_users.php?delete=<?= $u['id'] ?>" 
                           onclick="return confirm('Hapus akun ini?')"
                           class="shini-delete">Hapus</a>
                    <?php else: ?>
                        <span class="shini-self">(Akun Anda)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

</div>

<?php require_once __DIR__ . '/../templates/footer.php'; ?>
