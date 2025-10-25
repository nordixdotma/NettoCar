<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireLogin();

// Get payment history
$stmt = $con->prepare("SELECT p.*, pk.name as pack_name FROM payments p JOIN packs pk ON p.pack_id = pk.id WHERE p.user_id = ? ORDER BY p.date DESC");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$payments = $stmt->get_result();
$stmt->close();

// Calculate total spent
$total_stmt = $con->prepare("SELECT SUM(amount) as total FROM payments WHERE user_id = ? AND status = 'completed'");
$total_stmt->bind_param("i", $_SESSION['user_id']);
$total_stmt->execute();
$total_result = $total_stmt->get_result()->fetch_assoc();
$total_spent = $total_result['total'] ?? 0;
$total_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment History - NETTOCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="../index.php">NETTOCAR</a>
            <div class="collapse navbar-collapse">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="../auth/logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h1 class="mb-4">Payment History</h1>

        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-primary text-white">
                    <div class="card-body">
                        <h5 class="card-title">Total Spent</h5>
                        <p class="card-text display-5">$<?php echo number_format($total_spent, 2); ?></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
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

        <a href="../index.php" class="btn btn-secondary mt-3">Back to Home</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
