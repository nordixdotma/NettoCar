<?php
require_once 'config/session.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NETTOCAR - Car Wash Management</title>
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
        
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
            border-radius: var(--border-radius);
        }
        
        .hero-section h1 {
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .hero-section p {
            font-size: 1.1rem;
            opacity: 0.95;
        }
        
        .dashboard-card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.2s, box-shadow 0.2s;
            height: 100%;
        }
        
        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        
        .dashboard-card .card-body {
            padding: 2rem;
        }
        
        .dashboard-card h5 {
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 0.5rem;
        }
        
        .dashboard-card p {
            color: #666;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            font-weight: 500;
            transition: all 0.2s;
        }
        
        .btn-primary:hover {
            background-color: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-2px);
        }
        
        .alert {
            border: none;
            border-radius: var(--border-radius);
        }
        
        .alert-info {
            background-color: #e0f2fe;
            color: #0369a1;
        }
        
        .alert-warning {
            background-color: #fef3c7;
            color: #92400e;
        }
        
        .role-badge {
            display: inline-block;
            background-color: var(--primary-color);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        h1 {
            font-weight: 700;
            color: #1a1a1a;
        }
    </style>
</head>
<body>
    <!-- Updated navbar with modern gradient design -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="index.php">NETTOCAR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <span class="nav-link">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/logout.php">Logout</a>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/login.php">Login</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="auth/register.php">Register</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <?php if (isLoggedIn()): ?>
            <!-- Added hero section with role information -->
            <div class="hero-section">
                <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
                <p>You are logged in as <span class="role-badge"><?php echo htmlspecialchars(strtoupper($_SESSION['role'])); ?></span></p>
            </div>

            <div class="row g-4">
                <?php if (hasRole('admin')): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Admin Dashboard</h5>
                                <p class="card-text">Manage agencies, view payments, and system statistics</p>
                                <a href="admin/dashboard.php" class="btn btn-primary w-100">Go to Dashboard</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Payments</h5>
                                <p class="card-text">View all payment transactions and revenue</p>
                                <a href="admin/payments.php" class="btn btn-primary w-100">View Payments</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Statistics</h5>
                                <p class="card-text">Analyze system performance and trends</p>
                                <a href="admin/statistics.php" class="btn btn-primary w-100">View Statistics</a>
                            </div>
                        </div>
                    </div>
                <?php elseif (hasRole('agency')): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Agency Dashboard</h5>
                                <p class="card-text">Manage your services and reservations</p>
                                <a href="agency/dashboard.php" class="btn btn-primary w-100">Go to Dashboard</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Services</h5>
                                <p class="card-text">Add and manage your car wash services</p>
                                <a href="agency/services.php" class="btn btn-primary w-100">Manage Services</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Reservations</h5>
                                <p class="card-text">View and manage client reservations</p>
                                <a href="agency/reservations.php" class="btn btn-primary w-100">View Reservations</a>
                            </div>
                        </div>
                    </div>
                <?php elseif (hasRole('client')): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Client Dashboard</h5>
                                <p class="card-text">Book services and view your reservations</p>
                                <a href="client/dashboard.php" class="btn btn-primary w-100">Go to Dashboard</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 col-lg-4">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <h5 class="card-title">Book Service</h5>
                                <p class="card-text">Browse and book available car wash services</p>
                                <a href="client/book-service.php" class="btn btn-primary w-100">Book Now</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <!-- Improved welcome section for non-logged-in users -->
            <div class="hero-section">
                <h1>Welcome to NETTOCAR</h1>
                <p>Professional Car Wash Management System</p>
            </div>

            <div class="row g-4">
                <div class="col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">New User?</h5>
                            <p class="card-text">Create an account to get started with our car wash booking system</p>
                            <a href="auth/register.php" class="btn btn-primary w-100">Register Now</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card dashboard-card">
                        <div class="card-body text-center">
                            <h5 class="card-title">Already a Member?</h5>
                            <p class="card-text">Sign in to your account to access your dashboard</p>
                            <a href="auth/login.php" class="btn btn-primary w-100">Login</a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
