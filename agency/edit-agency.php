<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAgency();

$error = '';
$success = '';

// Get agency info
$stmt = $con->prepare("SELECT * FROM agencies WHERE owner_user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$agency = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$agency) {
    header("Location: create-agency.php");
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
        $stmt = $con->prepare("UPDATE agencies SET name = ?, address = ?, opening_hours = ?, pack_id = ? WHERE id = ?");
        $stmt->bind_param("sssii", $name, $address, $opening_hours, $pack_id, $agency['id']);

        if ($stmt->execute()) {
            $success = "Agency updated successfully!";
            $agency['name'] = $name;
            $agency['address'] = $address;
            $agency['opening_hours'] = $opening_hours;
            $agency['pack_id'] = $pack_id;
        } else {
            $error = "Failed to update agency.";
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
    <title>Edit Agency - NETTOCAR</title>
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
                        <h2 class="card-title mb-4">Edit Agency</h2>

                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                        <?php endif; ?>

                        <?php if ($success): ?>
                            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label for="name" class="form-label">Agency Name</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($agency['name']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" name="address" value="<?php echo htmlspecialchars($agency['address']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="opening_hours" class="form-label">Opening Hours</label>
                                <input type="text" class="form-control" id="opening_hours" name="opening_hours" value="<?php echo htmlspecialchars($agency['opening_hours']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="pack_id" class="form-label">Subscription Pack</label>
                                <select class="form-select" id="pack_id" name="pack_id" required>
                                    <?php 
                                    $packs = $con->query("SELECT id, name FROM packs");
                                    while ($pack = $packs->fetch_assoc()): 
                                    ?>
                                        <option value="<?php echo $pack['id']; ?>" <?php echo $pack['id'] === $agency['pack_id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pack['name']); ?>
                                        </option>
                                    <?php endwhile; ?>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Update Agency</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
