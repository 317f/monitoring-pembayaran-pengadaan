</div><!-- /.container -->
    </div><!-- /.main-content -->
    
    <footer class="bg-dark text-light py-4 mt-auto">
        <div class="container">
            <div class="row">
                <div class="col-md-6 text-center text-md-start">
                    <p class="mb-0">&copy; <?php echo date('Y'); ?> Monitoring Pembayaran Pengadaan</p>
                </div>
                <div class="col-md-6 text-center text-md-end">
                    <p class="mb-0">Version 1.0.0</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- Theme Toggle Script -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const themeToggle = document.getElementById('theme-toggle');
            
            // Check for saved theme preference
            const savedTheme = localStorage.getItem('theme');
            if (savedTheme) {
                document.body.setAttribute('data-theme', savedTheme);
                themeToggle.checked = savedTheme === 'dark';
            }
            
            // Theme toggle handler
            themeToggle.addEventListener('change', function() {
                if (this.checked) {
                    document.body.setAttribute('data-theme', 'dark');
                    localStorage.setItem('theme', 'dark');
                } else {
                    document.body.setAttribute('data-theme', 'light');
                    localStorage.setItem('theme', 'light');
                }
            });
            
            // Initialize DataTables
            if (document.querySelector('.datatable')) {
                $('.datatable').DataTable({
                    language: {
                        url: "//cdn.datatables.net/plug-ins/1.11.5/i18n/id.json"
                    },
                    responsive: true,
                    pageLength: 10,
                    order: [[0, 'desc']]
                });
            }
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // File input custom text
            document.querySelectorAll('.custom-file-input').forEach(function(input) {
                input.addEventListener('change', function(e) {
                    var fileName = e.target.files[0].name;
                    var nextSibling = e.target.nextElementSibling;
                    nextSibling.innerText = fileName;
                });
            });
            
            // Currency format input
            document.querySelectorAll('.currency-input').forEach(function(input) {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, "");
                    value = new Intl.NumberFormat('id-ID').format(value);
                    e.target.value = value;
                });
            });
            
            // Due date highlighting
            document.querySelectorAll('.due-date').forEach(function(element) {
                const dueDate = new Date(element.dataset.date);
                const today = new Date();
                const diffTime = dueDate - today;
                const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
                
                if (diffDays <= 0) {
                    element.classList.add('danger');
                } else if (diffDays <= 7) {
                    element.classList.add('warning');
                }
            });
        });
        
        // Print function
        function printContent(elementId) {
            const printContents = document.getElementById(elementId).innerHTML;
            const originalContents = document.body.innerHTML;
            
            document.body.innerHTML = printContents;
            window.print();
            document.body.innerHTML = originalContents;
            
            // Reinitialize scripts after printing
            location.reload();
        }
        
        // Confirm delete
        function confirmDelete(id, type) {
            if (confirm('Apakah Anda yakin ingin menghapus ' + type + ' ini?')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
</body>
</html>
