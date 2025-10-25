# 🚗 NETTOCAR - Car Service Booking Platform

A modern, full-stack web application for managing car rental and service bookings. Built with PHP, MySQL, and Bootstrap for a seamless user experience.

## ✨ Features

### 👤 User Roles
- **Admin** - Manage agencies, view payments, and monitor system statistics
- **Agency** - Create and manage services, handle reservations, and track bookings
- **Client** - Browse services, book appointments, and manage reservations

### 🎯 Core Functionality
- 🔐 **Secure Authentication** - User registration and login with password hashing
- 📅 **Reservation System** - Book services with date/time selection
- 💳 **Payment Processing** - Secure payment handling and history tracking
- 📊 **Analytics Dashboard** - Real-time statistics and insights
- 🏢 **Agency Management** - Create, edit, and manage service agencies
- 🔧 **Service Management** - Add and customize services with pricing

### 🎨 Modern UI/UX
- Clean, minimalist design with #ff4500 (OrangeRed) primary color
- Responsive layout for mobile and desktop
- Smooth transitions and hover effects
- Compact design with reduced corner radius
- Intuitive navigation and quick actions

## 🛠️ Tech Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **Security**: Password hashing, SQL prepared statements, session management

## 📋 Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Web server (Apache, Nginx, etc.)
- Modern web browser

## 🚀 Installation

1. **Clone the repository**
   \`\`\`bash
   git clone https://github.com/yourusername/nettocar.git
   cd nettocar
   \`\`\`

2. **Setup Database**
   - Create a new MySQL database
   - Import `database/schema.sql`
   - Update database credentials in `config/db.php`

3. **Configure Database Connection**
   \`\`\`php
   // config/db.php
   $con = new mysqli("localhost", "username", "password", "nettocar");
   \`\`\`

4. **Create Admin Account**
   \`\`\`bash
   php create_admin.php
   \`\`\`

5. **Start your web server**
   - Place files in your web root directory
   - Access via `http://localhost/nettocar`

## 📁 Project Structure

\`\`\`
nettocar/
├── admin/              # Admin dashboard and management pages
├── agency/             # Agency dashboard and service management
├── client/             # Client dashboard and booking pages
├── auth/               # Authentication pages (login, register, etc.)
├── payments/           # Payment processing pages
├── api/                # API endpoints
├── config/             # Database and session configuration
├── database/           # Database schema
├── public/             # Static assets (images, logos)
├── styles/             # CSS stylesheets
├── app/                # Application configuration
└── index.php           # Homepage
\`\`\`

## 🔐 Security Features

- ✅ SQL Injection Prevention (Prepared Statements)
- ✅ Password Hashing (bcrypt)
- ✅ Session Management
- ✅ Input Validation
- ✅ CSRF Protection Ready

## 📱 Responsive Design

- Mobile-first approach
- Optimized for all screen sizes
- Touch-friendly interface
- Fast loading times

## 🎨 Design System

### Color Palette
- **Primary**: #ff4500 (OrangeRed)
- **Primary Light**: #ff6b35
- **Primary Dark**: #e63e00
- **Success**: #10b981
- **Warning**: #f59e0b
- **Info**: #3b82f6

### Typography
- **Font Family**: System fonts (-apple-system, BlinkMacSystemFont, Segoe UI, Roboto)
- **Headings**: Bold, 600-700 weight
- **Body**: Regular, 400-500 weight

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 📞 Support

For support, email support@nettocar.com or open an issue on GitHub.

## 🙏 Acknowledgments

- Bootstrap 5 for responsive components
- PHP community for excellent documentation
- All contributors and users

---

**Made with ❤️ by the NETTOCAR Team**
