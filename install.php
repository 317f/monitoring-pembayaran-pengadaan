<?php
/**
 * Installation Script for Monitoring Pembayaran Pengadaan
 */

// Check PHP version
$minPhpVersion = '7.4.0';
if (version_compare(PHP_VERSION, $minPhpVersion, '<')) {
    die("PHP version $minPhpVersion or higher is required. Current version: " . PHP_VERSION);
}

// Check required extensions
$requiredExtensions = ['pdo', 'pdo_mysql', 'gd', 'fileinfo'];
$missingExtensions = [];
foreach ($requiredExtensions as $ext) {
    if (!extension_loaded($ext)) {
        $missingExtensions[] = $ext;
    }
}

if (!empty($missingExtensions)) {
    die("Required PHP extensions missing: " . implode(', ', $missingExtensions));
}

// Function to test database connection
function testDatabaseConnection($host, $user, $pass, $dbname) {
    try {
        $dsn = "mysql:host=$host;dbname=$dbname";
        $pdo = new PDO($dsn, $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return ['success' => true, 'message' => 'Database connection successful'];
    } catch (PDOException $e) {
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

// Function to create necessary directories
function createDirectories() {
    $directories = [
        'public/uploads',
        'views/errors',
        'views/auth',
        'views/payments',
        'views/layouts'
    ];

    $results = [];
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            if (mkdir($dir, 0755, true)) {
                $results[] = "Created directory: $dir";
            } else {
                $results[] = "Failed to create directory: $dir";
            }
        } else {
            $results[] = "Directory exists: $dir";
        }
    }
    return $results;
}

// Function to check file permissions
function checkPermissions() {
    $paths = [
        'public/uploads',
        'config',
        '.htaccess'
    ];

    $results = [];
    foreach ($paths as $path) {
        if (file_exists($path)) {
            $perms = substr(sprintf('%o', fileperms($path)), -4);
            $writable = is_writable($path);
            $results[] = [
                'path' => $path,
                'permissions' => $perms,
                'writable' => $writable
            ];
        }
    }
    return $results;
}

// Process form submission
$message = '';
$error = '';
$installationComplete = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $dbHost = $_POST['db_host'] ?? '';
    $dbUser = $_POST['db_user'] ?? '';
    $dbPass = $_POST['db_pass'] ?? '';
    $dbName = $_POST['db_name'] ?? '';

    if (empty($dbHost) || empty($dbUser) || empty($dbName)) {
        $error = 'All database fields are required except password.';
    } else {
        // Test database connection
        $dbTest = testDatabaseConnection($dbHost, $dbUser, $dbPass, $dbName);
        
        if ($dbTest['success']) {
            // Create config file
            $configContent = "<?php
define('DB_HOST', '" . addslashes($dbHost) . "');
define('DB_USER', '" . addslashes($dbUser) . "');
define('DB_PASS', '" . addslashes($dbPass) . "');
define('DB_NAME', '" . addslashes($dbName) . "');

try {
    \$pdo = new PDO(\"mysql:host=\".DB_HOST.\";dbname=\".DB_NAME, DB_USER, DB_PASS);
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException \$e) {
    die(\"Connection failed: \" . \$e->getMessage());
}
?>";
            
            if (file_put_contents('config/database.php', $configContent)) {
                // Create directories
                $dirResults = createDirectories();
                
                // Import database schema
                try {
                    $pdo = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUser, $dbPass);
                    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                    
                    $schema = file_get_contents('database/schema.sql');
                    $pdo->exec($schema);
                    
                    $message = "Installation completed successfully!";
                    $installationComplete = true;
                } catch (PDOException $e) {
                    $error = "Error importing database schema: " . $e->getMessage();
                }
            } else {
                $error = "Failed to create config file. Check permissions.";
            }
        } else {
            $error = "Database connection failed: " . $dbTest['message'];
        }
    }
}

// Check system requirements
$systemChecks = [
    'php_version' => [
        'name' => 'PHP Version',
        'required' => '>= ' . $minPhpVersion,
        'current' => PHP_VERSION,
        'status' => version_compare(PHP_VERSION, $minPhpVersion, '>=')
    ],
    'pdo' => [
        'name' => 'PDO Extension',
        'required' => 'Enabled',
        'current' => extension_loaded('pdo') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('pdo')
    ],
    'pdo_mysql' => [
        'name' => 'PDO MySQL Extension',
        'required' => 'Enabled',
        'current' => extension_loaded('pdo_mysql') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('pdo_mysql')
    ],
    'gd' => [
        'name' => 'GD Extension',
        'required' => 'Enabled',
        'current' => extension_loaded('gd') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('gd')
    ],
    'fileinfo' => [
        'name' => 'Fileinfo Extension',
        'required' => 'Enabled',
        'current' => extension_loaded('fileinfo') ? 'Enabled' : 'Disabled',
        'status' => extension_loaded('fileinfo')
    ]
];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Installation - Monitoring Pembayaran Pengadaan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <i class="fas fa-cog fa-3x text-primary mb-3"></i>
                            <h2>Installation</h2>
                            <p class="text-muted">Monitoring Pembayaran Pengadaan</p>
                        </div>

                        <?php if ($error): ?>
                            <div class="alert alert-danger">
                                <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <?php if ($message): ?>
                            <div class="alert alert-success">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!$installationComplete): ?>
                            <!-- System Requirements -->
                            <h5 class="mb-3">System Requirements</h5>
                            <div class="table-responsive mb-4">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Requirement</th>
                                            <th>Required</th>
                                            <th>Current</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($systemChecks as $check): ?>
                                            <tr>
                                                <td><?php echo $check['name']; ?></td>
                                                <td><?php echo $check['required']; ?></td>
                                                <td><?php echo $check['current']; ?></td>
                                                <td>
                                                    <?php if ($check['status']): ?>
                                                        <span class="badge bg-success">OK</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger">Failed</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Database Configuration Form -->
                            <h5 class="mb-3">Database Configuration</h5>
                            <form method="POST" action="" class="needs-validation" novalidate>
                                <div class="mb-3">
                                    <label for="db_host" class="form-label">Database Host</label>
                                    <input type="text" class="form-control" id="db_host" name="db_host" value="localhost" required>
                                </div>

                                <div class="mb-3">
                                    <label for="db_name" class="form-label">Database Name</label>
                                    <input type="text" class="form-control" id="db_name" name="db_name" value="procurement_db" required>
                                </div>

                                <div class="mb-3">
                                    <label for="db_user" class="form-label">Database Username</label>
                                    <input type="text" class="form-control" id="db_user" name="db_user" required>
                                </div>

                                <div class="mb-3">
                                    <label for="db_pass" class="form-label">Database Password</label>
                                    <input type="password" class="form-control" id="db_pass" name="db_pass">
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-check me-1"></i> Install Application
                                    </button>
                                </div>
                            </form>
                        <?php else: ?>
                            <div class="text-center">
                                <p class="mb-4">Installation has been completed successfully.</p>
                                <div class="alert alert-warning">
                                    <strong>Important:</strong> Please delete this installation file (install.php) for security reasons.
                                </div>
                                <a href="index.php" class="btn btn-primary">
                                    <i class="fas fa-home me-1"></i> Go to Application
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
</body>
</html>
