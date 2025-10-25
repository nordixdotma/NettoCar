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
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
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
        <h1 class="mb-4">System Statistics</h1>

        <div class="mb-3">
            <a href="?period=week" class="btn btn-outline-primary <?php echo $period === 'week' ? 'active' : ''; ?>">This Week</a>
            <a href="?period=month" class="btn btn-outline-primary <?php echo $period === 'month' ? 'active' : ''; ?>">This Month</a>
        </div>

        <!-- System Overview -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Users</h5>
                        <p class="card-text display-5"><?php echo $total_users; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Agencies</h5>
                        <p class="card-text display-5"><?php echo $total_agencies; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Services</h5>
                        <p class="card-text display-5"><?php echo $total_services; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <p class="card-text display-5">$<?php echo number_format($revenue_stats['total_revenue'] ?? 0, 0); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Period Statistics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Total Reservations</h5>
                        <p class="card-text display-5"><?php echo $overall_stats['total_reservations'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Completed</h5>
                        <p class="card-text display-5"><?php echo $overall_stats['completed'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">In Progress</h5>
                        <p class="card-text display-5"><?php echo $overall_stats['in_progress'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Waiting</h5>
                        <p class="card-text display-5"><?php echo $overall_stats['waiting'] ?? 0; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top Agencies -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Top Agencies</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
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
                                    <td><?php echo htmlspecialchars($agency['name']); ?></td>
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
