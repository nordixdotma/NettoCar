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
    <link rel="stylesheet" href="../app/globals.css">
    <style>
        :root {
            --primary-color: #ff4500;
            --primary-light: #ff6b35;
            --primary-dark: #e63e00;
            --bg-light: #f8f9fa;
            --border-color: #e0e0e0;
            --text-dark: #1a1a1a;
            --text-light: #666666;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .login-container {
            width: 100%;
            max-width: 420px;
        }
        
        .card {
            border: none;
            border-radius: 8px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        }
        
        .card-body {
            padding: 2.5rem;
        }
        
        .card-title {
            font-weight: 700;
            font-size: 1.75rem;
            color: var(--text-dark);
            margin-bottom: 2rem;
            text-align: center;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--text-dark);
            font-size: 0.95rem;
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 1px solid var(--border-color);
            border-radius: 6px;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(255, 69, 0, 0.1);
        }
        
        .btn-submit {
            background-color: var(--primary-color);
            color: white;
            border: none;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.95rem;
            padding: 0.75rem 1.5rem;
            width: 100%;
            transition: all 0.2s;
            margin-top: 1rem;
        }
        
        .btn-submit:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 69, 0, 0.3);
            color: white;
        }
        
        .alert {
            border-radius: 6px;
            border: 1px solid #fee2e2;
            background-color: #fef2f2;
            color: #991b1b;
            margin-bottom: 1.5rem;
        }
        
        .register-link {
            text-align: center;
            margin-top: 1.5rem;
            color: var(--text-light);
            font-size: 0.95rem;
        }
        
        .register-link a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s;
        }
        
        .register-link a:hover {
            color: var(--primary-dark);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Modern login card with gradient background -->
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">🚗 NETTOCAR</h2>
                
                <?php if ($error): ?>
                    <div class="alert"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="mb-3">
                        <label for="email" class="form-label">📧 Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label">🔐 Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <button type="submit" class="btn-submit">Login</button>
                </form>

                <p class="register-link">
                    Don't have an account? <a href="register.php">Register here</a>
                </p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
