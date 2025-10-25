# NETTOCAR - Car Wash Agency Management System

A PHP + MySQL backend for managing car-wash agencies, services, reservations, and subscription packs.

## Setup Instructions

### 1. Database Setup

1. Open phpMyAdmin or MySQL command line
2. Run the SQL schema from `database/schema.sql`:
   \`\`\`sql
   CREATE DATABASE IF NOT EXISTS nettocar;
   USE nettocar;
   -- Run all SQL from schema.sql file
   \`\`\`

### 2. Configuration

1. Update `config/db.php` with your database credentials:
   \`\`\`php
   $host = "localhost";
   $user = "root";
   $password = "";
   $database = "nettocar";
   \`\`\`

### 3. Running the Application

1. Place the project in your web server directory (htdocs for XAMPP, www for WAMP)
2. Access the application at `http://localhost/nettocar/`

## Project Structure

\`\`\`
nettocar/
├── config/
│   ├── db.php              # Database connection
│   └── session.php         # Session management
├── auth/
│   ├── register.php        # User registration
│   ├── login.php           # User login
│   └── logout.php          # User logout
├── admin/
│   ├── dashboard.php       # Admin dashboard
│   ├── agencies.php        # Manage agencies
│   └── payments.php        # View payments
├── agency/
│   ├── dashboard.php       # Agency dashboard
│   ├── services.php        # Manage services
│   ├── reservations.php    # View reservations
│   ├── create-agency.php   # Create agency
│   └── edit-agency.php     # Edit agency
├── client/
│   ├── dashboard.php       # Client dashboard
│   └── book-service.php    # Book service
├── database/
│   └── schema.sql          # Database schema
└── index.php               # Home page
\`\`\`

## Features

### Authentication
- User registration (Client, Agency Owner)
- Secure login with password hashing
- Session management
- Role-based access control

### Subscription Packs
- **Basic**: 10 reservations/day, no statistics
- **Standard**: Unlimited reservations, weekly statistics
- **Premium**: Unlimited, statistics + CSV export

### Agency Management
- Create, edit, delete agencies
- Manage services and pricing
- View reservations

### Reservations
- Book services
- View today/week reservations
- Update reservation status (waiting, in_progress, finished)

### Payments
- Record pack purchases
- Payment history
- Simulated payment processing

### Statistics
- Weekly/monthly reservation counts
- Revenue calculations
- CSV export (Premium only)

## Security Features

- Prepared statements (mysqli) to prevent SQL injection
- Password hashing with bcrypt
- Session-based authentication
- Role-based access control
- CSRF protection ready

## Default Test Accounts

After running the schema, you can create test accounts:
- Admin account (register with admin role)
- Agency account (register as agency owner)
- Client account (register as client)

## API Endpoints (Future Enhancement)

The system is ready to be extended with REST API endpoints:
- POST /api/auth/register
- POST /api/auth/login
- CRUD /api/agencies
- CRUD /api/services
- POST /api/reservations
- GET /api/statistics
- GET /api/export/reservations.csv

## Notes

- All passwords are hashed using bcrypt
- Database uses UTF-8 encoding
- Timestamps are automatically managed
- Foreign key constraints ensure data integrity
