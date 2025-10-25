<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAgency();

// Get agency
$stmt = $con->prepare("SELECT a.*, p.statistics_enabled FROM agencies a JOIN packs p ON a.pack_id = p.id WHERE a.owner_user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$agency = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$agency) {
    header("Location: create-agency.php");
    exit();
}

// Check if statistics are enabled for this pack
if (!$agency['statistics_enabled']) {
    $error_message = "Statistics are not available with your current plan. Please upgrade to Standard or Premium.";
} else {
    $agency_id = $agency['id'];
    $period = $_GET['period'] ?? 'week';

    // Calculate date range
    if ($period === 'week') {
        $start_date = date('Y-m-d', strtotime('monday this week'));
        $end_date = date('Y-m-d', strtotime('sunday this week'));
    } else {
        $start_date = date('Y-m-01');
        $end_date = date('Y-m-t');
    }

    // Get reservation statistics
    $stmt = $con->prepare("SELECT COUNT(*) as total, 
                          SUM(CASE WHEN status = 'finished' THEN 1 ELSE 0 END) as completed,
                          SUM(CASE WHEN status = 'in_progress' THEN 1 ELSE 0 END) as in_progress,
                          SUM(CASE WHEN status = 'waiting' THEN 1 ELSE 0 END) as waiting
                          FROM reservations 
                          WHERE agency_id = ? AND DATE(datetime) BETWEEN ? AND ?");
    $stmt->bind_param("iss", $agency_id, $start_date, $end_date);
    $stmt->execute();
    $stats = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Get revenue statistics
    $stmt = $con->prepare("SELECT SUM(s.price) as total_revenue, COUNT(r.id) as total_services
                          FROM reservations r
                          JOIN services s ON r.service_id = s.id
                          WHERE r.agency_id = ? AND r.status = 'finished' AND DATE(r.datetime) BETWEEN ? AND ?");
    $stmt->bind_param("iss", $agency_id, $start_date, $end_date);
    $stmt->execute();
    $revenue = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Get top services
    $stmt = $con->prepare("SELECT s.name, COUNT(r.id) as bookings, SUM(s.price) as revenue
                          FROM reservations r
                          JOIN services s ON r.service_id = s.id
                          WHERE r.agency_id = ? AND DATE(r.datetime) BETWEEN ? AND ?
                          GROUP BY s.id
                          ORDER BY bookings DESC
                          LIMIT 5");
    $stmt->bind_param("iss", $agency_id, $start_date, $end_date);
    $stmt->execute();
    $top_services = $stmt->get_result();
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistics - NETTOCAR</title>
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
        <h1 class="mb-4">Statistics</h1>

        <?php if (isset($error_message)): ?>
            <div class="alert alert-warning"><?php echo htmlspecialchars($error_message); ?></div>
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        <?php else: ?>
            <div class="mb-3">
                <a href="?period=week" class="btn btn-outline-primary <?php echo $period === 'week' ? 'active' : ''; ?>">This Week</a>
                <a href="?period=month" class="btn btn-outline-primary <?php echo $period === 'month' ? 'active' : ''; ?>">This Month</a>
            </div>

            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Total Reservations</h5>
                            <p class="card-text display-5"><?php echo $stats['total'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5 class="card-title">Completed</h5>
                            <p class="card-text display-5"><?php echo $stats['completed'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5 class="card-title">In Progress</h5>
                            <p class="card-text display-5"><?php echo $stats['in_progress'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Waiting</h5>
                            <p class="card-text display-5"><?php echo $stats['waiting'] ?? 0; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Revenue</h5>
                            <p class="card-text display-4">$<?php echo number_format($revenue['total_revenue'] ?? 0, 2); ?></p>
                            <p class="text-muted">From <?php echo $revenue['total_services'] ?? 0; ?> completed services</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Average Revenue per Service</h5>
                            <p class="card-text display-4">
                                $<?php 
                                $avg = ($revenue['total_services'] ?? 0) > 0 ? ($revenue['total_revenue'] ?? 0) / $revenue['total_services'] : 0;
                                echo number_format($avg, 2); 
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Top Services</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Service Name</th>
                                    <th>Bookings</th>
                                    <th>Revenue</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($service = $top_services->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($service['name']); ?></td>
                                        <td><?php echo $service['bookings']; ?></td>
                                        <td>$<?php echo number_format($service['revenue'], 2); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
