# 🌙 Moon Cafeteria Management System

A modern, secure cafeteria management system built with PHP, MySQL, and modern web technologies.

## Features

### 🔐 Security
- Secure authentication with password hashing (password_hash/password_verify)
- Role-based access control (Admin & Cashier)
- Session protection and timeout
- SQL injection prevention (prepared statements)
- Input validation and sanitization
- CSRF protection

### 👨‍💼 Admin Features
- Modern dashboard with statistics
- User management (CRUD operations)
- Product management (CRUD operations)
- Category management
- Sales reports (daily, weekly, monthly, custom range)
- Top products analysis

### 💰 Cashier Features
- Point of Sale (POS) interface
- Product browsing with category filters
- Shopping cart functionality
- Real-time total calculation
- Order processing
- Receipt generation and printing
- Order history

### 🎨 Modern UI
- Dark theme with vibrant accent colors
- Responsive design
- Smooth animations and transitions
- Professional dashboard layout
- Clean and intuitive interface

## Installation

### Prerequisites
- XAMPP (or any PHP/MySQL environment)
- PHP 7.4 or higher
- MySQL 5.7 or higher

### Setup Instructions

1. **Extract Files**
   - Extract the `moon-cafeteria` folder to your XAMPP `htdocs` directory
   - Path should be: `C:\xampp\htdocs\moon-cafeteria`

2. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Click "Import" tab
   - Choose file: `moon_cafeteria.sql`
   - Click "Go" to import

3. **Configure Database Connection**
   - Open `config/database.php`
   - Update credentials if needed (default: root with no password)
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'moon_cafeteria');
   ```

4. **Create Upload Directory**
   - Create folder: `assets/images/products/`
   - Set permissions to writable (755 or 777)

5. **Access the System**
   - Open browser and go to: `http://localhost/moon-cafeteria`
   - You'll be redirected to the login page

## Default Login Credentials

### Admin Account
- **Username:** `admin`
- **Password:** `admin123`

### Cashier Account
- **Username:** `cashier`
- **Password:** `cashier123`

> ⚠️ **Important:** Change these credentials after first login in production!

## Project Structure

```
moon-cafeteria/
├── admin/                  # Admin panel pages
│   ├── dashboard.php      # Admin dashboard
│   ├── products.php       # Product management
│   ├── product_add.php    # Add product
│   ├── product_edit.php   # Edit product
│   ├── product_delete.php # Delete product
│   ├── users.php          # User management
│   ├── user_add.php       # Add user
│   ├── user_edit.php      # Edit user
│   ├── user_delete.php    # Delete user
│   ├── categories.php     # Category management
│   └── reports.php        # Sales reports
├── cashier/               # Cashier panel pages
│   ├── dashboard.php      # Cashier dashboard
│   ├── pos.php            # Point of Sale
│   ├── process_order.php  # Order processing
│   ├── orders.php         # Order history
│   └── receipt.php        # Receipt view/print
├── auth/                  # Authentication
│   ├── login.php          # Login page
│   └── logout.php         # Logout handler
├── config/                # Configuration files
│   ├── config.php         # App configuration
│   └── database.php       # Database connection
├── includes/              # Common includes
│   ├── header.php         # Header template
│   ├── footer.php         # Footer template
│   ├── sidebar.php        # Sidebar navigation
│   ├── session.php        # Session management
│   └── functions.php      # Utility functions
├── assets/                # Static assets
│   ├── css/
│   │   └── style.css      # Main stylesheet
│   ├── js/
│   │   └── main.js        # Main JavaScript
│   └── images/
│       └── products/      # Product images
├── index.php              # Entry point
└── moon_cafeteria.sql     # Database schema
```

## Usage Guide

### For Administrators

1. **Login** with admin credentials
2. **Manage Products:**
   - Add new products with images, prices, and stock
   - Edit existing products
   - Delete products
   - Organize by categories
3. **Manage Users:**
   - Add cashier accounts
   - Edit user details
   - Deactivate/activate users
4. **View Reports:**
   - Daily, weekly, monthly sales
   - Custom date range reports
   - Top selling products
   - Print reports

### For Cashiers

1. **Login** with cashier credentials
2. **Process Sales:**
   - Open POS system
   - Browse products by category
   - Add items to cart
   - Adjust quantities
   - Select payment method
   - Complete checkout
3. **View Orders:**
   - Check order history
   - Print receipts

## Database Schema

- **roles** - User roles (Admin, Cashier)
- **users** - System users with authentication
- **categories** - Product categories
- **products** - Cafeteria products (food & drinks)
- **orders** - Customer orders
- **order_items** - Items in each order

## Security Features

- ✅ Password hashing with bcrypt
- ✅ Prepared statements (SQL injection prevention)
- ✅ Session hijacking protection
- ✅ Session timeout (30 minutes)
- ✅ Input sanitization
- ✅ CSRF token protection
- ✅ Role-based access control
- ✅ Secure file uploads

## Browser Compatibility

- Chrome (recommended)
- Firefox
- Edge
- Safari

## Support

For issues or questions, please check:
- Database connection settings
- PHP error logs
- File permissions on upload directory

## License

This project is created for educational and commercial use.

---

**Developed with ❤️ for Moon Cafeteria**
