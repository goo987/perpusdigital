<?php
require_once __DIR__.'/../templates/header.php';  

$auth = new Auth($db->pdo());
// Instansiasi class Auth

$auth->requireRole(['administrator']);
// Membatasi hanya Administrator yang bisa akses

$userModel = new UserModel($db->pdo());
// Model untuk melakukan query tabel users

$id = intval($_GET['id'] ?? 0);
// Ambil ID user dari URL

$user = $userModel->find($id);
// Cari data user berdasarkan ID

if(!$user){
    echo "Akun tidak ditemukan.";
    require_once __DIR__.'/../templates/footer.php';
    exit;
}

if($_SERVER['REQUEST_METHOD']==='POST'){
    // Ketika form disubmit

    $username = trim($_POST['username']);
    $nama     = $_POST['nama_lengkap'];
    $email    = $_POST['email'];
    $alamat   = $_POST['alamat'];
    $role     = $_POST['role'];

    // Jika password diisi maka update password
    // Jika tidak diisi maka password tetap.
    $password_sql = "";
    $params = [$username, $nama, $email, $alamat, $role, $id];

    if(!empty($_POST['password'])){
        $password_sql = ", password = ?";
        array_splice($params, 5, 0, password_hash($_POST['password'], PASSWORD_DEFAULT));
    }

    // Update data user
    $stmt = $db->pdo()->prepare("
        UPDATE users 
        SET username=?, nama_lengkap=?, email=?, alamat=?, role=? $password_sql 
        WHERE id=?
    ");
    $stmt->execute($params);

    header("Location: admin_users.php");
    exit;
}
?>

<style>
    /* Styling kartu form */
    .card-shinigami {
        background: rgba(20, 20, 30, 0.92);
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

    /* Input */
    .sh-input {
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
    .sh-input:focus {
        outline: none;
        border-color: rgb(180, 90, 255);
        box-shadow: 0 0 8px rgba(180, 90, 255, 0.5);
    }

    .sh-label {
        font-size: 15px;
        color: #ddd;
        font-weight: 500;
    }

    /* Tombol submit */
    .sh-btn {
        width: 100%;
        padding: 11px;
        background: linear-gradient(90deg, #6a00ff, #9b00ff);
        color: white;
        border-radius: 10px;
        font-size: 16px;
        font-weight: bold;
        transition: 0.25s;
    }
    .sh-btn:hover {
        background: linear-gradient(90deg, #7c1aff, #b133ff);
        transform: scale(1.02);
        box-shadow: 0 0 12px rgba(150, 0, 255, 0.6);
    }

    /* Tombol kembali */
    .sh-back {
        color: #bbb;
        transition: 0.2s;
    }
    .sh-back:hover {
        color: #d7aaff;
        text-shadow: 0 0 6px rgba(200, 120, 255, 0.7);
    }
</style>

<div class="max-w-lg mx-auto card-shinigami">

  <!-- Judul halaman + tombol kembali -->
  <div class="flex justify-between items-center mb-4">
      <h2 class="text-2xl font-bold text-purple-200">Edit Akun</h2>

      <a href="admin_users.php" class="text-sm sh-back">← Kembali</a>
  </div>

  <!-- FORM EDIT USER -->
  <form method="post">

    <!-- Username -->
    <label class="block sh-label">Username
      <input name="username" class="sh-input"
             value="<?= htmlspecialchars($user['username']) ?>" required>
    </label>

    <!-- Password (opsional) -->
    <label class="block sh-label mt-3">Password (opsional)
      <input type="password" name="password"
             class="sh-input"
             placeholder="Biarkan kosong jika tidak diubah">
    </label>

    <!-- Nama lengkap -->
    <label class="block sh-label mt-3">Nama Lengkap
      <input name="nama_lengkap" class="sh-input"
             value="<?= htmlspecialchars($user['nama_lengkap']) ?>">
    </label>

    <!-- Email -->
    <label class="block sh-label mt-3">Email
      <input type="email" name="email" class="sh-input"
             value="<?= htmlspecialchars($user['email']) ?>">
    </label>

    <!-- Alamat -->
    <label class="block sh-label mt-3">Alamat
      <textarea name="alamat" class="sh-input"
                rows="3"><?= htmlspecialchars($user['alamat']) ?></textarea>
    </label>

    <!-- Role -->
    <label class="block sh-label mt-3">Role
      <select name="role" class="sh-input">
        <option value="administrator" <?= $user['role']=='administrator'?'selected':'' ?>>Administrator</option>
        <option value="petugas" <?= $user['role']=='petugas'?'selected':'' ?>>Petugas</option>
      </select>
    </label>

    <!-- Submit -->
    <button class="sh-btn mt-6">
      Update Akun
    </button>

  </form>

</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
