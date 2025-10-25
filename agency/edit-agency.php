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
    <style>
        :root {
            --primary-color: #ff4500;
            --primary-dark: #e63e00;
            --bg-light: #f8f9fa;
            --border-radius: 6px;
        }
        
        body {
            background-color: var(--bg-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .navbar {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%) !important;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .navbar-brand {
            font-weight: 700;
            font-size: 1.3rem;
        }
        
        .nav-link {
            font-weight: 500;
            transition: opacity 0.2s;
        }
        
        .nav-link:hover {
            opacity: 0.8;
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        
        .card-body {
            padding: 2rem;
        }
        
        .card-title {
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
        }
        
        .form-label {
            font-weight: 500;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        
        .form-control,
        .form-select {
            border: 1px solid #e9ecef;
            border-radius: var(--border-radius);
            padding: 0.6rem 0.9rem;
            font-size: 0.95rem;
            transition: border-color 0.2s;
        }
        
        .form-control:focus,
        .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 69, 0, 0.1);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 69, 0, 0.3);
        }
        
        .alert {
            border: none;
            border-radius: var(--border-radius);
            border-left: 4px solid;
        }
        
        .alert-danger {
            border-left-color: #dc3545;
            background-color: #fff5f5;
            color: #721c24;
        }
        
        .alert-success {
            border-left-color: var(--primary-color);
            background-color: #fff8f5;
            color: #1a1a1a;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="../index.php">NETTOCAR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
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

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-body">
                        <h2 class="card-title">Edit Agency</h2>

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
