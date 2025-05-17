<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-lg">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <i class="fas fa-chart-line fa-3x mb-3 text-primary"></i>
                        <h3 class="card-title mb-3">Login</h3>
                        <p class="text-muted">Monitoring Pembayaran Pengadaan</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?php echo $error; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="index.php?page=login" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" 
                                       name="username" 
                                       class="form-control" 
                                       placeholder="Username"
                                       required
                                       autofocus>
                                <div class="invalid-feedback">
                                    Username harus diisi
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" 
                                       name="password" 
                                       class="form-control" 
                                       placeholder="Password"
                                       required>
                                <div class="invalid-feedback">
                                    Password harus diisi
                                </div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-sign-in-alt me-2"></i>Login
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="text-center mt-4 text-muted">
                <small>&copy; <?php echo date('Y'); ?> Monitoring Pembayaran Pengadaan</small>
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
</script>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
