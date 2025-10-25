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
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-color: #ff4500;
            --primary-dark: #e63e00;
            --primary-light: #ff6b35;
            --bg-light: #f5f5f5;
            --bg-white: #ffffff;
            --text-primary: #1a1a1a;
            --text-secondary: #666666;
            --border-color: #e0e0e0;
            --border-radius: 6px;
            --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.08);
            --shadow-md: 0 4px 12px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
        }

        body {
            background-color: var(--bg-light);
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            color: var(--text-primary);
        }

        /* Navbar styling - clean white design with subtle border */
        .navbar {
            background: var(--bg-white);
            border-bottom: 1px solid var(--border-color);
            box-shadow: var(--shadow-sm);
            padding: 0.75rem 0;
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.4rem;
            color: var(--primary-color) !important;
            letter-spacing: -0.5px;
        }

        .nav-link {
            color: var(--text-primary) !important;
            font-weight: 500;
            font-size: 0.9rem;
            transition: color 0.2s ease;
            margin-left: 1.5rem;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .nav-welcome {
            color: var(--text-secondary) !important;
            font-weight: 500;
            font-size: 0.9rem;
            margin-right: 1.5rem;
        }

        /* Main container and spacing */
        .container {
            max-width: 1200px;
        }

        .page-section {
            margin-bottom: 3rem;
        }

        /* Hero section - clean and minimal */
        .hero-section {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 3rem 2rem;
            border-radius: var(--border-radius);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-md);
        }

        .hero-section h1 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 0.5rem;
            line-height: 1.2;
        }

        .hero-section p {
            font-size: 1rem;
            opacity: 0.95;
            margin: 0;
        }

        .role-badge {
            display: inline-block;
            background-color: rgba(255, 255, 255, 0.25);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-weight: 600;
            font-size: 0.8rem;
            margin-top: 0.75rem;
        }

        /* Dashboard cards - consistent with other pages */
        .dashboard-card {
            background: var(--bg-white);
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
            height: 100%;
            overflow: hidden;
        }

        .dashboard-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .dashboard-card .card-body {
            padding: 2rem;
            display: flex;
            flex-direction: column;
        }

        .dashboard-card .card-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
            margin-bottom: 1rem;
        }

        .dashboard-card h5 {
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
            font-size: 1.1rem;
        }

        .dashboard-card p {
            color: var(--text-secondary);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            flex-grow: 1;
        }

        .dashboard-card .btn {
            align-self: flex-start;
        }

        /* Button styling */
        .btn {
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.9rem;
            padding: 0.65rem 1.5rem;
            border: none;
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-light) 100%);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 69, 0, 0.3);
            color: white;
        }

        .btn-secondary {
            background: var(--bg-light);
            color: var(--text-primary);
        }

        .btn-secondary:hover {
            background: var(--border-color);
            color: var(--text-primary);
        }

        /* Section title styling */
        .section-title {
            font-weight: 700;
            font-size: 1.5rem;
            color: var(--text-primary);
            margin-bottom: 1.5rem;
        }

        /* Grid layout for cards */
        .cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        /* Welcome section for non-logged-in users */
        .welcome-section {
            text-align: center;
            padding: 2rem 0;
        }

        .welcome-section h2 {
            font-weight: 700;
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
            color: var(--text-primary);
        }

        .welcome-section p {
            color: var(--text-secondary);
            font-size: 1rem;
            margin-bottom: 2rem;
        }

        .auth-buttons {
            display: flex;
            gap: 1rem;
            justify-content: center;
            flex-wrap: wrap;
        }

        .auth-buttons .btn {
            padding: 0.75rem 2rem;
            font-size: 0.95rem;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .hero-section {
                padding: 2rem 1.5rem;
            }

            .hero-section h1 {
                font-size: 1.5rem;
            }

            .section-title {
                font-size: 1.25rem;
            }

            .cards-grid {
                grid-template-columns: 1fr;
            }

            .nav-link {
                margin-left: 0;
                margin-top: 0.5rem;
            }

            .nav-welcome {
                margin-right: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Clean navbar with modern design -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand" href="index.php">NETTOCAR</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                        <li class="nav-item">
                            <span class="nav-welcome">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
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
            <!-- Hero section for logged-in users -->
            <div class="hero-section">
                <h1>Welcome back, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h1>
                <p>You are logged in as <span class="role-badge"><?php echo htmlspecialchars(strtoupper($_SESSION['role'])); ?></span></p>
            </div>

            <!-- Admin Dashboard Section -->
            <?php if (hasRole('admin')): ?>
                <div class="page-section">
                    <h2 class="section-title">Admin Dashboard</h2>
                    <div class="cards-grid">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="card-icon">📊</div>
                                <h5 class="card-title">Dashboard</h5>
                                <p class="card-text">View system overview and key metrics</p>
                                <a href="admin/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                            </div>
                        </div>
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="card-icon">💳</div>
                                <h5 class="card-title">Payments</h5>
                                <p class="card-text">Manage all payment transactions and revenue</p>
                                <a href="admin/payments.php" class="btn btn-primary">View Payments</a>
                            </div>
                        </div>
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="card-icon">📈</div>
                                <h5 class="card-title">Statistics</h5>
                                <p class="card-text">Analyze system performance and trends</p>
                                <a href="admin/statistics.php" class="btn btn-primary">View Statistics</a>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Agency Dashboard Section -->
            <?php elseif (hasRole('agency')): ?>
                <div class="page-section">
                    <h2 class="section-title">Agency Dashboard</h2>
                    <div class="cards-grid">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="card-icon">🏢</div>
                                <h5 class="card-title">Dashboard</h5>
                                <p class="card-text">View your agency overview and statistics</p>
                                <a href="agency/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                            </div>
                        </div>
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="card-icon">🧼</div>
                                <h5 class="card-title">Services</h5>
                                <p class="card-text">Add and manage your car wash services</p>
                                <a href="agency/services.php" class="btn btn-primary">Manage Services</a>
                            </div>
                        </div>
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="card-icon">📅</div>
                                <h5 class="card-title">Reservations</h5>
                                <p class="card-text">View and manage client reservations</p>
                                <a href="agency/reservations.php" class="btn btn-primary">View Reservations</a>
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Client Dashboard Section -->
            <?php elseif (hasRole('client')): ?>
                <div class="page-section">
                    <h2 class="section-title">Client Dashboard</h2>
                    <div class="cards-grid">
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="card-icon">👤</div>
                                <h5 class="card-title">Dashboard</h5>
                                <p class="card-text">View your profile and booking history</p>
                                <a href="client/dashboard.php" class="btn btn-primary">Go to Dashboard</a>
                            </div>
                        </div>
                        <div class="card dashboard-card">
                            <div class="card-body">
                                <div class="card-icon">🚗</div>
                                <h5 class="card-title">Book Service</h5>
                                <p class="card-text">Browse and book available car wash services</p>
                                <a href="client/book-service.php" class="btn btn-primary">Book Now</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <!-- Welcome section for non-logged-in users -->
            <div class="hero-section">
                <h1>Welcome to NETTOCAR</h1>
                <p>Professional Car Wash Management System</p>
            </div>

            <div class="welcome-section">
                <h2>Get Started</h2>
                <p>Join our platform to manage car wash services or book appointments</p>
                <div class="auth-buttons">
                    <a href="auth/register.php" class="btn btn-primary">Create Account</a>
                    <a href="auth/login.php" class="btn btn-secondary">Sign In</a>
                </div>
            </div>

            <div class="page-section">
                <h2 class="section-title">How It Works</h2>
                <div class="cards-grid">
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="card-icon">🔐</div>
                            <h5 class="card-title">Create Account</h5>
                            <p class="card-text">Sign up as a client or agency to get started with our platform</p>
                        </div>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="card-icon">🎯</div>
                            <h5 class="card-title">Browse Services</h5>
                            <p class="card-text">Explore available car wash services from agencies near you</p>
                        </div>
                    </div>
                    <div class="card dashboard-card">
                        <div class="card-body">
                            <div class="card-icon">✅</div>
                            <h5 class="card-title">Book & Manage</h5>
                            <p class="card-text">Book services and manage your reservations easily</p>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
