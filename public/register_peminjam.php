<?php
require_once __DIR__.'/../templates/header.php';

$userModel = new UserModel($db->pdo());

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $username = trim($_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $nama     = $_POST['nama_lengkap'];
    $email    = $_POST['email'];
    $alamat   = $_POST['alamat'];

    if($userModel->findByUsername($username)){
        $err = "Username sudah dipakai";
    } else {
        $userModel->create([
            'username'      => $username,
            'password'      => $password,
            'nama_lengkap'  => $nama,
            'email'         => $email,
            'role'          => 'peminjam',
            'alamat'        => $alamat
        ]);

        $sukses = "Registrasi berhasil. Silakan login.";
    }
}
?>

<style>
    
    .login-card {
        background: linear-gradient(160deg, #0c0c0d, #111113 60%, #0c0c0d);
        border: 1px solid rgba(255,255,255,0.08);
        padding: 28px;
        border-radius: 14px;
        box-shadow: 0 0 25px rgba(168,85,247,0.12);
        transition: .35s ease;
    }

    .login-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 0 28px rgba(168,85,247,0.25);
        border-color: rgba(168,85,247,0.35);
    }

    .login-title {
        text-shadow: 0 0 14px rgba(168,85,247,0.55);
        letter-spacing: 1.5px;
    }

    input, textarea {
        background: #1a1a1c;
        border: 1px solid rgba(255,255,255,0.12);
        color: #e5e5e5;
        transition: .3s;
    }

    input:focus, textarea:focus {
        border-color: #a855f7;
        box-shadow: 0 0 10px rgba(168,85,247,0.4);
        outline: none;
    }

    .btn-login {
        background: #7e22ce;
        transition: .3s ease;
    }

    .btn-login:hover {
        background: #6b13c0;
        transform: translateY(-2px);
        box-shadow: 0 0 14px rgba(168,85,247,0.45);
    }
</style>

<div class="max-w-md mx-auto mt-24">
    <div class="login-card">

        <h2 class="text-3xl font-extrabold text-center text-purple-300 mb-8 login-title">
            REGISTER PEMINJAM
        </h2>

        <!-- ERROR -->
        <?php if(!empty($err)): ?>
            <div class="text-red-500 mb-4 text-center text-sm">
                <?= htmlspecialchars($err) ?>
            </div>
        <?php endif; ?>

        <!-- SUKSES -->
        <?php if(!empty($sukses)): ?>
            <div class="text-green-400 mb-4 text-center text-sm">
                <?= htmlspecialchars($sukses) ?>
            </div>
        <?php endif; ?>

        <form method="post" class="space-y-5">

            <div>
                <label class="block text-gray-300 mb-1 font-medium">Username</label>
                <input name="username" class="w-full p-3 rounded-lg" required>
            </div>

            <div>
                <label class="block text-gray-300 mb-1 font-medium">Password</label>
                <input type="password" name="password" class="w-full p-3 rounded-lg" required>
            </div>

            <div>
                <label class="block text-gray-300 mb-1 font-medium">Nama Lengkap</label>
                <input name="nama_lengkap" class="w-full p-3 rounded-lg">
            </div>

            <div>
                <label class="block text-gray-300 mb-1 font-medium">Email</label>
                <input type="email" name="email" class="w-full p-3 rounded-lg">
            </div>

            <div>
                <label class="block text-gray-300 mb-1 font-medium">Alamat</label>
                <textarea name="alamat" class="w-full p-3 rounded-lg" rows="3"></textarea>
            </div>

            <button class="btn-login w-full text-white py-2 rounded-lg font-semibold text-lg">
                Register
            </button>
        </form>

        <div class="text-center text-gray-400 mt-6 text-sm">
            Sudah punya akun?
            <a href="login.php" class="text-purple-400 hover:text-purple-300 underline">
                Login
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
