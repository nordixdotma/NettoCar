<?php
require_once '../config/db.php';
require_once '../config/session.php';

$message = '';
$message_type = 'info';

// In a real application, you would verify the token from the email link
// For now, this is a placeholder for email verification functionality

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    // Verify token and mark user as verified
    $message = "Email verification feature is ready to be implemented.";
    $message_type = 'info';
} else {
    $message = "No verification token provided.";
    $message_type = 'warning';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - NETTOCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5 text-center">
                        <h2 class="card-title mb-4">Email Verification</h2>
                        <div class="alert alert-<?php echo $message_type; ?>">
                            <?php echo htmlspecialchars($message); ?>
                        </div>
                        <a href="../index.php" class="btn btn-primary">Back to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
