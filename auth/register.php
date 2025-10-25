<?php
require_once '../config/db.php';
require_once '../config/session.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $role = $_POST['role'] ?? 'client';

    // Validation
    if (empty($name) || empty($email) || empty($password)) {
        $error = "All fields are required.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } else {
        // Check if email already exists
        $stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            // Hash password and insert user
            $password_hash = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $con->prepare("INSERT INTO users (name, email, password_hash, role) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $password_hash, $role);

            if ($stmt->execute()) {
                $success = "Registration successful! Please login.";
            } else {
                $error = "Registration failed. Please try again.";
            }
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
    <title>Register - NETTOCAR</title>
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
            padding: 1rem 0;
        }

        .register-wrapper {
            width: 100%;
            max-width: 420px;
            padding: 1rem;
        }

        .register-card {
            background: white;
            border-radius: 6px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        .register-header {
            background: linear-gradient(135deg, #ff4500 0%, #ff6b35 100%);
            padding: 2rem 1.5rem;
            text-align: center;
            color: white;
        }

        .register-header h1 {
            font-size: 1.75rem;
            font-weight: 700;
            margin: 0;
        }

        .register-body {
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

        .form-control,
        .form-select {
            width: 100%;
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            font-family: inherit;
        }

        .form-control:focus,
        .form-select:focus {
            outline: none;
            border-color: #ff4500;
            box-shadow: 0 0 0 3px rgba(255, 69, 0, 0.1);
        }

        .btn-register {
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

        .btn-register:hover {
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

        .register-footer {
            padding: 1.5rem;
            text-align: center;
            border-top: 1px solid #f0f0f0;
            background: #fafafa;
        }

        .register-footer p {
            margin: 0;
            color: #666;
            font-size: 0.9rem;
        }

        .register-footer a {
            color: #ff4500;
            text-decoration: none;
            font-weight: 600;
            transition: color 0.2s ease;
        }

        .register-footer a:hover {
            color: #ff6b35;
        }
    </style>
</head>
<body>
    <div class="register-wrapper">
        <div class="register-card">
            <div class="register-header">
                <h1>Create Account</h1>
            </div>

            <div class="register-body">
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label for="name" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>

                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <div class="form-group">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>

                    <div class="form-group">
                        <label for="confirm_password" class="form-label">Confirm Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                    </div>

                    <div class="form-group">
                        <label for="role" class="form-label">Register as</label>
                        <select class="form-select" id="role" name="role">
                            <option value="client">Client</option>
                            <option value="agency">Agency Owner</option>
                        </select>
                    </div>

                    <button type="submit" class="btn-register">Create Account</button>
                </form>
            </div>

            <div class="register-footer">
                <p>Already have an account? <a href="login.php">Sign in</a></p>
            </div>
        </div>
    </div>
</body>
</html>
