<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireClient();

// Get client's reservations
$stmt = $con->prepare("SELECT r.*, s.name as service_name, a.name as agency_name FROM reservations r JOIN services s ON r.service_id = s.id JOIN agencies a ON r.agency_id = a.id WHERE r.client_user_id = ? ORDER BY r.datetime DESC LIMIT 10");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$reservations = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Client Dashboard - NETTOCAR</title>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-weight: 700;
            font-size: 2rem;
            color: #1a1a1a;
            margin: 0;
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

        .card {
            border: none;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            background: white;
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

        .badge {
            border-radius: 4px;
            padding: 0.4rem 0.8rem;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .badge.bg-success {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%) !important;
            color: white;
        }

        .badge.bg-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%) !important;
            color: white;
        }

        .badge.bg-secondary {
            background: linear-gradient(135deg, #6b7280 0%, #9ca3af 100%) !important;
            color: white;
        }

        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #666;
        }

        .empty-state p {
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
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
                        <span class="nav-link">Client: <?php echo htmlspecialchars($_SESSION['name']); ?></span>
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
            <h1>My Reservations</h1>
            <a href="book-service.php" class="btn btn-primary">Book a Service</a>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $hasReservations = false;
                        while ($reservation = $reservations->fetch_assoc()): 
                            $hasReservations = true;
                        ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($reservation['agency_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($reservation['service_name']); ?></td>
                                <td><?php echo htmlspecialchars($reservation['datetime']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $reservation['status'] === 'finished' ? 'success' : ($reservation['status'] === 'in_progress' ? 'warning' : 'secondary'); ?>">
                                        <?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $reservation['status']))); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                <?php if (!$hasReservations): ?>
                    <div class="empty-state">
                        <p>You don't have any reservations yet.</p>
                        <a href="book-service.php" class="btn btn-primary">Book Your First Service</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
