<?php
session_start();
require_once 'db.php';

function redirectOrShowSuccess($role, $fullName, $shopName) {
    $dashboardMap = [
        'admin'    => 'admin_dashboard.php',
        'stylist'  => 'stylist_dashboard.php',
        'customer' => 'customer_dashboard.php'
    ];
    $targetFile = $dashboardMap[$role] ?? 'customer_dashboard.php';

    // Only redirect if Role 5's dashboard file is present in the folder
    if (file_exists(__DIR__ . '/' . $targetFile)) {
        header("Location: " . $targetFile);
        exit;
    }
    echo "<!DOCTYPE html><html><head><title>Authenticated | QueueLess Ghana</title>
          <link rel='stylesheet' href='style.css'></head><body>
          <div class='auth-container' style='text-align:center;'>
              <h2 style='color:#0b1d35;'>Welcome, " . htmlspecialchars($fullName) . "!</h2>
              <p style='margin:12px 0; color:#15803d; font-weight:700;'>✓ Saved to MySQL & Logged In</p>
              <p><strong>Role:</strong> " . htmlspecialchars(ucfirst($role)) . "</p>" .
              ($shopName ? "<p><strong>Shop:</strong> " . htmlspecialchars($shopName) . "</p>" : "") .
              "<p style='font-size:13px; color:#64748b; margin:16px 0;'>
                  (Waiting for Role 5 to add <code>$targetFile</code>)
              </p>
              <a href='index.html' class='btn-submit' style='display:block; text-decoration:none;'>Back to Home</a>
          </div></body></html>";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'register') {
        $fullName = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone = trim($_POST['phone']);
        $role = in_array($_POST['role'], ['customer', 'stylist', 'admin']) ? $_POST['role'] : 'customer';
        $shopName = ($role !== 'customer') ? trim($_POST['shop_name']) : null;
        $shopType = ($role !== 'customer') ? $_POST['shop_type'] : null;
        $passwordHash = password_hash($_POST['password'], PASSWORD_DEFAULT);

        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
            die("Email already registered. <a href='auth.html#register'>Go back</a>");
        }

        $stmt = $pdo->prepare("
            INSERT INTO users (full_name, email, phone, password_hash, role, shop_name, shop_type)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        $stmt->execute([$fullName, $email, $phone, $passwordHash, $role, $shopName, $shopType]);

        $_SESSION['user_id'] = $pdo->lastInsertId();
        $_SESSION['full_name'] = $fullName;
        $_SESSION['role'] = $role;
        $_SESSION['shop_name'] = $shopName;

        redirectOrShowSuccess($role, $fullName, $shopName);
    }

    if ($action === 'login') {
        $email = trim($_POST['email']);
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['shop_name'] = $user['shop_name'];

            redirectOrShowSuccess($user['role'], $user['full_name'], $user['shop_name']);
        } else {
            die("Invalid email or password. <a href='auth.html'>Try again</a>");
        }
    }
}
?>