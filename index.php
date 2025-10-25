<?php
require_once 'config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NETTOCAR - Car Wash Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="index.php">NETTOCAR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row">
            <div class="col-md-12">
                <h1 class="mb-4">Welcome to NETTOCAR</h1>
                
                <?php if (isLoggedIn()): ?>
                    <div class="alert alert-info">
                        You are logged in as <strong><?php echo htmlspecialchars($_SESSION['role']); ?></strong>
                    </div>

                    <?php if (hasRole('admin')): ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Admin Dashboard</h5>
                                        <p class="card-text">Manage agencies and view statistics</p>
                                        <a href="admin/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif (hasRole('agency')): ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Agency Dashboard</h5>
                                        <p class="card-text">Manage your services and reservations</p>
                                        <a href="agency/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php elseif (hasRole('client')): ?>
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title">Client Dashboard</h5>
                                        <p class="card-text">Book services and view your reservations</p>
                                        <a href="client/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="alert alert-warning">
                        Please <a href="auth/login.php">login</a> or <a href="auth/register.php">register</a> to continue.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
