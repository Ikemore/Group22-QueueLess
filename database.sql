CREATE DATABASE IF NOT EXISTS queueless_ghana CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE queueless_ghana;
CREATE TABLE users(id INT AUTO_INCREMENT PRIMARY KEY, full_name VARCHAR(100) NOT NULL, email VARCHAR(150) UNIQUE NOT NULL, password_hash VARCHAR(255) NOT NULL, role ENUM('customer','business') NOT NULL);
CREATE TABLE businesses(id INT AUTO_INCREMENT PRIMARY KEY, owner_user_id INT NOT NULL, business_name VARCHAR(150) NOT NULL, location VARCHAR(150), FOREIGN KEY(owner_user_id) REFERENCES users(id));
CREATE TABLE services(id INT AUTO_INCREMENT PRIMARY KEY, business_id INT NOT NULL, service_name VARCHAR(120) NOT NULL, estimated_minutes INT NOT NULL DEFAULT 30, is_active TINYINT DEFAULT 1, FOREIGN KEY(business_id) REFERENCES businesses(id));
CREATE TABLE queue_tickets(id BIGINT AUTO_INCREMENT PRIMARY KEY, ticket_code VARCHAR(24) UNIQUE NOT NULL, customer_user_id INT NOT NULL, business_id INT NOT NULL, service_id INT NOT NULL, ticket_date DATE NOT NULL, daily_number INT NOT NULL, status ENUM('WAITING','CALLED','IN_SERVICE','COMPLETED','SKIPPED','CANCELLED') DEFAULT 'WAITING', joined_at DATETIME DEFAULT CURRENT_TIMESTAMP, called_at DATETIME NULL, service_started_at DATETIME NULL, completed_at DATETIME NULL, INDEX(business_id,ticket_date,status,daily_number), FOREIGN KEY(customer_user_id) REFERENCES users(id), FOREIGN KEY(business_id) REFERENCES businesses(id), FOREIGN KEY(service_id) REFERENCES services(id));
CREATE TABLE queue_events(id BIGINT AUTO_INCREMENT PRIMARY KEY, ticket_id BIGINT NOT NULL, actor_user_id INT NOT NULL, event_type VARCHAR(40) NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, FOREIGN KEY(ticket_id) REFERENCES queue_tickets(id), FOREIGN KEY(actor_user_id) REFERENCES users(id));
-- Demo password for both accounts: Queue123!
INSERT INTO users(id,full_name,email,password_hash,role) VALUES
(1,'Demo Customer','customer@queueless.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','customer'),
(2,'Demo Business Owner','owner@queueless.test','$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2uheWG/igi.','business');
INSERT INTO businesses(id,owner_user_id,business_name,location) VALUES(1,2,'QueueLess Demo Barbershop','Nkawie, Ashanti Region');
INSERT INTO services(id,business_id,service_name,estimated_minutes) VALUES(1,1,'Haircut',30),(2,1,'Haircut and beard trim',45),(3,1,'Hair wash',20);
