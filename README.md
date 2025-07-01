# 🚗 EasyParkir – Digital Parking System

**EasyParkir** is a lightweight PHP-based web application for managing digital vehicle parking operations. This project is designed for small to medium parking lots to easily track entry/exit, calculate parking fees, generate QR codes, and produce daily/monthly reports.

---

## 🔧 Key Features

- 🚘 Vehicle entry with auto-generated unique code and QR code
- 📤 QR code scanning for fast exit confirmation
- 🅿️ Slot management per vehicle type (e.g., Motorcycle, Car)
- 📊 Daily & monthly income reports (with Excel export)
- 👤 Role-based access: Admin & Operator
- 👨‍💼 User management (admin-only)
- 🌐 Public dashboard for real-time parking data & fee checking
- 🖨 Mini receipt printing with QR code (direct from browser)
- 💡 Clean, responsive UI

---

## 📁 Project Structure

```bash
/
├── admin/                # Admin and staff dashboard
├── assets/               # Static files (CSS, JS, images, QR)
├── config/               # Database configuration
├── template/             # Reusable template parts (header, nav, footer)
├── vendor/               # Third-party libraries (e.g., PHPQRCode)
├── index.php             # Public homepage
├── login.php             # Login interface
├── .htaccess             # Rewrite rules (optional)
└── README.md
````

---

## 🚀 Local Installation

### Requirements

* PHP 7.4+
* MySQL/MariaDB
* Web server (Apache/Nginx or PHP built-in)

### Steps

1. **Clone or download the project:**

```bash
git clone https://github.com/romiwebdev/easyparkir.git
cd easyparkir
```

2. **Configure the database:**

   * Import the provided `parkir.sql` into your MySQL server.
   * Update your DB credentials in `config/config.php`.

3. **Run locally with PHP built-in server:**

```bash
php -S localhost:8000
```

4. **Open in browser:**

```
http://localhost:8000
```

---


## 🛡 Security Highlights

* Secure password storage using `password_hash()`
* Role-based access control
* SQL Injection protection via `mysqli_real_escape_string()` & input validation

---


## 🧑‍💻 Contributing

Pull requests and contributions are welcome. Fork the repository and submit your improvements.

⭐ If you like this project, don't forget to give it a star!

---

## 📄 License

```

Let me know if you want me to generate additional files like `DEPLOY.md`, `CONTRIBUTING.md`, or add badges for GitHub stats.
```
