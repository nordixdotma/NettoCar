<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAdmin();

// Get statistics
$agencies_count = $con->query("SELECT COUNT(*) as count FROM agencies")->fetch_assoc()['count'];
$reservations_count = $con->query("SELECT COUNT(*) as count FROM reservations")->fetch_assoc()['count'];
$payments_count = $con->query("SELECT COUNT(*) as count FROM payments WHERE status = 'completed'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - NETTOCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">NETTOCAR</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">Admin: <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Admin Dashboard</h1>

        <div class="row">
            <div class="col-md-4 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Agencies</h5>
                        <p class="card-text display-4"><?php echo $agencies_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Reservations</h5>
                        <p class="card-text display-4"><?php echo $reservations_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Completed Payments</h5>
                        <p class="card-text display-4"><?php echo $payments_count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4">
            <div class="col-md-12">
                <h3>Quick Actions</h3>
                <a href="agencies.php" class="btn btn-primary me-2">Manage Agencies</a>
                <a href="payments.php" class="btn btn-success">View Payments</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
