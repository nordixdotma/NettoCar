<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAdmin();

$period = $_GET['period'] ?? 'month';

// Calculate date range
if ($period === 'week') {
    $start_date = date('Y-m-d', strtotime('monday this week'));
    $end_date = date('Y-m-d', strtotime('sunday this week'));
} else {
    $start_date = date('Y-m-01');
    $end_date = date('Y-m-t');
}

// Get overall statistics
$stmt = $con->prepare("SELECT COUNT(*) as total_reservations,
                      SUM(CASE WHEN status = 'finished' THEN 1 ELSE 0 END) as completed,
                      SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                      SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) as waiting
                      FROM reservations
                      WHERE DATE(datetime) BETWEEN ? AND ?");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$overall_stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get revenue statistics
$stmt = $con->prepare("SELECT SUM(amount) as total_revenue, COUNT(*) as total_payments
                      FROM payments
                      WHERE status = 'completed' AND DATE(date) BETWEEN ? AND ?");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$revenue_stats = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Get top agencies
$stmt = $con->prepare("SELECT a.name, COUNT(r.id) as reservations, SUM(s.price) as revenue
                      FROM reservations r
                      JOIN agencies a ON r.agency_id = a.id
                      JOIN services s ON r.service_id = s.id
                      WHERE r.status = 'finished' AND DATE(r.datetime) BETWEEN ? AND ?
                      GROUP BY a.id
                      ORDER BY reservations DESC
                      LIMIT 10");
$stmt->bind_param("ss", $start_date, $end_date);
$stmt->execute();
$top_agencies = $stmt->get_result();
$stmt->close();

// Get user statistics
$total_users = $con->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$total_agencies = $con->query("SELECT COUNT(*) as count FROM agencies")->fetch_assoc()['count'];
$total_services = $con->query("SELECT COUNT(*) as count FROM services")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Statistics - NETTOCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #ff4500;
            --primary-dark: #e63e00;
            --primary-light: #ff6b35;
            --bg-light: #f8f9fa;
            --border-radius: 6px;
        }
        
        body {
            background-color: var(--bg-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
        }
        
        .nav-link {
            font-weight: 500;
            transition: opacity 0.2s;
        }
        
        .nav-link:hover {
            opacity: 0.8;
        }
        
        .stat-card {
            border: none;
            border-radius: var(--border-radius);
            color: white;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
        
        .stat-card.primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        }
        
        .stat-card.success {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        }
        
        .stat-card.info {
            background: linear-gradient(135deg, #3b82f6 0%, #60a5fa 100%);
        }
        
        .stat-card.warning {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        }
        
        .stat-card h5 {
            font-weight: 600;
            font-size: 0.9rem;
            opacity: 0.95;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .display-5 {
            font-weight: 700;
            font-size: 2rem;
        }
        
        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 500;
        }
        
        .btn-outline-primary:hover,
        .btn-outline-primary.active {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        
        .card-header {
            background-color: transparent;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem;
        }
        
        .table {
            border-radius: var(--border-radius);
        }
        
        .table thead {
            background-color: var(--primary-color) !important;
            color: white;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        h1 {
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
        }
        
        /* Improved period filter buttons styling */
        .period-filter {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">NETTOCAR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <h1>System Statistics</h1>

        <!-- Improved period filter with better styling -->
        <div class="period-filter">
            <a href="?period=week" class="btn btn-outline-primary <?php echo $period === 'week' ? 'active' : ''; ?>">This Week</a>
            <a href="?period=month" class="btn btn-outline-primary <?php echo $period === 'month' ? 'active' : ''; ?>">This Month</a>
        </div>

        <!-- System Overview -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <h5>Total Users</h5>
                    <p class="display-5 mb-0"><?php echo $total_users; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <h5>Total Agencies</h5>
                    <p class="display-5 mb-0"><?php echo $total_agencies; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <h5>Total Services</h5>
                    <p class="display-5 mb-0"><?php echo $total_services; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <h5>Total Revenue</h5>
                    <p class="display-5 mb-0">$<?php echo number_format($revenue_stats['total_revenue'] ?? 0, 0); ?></p>
                </div>
            </div>
        </div>

        <!-- Period Statistics -->
        <div class="row mb-4 g-3">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Reservations</h5>
                        <p class="display-5 mb-0"><?php echo $overall_stats['total_reservations'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Completed</h5>
                        <p class="display-5 mb-0"><?php echo $overall_stats['completed'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">In Progress</h5>
                        <p class="display-5 mb-0"><?php echo $overall_stats['in_progress'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Waiting</h5>
                        <p class="display-5 mb-0"><?php echo $overall_stats['waiting'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Agencies -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top Agencies</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Agency Name</th>
                                <th>Reservations</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($agency = $top_agencies->fetch_assoc()): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($agency['name']); ?></strong></td>
                                    <td><?php echo $agency['reservations']; ?></td>
                                    <td>$<?php echo number_format($agency['revenue'], 2); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
