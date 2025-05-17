<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="container">
    <div class="row justify-content-center align-items-center min-vh-100">
        <div class="col-md-6 text-center">
            <div class="error-content">
                <i class="fas fa-exclamation-triangle fa-5x text-warning mb-4"></i>
                <h1 class="display-1 fw-bold">404</h1>
                <h2 class="mb-4">Halaman Tidak Ditemukan</h2>
                <p class="text-muted mb-4">
                    Maaf, halaman yang Anda cari tidak ditemukan atau tidak dapat diakses.
                </p>
                <div class="d-grid gap-2 col-6 mx-auto">
                    <a href="index.php?page=dashboard" class="btn btn-primary">
                        <i class="fas fa-home me-1"></i> Kembali ke Dashboard
                    </a>
                    <button onclick="history.back()" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-1"></i> Kembali ke Halaman Sebelumnya
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.error-content {
    animation: fadeInUp 0.5s ease-out;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.error-content i {
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 20%, 50%, 80%, 100% {
        transform: translateY(0);
    }
    40% {
        transform: translateY(-30px);
    }
    60% {
        transform: translateY(-15px);
    }
}
</style>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
