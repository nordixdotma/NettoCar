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
        :root {
            --primary-color: #ff4500;
            --primary-dark: #e63e00;
            --primary-light: #ff6b35;
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
        
        .stat-card {
            border: none;
            border-radius: var(--border-radius);
            color: white;
            padding: 1.5rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }
        
        .stat-card:hover {
            transform: translateY(-2px);
        }
        
        .stat-card.revenue {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
        }
        
        .stat-card.completed {
            background: linear-gradient(135deg, #10b981 0%, #34d399 100%);
        }
        
        .stat-card.pending {
            background: linear-gradient(135deg, #f59e0b 0%, #fbbf24 100%);
        }
        
        .stat-card h5 {
            font-weight: 600;
            font-size: 0.9rem;
            opacity: 0.95;
            margin-bottom: 0.5rem;
        }
        
        .stat-card .display-5 {
            font-weight: 700;
            font-size: 2rem;
        }
        
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
        
        h1 {
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 1.5rem;
        }
        
        .badge {
            border-radius: 4px;
            padding: 0.4rem 0.6rem;
            font-weight: 500;
        }
        
        .btn-simulate {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            color: white;
            border-radius: var(--border-radius);
            font-weight: 500;
            font-size: 0.85rem;
            padding: 0.4rem 0.8rem;
            transition: all 0.2s;
        }
        
        .btn-simulate:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            color: white;
        }
        
        .alert {
            border: none;
            border-radius: var(--border-radius);
        }
        
        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
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
        <h1>Payment Management</h1>

        <!-- Added success message for payment simulation -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['success']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="row mb-4 g-3">
            <div class="col-md-4">
                <div class="stat-card revenue">
                    <h5>Total Revenue</h5>
                    <p class="display-5 mb-0">$<?php echo number_format($total_revenue, 2); ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card completed">
                    <h5>Completed</h5>
                    <p class="display-5 mb-0"><?php echo $completed_payments; ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card pending">
                    <h5>Pending</h5>
                    <p class="display-5 mb-0"><?php echo $pending_payments; ?></p>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
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
                                    <span class="badge" style="background-color: <?php echo $payment['status'] === 'completed' ? '#10b981' : ($payment['status'] === 'pending' ? '#f59e0b' : '#ef4444'); ?>; color: white;">
                                        <?php echo htmlspecialchars(ucfirst($payment['status'])); ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars(date('M d, Y', strtotime($payment['date']))); ?></td>
                                <td>
                                    <!-- Added simulate payment button for pending payments -->
                                    <?php if ($payment['status'] === 'pending'): ?>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="action" value="simulate_payment">
                                            <input type="hidden" name="payment_id" value="<?php echo $payment['id']; ?>">
                                            <button type="submit" class="btn btn-simulate">Simulate</button>
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
