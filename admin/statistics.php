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

        .period-filter {
            display: flex;
            gap: 0.5rem;
            margin-bottom: 2rem;
            flex-wrap: wrap;
        }

        .btn-period {
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.6rem 1.2rem;
            border: 2px solid #ff4500;
            color: #ff4500;
            background: white;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-period:hover,
        .btn-period.active {
            background: #ff4500;
            color: white;
        }

        .stat-card {
            border: none;
            border-radius: 6px;
            color: white;
            padding: 1.5rem;
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

        .card {
            border: none;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            background: white;
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid #e9ecef;
            padding: 1.25rem;
        }

        .card-header h5 {
            font-weight: 600;
            color: #1a1a1a;
            margin: 0;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: #ff4500;
            color: white;
        }

        .table thead th {
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f9f9f9;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
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
                        <a class="nav-link" href="dashboard.php">Dashboard</a>
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
            <h1>System Statistics</h1>
        </div>

        <div class="period-filter">
            <a href="?period=week" class="btn-period <?php echo $period === 'week' ? 'active' : ''; ?>">This Week</a>
            <a href="?period=month" class="btn-period <?php echo $period === 'month' ? 'active' : ''; ?>">This Month</a>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="stat-card primary">
                    <div class="stat-label">Total Users</div>
                    <p class="stat-value"><?php echo $total_users; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card success">
                    <div class="stat-label">Total Agencies</div>
                    <p class="stat-value"><?php echo $total_agencies; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card info">
                    <div class="stat-label">Total Services</div>
                    <p class="stat-value"><?php echo $total_services; ?></p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card warning">
                    <div class="stat-label">Total Revenue</div>
                    <p class="stat-value">$<?php echo number_format($revenue_stats['total_revenue'] ?? 0, 0); ?></p>
                </div>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Reservations</h5>
                        <p class="stat-value"><?php echo $overall_stats['total_reservations'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Completed</h5>
                        <p class="stat-value"><?php echo $overall_stats['completed'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">In Progress</h5>
                        <p class="stat-value"><?php echo $overall_stats['in_progress'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Waiting</h5>
                        <p class="stat-value"><?php echo $overall_stats['waiting'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5>Top Agencies</h5>
            </div>
            <div class="table-responsive">
                <table class="table">
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
