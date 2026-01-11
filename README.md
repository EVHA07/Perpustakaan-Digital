# Perpustakaan Digital

Sistem perpustakaan digital berbasis web untuk mengelola buku, melacak waktu baca siswa, dan statistik pembelajaran.

## Fitur Utama

### 👥 Role Management
- **Admin**: Kelola user, buku, dan lihat dashboard statistik
- **Student**: Akses buku, pelacakan waktu baca, dan riwayat pembelajaran

### 📚 Manajemen Buku
- Upload buku PDF
- Auto-detect jumlah halaman
- Kategori dan penulis
- Status aktif/non-aktif

### 📖 PDF Reader
- Full-screen reader dengan scroll
- Pelacakan halaman otomatis
- Dark/Light mode
- Navigasi keyboard

### ⏱️ Reading Timer (Architecture Baru)

**Design Principle**: Backend sebagai single source of truth, frontend hanya menghitung delta.

#### Database Structure
```sql
-- reading_sessions: Tracking sesi aktif (TIDAK menyimpan durasi)
- id
- user_id, book_id
- started_at
- last_ping_at
- created_at, updated_at

-- user_book_stats: Single source of truth untuk total waktu baca
- id
- user_id, book_id
- total_seconds (UNSIGNED INTEGER)
- created_at, updated_at
-- UNIQUE: (user_id, book_id)
```

#### API Endpoints
```
POST /buku/{id}/reading/start
→ Creates reading session, returns session_id

POST /buku/{id}/reading/sync
→ Adds delta_seconds to user_book_stats.total_seconds
→ Updates last_ping_at

POST /buku/{id}/reading/end
→ Final sync before closing session
```

#### Frontend Flow
1. On load: `startReadingSession()` → get `session_id`
2. Start local stopwatch for UI only
3. Every 15s: Send `{session_id, delta_seconds}` to backend
4. Backend: `user_book_stats.total_seconds += delta_seconds`
5. On exit/tab hidden: `endReadingSession()` → final delta sync

#### Key Features
- ✅ Total time NEVER resets (only increments)
- ✅ Safe against refresh, reload, network failure
- ✅ No `duration_seconds` in sessions table
- ✅ Frontend calculates delta only for UI
- ✅ Backend is the ONLY source of truth

## Installation

### Prerequisites
- PHP 8.1+
- Composer
- MySQL
- Node.js & NPM

### Setup
```bash
# Clone repository
git clone https://github.com/EVHA07/Perpustakaan-Digital.git
cd Perpustakaan-Digital

# Install dependencies
composer install
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Database configuration
# Edit .env file with your database credentials

# Run migrations
php artisan migrate

# Build assets
npm run build

# Start development server
php artisan serve
```

### Default Credentials
```
Admin:
Email: admin@example.com
Password: password

Student:
Email: student@example.com
Password: password
```

## Tech Stack

### Backend
- **Laravel 11** - Framework
- **MySQL** - Database
- **Eloquent ORM** - Database queries

### Frontend
- **Vite** - Build tool
- **Tailwind CSS** - Styling
- **PDF.js** - PDF rendering
- **Alpine.js** - Interactivity

## Project Structure

```
app/
├── Http/Controllers/
│   ├── Admin/
│   │   ├── DashboardController.php
│   │   ├── BookController.php
│   │   └── UserController.php
│   └── Frontend/
│       ├── HomeController.php
│       ├── BookController.php
│       ├── SearchController.php
│       └── HistoryController.php
├── Models/
│   ├── User.php
│   ├── Book.php
│   ├── History.php
│   ├── UserBookStats.php
│   └── ReadingSession.php

database/
├── migrations/
│   ├── 2024_01_01_000001_create_users_table.php
│   ├── 2024_01_01_000002_create_books_table.php
│   ├── 2026_01_11_000001_create_user_book_stats_table.php
│   ├── 2026_01_11_000002_recreate_reading_sessions_table.php
│   └── ...

resources/
├── views/
│   ├── layouts/
│   ├── admin/
│   ├── frontend/
│   └── auth/
```

## Development

### Run migrations
```bash
php artisan migrate
```

### Build assets for production
```bash
npm run build
```

### Run tests
```bash
php artisan test
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
