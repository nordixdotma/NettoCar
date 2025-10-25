<?php
require_once '../config/db.php';
require_once '../config/session.php';

requireLogin();

$error = '';
$success = '';

// Get all packs with pricing
$packs = $con->query("SELECT * FROM packs ORDER BY id");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pack_id = intval($_POST['pack_id'] ?? 0);
    $payment_method = trim($_POST['payment_method'] ?? '');

    if ($pack_id === 0 || empty($payment_method)) {
        $error = "Please select a pack and payment method.";
    } else {
        // Get pack details
        $stmt = $con->prepare("SELECT id, name FROM packs WHERE id = ?");
        $stmt->bind_param("i", $pack_id);
        $stmt->execute();
        $pack = $stmt->get_result()->fetch_assoc();
        $stmt->close();

        if (!$pack) {
            $error = "Invalid pack selected.";
        } else {
            // Define pack prices
            $pack_prices = [
                1 => 29.99,  // Basic
                2 => 79.99,  // Standard
                3 => 149.99  // Premium
            ];

            $amount = $pack_prices[$pack_id] ?? 0;

            if ($amount === 0) {
                $error = "Invalid pack price.";
            } else {
                // Simulate payment processing
                $payment_status = 'completed'; // In real app, integrate with payment gateway

                // Record payment
                $stmt = $con->prepare("INSERT INTO payments (user_id, pack_id, amount, method, status) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("iidss", $_SESSION['user_id'], $pack_id, $amount, $payment_method, $payment_status);

                if ($stmt->execute()) {
                    $success = "Payment successful! Your pack has been activated.";
                    
                    // If user is agency owner, update their agency pack
                    if ($_SESSION['role'] === 'agency') {
                        $update_stmt = $con->prepare("UPDATE agencies SET pack_id = ? WHERE owner_user_id = ?");
                        $update_stmt->bind_param("ii", $pack_id, $_SESSION['user_id']);
                        $update_stmt->execute();
                        $update_stmt->close();
                    }
                } else {
                    $error = "Payment processing failed.";
                }
                $stmt->close();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Purchase Pack - NETTOCAR</title>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <h1 class="mb-4">Purchase Subscription Pack</h1>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <div class="row mb-4">
                    <?php 
                    $pack_details = [
                        1 => ['name' => 'Basic', 'price' => 29.99, 'features' => ['10 reservations/day', 'No statistics']],
                        2 => ['name' => 'Standard', 'price' => 79.99, 'features' => ['Unlimited reservations', 'Weekly statistics']],
                        3 => ['name' => 'Premium', 'price' => 149.99, 'features' => ['Unlimited reservations', 'Weekly statistics', 'CSV export', 'Priority support']]
                    ];
                    
                    foreach ($pack_details as $id => $details): 
                    ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo $details['name']; ?></h5>
                                    <p class="card-text display-6">$<?php echo number_format($details['price'], 2); ?></p>
                                    <ul class="list-unstyled">
                                        <?php foreach ($details['features'] as $feature): ?>
                                            <li><i class="bi bi-check"></i> <?php echo htmlspecialchars($feature); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Complete Your Purchase</h5>
                        <form method="POST">
                            <div class="mb-3">
                                <label for="pack_id" class="form-label">Select Pack</label>
                                <select class="form-select" id="pack_id" name="pack_id" required>
                                    <option value="">Choose a pack...</option>
                                    <option value="1">Basic - $29.99</option>
                                    <option value="2">Standard - $79.99</option>
                                    <option value="3">Premium - $149.99</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="payment_method" class="form-label">Payment Method</label>
                                <select class="form-select" id="payment_method" name="payment_method" required>
                                    <option value="">Select payment method...</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="paypal">PayPal</option>
                                    <option value="bank_transfer">Bank Transfer</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-success w-100">Process Payment</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
