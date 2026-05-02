-- Pricing and Payments Schema
-- Add these tables to your existing database

-- Pricing Plans Table
CREATE TABLE IF NOT EXISTS pricing_plans (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    billing_cycle ENUM('monthly', 'yearly', 'one-time') DEFAULT 'monthly',
    features JSON,
    is_active BOOLEAN DEFAULT TRUE,
    sort_order INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Subscriptions Table
CREATE TABLE IF NOT EXISTS subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    plan_id INT NOT NULL,
    status ENUM('active', 'cancelled', 'expired', 'pending') DEFAULT 'pending',
    start_date DATE,
    end_date DATE,
    next_billing_date DATE,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('gateway', 'manual') DEFAULT 'manual',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (plan_id) REFERENCES pricing_plans(id)
) ENGINE=InnoDB;

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    subscription_id INT,
    amount DECIMAL(10,2) NOT NULL,
    payment_method ENUM('gateway', 'manual', 'other') DEFAULT 'manual',
    status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    transaction_id VARCHAR(255),
    gateway_response JSON,
    donor_name VARCHAR(255),
    donor_email VARCHAR(255),
    donor_phone VARCHAR(50),
    notes TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (subscription_id) REFERENCES subscriptions(id)
) ENGINE=InnoDB;

-- Insert Sample Pricing Plans
INSERT INTO pricing_plans (name, slug, description, price, billing_cycle, features, sort_order) VALUES
('Partner', 'partner', 'Monthly partnership for ministry support', 25.00, 'monthly', 
 '["Monthly newsletter", "Prayer requests", "Access to sermons", "Ministry updates"]', 1),
('Kingdom Builder', 'kingdom', 'Enhanced partnership with exclusive benefits', 50.00, 'monthly',
 '["All Partner benefits", "Exclusive teachings", "Early event access", "Personal prayer", "Ministry resources"]', 2),
('Legacy Giver', 'legacy', 'Premium partnership with personalized benefits', 100.00, 'monthly',
 '["All Kingdom Builder benefits", "One-on-one mentoring", "VIP event invitations", "Prophetic insights", "Legacy recognition"]', 3);
