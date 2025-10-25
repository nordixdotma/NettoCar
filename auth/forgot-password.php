<?php
require_once '../config/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');

    if (empty($email)) {
        $error = "Email is required.";
    } else {
        // Check if email exists
        $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // In a real application, you would send a password reset email here
            // For now, we'll show a success message
            $success = "If an account exists with this email, you will receive password reset instructions.";
        } else {
            $success = "If an account exists with this email, you will receive password reset instructions.";
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
    <title>Forgot Password - NETTOCAR</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .forgot-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
        }

        .forgot-card {
            background: white;
            border-radius: 6px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .forgot-header {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            padding: 2rem 1.5rem;
            text-align: center;
            color: white;
        }

        .forgot-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }

        .forgot-body {
            padding: 2rem 1.5rem;
        }

        .forgot-description {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 1.5rem;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 1.25rem;
        }

        .form-label {
            display: block;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .form-control:focus {
            outline: none;
            border-color: #ff4500;
            box-shadow: 0 0 0 3px rgba(255, 69, 0, 0.1);
        }

        .btn-reset {
            width: 100%;
            padding: 0.85rem 1rem;
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            color: white;
            border: none;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .btn-reset:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 69, 0, 0.3);
        }

        .alert {
            padding: 0.75rem 1rem;
            border-radius: 4px;
            margin-bottom: 1.25rem;
            border-left: 4px solid;
            font-size: 0.9rem;
        }

        .alert-danger {
            background-color: #fff5f5;
            color: #c53030;
            border-left-color: #fc8181;
        }

        .alert-success {
            background-color: #f0fdf4;
            color: #166534;
            border-left-color: #86efac;
        }

        .forgot-footer {
            padding: 1.5rem;
            text-align: center;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .forgot-footer p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .forgot-footer a {
            color: #ff4500;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .forgot-footer a:hover {
            color: #ff6b35;
        }
    </style>
</head>
<body>
    <div class="forgot-wrapper">
        <div class="forgot-card">
            <div class="forgot-header">
                <h1>Reset Password</h1>
            </div>

            <div class="forgot-body">
                <p class="forgot-description">Enter your email address and we'll send you a link to reset your password.</p>

                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <button type="submit" class="btn-reset">Send Reset Link</button>
                </form>
            </div>

            <div class="forgot-footer">
                <p>Remember your password? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
</body>
</html>
