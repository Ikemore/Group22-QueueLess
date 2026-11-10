# QueueLess Ghana — Queue Management Module

## Run locally with XAMPP
1. Extract this folder into `C:\xampp\htdocs\QueueLess_Ghana_Queue_Module`.
2. Start Apache and MySQL in XAMPP.
3. Visit `http://localhost/phpmyadmin`, choose Import, and import `database.sql`.
4. Check `config.php`. Default XAMPP credentials are user `root`, blank password. Change these if yours differ.
5. Visit `http://localhost/QueueLess_Ghana_Queue_Module/login.php`.

## Demo accounts
Password for both: `Queue123!`
- Customer: `customer@queueless.test`
- Business owner: `owner@queueless.test`

If the demo password hash does not verify on your PHP setup, create a temporary PHP file with `<?php echo password_hash('Queue123!', PASSWORD_DEFAULT);`, open it via localhost, copy the generated hash, and update both demo users' password_hash values in phpMyAdmin.

## Test the workflow
1. Log in as the customer and join Haircut.
2. Keep the ticket page open.
3. Open another browser/incognito window and log in as the business owner.
4. Click Call; customer ticket should update within about 8 seconds.
5. Click Start service, then Complete.
6. Join another ticket and test Skip.
7. Join multiple tickets and verify the later ticket's people-ahead count.
8. Try opening another customer's ticket ID while logged in as a different customer; it should not disclose the ticket.

## Queue transitions
WAITING -> CALLED -> IN_SERVICE -> COMPLETED. A WAITING ticket can also become SKIPPED. Invalid transitions are rejected server-side. Customer status uses periodic Fetch/AJAX polling every 8 seconds; this is not WebSockets or push notification.

## Important integration note
This is a standalone prototype with its own simplified `users`, `businesses`, `services` and queue tables. Before merging into the team's final application, agree on shared table names, user roles, business ownership, ticket numbering rules and skip policy with the other developers. Do not import duplicate tables blindly. The business owner is derived from the logged-in account, not from a browser-supplied business ID.

## Deployment checklist
- Use PHP/MySQL-compatible hosting.
- Create a production database and restricted database user.
- Import the team's final schema and upload the application.
- Put credentials in environment variables or a protected config outside the public web root where supported.
- Enable HTTPS, secure cookies and private error logging.
- Remove demo accounts and credentials.
- Test every queue transition on the hosted site.
- Back up the database; never commit passwords, API keys or customer data to GitHub.
- Add rate limiting, automated tests, privacy/retention rules and account recovery before public launch.

## 10-minute presentation outline
1. 0–1 min: Ghana barbershop/salon queue problem and project aim.
2. 1–2 min: Users and system architecture (browser, PHP, MySQL).
3. 2–3 min: Database and ticket status lifecycle.
4. 3–6.5 min: Live demo — customer joins, gets ticket, owner calls, starts and completes; show ticket page refreshing.
5. 6.5–7.5 min: Security — password hashing, PDO prepared statements, CSRF, role/ownership checks, transactions.
6. 7.5–8.5 min: Test cases and results.
7. 8.5–9.5 min: Limitations/future work — SMS, staff accounts, offline/PWA, analytics.
8. 9.5–10 min: Conclusion.

## Suggested test cases
| ID | Test | Expected result |
|---|---|---|
| Q01 | Join active service | Unique WAITING ticket created |
| Q02 | Invalid service ID | No ticket created |
| Q03 | View own ticket | Ticket and position shown |
| Q04 | View another user's ticket | Not found/access denied |
| Q05 | Call waiting ticket | CALLED |
| Q06 | Start called ticket | IN_SERVICE |
| Q07 | Complete in-service ticket | COMPLETED |
| Q08 | Invalid transition | Rejected |
| Q09 | Other business attempts action | Rejected by ownership check |
| Q10 | Keep ticket open | Status refreshes periodically |

For the course automated-testing requirement, add a PHPUnit or integration test against a separate test database. Never run destructive tests against live data.
