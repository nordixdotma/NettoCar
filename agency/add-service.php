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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $duration = intval($_POST['duration'] ?? 0);

    if (empty($name) || $price <= 0 || $duration <= 0) {
        $error = "All fields are required and must be valid.";
    } else {
        $stmt = $con->prepare("INSERT INTO services (agency_id, name, price, estimated_duration_minutes) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isdi", $agency_id, $name, $price, $duration);

        if ($stmt->execute()) {
            $success = "Service added successfully!";
            header("Location: services.php");
            exit();
        } else {
            $error = "Failed to add service.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Service - NETTOCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">NETTOCAR</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="services.php">Services</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title mb-4">Add New Service</h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Service Name</label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="e.g., Basic Wash" required>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price ($)</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" placeholder="0.00" required>
                            </div>

                            <div class="mb-3">
                                <label for="duration" class="form-label">Estimated Duration (minutes)</label>
                                <input type="number" class="form-control" id="duration" name="duration" placeholder="30" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Add Service</button>
                        </form>

                        <a href="services.php" class="btn btn-secondary w-100 mt-2">Back to Services</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
