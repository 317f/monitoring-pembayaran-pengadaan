<?php 
include __DIR__ . '/../layouts/header.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    header('Location: index.php?page=login');
    exit;
}
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-key fa-3x mb-3 text-primary"></i>
                        <h3 class="card-title">Ganti Password</h3>
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

                    <form method="POST" action="index.php?page=change_password" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="current_password" class="form-label">Password Saat Ini <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       name="current_password" 
                                       class="form-control" 
                                       id="current_password"
                                       required>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('current_password')">
                                    <i class="fas fa-eye" id="current_password_icon"></i>
                                </button>
                                <div class="invalid-feedback">
                                    Password saat ini harus diisi
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" 
                                       name="new_password" 
                                       class="form-control" 
                                       id="new_password"
                                       required
                                       minlength="6"
                                       onkeyup="checkPasswordStrength(this.value)">
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('new_password')">
                                    <i class="fas fa-eye" id="new_password_icon"></i>
                                </button>
                                <div class="invalid-feedback">
                                    Password baru minimal 6 karakter
                                </div>
                            </div>
                            <div id="password-strength" class="mt-2"></div>
                        </div>

                        <div class="mb-4">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-key"></i>
                                </span>
                                <input type="password" 
                                       name="confirm_password" 
                                       class="form-control" 
                                       id="confirm_password"
                                       required>
                                <button class="btn btn-outline-secondary" 
                                        type="button" 
                                        onclick="togglePassword('confirm_password')">
                                    <i class="fas fa-eye" id="confirm_password_icon"></i>
                                </button>
                                <div class="invalid-feedback">
                                    Password tidak cocok
                                </div>
                            </div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-1"></i> Simpan Password Baru
                            </button>
                            <a href="index.php?page=dashboard" class="btn btn-secondary">
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
    var newPassword = document.getElementById('new_password')
    var confirmPassword = document.getElementById('confirm_password')

    // Password match validation
    function validatePassword() {
        if (newPassword.value != confirmPassword.value) {
            confirmPassword.setCustomValidity('Passwords tidak cocok')
        } else {
            confirmPassword.setCustomValidity('')
        }
    }

    newPassword.onchange = validatePassword
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

// Toggle password visibility
function togglePassword(inputId) {
    const input = document.getElementById(inputId)
    const icon = document.getElementById(inputId + '_icon')
    
    if (input.type === 'password') {
        input.type = 'text'
        icon.classList.remove('fa-eye')
        icon.classList.add('fa-eye-slash')
    } else {
        input.type = 'password'
        icon.classList.remove('fa-eye-slash')
        icon.classList.add('fa-eye')
    }
}

// Check password strength
function checkPasswordStrength(password) {
    const strengthDiv = document.getElementById('password-strength')
    const strength = {
        1: 'Sangat Lemah',
        2: 'Lemah',
        3: 'Sedang',
        4: 'Kuat',
        5: 'Sangat Kuat'
    }
    
    let strengthValue = 0
    
    if (password.length >= 6) strengthValue++
    if (password.length >= 8) strengthValue++
    if (password.match(/[a-z]+/)) strengthValue++
    if (password.match(/[A-Z]+/)) strengthValue++
    if (password.match(/[0-9]+/)) strengthValue++
    if (password.match(/[$@#&!]+/)) strengthValue++

    // Normalize to 5 levels
    strengthValue = Math.min(Math.ceil(strengthValue * (5/6)), 5)
    
    const backgroundColor = {
        1: '#dc3545',
        2: '#ffc107',
        3: '#fd7e14',
        4: '#20c997',
        5: '#198754'
    }
    
    strengthDiv.innerHTML = `
        <div class="progress" style="height: 5px;">
            <div class="progress-bar" 
                 role="progressbar" 
                 style="width: ${strengthValue * 20}%; background-color: ${backgroundColor[strengthValue]}" 
                 aria-valuenow="${strengthValue * 20}" 
                 aria-valuemin="0" 
                 aria-valuemax="100"></div>
        </div>
        <small class="text-muted">Kekuatan Password: ${strength[strengthValue]}</small>
    `
}
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
