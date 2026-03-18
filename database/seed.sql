-- Iphilo Fragrance Sample Data Seed
START TRANSACTION;

-- Seed Admins (password: admin123)
INSERT INTO `admins` (`username`, `password`, `email`, `full_name`, `role`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@iphilo.co.za', 'Iphilo Admin', 'super_admin');

-- Seed Categories
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Ladies Fragrances', 'ladies-fragrances', 'Elegant and long-lasting perfumes for women.'),
('Men\'s Colognes', 'mens-colognes', 'Bold and sophisticated colognes for men.'),
('Lotions', 'lotions', 'Scented body lotions for all-day freshness.'),
('Home Fragrance', 'home-fragrance', 'Reed diffusers and room sprays.');

-- Seed Products
INSERT INTO `products` (`category_id`, `name`, `slug`, `description`, `fragrance_notes`, `price`, `sale_price`, `stock_quantity`, `is_featured`, `is_bestseller`) VALUES
(1, 'Midnight Rose', 'midnight-rose', 'A deep, mysterious floral scent perfect for evening wear.', 'Top: Pink Pepper, Heart: Rose, Base: Vanilla', 550.00, 495.00, 50, 1, 1),
(1, 'Summer Breeze', 'summer-breeze', 'Light and airy floral notes with a hint of citrus.', 'Top: Lemon, Heart: Jasmine, Base: White Musk', 450.00, NULL, 30, 1, 0),
(2, 'Oceanic Blue', 'oceanic-blue', 'A fresh, aquatic fragrance with woody undertones.', 'Top: Sea Water, Heart: Cedar, Base: Amber', 600.00, 540.00, 25, 1, 1),
(2, 'Midnight Leather', 'midnight-leather', 'A bold, masculine scent with rich leather and spice.', 'Top: Cardamom, Heart: Leather, Base: Patchouli', 650.00, NULL, 15, 0, 1),
(3, 'Velvet Orchid Lotion', 'velvet-orchid-lotion', 'Luxurious moisturizing lotion with orchid extract.', 'Scent: Floral, Moisturizer: Shea Butter', 150.00, NULL, 100, 0, 0);

-- Seed Site Settings
INSERT INTO `site_settings` (`setting_key`, `setting_value`) VALUES
('company_name', 'Iphilo Fragrance'),
('tagline', 'Perfumes and More'),
('established_date', '2018-03-29'),
('company_bio', 'Iphilo Fragrance is a premium perfume brand established in 2018, dedicated to providing high-quality, long-lasting fragrances.'),
('registration_number', '2018/123456/07'),
('whatsapp_link', 'https://wa.me/27123456789'),
('physical_address', '123 Fragrance Lane, Scent City, 1234'),
('contact_email', 'info@iphilo.co.za'),
('support_number', '+27 12 345 6789'),
('bank_name', 'First National Bank'),
('account_holder', 'Iphilo Fragrance PTY LTD'),
('account_number', '62123456789'),
('branch_code', '250655');

-- Seed Social Links
INSERT INTO `social_links` (`platform`, `url`, `icon_class`) VALUES
('Facebook', 'https://www.facebook.com/isphilo/', 'fab fa-facebook-f'),
('Instagram', 'https://www.instagram.com/iqabunga_isphilo/', 'fab fa-instagram'),
('TikTok', 'https://www.tiktok.com/@iphilo_fragrance', 'fab fa-tiktok');

COMMIT;
