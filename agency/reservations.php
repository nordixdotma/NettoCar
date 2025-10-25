<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAgency();

$error = '';
$success = '';

// Get agency
$stmt = $con->prepare("SELECT id FROM agencies WHERE owner_user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$agency = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$agency) {
    header("Location: create-agency.php");
    exit();
}

$agency_id = $agency['id'];

// Handle status update
if (isset($_POST['update_status'])) {
    $reservation_id = intval($_POST['reservation_id']);
    $status = trim($_POST['status']);

    if (in_array($status, ['waiting', 'in_progress', 'finished'])) {
        $stmt = $con->prepare("UPDATE reservations SET status = ? WHERE id = ? AND agency_id = ?");
        $stmt->bind_param("sii", $status, $reservation_id, $agency_id);

        if ($stmt->execute()) {
            $success = "Reservation status updated.";
        } else {
            $error = "Failed to update status.";
        }
        $stmt->close();
    }
}

// Get filter
$filter = $_GET['filter'] ?? 'all';
$where = "WHERE r.agency_id = ?";

if ($filter === 'today') {
    $where .= " AND DATE(r.datetime) = CURDATE()";
} elseif ($filter === 'week') {
    $where .= " AND WEEK(r.datetime) = WEEK(CURDATE())";
}

// Get reservations
$query = "SELECT r.*, s.name as service_name, u.name as client_name FROM reservations r 
          JOIN services s ON r.service_id = s.id 
          JOIN users u ON r.client_user_id = u.id 
          $where 
          ORDER BY r.datetime DESC";

$stmt = $con->prepare($query);
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$reservations = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservations - NETTOCAR</title>
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
        <h1 class="mb-4">Reservations</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="mb-3">
            <a href="?filter=all" class="btn btn-outline-primary <?php echo $filter === 'all' ? 'active' : ''; ?>">All</a>
            <a href="?filter=today" class="btn btn-outline-primary <?php echo $filter === 'today' ? 'active' : ''; ?>">Today</a>
            <a href="?filter=week" class="btn btn-outline-primary <?php echo $filter === 'week' ? 'active' : ''; ?>">This Week</a>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Client</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($reservation = $reservations->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($reservation['client_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['service_name']); ?></td>
                            <td><?php echo htmlspecialchars($reservation['datetime']); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $reservation['status'] === 'finished' ? 'success' : ($reservation['status'] === 'in_progress' ? 'warning' : 'secondary'); ?>">
                                    <?php echo htmlspecialchars($reservation['status']); ?>
                                </span>
                            </td>
                            <td>
                                <form method="POST" style="display: inline;">
                                    <input type="hidden" name="reservation_id" value="<?php echo $reservation['id']; ?>">
                                    <select name="status" class="form-select form-select-sm" style="width: auto; display: inline-block;">
                                        <option value="waiting" <?php echo $reservation['status'] === 'waiting' ? 'selected' : ''; ?>>Waiting</option>
                                        <option value="in_progress" <?php echo $reservation['status'] === 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="finished" <?php echo $reservation['status'] === 'finished' ? 'selected' : ''; ?>>Finished</option>
                                    </select>
                                    <button type="submit" name="update_status" class="btn btn-sm btn-primary">Update</button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
