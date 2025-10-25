<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAgency();

$error = '';
$success = '';

// Check if user already has an agency
$stmt = $con->prepare("SELECT id FROM agencies WHERE owner_user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$existing = $stmt->get_result();
$stmt->close();

if ($existing->num_rows > 0) {
    header("Location: dashboard.php");
    exit();
}

// Get available packs
$packs = $con->query("SELECT id, name FROM packs");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $opening_hours = trim($_POST['opening_hours'] ?? '');
    $pack_id = intval($_POST['pack_id'] ?? 0);

    if (empty($name) || empty($address) || empty($opening_hours) || $pack_id === 0) {
        $error = "All fields are required.";
    } else {
        $stmt = $con->prepare("INSERT INTO agencies (name, address, opening_hours, pack_id, owner_user_id) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssii", $name, $address, $opening_hours, $pack_id, $_SESSION['user_id']);

        if ($stmt->execute()) {
            $success = "Agency created successfully!";
            header("Location: dashboard.php");
            exit();
        } else {
            $error = "Failed to create agency.";
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
    <title>Create Agency - NETTOCAR</title>
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
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="card-title mb-4">Create Your Agency</h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Agency Name</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" required>
                            </div>

                            <div class="mb-3">
                                <label for="opening_hours" class="form-label">Opening Hours</label>
                                <input type="text" class="form-control" id="opening_hours" name="opening_hours" placeholder="e.g., 9:00 AM - 6:00 PM" required>
                            </div>

                            <div class="mb-3">
                                <label for="pack_id" class="form-label">Subscription Pack</label>
                                <select class="form-select" id="pack_id" name="pack_id" required>
                                    <option value="">Select a pack</option>
                                    <?php while ($pack = $packs->fetch_assoc()): ?>
                                        <option value="<?php echo $pack['id']; ?>"><?php echo htmlspecialchars($pack['name']); ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Create Agency</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
