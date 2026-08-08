<?php
session_start();
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: ../auth/login.php');
    exit;
}

$pageTitle  = 'Kelola User';
$activePage = 'admin-user';
$base       = '../';

$pesan = '';
$error = '';

// Pastikan tabel users dan kolom password_plain ada
$tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
if ($tableCheck->num_rows === 0) {
    $conn->query("CREATE TABLE IF NOT EXISTS `users` (
        `id`             INT AUTO_INCREMENT PRIMARY KEY,
        `username`       VARCHAR(60) NOT NULL UNIQUE,
        `password`       VARCHAR(255) NOT NULL,
        `password_plain` VARCHAR(255) DEFAULT NULL,
        `nama`           VARCHAR(100) NOT NULL,
        `email`          VARCHAR(100) DEFAULT NULL,
        `created_at`     DATETIME DEFAULT CURRENT_TIMESTAMP,
        `status`         ENUM('aktif','nonaktif') DEFAULT 'aktif'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
} else {
    $colCheck = $conn->query("SHOW COLUMNS FROM `users` LIKE 'password_plain'");
    if ($colCheck->num_rows === 0) {
        $conn->query("ALTER TABLE `users` ADD COLUMN `password_plain` VARCHAR(255) DEFAULT NULL AFTER `password`");
    }
}

// Tambah user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'tambah') {
    $username = trim($_POST['username'] ?? '');
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $status   = in_array($_POST['status'] ?? '', ['aktif','nonaktif']) ? $_POST['status'] : 'aktif';

    if (!$username || !$nama || !$password) {
        $error = 'Username, nama, dan password wajib diisi.';
    } elseif (strlen($password) < 4) {
        $error = 'Password minimal 4 karakter.';
    } else {
        $hashed = md5($password);
        $stmt = $conn->prepare("INSERT INTO users (username, password, password_plain, nama, email, status) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $username, $hashed, $password, $nama, $email, $status);
        if ($stmt->execute()) {
            $pesan = "User <strong>" . htmlspecialchars($username) . "</strong> berhasil ditambahkan!";
        } else {
            $error = 'Gagal menambahkan user. Username mungkin sudah digunakan.';
        }
    }
}

// Update user
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['aksi'] ?? '') === 'update') {
    $id      = (int)$_POST['id'];
    $nama    = trim($_POST['nama'] ?? '');
    $email   = trim($_POST['email'] ?? '');
    $status  = in_array($_POST['status'] ?? '', ['aktif','nonaktif']) ? $_POST['status'] : 'aktif';
    $newPass = trim($_POST['new_password'] ?? '');

    if (!$nama) {
        $error = 'Nama lengkap wajib diisi.';
    } else {
        if ($newPass !== '') {
            if (strlen($newPass) < 4) {
                $error = 'Password baru minimal 4 karakter.';
            } else {
                $hashed = md5($newPass);
                $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, status=?, password=?, password_plain=? WHERE id=?");
                $stmt->bind_param("sssssi", $nama, $email, $status, $hashed, $newPass, $id);
                if ($stmt->execute()) {
                    $pesan = 'Data user dan password berhasil diperbarui!';
                } else {
                    $error = 'Gagal memperbarui data user.';
                }
            }
        } else {
            $stmt = $conn->prepare("UPDATE users SET nama=?, email=?, status=? WHERE id=?");
            $stmt->bind_param("sssi", $nama, $email, $status, $id);
            if ($stmt->execute()) {
                $pesan = 'Data user berhasil diperbarui!';
            } else {
                $error = 'Gagal memperbarui data user.';
            }
        }
    }
}

// Hapus user
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM users WHERE id = $id");
    header('Location: kelola-user.php?pesan=hapus');
    exit;
}
if (($_GET['pesan'] ?? '') === 'hapus') {
    $pesan = 'User berhasil dihapus dari sistem.';
}

// Edit data
$editData = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editData = $conn->query("SELECT * FROM users WHERE id = " . (int)$_GET['edit'])->fetch_assoc();
}

// Daftar users
$allUsers = $conn->query("SELECT * FROM users ORDER BY created_at DESC");
$totalUsers = $conn->query("SELECT COUNT(*) AS n FROM users")->fetch_assoc()['n'];

require_once '../includes/header.php';
?>

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div class="page-title"><i class="fas fa-users-gear"></i> Kelola User</div>
        <div class="page-sub">Tambah, pantau informasi akun pengguna (nama, email, password), dan kelola status akses.</div>
    </div>
    <a href="dashboard.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
</div>

<?php if ($pesan): ?>
<div class="alert alert-success"><span class="alert-icon"><i class="fas fa-circle-check"></i></span><div><?= $pesan ?></div></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger"><span class="alert-icon"><i class="fas fa-ban"></i></span><div><?= $error ?></div></div>
<?php endif; ?>

<div class="grid-2">
<!-- FORM TAMBAH / EDIT USER -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(<?= $editData ? '245,158,11' : '16,185,129' ?>,0.15);">
            <i class="fas <?= $editData ? 'fa-pen' : 'fa-user-plus' ?>"></i>
        </span>
        <?= $editData ? 'Edit User &mdash; <strong style="color:#93c5fd;">' . htmlspecialchars($editData['username']) . '</strong>' : 'Tambah User Baru' ?>
    </div>

    <form method="POST" id="userForm">
        <input type="hidden" name="aksi" value="<?= $editData ? 'update' : 'tambah' ?>">
        <?php if ($editData): ?>
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>

        <?php if (!$editData): ?>
        <div class="form-group">
            <label>Username <span style="color:#fda4af;">*</span></label>
            <input type="text" name="username" class="form-input" placeholder="Contoh: ujang_user" maxlength="60" required
                   value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" style="max-width:100%;">
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label>Nama Lengkap <span style="color:#fda4af;">*</span></label>
            <input type="text" name="nama" class="form-input" placeholder="Contoh: Ujang Supriatna" maxlength="100" required
                   style="max-width:100%;"
                   value="<?= htmlspecialchars($editData['nama'] ?? ($_POST['nama'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label>Email <span style="color:var(--text-muted);font-weight:400;">(opsional)</span></label>
            <input type="email" name="email" class="form-input" placeholder="contoh: user@email.com" maxlength="100"
                   style="max-width:100%;"
                   value="<?= htmlspecialchars($editData['email'] ?? ($_POST['email'] ?? '')) ?>">
        </div>

        <div class="form-group">
            <label><?= $editData ? 'Password Baru <span style="color:var(--text-muted);font-weight:400;">(kosongkan jika tidak diubah)</span>' : 'Password <span style="color:#fda4af;">*</span>' ?></label>
            <div style="position:relative;">
                <input type="password" id="inputPass" name="<?= $editData ? 'new_password' : 'password' ?>" class="form-input"
                       placeholder="<?= $editData ? 'Kosongkan jika tetap: ' . htmlspecialchars($editData['password_plain'] ?? '***') : 'Minimal 4 karakter' ?>"
                       style="max-width:100%;padding-right:40px;"
                       <?= !$editData ? 'required minlength="4"' : '' ?>>
                <button type="button" onclick="toggleFormPass()"
                    style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:0.95rem;padding:0;">
                    <i class="fas fa-eye" id="formEye"></i>
                </button>
            </div>
            <?php if ($editData && !empty($editData['password_plain'])): ?>
            <div style="font-size:0.78rem;color:#93c5fd;margin-top:5px;">
                <i class="fas fa-key"></i> Password saat ini: <code><?= htmlspecialchars($editData['password_plain']) ?></code>
            </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label>Status Akun</label>
            <select name="status" class="form-input" style="max-width:100%;">
                <option value="aktif"    <?= ($editData['status'] ?? 'aktif') === 'aktif'    ? 'selected' : '' ?>>🟢 Aktif</option>
                <option value="nonaktif" <?= ($editData['status'] ?? '') === 'nonaktif' ? 'selected' : '' ?>>🔴 Non-aktif</option>
            </select>
        </div>

        <div style="display:flex;gap:10px;margin-top:8px;">
            <button type="submit" class="btn <?= $editData ? 'btn-purple' : 'btn-success' ?> btn-lg" style="flex:1;justify-content:center;">
                <i class="fas <?= $editData ? 'fa-floppy-disk' : 'fa-user-plus' ?>"></i>
                <?= $editData ? 'Simpan Perubahan' : 'Tambah User' ?>
            </button>
            <?php if ($editData): ?>
            <a href="kelola-user.php" class="btn btn-ghost btn-lg">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- DAFTAR USER BESERTA DETAIL LENGKAP -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-users"></i></span>
        Daftar Pengguna &amp; Kredensial
        <span style="margin-left:auto;font-size:0.82rem;font-weight:500;color:var(--text-muted);"><?= $totalUsers ?> akun</span>
    </div>

    <?php if ($totalUsers == 0): ?>
    <div class="empty-state" style="padding:40px 20px;">
        <div style="font-size:2.5rem;margin-bottom:12px;"><i class="fas fa-users" style="color:var(--text-muted);"></i></div>
        <p>Belum ada user. Tambahkan user baru melalui form di samping.</p>
    </div>
    <?php else: ?>
    <div class="table-wrap" style="max-height:540px;overflow-y:auto;">
        <table class="mini-table">
            <thead style="position:sticky;top:0;z-index:10;">
                <tr>
                    <th>Pengguna</th>
                    <th>Email</th>
                    <th>Password</th>
                    <th class="td-center">Status</th>
                    <th class="td-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($u = $allUsers->fetch_assoc()):
                $plainPass = $u['password_plain'] ?? '';
                if ($plainPass === '' && $u['password'] !== '') {
                    $plainPass = '(Tersimpan MD5)';
                }
            ?>
                <tr style="<?= isset($_GET['edit']) && $_GET['edit'] == $u['id'] ? 'background:rgba(139,92,246,0.1);' : '' ?>">
                    <td>
                        <div style="font-weight:700;color:#f1f5f9;font-size:0.9rem;">
                            <?= htmlspecialchars($u['nama']) ?>
                        </div>
                        <div style="font-family:monospace;font-size:0.78rem;color:#93c5fd;display:flex;align-items:center;gap:4px;margin-top:2px;">
                            <i class="fas fa-user" style="font-size:0.7rem;opacity:0.7;"></i> <?= htmlspecialchars($u['username']) ?>
                        </div>
                    </td>
                    <td style="font-size:0.80rem;">
                        <?php if (!empty($u['email'])): ?>
                            <a href="mailto:<?= htmlspecialchars($u['email']) ?>" style="color:#93c5fd;text-decoration:none;display:inline-flex;align-items:center;gap:4px;">
                                <i class="fas fa-envelope" style="font-size:0.75rem;opacity:0.7;"></i> <?= htmlspecialchars($u['email']) ?>
                            </a>
                        <?php else: ?>
                            <span style="color:var(--text-muted);font-style:italic;">-</span>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div style="display:inline-flex;align-items:center;gap:6px;background:rgba(15,23,42,0.6);padding:3px 8px;border-radius:6px;border:1px solid var(--border);">
                            <span class="pass-text" id="pass-<?= $u['id'] ?>" data-pass="<?= htmlspecialchars($plainPass) ?>" style="font-family:monospace;font-size:0.80rem;color:#fcd34d;">
                                <?= str_repeat('•', min(8, max(4, strlen($plainPass)))) ?>
                            </span>
                            <button type="button" onclick="toggleRowPass(<?= $u['id'] ?>)" title="Lihat/Sembunyikan Password"
                                style="background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:0.78rem;padding:0 2px;">
                                <i class="fas fa-eye" id="eye-<?= $u['id'] ?>"></i>
                            </button>
                        </div>
                    </td>
                    <td class="td-center">
                        <span class="<?= $u['status'] === 'aktif' ? 'status-aktif' : 'status-nonaktif' ?>">
                            <?= ucfirst($u['status']) ?>
                        </span>
                    </td>
                    <td class="td-center" style="white-space:nowrap;">
                        <a href="kelola-user.php?edit=<?= $u['id'] ?>" class="btn btn-ghost btn-xs" title="Edit User" style="margin-right:4px;">
                            <i class="fas fa-pen"></i>
                        </a>
                        <a href="kelola-user.php?hapus=<?= $u['id'] ?>"
                           onclick="return confirm('Hapus user <?= htmlspecialchars($u['username'], ENT_QUOTES) ?> (<?= htmlspecialchars($u['nama'], ENT_QUOTES) ?>)?')"
                           class="btn btn-danger btn-xs" title="Hapus User">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
</div>

<script>
function toggleFormPass() {
    const inp = document.getElementById('inputPass');
    const ico = document.getElementById('formEye');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'fas fa-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'fas fa-eye';
    }
}

function toggleRowPass(id) {
    const span = document.getElementById('pass-' + id);
    const ico  = document.getElementById('eye-' + id);
    const pass = span.getAttribute('data-pass');
    
    if (span.getAttribute('data-shown') === '1') {
        span.textContent = '••••••••';
        span.setAttribute('data-shown', '0');
        ico.className = 'fas fa-eye';
    } else {
        span.textContent = pass;
        span.setAttribute('data-shown', '1');
        ico.className = 'fas fa-eye-slash';
    }
}
</script>

<?php require_once '../includes/footer.php'; ?>
