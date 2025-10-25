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
    <link rel="stylesheet" href="../app/globals.css">
    <style>
        :root {
            --primary-color: #ff4500;
            --primary-light: #ff6b35;
            --primary-dark: #e63e00;
            --bg-light: #f8f9fa;
            --border-color: #e0e0e0;
            --text-dark: #1a1a1a;
            --text-light: #666666;
        }
        
        body {
            background-color: var(--bg-light);
            color: var(--text-dark);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .navbar {
            background-color: white !important;
            border-bottom: 1px solid var(--border-color);
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            padding: 0.75rem 0;
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--primary-color) !important;
            letter-spacing: -0.5px;
        }
        
        .nav-link {
            color: var(--text-dark) !important;
            font-weight: 500;
            font-size: 0.95rem;
            transition: color 0.2s;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
        }
        
        .container {
            max-width: 1200px;
        }
        
        h1 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--text-dark);
        }
        
        h3 {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        
        .stat-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        
        .stat-card.primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
            color: white;
            border: none;
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
            color: white;
            border: none;
        }
        
        .stat-card .card-title {
            font-weight: 600;
            font-size: 0.95rem;
            opacity: 0.95;
            margin-bottom: 0.75rem;
        }
        
        .stat-card .card-text {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .btn {
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.6rem 1.2rem;
            border: none;
            transition: all 0.2s;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            color: white;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(255, 69, 0, 0.2);
        }
        
        .btn-success {
            background-color: #10b981;
        }
        
        .btn-success:hover {
            background-color: #059669;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(16, 185, 129, 0.2);
        }
        
        .quick-actions {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <!-- Modern navbar with clean design -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="../index.php">🚗 NETTOCAR</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <span class="nav-link">👤 <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">🚪 Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>📊 Admin Dashboard</h1>

        <!-- Modern stat cards with gradients -->
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card stat-card primary">
                    <div class="card-body">
                        <h5 class="card-title">Total Agencies</h5>
                        <p class="card-text"><?php echo $agencies_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card stat-card success">
                    <div class="card-body">
                        <h5 class="card-title">Total Reservations</h5>
                        <p class="card-text"><?php echo $reservations_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card stat-card info">
                    <div class="card-body">
                        <h5 class="card-title">Completed Payments</h5>
                        <p class="card-text"><?php echo $payments_count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick actions section with modern styling -->
        <div class="quick-actions">
            <h3>⚡ Quick Actions</h3>
            <a href="agencies.php" class="btn btn-primary me-2">Manage Agencies</a>
            <a href="payments.php" class="btn btn-success">View Payments</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
