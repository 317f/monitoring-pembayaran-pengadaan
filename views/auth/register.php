<?php 
include __DIR__ . '/../layouts/header.php';

// Ensure only administrators can access this page
if (!isAdmin()) {
    header('Location: index.php?page=dashboard');
    exit;
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-user-plus fa-3x mb-3 text-primary"></i>
                        <h3 class="card-title">Tambah User Baru</h3>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($success)): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?php echo $success; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=register" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" 
                                       name="username" 
                                       class="form-control" 
                                       id="username"
                                       required
                                       pattern="[a-zA-Z0-9_-]{3,20}"
                                       title="Username harus terdiri dari 3-20 karakter (huruf, angka, underscore, atau dash)">
                                <div class="invalid-feedback">
                                    Username harus terdiri dari 3-20 karakter (huruf, angka, underscore, atau dash)
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       name="password" 
                                       class="form-control" 
                                       id="password"
                                       required
                                       minlength="6">
                                <div class="invalid-feedback">
                                    Password minimal 6 karakter
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       name="confirm_password" 
                                       class="form-control" 
                                       id="confirm_password"
                                       required>
                                <div class="invalid-feedback">
                                    Password tidak cocok
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="role" class="form-label">Role <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user-tag"></i>
                                </span>
                                <select name="role" class="form-select" id="role" required>
                                    <option value="">Pilih Role</option>
                                    <option value="Administrator">Administrator</option>
                                    <option value="Pengguna">Pengguna</option>
                                </select>
                                <div class="invalid-feedback">
                                    Pilih role user
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-user-plus me-1"></i> Daftarkan User
                            </button>
                            <a href="index.php?page=manage_users" class="btn btn-secondary">
                                <i class="fas fa-arrow-left me-1"></i> Kembali
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Form validation
(function () {
    'use strict'

    var forms = document.querySelectorAll('.needs-validation')
    var password = document.getElementById('password')
    var confirmPassword = document.getElementById('confirm_password')

    // Password match validation
    function validatePassword() {
        if (password.value != confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords tidak cocok')
        } else {
            confirmPassword.setCustomValidity('')
        }
    }

    password.onchange = validatePassword
    confirmPassword.onkeyup = validatePassword

    // Form validation
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
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
