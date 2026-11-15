<?php
declare(strict_types=1);

function db(): PDO {
 static $pdo = null;
 if ($pdo instanceof PDO) return $pdo;

 $dsn = getenv('QUEUELESS_DB_DSN') ?: 'sqlite:'.__DIR__.'/data/queueless.sqlite';
 $user = getenv('QUEUELESS_DB_USER') ?: null;
 $password = getenv('QUEUELESS_DB_PASSWORD') ?: null;
 $pdo = new PDO($dsn, $user, $password, [
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  PDO::ATTR_EMULATE_PREPARES => false,
 ]);

 if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
  $pdo->exec('PRAGMA foreign_keys = ON');
  $pdo->exec(<<<'SQL'
CREATE TABLE IF NOT EXISTS users (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 full_name TEXT NOT NULL,
 email TEXT UNIQUE NOT NULL,
 password_hash TEXT NOT NULL,
 role TEXT NOT NULL CHECK(role IN ('customer','business'))
);
CREATE TABLE IF NOT EXISTS businesses (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 owner_user_id INTEGER NOT NULL,
 business_name TEXT NOT NULL,
 location TEXT,
 FOREIGN KEY(owner_user_id) REFERENCES users(id)
);
CREATE TABLE IF NOT EXISTS services (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 business_id INTEGER NOT NULL,
 service_name TEXT NOT NULL,
 estimated_minutes INTEGER NOT NULL DEFAULT 30,
 is_active INTEGER NOT NULL DEFAULT 1,
 FOREIGN KEY(business_id) REFERENCES businesses(id)
);
CREATE TABLE IF NOT EXISTS queue_tickets (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 ticket_code TEXT UNIQUE NOT NULL,
 customer_user_id INTEGER NOT NULL,
 business_id INTEGER NOT NULL,
 service_id INTEGER NOT NULL,
 ticket_date TEXT NOT NULL,
 daily_number INTEGER NOT NULL,
 status TEXT NOT NULL DEFAULT 'WAITING' CHECK(status IN ('WAITING','CALLED','IN_SERVICE','COMPLETED','SKIPPED','CANCELLED')),
 joined_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
 called_at TEXT,
 service_started_at TEXT,
 completed_at TEXT,
 FOREIGN KEY(customer_user_id) REFERENCES users(id),
 FOREIGN KEY(business_id) REFERENCES businesses(id),
 FOREIGN KEY(service_id) REFERENCES services(id)
);
CREATE INDEX IF NOT EXISTS queue_tickets_business_day_status_number
 ON queue_tickets(business_id,ticket_date,status,daily_number);
CREATE TABLE IF NOT EXISTS queue_events (
 id INTEGER PRIMARY KEY AUTOINCREMENT,
 ticket_id INTEGER NOT NULL,
 actor_user_id INTEGER NOT NULL,
 event_type TEXT NOT NULL,
 created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(ticket_id) REFERENCES queue_tickets(id),
 FOREIGN KEY(actor_user_id) REFERENCES users(id)
);
SQL);
  $userCount = (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
  if ($userCount === 0) {
   $pdo->beginTransaction();
   $hash = password_hash('Queue123!', PASSWORD_DEFAULT);
   $userInsert = $pdo->prepare('INSERT INTO users (id,full_name,email,password_hash,role) VALUES (?,?,?,?,?)');
   $userInsert->execute([1, 'Demo Customer', 'customer@queueless.test', $hash, 'customer']);
   $userInsert->execute([2, 'Demo Business Owner', 'owner@queueless.test', $hash, 'business']);
   $pdo->exec("INSERT INTO businesses (id,owner_user_id,business_name,location) VALUES (1,2,'QueueLess Demo Barbershop','Nkawie, Ashanti Region')");
   $serviceInsert = $pdo->prepare('INSERT INTO services (business_id,service_name,estimated_minutes) VALUES (1,?,?)');
   foreach ([['Haircut', 30], ['Haircut and beard trim', 45], ['Hair wash', 20]] as [$name, $minutes]) {
    $serviceInsert->execute([$name, $minutes]);
   }
   $pdo->commit();
  }
 }

 return $pdo;
}
