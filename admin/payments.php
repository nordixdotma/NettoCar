<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAdmin();

// Get all payments
$payments = $con->query("SELECT p.*, u.name as user_name, u.email, pk.name as pack_name FROM payments p JOIN users u ON p.user_id = u.id JOIN packs pk ON p.pack_id = pk.id ORDER BY p.date DESC");

// Calculate statistics
$total_revenue = $con->query("SELECT SUM(amount) as total FROM payments WHERE status = 'completed'")->fetch_assoc()['total'] ?? 0;
$completed_payments = $con->query("SELECT COUNT(*) as count FROM payments WHERE status = 'completed'")->fetch_assoc()['count'];
$pending_payments = $con->query("SELECT COUNT(*) as count FROM payments WHERE status = 'pending'")->fetch_assoc()['count'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments - NETTOCAR</title>
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
        <h1 class="mb-4">Payment Management</h1>

        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <div class="card bg-success text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Revenue</h5>
                        <p class="card-text display-5">$<?php echo number_format($total_revenue, 2); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-info text-white">
                    <div class="card-body">
                        <h5 class="card-title">Completed</h5>
                        <p class="card-text display-5"><?php echo $completed_payments; ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-3">
                <div class="card bg-warning text-white">
                    <div class="card-body">
                        <h5 class="card-title">Pending</h5>
                        <p class="card-text display-5"><?php echo $pending_payments; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>User</th>
                        <th>Email</th>
                        <th>Pack</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($payment = $payments->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($payment['user_name']); ?></td>
                            <td><?php echo htmlspecialchars($payment['email']); ?></td>
                            <td><?php echo htmlspecialchars($payment['pack_name']); ?></td>
                            <td>$<?php echo number_format($payment['amount'], 2); ?></td>
                            <td><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($payment['method']))); ?></td>
                            <td>
                                <span class="badge bg-<?php echo $payment['status'] === 'completed' ? 'success' : ($payment['status'] === 'pending' ? 'warning' : 'danger'); ?>">
                                    <?php echo htmlspecialchars(ucfirst($payment['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(date('M d, Y', strtotime($payment['date']))); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
