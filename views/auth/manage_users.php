<?php 
include __DIR__ . '/../layouts/header.php';

// Ensure only administrators can access this page
if (!isAdmin()) {
    header('Location: index.php?page=dashboard');
    exit;
}
?>

<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0">Kelola Users</h2>
        <a href="index.php?page=register" class="btn btn-primary">
            <i class="fas fa-user-plus me-1"></i> Tambah User
        </a>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['success'];
            unset($_SESSION['success']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <div class="card shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Tanggal Dibuat</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $index => $user): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td>
                                <?php if ($user['role'] === 'Administrator'): ?>
                                    <span class="badge bg-primary">Administrator</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Pengguna</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('d/m/Y H:i', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" 
                                            class="btn btn-sm btn-warning" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#editUser<?php echo $user['id']; ?>"
                                            <?php echo $user['id'] === $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" 
                                            class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete(<?php echo $user['id']; ?>, 'user')"
                                            <?php echo $user['id'] === $_SESSION['user_id'] ? 'disabled' : ''; ?>>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>

                                <!-- Edit User Modal -->
                                <div class="modal fade" id="editUser<?php echo $user['id']; ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit User</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <form action="index.php?page=manage_users&action=edit&id=<?php echo $user['id']; ?>" method="POST" class="needs-validation" novalidate>
                                                <div class="modal-body">
                                                    <div class="mb-3">
                                                        <label for="username<?php echo $user['id']; ?>" class="form-label">Username</label>
                                                        <input type="text" 
                                                               class="form-control" 
                                                               id="username<?php echo $user['id']; ?>" 
                                                               name="username" 
                                                               value="<?php echo htmlspecialchars($user['username']); ?>"
                                                               required>
                                                        <div class="invalid-feedback">Username harus diisi</div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="password<?php echo $user['id']; ?>" class="form-label">Password Baru</label>
                                                        <input type="password" 
                                                               class="form-control" 
                                                               id="password<?php echo $user['id']; ?>" 
                                                               name="password"
                                                               minlength="6">
                                                        <small class="text-muted">Kosongkan jika tidak ingin mengubah password</small>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="role<?php echo $user['id']; ?>" class="form-label">Role</label>
                                                        <select class="form-select" 
                                                                id="role<?php echo $user['id']; ?>" 
                                                                name="role" 
                                                                required>
                                                            <option value="Administrator" <?php echo $user['role'] === 'Administrator' ? 'selected' : ''; ?>>Administrator</option>
                                                            <option value="Pengguna" <?php echo $user['role'] === 'Pengguna' ? 'selected' : ''; ?>>Pengguna</option>
                                                        </select>
                                                        <div class="invalid-feedback">Role harus dipilih</div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <!-- Delete User Form -->
                                <form id="delete-form-<?php echo $user['id']; ?>" 
                                      action="index.php?page=manage_users&action=delete" 
                                      method="POST" 
                                      style="display: none;">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms)
        .forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
})()

// Confirm delete
function confirmDelete(id, type) {
    if (confirm('Apakah Anda yakin ingin menghapus ' + type + ' ini?')) {
        document.getElementById('delete-form-' + id).submit()
    }
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
