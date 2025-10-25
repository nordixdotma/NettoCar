<?php
require_once '../config/db.php';
require_once '../config/session.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email and password are required.";
    } else {
        // Prepare statement to prevent SQL injection
        $stmt = $con->prepare("SELECT id, name, password_hash, role FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $email;
                $_SESSION['role'] = $user['role'];

                // Redirect based on role
                if ($user['role'] === 'admin') {
                    header("Location: ../admin/dashboard.php");
                } elseif ($user['role'] === 'agency') {
                    header("Location: ../agency/dashboard.php");
                } else {
                    header("Location: ../client/dashboard.php");
                }
                exit();
            } else {
                $error = "Invalid email or password.";
            }
        } else {
            $error = "Invalid email or password.";
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
    <title>Login - NETTOCAR</title>
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

        .login-wrapper {
            width: 100%;
            max-width: 400px;
            padding: 1rem;
        }

        .login-card {
            background: white;
            border-radius: 6px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .login-header {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            padding: 2rem 1.5rem;
            text-align: center;
            color: white;
        }

        .login-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }

        .login-body {
            padding: 2rem 1.5rem;
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

        .btn-login {
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
            margin-top: 0.5rem;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255, 69, 0, 0.3);
        }

        .btn-login:active {
            transform: translateY(0);
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

        .login-footer {
            padding: 1.5rem;
            text-align: center;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .login-footer p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .login-footer a {
            color: #ff4500;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .login-footer a:hover {
            color: #ff6b35;
        }

        .forgot-password {
            text-align: right;
            margin-top: 0.5rem;
        }

        .forgot-password a {
            color: #ff4500;
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 500;
            transition: color 0.2s ease;
        }

        .forgot-password a:hover {
            color: #ff6b35;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <div class="login-header">
                <h1>NETTOCAR</h1>
            </div>

            <div class="login-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="forgot-password">
                        <a href="forgot-password.php">Forgot password?</a>
                    </div>

                    <button type="submit" class="btn-login">Sign In</button>
                </form>
            </div>

            <div class="login-footer">
                <p>Don't have an account? <a href="register.php">Create one</a></p>
            </div>
        </div>
    </div>
</body>
</html>
