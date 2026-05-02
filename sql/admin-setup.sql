-- Admin Account Setup SQL
-- Use this to create or update the admin account

-- Delete existing admin if it exists
DELETE FROM admins WHERE username = 'admin';

-- Insert new admin with specified credentials
INSERT INTO admins (username, password_hash) VALUES 
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Note: The password hash above is for the password "password"
-- If you want to use a different password, generate a new hash using:
-- password_hash('your_password', PASSWORD_DEFAULT)

-- Verify the admin was created
SELECT * FROM admins WHERE username = 'admin';
