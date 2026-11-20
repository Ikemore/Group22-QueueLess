# QueueLess Ghana — Role 3: Authentication & Initial Pages

**Skip the Wait. Get There Faster.**  
Smart Queue Management System for Barbershops & Salons.



##  Module Overview (Role 3 Deliverable)
This folder contains the initial public interface, client-side validation, and backend authentication scripts for **QueueLess Ghana**:

* `index.html` — Public landing page with live ticket lookup preview (`Q005`), key features, and user role overview.
* `auth.html` — Dual-tab Login and Registration interface supporting **Customers**, **Barbers / Stylists**, and **Administrators / Owners**.
* `style.css` — Main stylesheet using the QueueLess Ghana navy (`#0b1d35`) and gold (`#f5b700`) theme.
* `validation.js` — Client-side form validation, role-based input toggling, and offline demo fallback.
* `db.php` — PDO MySQL database connection script.
* `auth_process.php` — Backend registration, password hashing (`password_hash`), login verification, and role-based session routing.
* `logout.php` — Session destruction script for dashboard logout buttons.
* `users.sql` — SQL script to create `queueless_ghana_db` and the `users` table.



##  Local Setup Instructions (For Team Members)

1. **Import the Database:**
   * Open **MySQL Workbench** or **phpMyAdmin**.
   * Run the `users.sql` script to create the `queueless_ghana_db` database and `users` table.

2. **Configure Database Credentials:**
   * Open `db.php` and set `$password` to match your local MySQL root password (leave empty `""` if using default XAMPP/WAMP).

3. **Run the Local PHP Server:**
   * Using **VS Code Terminal**: Run `php -S localhost:8000` and open `http://localhost:8000/index.html`.
   * Using **XAMPP / WAMP**: Place the project folder inside `htdocs` or `www` and open `http://localhost/Queless_Gh/index.html`.

---

## 🔗 Integration Guide for Other Roles

### For Role 2 (Database & System Design)
The `users` table uses `id` (`INT AUTO_INCREMENT PRIMARY KEY`) and `role` (`ENUM('customer', 'stylist', 'admin')`). Use `users.id` as the foreign key when linking to the `shops`, `services`, and `tickets` tables.

### For Role 4 (Queue Management) & Role 6 (Integration)
The live ticket lookup input (`#ticketInput`) and `checkTicket()` function in `index.html` / `validation.js` are ready to be connected to your live ticket status endpoint.

### For Role 5 (Dashboard Development)
Upon successful login or registration, `auth_process.php` automatically redirects users based on their role. Please name your main dashboard entry files:
* **Customer Dashboard:** `customer_dashboard.php`
* **Barber / Stylist Dashboard:** `stylist_dashboard.php`
* **Administrator / Owner Dashboard:** `admin_dashboard.php`

**Available PHP Session Variables (`$_SESSION`):**
Start your dashboard files with `session_start();` to access the logged-in user's details:
* `$_SESSION['user_id']` — User's database ID
* `$_SESSION['full_name']` — User's full name
* `$_SESSION['role']` — `'customer'`, `'stylist'`, or `'admin'`
* `$_SESSION['shop_name']` — Barbershop or Salon name (for stylists and admins)

Link your dashboard logout button to `<a href="logout.php">Logout</a>`.