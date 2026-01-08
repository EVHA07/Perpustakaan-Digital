# Setup Guide

## Installation

1. Clone project dan install dependencies:
```bash
composer install
npm install
```

2. Copy environment file:
```bash
cp .env.example .env
```

3. Generate application key:
```bash
php artisan key:generate
```

4. Configure database di `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=perpustakaan_digital
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Run migrations:
```bash
php artisan migrate
```

6. Create symlink untuk storage:
```bash
php artisan storage:link
php artisan session:table
```

7. Create admin account:
```bash
php artisan tinker
>>> \App\Models\User::create(['name' => 'Admin', 'email' => 'admin@example.com', 'password' => bcrypt('password'), 'role' => 'admin']);
```

8. Run development server:
```bash
php artisan serve
npm run dev
```

## Middleware Registration

Tambahkan middleware ke `app/Http/Kernel.php`:

```php
protected $middlewareAliases = [
    'is.student' => \App\Http\Middleware\IsStudent::class,
    'is.admin' => \App\Http\Middleware\IsAdmin::class,
];
```

## File Storage

Upload book cover dan file ke `storage/app/public`:
- Cover images: `storage/app/public/books/covers/`
- Book files: `storage/app/public/books/files/`
