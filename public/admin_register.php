<?php
require_once __DIR__.'/../templates/header.php';

$auth = new Auth($db->pdo());
$auth->requireRole(['administrator']); // hanya admin yang boleh membuka halaman ini

$userModel = new UserModel($db->pdo());

if($_SERVER['REQUEST_METHOD']==='POST'){
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama     = $_POST['nama_lengkap'];
    $email    = $_POST['email'];
    $alamat   = $_POST['alamat'];
    $role     = $_POST['role'] === 'petugas' ? 'petugas' : 'administrator';

    // Cek username duplikat
    if($userModel->findByUsername($username)){
        $err = "Username sudah dipakai.";
    } else {

        // Simpan akun baru ke database
        $userModel->create([
            'username'      => $username,
            'password'      => $password,
            'nama_lengkap'  => $nama,
            'email'         => $email,
            'role'          => $role,
            'alamat'        => $alamat
        ]);

        $sukses = "Akun $role berhasil dibuat.";
    }
}
?>

<style>

    .card-shinigami {
        background: rgba(20, 20, 30, 0.9);
        border-radius: 12px;
        padding: 28px;
        box-shadow: 0 0 25px rgba(140, 0, 255, 0.25);
        backdrop-filter: blur(6px);
        transition: 0.25s ease;
    }

    .card-shinigami:hover {
        box-shadow: 0 0 38px rgba(170, 0, 255, 0.4);
        transform: translateY(-2px);
    }

    .shinigami-input {
        background: rgba(40, 40, 58, 0.85);
        border: 1px solid rgba(130, 0, 255, 0.35);
        color: #eee;
        padding: 10px 12px;
        border-radius: 8px;
        width: 100%;
        margin-top: 5px;
        font-size: 15px;
        transition: 0.2s;
    }

    .shinigami-input:focus {
        outline: none;
        border-color: rgb(180, 90, 255);
        box-shadow: 0 0 8px rgba(180, 90, 255, 0.5);
    }

    .shinigami-btn {
        width: 100%;
        padding: 11px;
        background: linear-gradient(90deg, #6a00ff, #9b00ff);
        color: white;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        transition: 0.25s;
    }

    .shinigami-btn:hover {
        background: linear-gradient(90deg, #7c1aff, #b133ff);
        transform: scale(1.02);
        box-shadow: 0 0 12px rgba(150, 0, 255, 0.6);
    }

    .shinigami-label {
        font-size: 15px;
        color: #ddd;
        font-weight: 500;
    }

    .link-back {
        color: #bbb;
        transition: 0.2s;
    }

    .link-back:hover {
        color: #d7aaff;
        text-shadow: 0 0 6px rgba(200, 120, 255, 0.7);
    }

    .alert-error {
        color: #ff7b7b;
        font-weight: bold;
        text-align: center;
        margin-bottom: 12px;
    }

    .alert-success {
        color: #8bffb8;
        font-weight: bold;
        text-align: center;
        margin-bottom: 12px;
    }
</style>

<div class="max-w-lg mx-auto card-shinigami">

    <!-- HEADER -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-2xl font-bold text-purple-200">Register Admin / Petugas</h2>

        <a href="admin_users.php" class="link-back text-sm">← Kembali</a>
    </div>

    <!-- ERROR -->
    <?php if(!empty($err)): ?>
        <div class="alert-error">
            <?= htmlspecialchars($err) ?>
        </div>
    <?php endif; ?>

    <!-- SUKSES -->
    <?php if(!empty($sukses)): ?>
        <div class="alert-success">
            <?= htmlspecialchars($sukses) ?>
        </div>
    <?php endif; ?>

    <form method="post">

        <label class="shinigami-label">Username
            <input name="username" class="shinigami-input" required>
        </label>

        <label class="shinigami-label mt-3 block">Password
            <input type="password" name="password" class="shinigami-input" required>
        </label>

        <label class="shinigami-label mt-3 block">Nama Lengkap
            <input name="nama_lengkap" class="shinigami-input">
        </label>

        <label class="shinigami-label mt-3 block">Email
            <input type="email" name="email" class="shinigami-input">
        </label>

        <label class="shinigami-label mt-3 block">Alamat
            <textarea name="alamat" class="shinigami-input" rows="3"></textarea>
        </label>

        <label class="shinigami-label mt-3 block">Role
            <select name="role" class="shinigami-input">
                <option value="administrator">Administrator</option>
                <option value="petugas">Petugas</option>
            </select>
        </label>

        <button class="shinigami-btn mt-6">
            Buat Akun
        </button>

    </form>
</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
