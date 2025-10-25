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
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
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
        <h1 class="mb-4">My Reservations</h1>

        <div class="row">
            <div class="col-md-12">
                <a href="book-service.php" class="btn btn-primary mb-3">Book a Service</a>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Agency</th>
                            <th>Service</th>
                            <th>Date & Time</th>
                            <th>Status</th>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
