<?php
// Iphilo Fragrance Configuration File

// Site Info
define('SITE_NAME', 'Iphilo Fragrance');
define('SITE_TAGLINE', 'Perfumes and More');
define('ESTABLISHED_DATE', '29 March 2018');
define('BASE_URL', 'http://localhost/iphilo-fragrance'); // Adjust for local setup

// Database Credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'iphilo_fragrance');

// Contact Info (Placeholders)
define('CONTACT_EMAIL', 'info@iphilo.co.za');
define('SUPPORT_NUMBER', '+27 12 345 6789');
define('WHATSAPP_LINK', 'https://wa.me/27123456789');

// Social Media (Placeholders)
define('FACEBOOK_LINK', 'https://www.facebook.com/isphilo/');
define('INSTAGRAM_LINK', 'https://www.instagram.com/iqabunga_isphilo/');
define('TIKTOK_LINK', 'https://www.tiktok.com/@iphilo_fragrance');

// Third-party API Key Placeholders
define('PAYFAST_MERCHANT_ID', '');
define('PAYFAST_MERCHANT_KEY', '');
define('GOOGLE_MAPS_API_KEY', '');
define('SMTP_HOST', '');
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_PORT', 587);

// Security Settings
define('SESSION_LIFETIME', 3600); // 1 hour
define('UPLOAD_DIR', __DIR__ . '/../assets/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Error Reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
