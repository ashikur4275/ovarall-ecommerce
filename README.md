# OVARALL E-Commerce Website

A complete, fully functional e-commerce website built with HTML, CSS, JavaScript, Bootstrap, PHP, and MySQL.

## Features

### User Features
- **Home Page** - Hero slider, featured products, categories, promotions
- **Shop Page** - Browse all products with sidebar categories filter
- **Product Details** - Full product info with size/color options, reviews
- **Shopping Cart** - Add, update, remove items
- **Checkout** - Shipping info, multiple payment methods (COD, bKash, Nagad)
- **User Account** - Registration, login, order history, wishlist
- **Search** - Find products by name or description

### Admin Features
- **Dashboard** - Sales statistics, recent orders, low stock alerts
- **Product Management** - Add, edit, delete products
- **Order Management** - View orders, update status
- **User Management** - View registered customers
- **Category Management** - Organize products by category

### Categories
- Fashion
- Accessories
- Electronics (Mobile, Computer, Earphone)
- Health

## Currency
BDT (Bangladeshi Taka) - ৳

## Contact Information
- **Phone:** 01981622758
- **WhatsApp:** 01818622751
- **Email:** ashikur4275@gmail.com

## Installation

### Requirements
- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- mod_rewrite enabled (for Apache)

### Step 1: Database Setup
1. Create a MySQL database named `ovarall_db`
2. Import the `database.sql` file:
   ```bash
   mysql -u root -p ovarall_db < database.sql
   ```

### Step 2: Configuration
1. Open `includes/config.php`
2. Update database credentials:
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'your_username');
   define('DB_PASS', 'your_password');
   define('DB_NAME', 'ovarall_db');
   ```

### Step 3: Upload Files
1. Upload all files to your web server
2. Ensure the `uploads` folder is writable (chmod 755)

### Step 4: Admin Access
- **URL:** `http://yourdomain.com/admin/`
- **Email:** admin@ovarall.com
- **Password:** admin123

**Important:** Change the default admin password after first login!

## File Structure

```
ovarall-php/
├── admin/              # Admin panel
│   ├── index.php      # Dashboard
│   ├── products.php   # Product management
│   ├── orders.php     # Order management
│   ├── users.php      # User management
│   └── categories.php # Category management
├── ajax/              # AJAX handlers
│   └── cart.php       # Cart operations
├── assets/            # Static assets
│   ├── css/          # Stylesheets
│   ├── js/           # JavaScript files
│   └── images/       # Image uploads
├── includes/          # PHP includes
│   ├── config.php    # Configuration
│   ├── header.php    # Site header
│   └── footer.php    # Site footer
├── uploads/           # File uploads
├── .htaccess          # Apache config
├── cart.php          # Shopping cart
├── checkout.php      # Checkout page
├── dashboard.php     # User dashboard
├── database.sql      # Database schema
├── index.php         # Home page
├── login.php         # Login page
├── logout.php        # Logout handler
├── order-confirmation.php # Order success
├── product.php       # Product details
├── register.php      # Registration
├── search.php        # Search page
├── shop.php          # Shop page
├── wishlist.php      # Wishlist page
└── README.md         # This file
```

## Default Credentials

### Admin
- Email: admin@ovarall.com
- Password: admin123

## Security Notes

1. Change default admin password immediately
2. Use strong passwords for all accounts
3. Keep PHP and MySQL updated
4. Regularly backup the database
5. Use HTTPS in production

## Payment Methods

- **Cash on Delivery (COD)** - Default
- **bKash** - Manual verification
- **Nagad** - Manual verification

## Support

For support or inquiries:
- Call: 01981622758
- WhatsApp: 01818622751
- Email: ashikur4275@gmail.com

## License

This project is open source. Feel free to modify and use as needed.

## Credits

Built with:
- Bootstrap 5
- Font Awesome
- Google Fonts (Inter)
- PHP & MySQL
