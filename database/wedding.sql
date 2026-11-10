-- ============================================================
-- DATABASE: wedding_invitation
-- Tool: XAMPP phpMyAdmin / MySQL
-- ============================================================

CREATE DATABASE IF NOT EXISTS wedding_invitation 
  CHARACTER SET utf8mb4 
  COLLATE utf8mb4_unicode_ci;

USE wedding_invitation;

-- ============================================================
-- TABLE: admin (untuk login admin)
-- ============================================================
CREATE TABLE IF NOT EXISTS admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,          -- bcrypt hash
  email VARCHAR(100),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Default admin: username=admin, password=admin123
INSERT INTO admin (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@wedding.com');
-- CATATAN: password di atas adalah hash bcrypt dari "password"
-- Nanti kita buat script untuk generate hash password baru

-- ============================================================
-- TABLE: invitation_templates (template/desain undangan)
-- ============================================================
CREATE TABLE IF NOT EXISTS invitation_templates (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,              -- Nama template, e.g. "Royal Garden"
  slug VARCHAR(100) NOT NULL UNIQUE,       -- URL-friendly name
  thumbnail VARCHAR(255),                  -- Path gambar preview
  description TEXT,
  color_scheme VARCHAR(50),               -- e.g. "gold", "blush", "navy"
  is_active TINYINT(1) DEFAULT 1,
  price DECIMAL(10,2) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Sample templates
INSERT INTO invitation_templates (name, slug, thumbnail, description, color_scheme, price) VALUES
('Royal Garden', 'royal-garden', 'assets/images/templates/royal-garden.jpg', 
  'Desain elegan dengan motif bunga roses dan warna gold yang mewah', 'gold', 150000),
('Modern Minimalist', 'modern-minimalist', 'assets/images/templates/modern-minimalist.jpg', 
  'Desain bersih dan modern dengan tipografi yang kuat', 'black-white', 100000),
('Rustic Bohemian', 'rustic-bohemian', 'assets/images/templates/rustic-bohemian.jpg', 
  'Nuansa natural dengan sentuhan bohemian yang hangat', 'earth-tone', 125000),
('Luxury Gold', 'luxury-gold', 'assets/images/templates/luxury-gold.jpg', 
  'Mewah dengan ornamen gold foil dan latar gelap yang dramatis', 'luxury', 175000);

-- ============================================================
-- TABLE: weddings (data pasangan pengantin)
-- ============================================================
CREATE TABLE IF NOT EXISTS weddings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  slug VARCHAR(100) NOT NULL UNIQUE,       -- URL: /invitation/slug
  template_id INT,
  
  -- Data Mempelai Pria
  groom_name VARCHAR(100) NOT NULL,
  groom_full_name VARCHAR(150),
  groom_father VARCHAR(100),
  groom_mother VARCHAR(100),
  groom_photo VARCHAR(255),
  
  -- Data Mempelai Wanita
  bride_name VARCHAR(100) NOT NULL,
  bride_full_name VARCHAR(150),
  bride_father VARCHAR(100),
  bride_mother VARCHAR(100),
  bride_photo VARCHAR(255),
  
  -- Foto & Media
  couple_photo VARCHAR(255),              -- Foto berdua utama
  gallery_photos TEXT,                    -- JSON array of photo paths
  
  -- Akad Nikah
  akad_date DATE,
  akad_time TIME,
  akad_venue VARCHAR(200),
  akad_address TEXT,
  akad_maps_url TEXT,
  
  -- Resepsi
  reception_date DATE,
  reception_time_start TIME,
  reception_time_end TIME,
  reception_venue VARCHAR(200),
  reception_address TEXT,
  reception_maps_url TEXT,
  
  -- Love Story / Our Story
  love_story TEXT,
  
  -- QRIS & Pembayaran
  qris_image VARCHAR(255),               -- Path gambar QRIS
  bank_name VARCHAR(50),
  bank_account_number VARCHAR(50),
  bank_account_name VARCHAR(100),
  
  -- Musik
  music_url VARCHAR(255),               -- URL file musik background
  
  -- Pengaturan
  is_active TINYINT(1) DEFAULT 1,
  is_published TINYINT(1) DEFAULT 0,
  views INT DEFAULT 0,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (template_id) REFERENCES invitation_templates(id)
);

-- ============================================================
-- TABLE: rsvp (konfirmasi kehadiran tamu)
-- ============================================================
CREATE TABLE IF NOT EXISTS rsvp (
  id INT AUTO_INCREMENT PRIMARY KEY,
  wedding_id INT NOT NULL,
  guest_name VARCHAR(100) NOT NULL,
  guest_phone VARCHAR(20),
  attendance ENUM('hadir', 'tidak_hadir', 'mungkin') NOT NULL DEFAULT 'hadir',
  total_guests TINYINT DEFAULT 1,
  message TEXT,                          -- Ucapan selamat
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (wedding_id) REFERENCES weddings(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE: messages (ucapan & doa tamu)
-- ============================================================
CREATE TABLE IF NOT EXISTS messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  wedding_id INT NOT NULL,
  guest_name VARCHAR(100) NOT NULL,
  message TEXT NOT NULL,
  is_approved TINYINT(1) DEFAULT 1,      -- Admin bisa moderasi
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  
  FOREIGN KEY (wedding_id) REFERENCES weddings(id) ON DELETE CASCADE
);

-- ============================================================
-- TABLE: orders (pemesanan undangan)
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_number VARCHAR(50) UNIQUE,        -- e.g. WED-20240115-001
  template_id INT,
  
  -- Data pemesan
  customer_name VARCHAR(100) NOT NULL,
  customer_email VARCHAR(100) NOT NULL,
  customer_phone VARCHAR(20) NOT NULL,
  
  -- Detail pesanan
  package_type ENUM('basic', 'premium', 'luxury') DEFAULT 'basic',
  total_price DECIMAL(10,2),
  
  -- Status
  status ENUM('pending', 'confirmed', 'processing', 'completed', 'cancelled') DEFAULT 'pending',
  payment_status ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
  
  -- Notes
  notes TEXT,
  
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  
  FOREIGN KEY (template_id) REFERENCES invitation_templates(id)
);

-- ============================================================
-- TABLE: settings (konfigurasi website)
-- ============================================================
CREATE TABLE IF NOT EXISTS settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(100) NOT NULL UNIQUE,
  setting_value TEXT,
  description VARCHAR(255)
);

INSERT INTO settings (setting_key, setting_value, description) VALUES
('site_name', 'LoveStory Invitation', 'Nama website'),
('site_tagline', 'Undangan Pernikahan Digital Elegan', 'Tagline website'),
('whatsapp_number', '082131089272', 'Nomor WhatsApp CS'),
('email_cs', 'farisgunawan3106@gmail.com', 'Email customer service'),
('instagram', '@hafidzmuzaki4165', 'Instagram handle'),
('maintenance_mode', '0', 'Mode maintenance (1=aktif)');

-- ============================================================
-- Contoh data wedding (untuk testing)
-- ============================================================
INSERT INTO weddings (
  slug, template_id,
  groom_name, groom_full_name, groom_father, groom_mother,
  bride_name, bride_full_name, bride_father, bride_mother,
  akad_date, akad_time, akad_venue, akad_address,
  reception_date, reception_time_start, reception_time_end,
  reception_venue, reception_address,
  love_story, bank_name, bank_account_number, bank_account_name,
  is_active, is_published
) VALUES (
  'rizky-aulia', 1,
  'Rizky', 'Muhammad Rizky Pratama', 'Bapak Ahmad', 'Ibu Siti',
  'Aulia', 'Aulia Rahmawati', 'Bapak Hendra', 'Ibu Dewi',
  '2025-08-17', '09:00:00', 'Masjid Al-Ikhlas', 'Jl. Merdeka No. 10, Bangil, Pasuruan',
  '2025-08-17', '11:00:00', '14:00:00',
  'Gedung Serbaguna Bangil', 'Jl. Raya Bangil No. 45, Pasuruan, Jawa Timur',
  'Kami bertemu di bangku kuliah pada tahun 2019. Pertemuan sederhana di perpustakaan kampus menjadi awal kisah cinta yang indah ini.',
  'BCA', '1234567890', 'Muhammad Rizky Pratama',
  1, 1
);

-- ============================================================
-- Tampilkan ringkasan
-- ============================================================
SELECT 'Database berhasil dibuat!' AS status;
SELECT table_name, table_rows FROM information_schema.tables 
WHERE table_schema = 'wedding_invitation';
