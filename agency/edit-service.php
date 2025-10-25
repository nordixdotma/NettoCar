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
$service_id = intval($_GET['id'] ?? 0);

// Get service
$stmt = $con->prepare("SELECT * FROM services WHERE id = ? AND agency_id = ?");
$stmt->bind_param("ii", $service_id, $agency_id);
$stmt->execute();
$service = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$service) {
    header("Location: services.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $price = floatval($_POST['price'] ?? 0);
    $duration = intval($_POST['duration'] ?? 0);

    if (empty($name) || $price <= 0 || $duration <= 0) {
        $error = "All fields are required and must be valid.";
    } else {
        $stmt = $con->prepare("UPDATE services SET name = ?, price = ?, estimated_duration_minutes = ? WHERE id = ?");
        $stmt->bind_param("sdii", $name, $price, $duration, $service_id);

        if ($stmt->execute()) {
            $success = "Service updated successfully!";
            $service['name'] = $name;
            $service['price'] = $price;
            $service['estimated_duration_minutes'] = $duration;
        } else {
            $error = "Failed to update service.";
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
    <title>Edit Service - NETTOCAR</title>
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
                        <h2 class="card-title mb-4">Edit Service</h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Service Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($service['name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="price" class="form-label">Price ($)</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" value="<?php echo $service['price']; ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="duration" class="form-label">Estimated Duration (minutes)</label>
                                <input type="number" class="form-control" id="duration" name="duration" value="<?php echo $service['estimated_duration_minutes']; ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Update Service</button>
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
