<?php
require_once __DIR__.'/../templates/header.php';

$auth = new Auth($db->pdo());

/*
 PROSES FORM LOGIN (METHOD POST)
 Jika user menekan tombol login, maka input username & password diambil.
 Lalu Auth->login() mencoba mencocokkan username/password.
 Jika berhasil maka redirect ke index.
 Jika gagal maka tampilkan pesan error.
*/
if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $u = $_POST['username'] ?? '';
    $p = $_POST['password'] ?? '';

    if($auth->login($u, $p)){
        header('Location: index.php');
        exit;
    } else {
        $err = "Username atau password salah";
    }
}
?>

<style>
    /*
       CARD LOGIN DESIGN
    */
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

    /*
       STYLING INPUT
    */
    input {
        background: #1a1a1c;
        border: 1px solid rgba(255,255,255,0.12);
        color: #e5e5e5;
        transition: .3s;
    }
    input:focus {
        border-color: #a855f7;
        box-shadow: 0 0 10px rgba(168,85,247,0.4);
        outline: none;
    }

    /*
       BUTTON LOGIN
    */
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

        <!-- TITLE -->
        <h2 class="text-3xl font-extrabold text-center text-purple-300 mb-8 login-title">
            LOGIN
        </h2>

        <!-- PESAN ERROR JIKA LOGIN GAGAL -->
        <?php if(!empty($err)): ?>
            <div class="text-red-500 mb-4 text-center text-sm">
                <?= htmlspecialchars($err) ?>
            </div>
        <?php endif; ?>

        <!-- FORM LOGIN -->
        <form method="post" class="space-y-5">

            <div>
                <label class="block text-gray-300 mb-1 font-medium">Username</label>
                <input name="username"
                       class="w-full p-3 rounded-lg"
                       required>
            </div>

            <div>
                <label class="block text-gray-300 mb-1 font-medium">Password</label>
                <input type="password"
                       name="password"
                       class="w-full p-3 rounded-lg"
                       required>
            </div>

            <button class="btn-login w-full text-white py-2 rounded-lg font-semibold text-lg">
                Login
            </button>
        </form>

        <!-- LINK REGISTER -->
        <div class="text-center text-gray-400 mt-6 text-sm">
            Belum punya akun?
            <a href="register_peminjam.php"
               class="text-purple-400 hover:text-purple-300 underline">
                Register
            </a>
        </div>

    </div>
</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
