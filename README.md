# Iphilo Fragrance - Full-Stack Website & CRM

A premium, modern, and database-driven e-commerce platform for Iphilo Fragrance, built with PHP, MySQL, and Bootstrap 5.

## 🚀 Features

### Public Website
- **Luxury Home Page**: Featured products, latest arrivals, and best sellers.
- **Product Catalog**: Full shop with filtering by category and sorting.
- **Product Details**: Fragrance notes, image gallery, and customer reviews.
- **Shopping Cart**: Real-time updates and persistence.
- **Secure Checkout**: Support for Card payments and EFT (with proof of payment upload).
- **Customer Dashboard**: Order history, profile management, and wishlist.
- **Order Tracking**: Track status without logging in.

### Admin System
- **Comprehensive Dashboard**: Real-time sales statistics and low-stock alerts.
- **Product Management**: Add, edit, and delete products and categories.
- **Order Management**: Update statuses and verify EFT payments.
- **CRM / Customer Management**: Single view of customer profiles and activity history.
- **Reporting & Analytics**: Daily/Monthly sales charts and top-selling products.
- **Review Moderation**: Approve or reject customer feedback.

## 🛠️ Technology Stack
- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript (jQuery), Bootstrap 5
- **Icons**: Font Awesome 6
- **Fonts**: Playfair Display & Poppins

## 📂 Project Structure
```bash
/iphilo-fragrance/
├── index.php             # Homepage
├── shop.php              # Product catalog
├── product.php           # Single product view
├── cart.php              # Shopping cart
├── checkout.php          # Checkout process
├── order-tracking.php    # Order tracking
├── login.php             # Customer login
├── register.php          # Customer registration
├── customer-dashboard.php # Customer account
│
├── /admin/               # Admin Portal
│   ├── index.php         # Admin login
│   ├── dashboard.php     # Admin home
│   ├── products.php      # Catalog management
│   └── orders.php        # Order management
│
├── /includes/            # Core PHP includes
│   ├── config.php        # Site configuration
│   ├── db.php            # Database connection
│   ├── functions.php     # Helper functions
│   └── auth.php          # Authentication logic
│
├── /assets/              # Static assets
│   ├── /css/             # Custom styles
│   ├── /js/              # Frontend logic
│   └── /uploads/         # User-uploaded files
│
└── /database/            # SQL scripts
    ├── database.sql      # Schema
    └── seed.sql          # Sample data
```

## 🔧 Installation Instructions (Local Setup)

1. **Prerequisites**: Install XAMPP, WAMP, or Laragon.
2. **Database Setup**:
   - Create a new database named `iphilo_fragrance` in phpMyAdmin.
   - Import `database/database.sql` to create tables.
   - Import `database/seed.sql` to populate sample data.
3. **Configuration**:
   - Open `includes/config.php`.
   - Update `DB_HOST`, `DB_USER`, `DB_PASS`, and `DB_NAME` to match your local environment.
   - Update `BASE_URL` to your local folder path (e.g., `http://localhost/iphilo-fragrance`).
4. **Access**:
   - **Public Site**: `http://localhost/iphilo-fragrance`
   - **Admin Portal**: `http://localhost/iphilo-fragrance/admin`
   - **Admin Login**: Username: `admin` | Password: `admin123`

## 🛡️ Security Features
- **SQL Injection Prevention**: Using PDO with prepared statements.
- **XSS Protection**: Sanitizing all user input before outputting.
- **CSRF Protection**: Tokens for sensitive form submissions.
- **Password Hashing**: Using `password_hash()` for all user passwords.

## 📝 Customization
- **Logo**: Replace `assets/images/logo.png` with your company logo.
- **Company Details**: Edit via the `site_settings` table in the database or the Admin Settings page.
- **Social Links**: Manage through the `social_links` table.

## 📧 Support
For any technical queries, contact [info@iphilo.co.za](mailto:info@iphilo.co.za).

---
*Established 29 March 2018*
