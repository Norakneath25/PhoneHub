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
composer install --no-dev --optimize-autoloader
npm install
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
│       ├── Components/
│       │   ├── Phones/             # Phone cards, filters, compare bar, reviews
│       │   ├── Site/               # Navbar, footer, home hero
│       │   └── *.vue               # Reusable auth/form UI components
│       └── Pages/
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
- 🤖 Bulk scraping from Nika2u and IMobi

---

## 🔑 Admin Access

The admin panel is available at `/admin`, but only users with `is_admin = true` can open it.

### Create an admin account on a hosted server

Add these values to your production `.env` file:

```env
ADMIN_NAME="PhoneHub Admin"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="change-this-password"
```

Then run:

```bash
php artisan admin:create
```

After that, visit `/login`, sign in with the admin email/password, and open `/admin`.

> Change `ADMIN_PASSWORD` to a strong password before running this on production.

### Make an existing user an admin

If you already registered a user account, you can promote it with Tinker:

```bash
php artisan tinker
```

```php
App\Models\User::where('email', 'your@email.com')->update(['is_admin' => true]);
```

---

## 📝 Blog Setup & Management

Blog posts are stored in the `blog_posts` database table and displayed at:

- `/blog` for the blog list
- `/blog/{slug}` for a single article

### Seed the default blog posts

Run this after migrations:

```bash
php artisan db:seed --class=BlogPostSeeder
```

The seed data lives in:

```text
database/seeders/BlogPostSeeder.php
```

### Add or edit blog posts

Currently, the project does not have a blog admin editor page. To update the blog, edit `database/seeders/BlogPostSeeder.php`, then run:

```bash
php artisan db:seed --class=BlogPostSeeder
```

The seeder uses `updateOrCreate` by `slug`, so re-running it updates existing seeded posts instead of duplicating them.

Each blog post supports these fields:

| Field       | Description                                      |
|-------------|--------------------------------------------------|
| `slug`      | URL path, for example `iphone-17-air-news`       |
| `category`  | Blog category such as `Reviews`, `News`, `Tips`  |
| `title`     | Article title                                    |
| `excerpt`   | Short summary shown on the blog list             |
| `content`   | Full article HTML                                |
| `author`    | Author name                                      |
| `read_time` | Example: `5 min read`                            |
| `image`     | Article image URL                                |
| `featured`  | `true` or `false`                                |
| `tags`      | JSON encoded tag array                           |

---

## 🌐 Deploying to DigitalOcean

```bash
# On the server, after cloning the repository:
cp .env.example .env
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate

# Set APP_URL, DB credentials, and ADMIN_* values in .env, then:
php artisan migrate
php artisan db:seed --class=BlogPostSeeder
php artisan admin:create
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

> ⚠️ Never run `php artisan migrate:fresh` on production — it will wipe all your data.

## 🌐 Deploying to Namecheap Shared Hosting

This project can run on Namecheap Shared Hosting if your hosting account supports:

- PHP 8.3 or newer
- Composer
- Node.js 20 or newer
- MySQL or MariaDB
- SSH access

Namecheap shared hosting is suitable for a small PhoneHub deployment. For heavier traffic, long-running queues, or frequent scraping jobs, use a VPS instead.

### 1. Upload the project

Upload or clone the project into your hosting account. Keep the full Laravel project outside the public web root when possible.

Your domain or subdomain document root must point to:

```text
PhoneHub/public
```

If cPanel does not let you point the domain directly to `public`, place the project outside `public_html` and configure the domain document root to the app's `public` folder.

### 2. Configure `.env`

Create the production environment file:

```bash
cp .env.example .env
```

Update these values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=localhost
DB_PORT=3306
DB_DATABASE=your_cpanel_database_name
DB_USERNAME=your_cpanel_database_user
DB_PASSWORD=your_database_password
```

Use the exact database name and username created in cPanel. On many shared hosts, cPanel prefixes them with your account name.

### 3. Install dependencies and build assets

Run these commands over SSH from the project folder:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan key:generate
php artisan migrate --force
php artisan db:seed --class=BlogPostSeeder
php artisan admin:create
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Set writable folders

Make sure Laravel can write to:

```text
storage
bootstrap/cache
```

If uploads or cached files fail, check permissions for those folders in cPanel File Manager or over SSH.

### Production `.env` checklist

Set these before running the production commands:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phonehub
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password

ADMIN_NAME="PhoneHub Admin"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="change-this-password"
```

---

## 📄 License

MIT
