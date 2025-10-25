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

// Handle delete
if (isset($_GET['delete'])) {
    $service_id = intval($_GET['delete']);
    $stmt = $con->prepare("DELETE FROM services WHERE id = ? AND agency_id = ?");
    $stmt->bind_param("ii", $service_id, $agency_id);
    
    if ($stmt->execute()) {
        $success = "Service deleted successfully.";
    } else {
        $error = "Failed to delete service.";
    }
    $stmt->close();
}

// Get all services for this agency
$stmt = $con->prepare("SELECT * FROM services WHERE agency_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $agency_id);
$stmt->execute();
$services = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Services - NETTOCAR</title>
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
        <div class="row">
            <div class="col-md-8">
                <h1 class="mb-4">Manage Services</h1>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <a href="add-service.php" class="btn btn-primary mb-3">Add New Service</a>

                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>Service Name</th>
                                <th>Price</th>
                                <th>Duration (min)</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($service = $services->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($service['name']); ?></td>
                                    <td>$<?php echo number_format($service['price'], 2); ?></td>
                                    <td><?php echo $service['estimated_duration_minutes']; ?></td>
                                    <td>
                                        <a href="edit-service.php?id=<?php echo $service['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                        <a href="?delete=<?php echo $service['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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
