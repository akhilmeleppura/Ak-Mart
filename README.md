# Sneat E-Commerce Admin Template

A modern e-commerce administration panel built with Laravel 12, PHP 8.2, and the Sneat Admin Template. The project includes role-based access control using Spatie Laravel Permission and a scalable architecture for managing products, categories, orders, and users.

---

## 🚀 Features

* 🎨 Sneat Laravel Admin Template integration
* 🔐 Authentication and authorization
* 👥 Role & Permission management with Spatie Laravel Permission
* 🛍️ Product management
* 🗂️ Category and brand management
* 📦 Order management
* 👤 Customer management
* 📊 Dashboard analytics
* 📁 Media uploads
* 🌐 Responsive admin interface
* 🧪 Automated testing support

---

## 🛠️ Tech Stack

| Technology                | Version |
| ------------------------- | ------- |
| PHP                       | 8.2+    |
| Laravel                   | 12.x    |
| MySQL                     | 8.0+    |
| Node.js                   | 18+     |
| Vite                      | Latest  |
| Sneat Admin Template      | Latest  |
| Spatie Laravel Permission | Latest  |

---

## 📂 Project Structure

```text
app/
├── Http/
│   ├── Controllers/
│   ├── Middleware/
│   └── Requests/
├── Models/
├── Services/
├── Repositories/
└── Traits/

database/
├── migrations/
├── seeders/
└── factories/

resources/
├── views/
├── js/
└── css/

routes/
├── web.php
├── api.php
└── admin.php
```

---

## ⚙️ Installation

### 1. Clone the Repository

```bash
git clone https://github.com/akhilmeleppura/sneat-ecom-template.git
cd sneat-ecom-template
```

### 2. Install Dependencies

```bash
composer install
npm install
```

### 3. Configure Environment

```bash
cp .env.example .env
php artisan key:generate
```

Update `.env` with your database credentials.

### 4. Run Migrations and Seeders

```bash
php artisan migrate --seed
```

### 5. Build Assets

```bash
npm run build
```

For development:

```bash
npm run dev
```

### 6. Start the Application

```bash
php artisan serve
```

Open:

```text
http://127.0.0.1:8000
```

---

## 🔑 Default Admin Credentials

> Update these credentials after installation.

| Email    | [admin@example.com](mailto:admin@example.com) |
| -------- | --------------------------------------------- |
| Password | password                                      |

---

## 🔐 Roles and Permissions

This project uses Spatie Laravel Permission.

### Default Roles

* Super Admin
* Admin
* Manager
* Staff

### Example Permission Commands

```bash
php artisan permission:cache-reset
```

---

## 📦 Core Modules

### Dashboard

* Sales summary
* Recent orders
* Revenue analytics

### Product Management

* CRUD operations
* SKU management
* Image uploads

### Category Management

* Nested categories
* Slugs

### Order Management

* Status updates
* Invoice support

### User Management

* Roles and permissions

---

## 🧪 Running Tests

```bash
php artisan test
```

---

## 📝 Coding Standards

* PSR-12
* Laravel conventions
* Service and repository pattern where appropriate
* Conventional Commits

---

## 🌿 Git Workflow

### Create a Feature Branch

```bash
git checkout -b feature/product-management
```

### Commit Changes

```bash
git add .
git commit -m "feat: add product management module"
```

### Push Branch

```bash
git push -u origin feature/product-management
```

---

## 🏷️ Versioning

This project follows Semantic Versioning.

### Create a Release Tag

```bash
git tag -a v1.0.0 -m "Initial release"
git push origin v1.0.0
```

---

## 📄 License

This project is licensed under the MIT License.

---

## 👨‍💻 Author

**Akhil Meleppura**

* GitHub: [https://github.com/akhilmeleppura](https://github.com/akhilmeleppura)

---

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to your branch
5. Open a Pull Request

---

## 📌 Roadmap

* Multi-vendor marketplace support
* REST API
* Payment gateway integration
* Notification system
* Multi-language support

---

## ⭐ Support

If you find this project useful, please star the repository on GitHub.

---

## 📷 Screenshots

Add screenshots of:

* Dashboard
* Product management
* Role and permission management
* Orders

---

## 🔗 Repository

[https://github.com/akhilmeleppura/sneat-ecom-template](https://github.com/akhilmeleppura/sneat-ecom-template)
