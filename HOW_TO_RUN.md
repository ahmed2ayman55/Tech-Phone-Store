# How to Run Tech Phone Store

## Prerequisites

- **PHP 7.4+** installed
- **MySQL 5.7+** installed and running
- **XAMPP** (recommended) or standalone PHP/MySQL

## Step 1: Start MySQL

### Using XAMPP:
1. Open **XAMPP Control Panel**
2. Click **Start** next to MySQL
3. Wait until it shows "Running"

### Using Standalone MySQL:
- Start MySQL service from your system services

## Step 2: Set Up Database

### Option A: Using Command Line (Recommended)

```bash
C:\xampp\mysql\bin\mysql.exe -u root < database\schema.sql
```

Or using PowerShell:
```powershell
Get-Content database\schema.sql | C:\xampp\mysql\bin\mysql.exe -u root
```

### Option B: Using phpMyAdmin

1. Start Apache in XAMPP (for phpMyAdmin)
2. Open: http://localhost/phpmyadmin
3. Click **New** → Database name: `tech_phone_store`
4. Select the database
5. Go to **Import** tab
6. Choose file: `database/schema.sql`
7. Click **Go**

### Option C: Using Batch File

Double-click `setup-db-simple.bat`

## Step 3: Configure Database

Edit `config/database.php`:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'tech_phone_store');
define('DB_USER', 'root');
define('DB_PASS', '');  // Your MySQL password (empty for XAMPP default)
```

## Step 4: Test Database Connection

```bash
C:\xampp\php\php.exe test-db.php
```

Should output: "✓ Database connection successful!"

## Step 5: Create Admin Account

```bash
C:\xampp\php\php.exe seed-admin.php
```

Or create custom admin:
```bash
C:\xampp\php\php.exe create-admin.php admin@example.com "YourPassword123!"
```

**Default Admin Credentials:**
- Email: `admin@techstore.com`
- Password: `Admin123!`

## Step 6: Start PHP Server

### Option A: Using Batch File (Windows)

Double-click `START_SERVER.bat`

### Option B: Using Command Line

```bash
C:\xampp\php\php.exe -S localhost:8000
```

### Option C: Using XAMPP Apache

1. Copy project to `C:\xampp\htdocs\tech-phone-store`
2. Start Apache from XAMPP Control Panel
3. Access: http://localhost/tech-phone-store

## Step 7: Access the Application

Open your browser and go to:

**PHP Built-in Server:**
- Homepage: http://localhost:8000
- Admin Login: http://localhost:8000/admin-login.php

**XAMPP Apache:**
- Homepage: http://localhost/tech-phone-store
- Admin Login: http://localhost/tech-phone-store/admin-login.php

## Quick Start Commands

```bash
# 1. Test database
C:\xampp\php\php.exe test-db.php

# 2. Create admin (if not done)
C:\xampp\php\php.exe seed-admin.php

# 3. Start server
C:\xampp\php\php.exe -S localhost:8000
```

## Troubleshooting

### "Database connection failed"
- Check MySQL is running
- Verify credentials in `config/database.php`
- Run `test-db.php` to diagnose

### "PHP is not recognized"
- Add PHP to system PATH
- Or use full path: `C:\xampp\php\php.exe`
- Or install XAMPP

### "Port 8000 already in use"
- Use different port: `php -S localhost:8080`
- Or stop other service using port 8000

### "404 on API calls"
- Make sure you're using PHP built-in server
- Check browser console (F12) for errors
- Verify `api/` folder exists with PHP files

## Admin Access

1. Go to: http://localhost:8000/admin-login.php
2. Login with admin credentials
3. Access dashboard: http://localhost:8000/admin.php

## Project Structure

```
tech-phone-store/
├── api/              # API endpoints
├── assets/           # CSS and JavaScript
├── config/           # Configuration files
├── database/         # Database schema
├── includes/         # Header and footer
├── index.php         # Homepage
├── products.php      # Product listing
├── admin.php         # Admin dashboard
└── admin-login.php   # Admin login
```

## Features

- ✅ Product catalog with search and filtering
- ✅ Shopping cart
- ✅ User authentication
- ✅ Order management
- ✅ Product reviews
- ✅ Admin dashboard

## Need Help?

- Check `test-db.php` for database issues
- Check browser console (F12) for JavaScript errors
- Verify all files are in correct locations

---

**That's it! Your Tech Phone Store is ready to use!** 🚀
