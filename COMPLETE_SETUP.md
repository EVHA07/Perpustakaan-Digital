# Laravel Perpustakaan Digital - Setup Guide

## Database Setup

### Option 1: MySQL (Recommended)

1. **Install MySQL** jika belum terinstall:
   - Windows: Download dari https://dev.mysql.com/downloads/installer/
   - Mac: `brew install mysql`
   - Linux: `sudo apt-get install mysql-server`

2. **Create Database**:
   ```sql
   CREATE DATABASE perpustakaan_digital CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Configure .env** (already done):
   ```
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=perpustakaan_digital
   DB_USERNAME=root
   DB_PASSWORD=
   ```

4. **Update password** jika MySQL Anda punya password:
   ```bash
   # Ganti 'your_password' dengan password MySQL Anda
   sed -i 's/DB_PASSWORD=/DB_PASSWORD=your_password/' .env
   ```

5. **Run Migrations**:
   ```bash
   php artisan migrate
   ```

6. **Seed Admin User**:
   ```bash
   php artisan db:seed --class=AdminSeeder
   ```

### Option 2: PostgreSQL

1. Install PostgreSQL
2. Create database:
   ```sql
   CREATE DATABASE perpustakaan_digital;
   ```

3. Configure .env:
   ```
   DB_CONNECTION=pgsql
   DB_HOST=127.0.0.1
   DB_PORT=5432
   DB_DATABASE=perpustakaan_digital
   DB_USERNAME=postgres
   DB_PASSWORD=your_password
   ```

4. Run migrations:
   ```bash
   php artisan migrate
   php artisan db:seed --class=AdminSeeder
   ```

## Storage Setup

```bash
php artisan storage:link
```

## Create Directories for Book Files

```bash
mkdir -p storage/app/public/books/covers
mkdir -p storage/app/public/books/files
```

## Default Accounts

After running AdminSeeder, you'll have:

**Admin Account:**
- Email: admin@perpustakaan.id
- Password: admin123

**Demo Student Account:**
- Email: siswa@perpustakaan.id
- Password: siswa123

## Run Development Server

```bash
# Start PHP server
php artisan serve

# Start Vite for frontend (separate terminal)
npm run dev
```

Access: http://localhost:8000

## Troubleshooting

### Database Connection Error

Check your database credentials in `.env` file and ensure MySQL/PostgreSQL is running.

```bash
# MySQL
sudo systemctl start mysql  # Linux
brew services start mysql   # Mac
net start mysql             # Windows (as Administrator)

# PostgreSQL
sudo systemctl start postgresql
```

### Migration Errors

```bash
# Reset migrations (CAUTION: deletes all data)
php artisan migrate:fresh
php artisan db:seed --class=AdminSeeder
```

### Permission Issues

```bash
# Linux/Mac
chmod -R 775 storage bootstrap/cache

# Windows (run as Administrator)
icacls storage /grant Everyone:(OI)(CI)F
icacls bootstrap/cache /grant Everyone:(OI)(CI)F
```

### Storage Link Not Working

```bash
# Remove and recreate
rm public/storage
php artisan storage:link
```

## File Upload

Upload book covers and files to:
- Covers: `storage/app/public/books/covers/`
- Files: `storage/app/public/books/files/`

Or use Laravel's Storage facade in your admin panel:

```php
use Illuminate\Support\Facades\Storage;

// Upload cover
$coverPath = $request->file('cover')->store('books/covers', 'public');

// Upload book file
$filePath = $request->file('file')->store('books/files', 'public');
```

## Next Steps

1. Complete database setup
2. Access admin panel to create students
3. Upload books and organize collections
4. Test student access
