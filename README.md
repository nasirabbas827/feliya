# feliya_final

## Overview
`feliya_final` is a PHP‑based web application that provides an admin dashboard for managing offers, packages, users, and subscriptions, as well as a public interface for registration, login, and participation in events. The project includes a MySQL database schema, a set of reusable UI components, and a simple authentication system.

## Features
- **Admin Panel**
  - Add, edit, and delete offers and packages
  - Manage users, view feedback, and handle password‑reset requests
  - Monitor subscriptions, participants, and draw results
- **User Experience**
  - Secure registration and login
  - Password recovery workflow
  - Responsive navigation bar and styled UI
- **Database**
  - Pre‑defined schema (`Database/feliya_db.sql`) with tables for users, offers, packages, subscriptions, feedback, and more
- **Modular Structure**
  - Centralized configuration files (`config.php`, `admin/config.php`, `user/config.php`)
  - Reusable CSS (`css/style.css`) and navigation components (`navbar.php`, `admin_navbar.php`)

## Tech Stack
| Layer | Technology |
|------|------------|
| Backend | PHP 7.4+ |
| Database | MySQL / MariaDB |
| Front‑end | HTML5, CSS3 |
| Server | Apache / Nginx |
| Version Control | Git |

## Installation
1. **Clone the repository**  
   ```bash
   git clone https://github.com/yourusername/feliya_final.git
   cd feliya_final
   ```

2. **Create a MySQL database**  
   ```sql
   CREATE DATABASE feliya_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Import the schema**  
   ```bash
   mysql -u your_user -p feliya_db < Database/feliya_db.sql
   ```

4. **Configure database connection**  
   - Open `config.php`, `admin/config.php`, and `user/config.php`.
   - Replace placeholder values with your credentials:
     ```php
     define('DB_HOST', 'YOUR_DB_HOST');
     define('DB_NAME', 'feliya_db');
     define('DB_USER', 'YOUR_DB_USER');
     define('DB_PASS', 'YOUR_DB_PASSWORD');
     ```

5. **Set up a web server**  
   - Place the project in the web root (e.g., `htdocs` or `public_html`).
   - Ensure the server points to `index.php` as the default document.
   - Enable PHP and MySQL extensions.

6. **Adjust file permissions** (if required)  
   ```bash
   chmod -R 755 .
   ```

## Usage
- **Access the public site**:  
  `http://your-domain.com/` – users can register, log in, and view offers.

- **Admin login**:  
  `http://your-domain.com/admin_login.php` – use the credentials stored in the `admins` table.

- **Admin dashboard**:  
  After login you will be redirected to `admin/admin_home.php` where you can navigate to:
  - `view_offers.php`, `add_offer.php`, `edit_offer.php`
  - `view_packages.php`, `add_package.php`, `edit_package.php`
  - `view_users.php`, `edit_user.php`
  - `view_subscriptions.php`, `view_participants.php`, `view_widrawls.php`
  - `admin_feedback.php`, `admin_reply.php`

- **Password recovery**:  
  Users can request a reset via `forgot_password.php`; admins can manage requests through `admin/forgot_password_requests.php`.

- **Logout**:  
  `logout.php` (user) and `admin/logout.php` (admin) terminate the session.

## License
This project is licensed under the **MIT License**. See the `LICENSE` file