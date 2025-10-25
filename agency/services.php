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
        
        /* Modern table and button styling */
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }
        
        .table {
            border-radius: var(--border-radius);
        }
        
        .table thead {
            background-color: var(--primary-color) !important;
            color: white;
        }
        
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(255, 69, 0, 0.3);
        }
        
        .btn-warning,
        .btn-danger {
            border-radius: var(--border-radius);
            font-weight: 500;
        }
        
        h1 {
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
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
        <h1>Manage Services</h1>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <a href="add-service.php" class="btn btn-primary mb-3">Add New Service</a>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
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
                                <td><strong><?php echo htmlspecialchars($service['name']); ?></strong></td>
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
