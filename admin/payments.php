<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireAdmin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'simulate_payment') {
    $payment_id = intval($_POST['payment_id']);
    
    // Update payment status to completed
    $stmt = $con->prepare("UPDATE payments SET status = 'completed' WHERE id = ?");
    $stmt->bind_param("i", $payment_id);
    $stmt->execute();
    $stmt->close();
    
    // Redirect to refresh the page
    header("Location: payments.php?success=Payment simulated successfully");
    exit();
}

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
        }

        .page-header h1 {
            font-weight: 700;
            font-size: 2rem;
            color: #1a1a1a;
            margin: 0;
        }

        .stat-card {
            border: none;
            border-radius: 6px;
            color: white;
            padding: 1.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            height: 100%;
        }

        .stat-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
            transform: translateY(-4px);
        }

        .stat-card.revenue {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
        }

        .stat-card.completed {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        }

        .stat-card.pending {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        }

        .stat-label {
            font-size: 0.9rem;
            font-weight: 500;
            opacity: 0.95;
            margin-bottom: 0.75rem;
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 700;
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

        .badge {
            border-radius: 4px;
            padding: 0.4rem 0.8rem;
            font-weight: 500;
            font-size: 0.85rem;
        }

        .btn {
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem 1rem;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-simulate {
            background: #ff4500;
            color: white;
        }

        .btn-simulate:hover {
            background: #ff6b35;
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

        .alert-success {
            background: #f0fdf4;
            color: #166534;
            border-left-color: #86efac;
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
            <h1>Payment Management</h1>
        </div>

        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="stat-card revenue">
                    <div class="stat-label">Total Revenue</div>
                    <p class="stat-value">$<?php echo number_format($total_revenue, 2); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card completed">
                    <div class="stat-label">Completed</div>
                    <p class="stat-value"><?php echo $completed_payments; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card pending">
                    <div class="stat-label">Pending</div>
                    <p class="stat-value"><?php echo $pending_payments; ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Email</th>
                            <th>Pack</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($payment = $payments->fetch_assoc()): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($payment['user_name']); ?></strong></td>
                                <td><?php echo htmlspecialchars($payment['email']); ?></td>
                                <td><?php echo htmlspecialchars($payment['pack_name']); ?></td>
                                <td><strong>$<?php echo number_format($payment['amount'], 2); ?></strong></td>
                                <td><?php echo htmlspecialchars(str_replace('_', ' ', ucfirst($payment['method']))); ?></td>
                                <td>
                                    <span class="badge" style="background: <?php echo $payment['status'] === 'completed' ? 'linear-gradient(135deg, #10b981 0%, #34d399 100%)' : ($payment['status'] === 'pending' ? 'linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%)' : 'linear-gradient(135deg, #ef4444 0%, #f87171 100%)'); ?>; color: white;">
                                        <?php echo htmlspecialchars(ucfirst($payment['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date('M d, Y', strtotime($payment['date']))); ?></td>
                                <td>
                                    <?php if ($payment['status'] === 'pending'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="simulate_payment">
                                            <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                            <button type="submit" class="btn btn-sm btn-simulate">Simulate</button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-muted" style="font-size: 0.85rem;">-</span>
                                    <?php endif; ?>
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
