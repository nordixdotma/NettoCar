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
        
        h3 {
            font-weight: 600;
            font-size: 1.1rem;
            margin-bottom: 1rem;
            color: var(--text-dark);
        }
        
        .info-card {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .info-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
            transform: translateY(-2px);
        }
        
        .info-card h5 {
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 1rem;
        }
        
        .info-card p {
            color: var(--text-light);
            margin-bottom: 0.5rem;
            font-size: 0.95rem;
        }
        
        .info-card strong {
            color: var(--text-dark);
        }
        
        .stat-card {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
            border: none;
            border-radius: 6px;
            padding: 1.5rem;
            transition: all 0.3s ease;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
        }
        
        .stat-card:hover {
            box-shadow: 0 4px 12px rgba(255, 69, 0, 0.2);
            transform: translateY(-2px);
        }
        
        .stat-card h5 {
            font-weight: 600;
            font-size: 0.95rem;
            opacity: 0.95;
            margin-bottom: 0.75rem;
        }
        
        .stat-card p {
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
        
        .btn-warning {
            background-color: #f59e0b;
            color: white;
        }
        
        .btn-warning:hover {
            background-color: #d97706;
            color: white;
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(245, 158, 11, 0.2);
        }
        
        .quick-actions {
            background: white;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .alert {
            border-radius: 6px;
            border: 1px solid var(--border-color);
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
                        <span class="nav-link">🏢 <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">🚪 Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1>🏢 Agency Dashboard</h1>

        <?php if ($agency): ?>
            <!-- Modern info and stat cards -->
            <div class="row">
                <div class="col-md-6 mb-4">
                    <div class="info-card">
                        <h5><?php echo htmlspecialchars($agency['name']); ?></h5>
                        <p><strong>📍 Address:</strong> <?php echo htmlspecialchars($agency['address']); ?></p>
                        <p><strong>🕐 Hours:</strong> <?php echo htmlspecialchars($agency['opening_hours']); ?></p>
                        <p><strong>📦 Pack:</strong> <?php echo htmlspecialchars($agency['pack_name']); ?></p>
                    </div>
                </div>
                <div class="col-md-6 mb-4">
                    <div class="stat-card">
                        <h5>Today's Reservations</h5>
                        <p><?php echo $today_reservations; ?></p>
                    </div>
                </div>
            </div>

            <!-- Quick actions section -->
            <div class="quick-actions">
                <h3>⚡ Quick Actions</h3>
                <a href="services.php" class="btn btn-primary me-2">Manage Services</a>
                <a href="reservations.php" class="btn btn-success me-2">View Reservations</a>
                <a href="edit-agency.php" class="btn btn-warning">Edit Agency</a>
            </div>
        <?php else: ?>
            <div class="alert alert-warning" role="alert">
                ⚠️ You don't have an agency yet. <a href="create-agency.php" class="alert-link">Create one now</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
