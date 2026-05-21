# 📱 PhoneHub

A full-stack phone comparison and review platform built with Laravel, Inertia.js, Vue 3, and Tailwind CSS. Browse phones, compare specs, read blog articles, and leave reviews.

---

## 🛠 Tech Stack

| Layer      | Technology                          |
|------------|-------------------------------------|
| Backend    | Laravel 13, PHP 8.3                 |
| Frontend   | Vue 3, TypeScript, Inertia.js v2    |
| Styling    | Tailwind CSS v4                     |
| Build Tool | Vite 8                              |
| Database   | SQLite (local) / MySQL (production) |
| Auth       | Laravel Breeze + Sanctum            |

---

## ✅ Requirements

Make sure you have these installed before getting started:

- **PHP** >= 8.3
- **Composer** >= 2.x
- **Node.js** >= 20.x
- **npm** or **pnpm**
- **SQLite** (included with PHP) or MySQL

---

## 🚀 Local Setup

### 1. Clone the repository

```bash
git clone https://github.com/Norakneath25/PhoneHub.git
cd PhoneHub
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Install Node dependencies

```bash
npm install
```

### 4. Set up environment file

```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure your database

Open `.env` and set your database connection. For local SQLite (easiest):

```env
DB_CONNECTION=sqlite or ur prefer database such as mysql or mariadb
```

Then create the database file:

```bash
touch database/database.sqlite
```

For MySQL, update `.env` with:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phonehub
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 6. Run migrations

```bash
php artisan migrate
```

### 7. Seed the database

```bash
php artisan db:seed --class=BlogPostSeeder
```

### 8. Start the development server

```bash
# Runs Laravel + Vite together
composer run dev
```

Then open [http://localhost:8000](http://localhost:8000) in your browser.

---

## 🏗 Building for Production

```bash
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 📂 Project Structure

```
PhoneHub/
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminController.php     # Phone CRUD + scraping
│   │   ├── BlogController.php      # Blog listing & single post
│   │   ├── PhoneController.php     # Phone browsing & compare
│   │   └── ReviewController.php    # User reviews
│   └── Models/
│       ├── BlogPost.php
│       ├── Phone.php
│       ├── Review.php
│       └── User.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── BlogPostSeeder.php
├── resources/
│   └── js/
│       ├── components/
│       │   └── Navbar.vue
│       └── pages/
│           ├── Admin/
│           ├── Blog/
│           │   ├── Index.vue
│           │   └── Show.vue
│           └── Home.vue
└── routes/
    └── web.php
```

---

## ✨ Features

- 📱 Browse and search phones by brand, specs, and price
- ⚖️ Side-by-side phone comparison
- ⭐ User reviews and ratings
- 📝 Phone blog with categories (Reviews, News, Comparisons, Tips & Tricks)
- 🔐 Authentication (register, login, logout)
- 🛠 Admin panel with phone CRUD
- 🤖 Bulk scraping from Nika2u

---

## 🔑 Admin Access

To access the admin panel at `/admin`, your user account needs `is_admin = true` in the database. You can set this via Tinker:

```bash
php artisan tinker
>>> App\Models\User::where('email', 'your@email.com')->update(['is_admin' => true]);
```

---

## 🌐 Deploying to DigitalOcean

```bash
# On the server, after cloning and installing dependencies:
cp .env.example .env
php artisan key:generate

# Set DB credentials in .env, then:
php artisan migrate
php artisan db:seed --class=BlogPostSeeder
npm run build
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> ⚠️ Never run `php artisan migrate:fresh` on production — it will wipe all your data.

---

## 📄 License

MIT