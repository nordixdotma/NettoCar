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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: #f5f5f5;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: #1a1a1a;
        }

        .navbar {
            background: white;
            border-bottom: 1px solid #e0e0e0;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: #ff4500 !important;
            letter-spacing: -0.5px;
        }

        .nav-link {
            color: #1a1a1a !important;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s ease;
        }

        .nav-link:hover {
            color: #ff4500 !important;
        }

        .container {
            max-width: 1200px;
        }

        .page-header {
            margin-bottom: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-header h1 {
            font-weight: 700;
            font-size: 2rem;
            color: #1a1a1a;
            margin: 0;
        }

        .card {
            border: none;
            border-radius: 6px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            background: white;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: #ff4500;
            color: white;
        }

        .table thead th {
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: #f9f9f9;
        }

        .table tbody tr:last-child td {
            border-bottom: none;
        }

        .btn {
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background: #ff4500;
            color: white;
        }

        .btn-primary:hover {
            background: #ff6b35;
            color: white;
            transform: translateY(-1px);
        }

        .btn-warning {
            background: #f59e0b;
            color: white;
        }

        .btn-warning:hover {
            background: #d97706;
            color: white;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            color: white;
            transform: translateY(-1px);
        }

        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.8rem;
        }

        .alert {
            border: none;
            border-radius: 6px;
            border-left: 4px solid;
            margin-bottom: 1.5rem;
        }

        .alert-danger {
            background: #fff5f5;
            color: #c53030;
            border-left-color: #fc8181;
        }

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left-color: #86efac;
        }

        h1 {
            font-weight: 700;
            color: #1a1a1a;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
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
        <div class="page-header">
            <h1>Manage Services</h1>
            <a href="add-service.php" class="btn btn-primary">Add New Service</a>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
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
                                    <a href="?delete=<?php echo $service['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this service?')">Delete</a>
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
