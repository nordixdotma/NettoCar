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
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1a1a1a;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: #ff4500 !important;
            letter-spacing: -0.5px;
        }

        .nav-link {
            color: #1a1a1a !important;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: #ff4500 !important;
        }

        .container {
            max-width: 1200px;
        }

        .page-header {
            margin-bottom: 2rem;
        }

        .page-header h1 {
            font-weight: 700;
            font-size: 2rem;
            color: #1a1a1a;
            margin: 0;
        }

        .stat-card {
            background: white;
            border-radius: 6px;
            padding: 1.5rem;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        .stat-card.primary {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            color: white;
        }

        .stat-card.success {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: white;
        }

        .stat-card.info {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            color: white;
        }

        .stat-label {
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0.95;
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }

        .quick-actions {
            background: white;
            border-radius: 6px;
            padding: 1.5rem;
            margin-top: 2rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
        }

        .quick-actions h3 {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: #1a1a1a;
        }

        .btn {
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.6rem 1.2rem;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 69, 0, 0.3);
            color: white;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: white;
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(16, 185, 129, 0.3);
            color: white;
        }

        .btn-info {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            color: white;
        }

        .btn-info:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(59, 130, 246, 0.3);
            color: white;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
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
        <div class="page-header">
            <h1>Admin Dashboard</h1>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card stat-card primary">
                    <div class="stat-label">Total Agencies</div>
                    <p class="stat-value"><?php echo $agencies_count; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card success">
                    <div class="stat-label">Total Reservations</div>
                    <p class="stat-value"><?php echo $reservations_count; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card stat-card info">
                    <div class="stat-label">Completed Payments</div>
                    <p class="stat-value"><?php echo $payments_count; ?></p>
                </div>
            </div>
        </div>

        <div class="quick-actions">
            <h3>Quick Actions</h3>
            <a href="agencies.php" class="btn btn-primary me-2">Manage Agencies</a>
            <a href="payments.php" class="btn btn-success me-2">View Payments</a>
            <a href="statistics.php" class="btn btn-info">View Statistics</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
