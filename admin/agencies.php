<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAdmin();

$error = '';
$success = '';

// Handle delete
if (isset($_GET['delete'])) {
    $agency_id = intval($_GET['delete']);
    $stmt = $con->prepare("DELETE FROM agencies WHERE id = ?");
    $stmt->bind_param("i", $agency_id);
    
    if ($stmt->execute()) {
        $success = "Agency deleted successfully.";
    } else {
        $error = "Failed to delete agency.";
    }
    $stmt->close();
}

// Get all agencies
$agencies = $con->query("SELECT a.*, u.name as owner_name, p.name as pack_name FROM agencies a JOIN users u ON a.owner_user_id = u.id JOIN packs p ON a.pack_id = p.id ORDER BY a.created_at DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Agencies - NETTOCAR</title>
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
        <h1 class="mb-4">Manage Agencies</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Name</th>
                        <th>Owner</th>
                        <th>Address</th>
                        <th>Pack</th>
                        <th>Hours</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($agency = $agencies->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($agency['name']); ?></td>
                            <td><?php echo htmlspecialchars($agency['owner_name']); ?></td>
                            <td><?php echo htmlspecialchars($agency['address']); ?></td>
                            <td><span class="badge bg-info"><?php echo htmlspecialchars($agency['pack_name']); ?></span></td>
                            <td><?php echo htmlspecialchars($agency['opening_hours']); ?></td>
                            <td>
                                <a href="?delete=<?php echo $agency['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">Delete</a>
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
