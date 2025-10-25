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
        
        h1 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 2rem;
            color: var(--text-dark);
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
        
        .table {
            background: white;
            border-radius: 6px;
            overflow: hidden;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            border: 1px solid var(--border-color);
        }
        
        .table thead {
            background-color: var(--bg-light);
            border-bottom: 2px solid var(--border-color);
        }
        
        .table thead th {
            font-weight: 600;
            color: var(--text-dark);
            padding: 1rem;
            border: none;
        }
        
        .table tbody td {
            padding: 1rem;
            color: var(--text-light);
            border-color: var(--border-color);
        }
        
        .table tbody tr:hover {
            background-color: var(--bg-light);
        }
        
        .badge {
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
        }
        
        .badge.bg-success {
            background-color: #10b981 !important;
        }
        
        .badge.bg-warning {
            background-color: #f59e0b !important;
            color: white;
        }
        
        .badge.bg-secondary {
            background-color: #6b7280 !important;
        }
        
        .table-container {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 1.5rem;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
    <!-- Modern navbar -->
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
        <h1>📋 My Reservations</h1>

        <div class="row">
            <div class="col-md-12">
                <!-- Modern button styling -->
                <a href="book-service.php" class="btn btn-primary mb-4">+ Book a Service</a>

                <!-- Modern table styling -->
                <div class="table-container">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>🏢 Agency</th>
                                <th>🔧 Service</th>
                                <th>📅 Date & Time</th>
                                <th>✅ Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($reservation = $reservations->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($reservation['agency_name']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['service_name']); ?></td>
                                    <td><?php echo htmlspecialchars($reservation['datetime']); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo $reservation['status'] === 'finished' ? 'success' : ($reservation['status'] === 'in_progress' ? 'warning' : 'secondary'); ?>">
                                            <?php echo htmlspecialchars($reservation['status']); ?>
                                        </span>
                                    </td>
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
