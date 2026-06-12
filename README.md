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
│   │   ├── AdminBlogController.php # Admin blog CRUD
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
│           │   └── Blog/           # Admin blog list/create/edit pages
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
- 🛠 Admin panel with phone CRUD, blog publishing (create, edit, delete), and review management
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
- `/admin/blog` for admin blog management
- `/admin/blog/create` for publishing a new blog post

### Where blog data comes from

The public blog reads from the `blog_posts` table through `BlogController`.
Default starter posts live in:

```text
database/seeders/BlogPostSeeder.php
```

If the database has no blog posts yet, the app falls back to those default seeder posts so `/blog` is not empty during setup.

### Seed the default blog posts

Run this after migrations:

```bash
php artisan db:seed --class=BlogPostSeeder
```

The seed data lives in:

```text
database/seeders/BlogPostSeeder.php
```

The seeder uses `updateOrCreate` by `slug`, so re-running it updates existing seeded posts instead of duplicating them.

### Post blog articles from the admin panel

1. Create or promote an admin user.
2. Sign in at `/login`.
3. Open `/admin/blog`.
4. Click **New post**.
5. Fill in the title, category, author, excerpt, image URL, content HTML, tags, and featured status.
6. Submit the form. The post is saved to `blog_posts` and appears on `/blog`.

Admins can also edit or delete existing posts from `/admin/blog`. The edit form supports auto-generating the slug from the title (on new posts), and the delete action removes the post immediately after confirmation.

### Add blog posts from code or CLI

You can still manage starter content in the seeder. Edit `database/seeders/BlogPostSeeder.php`, then run:

```bash
php artisan db:seed --class=BlogPostSeeder
```

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

PhoneHub is hosted on a DigitalOcean Droplet with Ubuntu LTS, Nginx, PHP 8.3+, Composer, Node.js 20+, and MySQL/MariaDB.

### 1. Create the droplet

Provision a standard Ubuntu droplet (at least 2 GB RAM recommended) and point your domain to its public IP.

SSH into your droplet and install the required packages:

```bash
sudo apt update && sudo apt upgrade -y

# Install Nginx
sudo apt install nginx -y

# Install PHP 8.3 and extensions
sudo apt install php8.3-fpm php8.3-cli php8.3-mbstring php8.3-xml php8.3-curl \
  php8.3-mysql php8.3-sqlite3 php8.3-zip php8.3-bcmath php8.3-gd php8.3-intl -y

# Install Composer
sudo apt install composer -y

# Install Node.js 20.x
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt install nodejs -y

# Install MySQL
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Install Git
sudo apt install git -y
```

### 2. Create the database

```bash
sudo mysql -u root -p
```

Run these SQL commands:

```sql
CREATE DATABASE phonehub;
CREATE USER 'phonehub_user'@'localhost' IDENTIFIED BY 'your_strong_password';
GRANT ALL PRIVILEGES ON phonehub.* TO 'phonehub_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

### 3. Clone the repository

```bash
cd /var/www
sudo git clone https://github.com/Norakneath25/PhoneHub.git
sudo chown -R $USER:$USER PhoneHub
cd PhoneHub
```

### 4. Configure `.env`

```bash
cp .env.example .env
nano .env
```

Set the production values:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=phonehub
DB_USERNAME=phonehub_user
DB_PASSWORD=your_strong_password

ADMIN_NAME="PhoneHub Admin"
ADMIN_EMAIL="admin@example.com"
ADMIN_PASSWORD="change-this-password"
```

### 5. Install dependencies and build assets

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

### 6. Set permissions

```bash
sudo chown -R www-data:www-data /var/www/PhoneHub/storage
sudo chown -R www-data:www-data /var/www/PhoneHub/bootstrap/cache
sudo chmod -R 775 /var/www/PhoneHub/storage
sudo chmod -R 775 /var/www/PhoneHub/bootstrap/cache
```

### 7. Configure Nginx

Create an Nginx site configuration:

```bash
sudo nano /etc/nginx/sites-available/phonehub
```

Paste this configuration (replace `your-domain.com` with your actual domain):

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com www.your-domain.com;

    root /var/www/PhoneHub/public;
    index index.php index.html;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable the site and reload Nginx:

```bash
sudo ln -s /etc/nginx/sites-available/phonehub /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

### 8. Enable HTTPS with Let's Encrypt

```bash
sudo apt install certbot python3-certbot-nginx -y
sudo certbot --nginx -d your-domain.com -d www.your-domain.com
```

Certbot will automatically update your Nginx config and set up auto-renewal.

### 9. Run the queue worker (optional)

If scraping or other queued jobs are used:

```bash
# Test manually first
php artisan queue:work --tries=1 --timeout=0

# For production, set up Supervisor
sudo apt install supervisor -y
sudo nano /etc/supervisor/conf.d/phonehub-worker.conf
```

Add:

```ini
[program:phonehub-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/PhoneHub/artisan queue:work --tries=1 --timeout=0
autostart=true
autorestart=true
user=www-data
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/PhoneHub/storage/logs/worker.log
```

Then:

```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start phonehub-worker:*
```

### 10. Set up the scheduler cron

```bash
sudo crontab -e
```

Add:

```bash
* * * * * cd /var/www/PhoneHub && php artisan schedule:run >> /dev/null 2>&1
```

> ⚠️ Never run `php artisan migrate:fresh` on production. It wipes the database.

---

## 📄 License

MIT
