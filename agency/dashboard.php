<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAgency();

// Get agency info
$stmt = $con->prepare("SELECT a.*, p.name as pack_name FROM agencies a JOIN packs p ON a.pack_id = p.id WHERE a.owner_user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$agency = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$agency) {
    $agency = null;
}

// Get today's reservations
$today_reservations = 0;
if ($agency) {
    $stmt = $con->prepare("SELECT COUNT(*) as count FROM reservations WHERE agency_id = ? AND DATE(datetime) = CURDATE()");
    $stmt->bind_param("i", $agency['id']);
    $stmt->execute();
    $today_reservations = $stmt->get_result()->fetch_assoc()['count'];
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agency Dashboard - NETTOCAR</title>
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

        .info-card {
            background: white;
            border-radius: 6px;
            padding: 1.5rem;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .info-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        .info-card h5 {
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 1rem;
            font-size: 1.1rem;
        }

        .info-card p {
            color: #666;
            margin-bottom: 0.75rem;
            font-size: 0.9rem;
        }

        .info-card strong {
            color: #1a1a1a;
        }

        .stat-card {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            color: white;
            border-radius: 6px;
            padding: 1.5rem;
            border: none;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            box-shadow: 0 8px 16px rgba(255, 69, 0, 0.2);
            transform: translateY(-4px);
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

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
            color: white;
        }

        .btn-warning:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(245, 158, 11, 0.3);
            color: white;
        }

        .alert {
            border: none;
            border-radius: 6px;
            border-left: 4px solid;
            margin-bottom: 1.5rem;
        }

        .alert-warning {
            background: #fffbeb;
            color: #92400e;
            border-left-color: #fcd34d;
        }

        .alert-link {
            color: #ff4500;
            font-weight: 600;
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
                        <span class="nav-link">Agency: <?php echo htmlspecialchars($_SESSION['name']); ?></span>
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
            <h1>Agency Dashboard</h1>
        </div>

        <?php if ($agency): ?>
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="info-card">
                        <h5><?php echo htmlspecialchars($agency['name']); ?></h5>
                        <p><strong>Address:</strong> <?php echo htmlspecialchars($agency['address']); ?></p>
                        <p><strong>Hours:</strong> <?php echo htmlspecialchars($agency['opening_hours']); ?></p>
                        <p><strong>Pack:</strong> <?php echo htmlspecialchars($agency['pack_name']); ?></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="stat-card">
                        <div class="stat-label">Today's Reservations</div>
                        <p class="stat-value"><?php echo $today_reservations; ?></p>
                    </div>
                </div>
            </div>

            <div class="quick-actions">
                <h3>Quick Actions</h3>
                <a href="services.php" class="btn btn-primary me-2">Manage Services</a>
                <a href="reservations.php" class="btn btn-success me-2">View Reservations</a>
                <a href="edit-agency.php" class="btn btn-warning">Edit Agency</a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                You don't have an agency yet. <a href="create-agency.php" class="alert-link">Create one now</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
