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
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
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
        <h1 class="mb-4">Agency Dashboard</h1>

        <?php if ($agency): ?>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($agency['name']); ?></h5>
                            <p class="card-text">
                                <strong>Address:</strong> <?php echo htmlspecialchars($agency['address']); ?><br>
                                <strong>Hours:</strong> <?php echo htmlspecialchars($agency['opening_hours']); ?><br>
                                <strong>Pack:</strong> <?php echo htmlspecialchars($agency['pack_name']); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5 class="card-title">Today's Reservations</h5>
                            <p class="card-text display-4"><?php echo $today_reservations; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row mt-4">
                <div class="col-md-12">
                    <h3>Quick Actions</h3>
                    <a href="services.php" class="btn btn-primary me-2">Manage Services</a>
                    <a href="reservations.php" class="btn btn-success me-2">View Reservations</a>
                    <a href="edit-agency.php" class="btn btn-warning">Edit Agency</a>
                </div>
            </div>
        <?php else: ?>
            <div class="alert alert-warning">
                You don't have an agency yet. <a href="create-agency.php">Create one now</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
