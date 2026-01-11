
Digital Library

A web-based digital library system for managing books, tracking student reading time, and monitoring learning statistics.

Key Features

👥 Role Management
 * Admin: Manage users, books, and access the statistics dashboard.
 * Student: Access books, track reading progress, and view learning history.

📚 Book Management
 * PDF book uploads.
 * Auto-detect page counts.
 * Category and author management.
 * Active/Inactive status toggle.

📖 PDF Reader
 * Full-screen reader with scroll support.
 * Automatic page tracking.
 * Dark/Light mode.
 * Keyboard navigation.

⏱️ Reading Timer (New Architecture)
Design Principle: Backend as the single source of truth; frontend only calculates the delta (time elapsed).

Database Structure
-- reading_sessions: Tracks active sessions (DOES NOT store duration)
- id
- user_id, book_id
- started_at
- last_ping_at
- created_at, updated_at

-- user_book_stats: Single source of truth for total reading time
- id
- user_id, book_id
- total_seconds (UNSIGNED INTEGER)
- created_at, updated_at
-- UNIQUE: (user_id, book_id)

API Endpoints
POST /book/{id}/reading/start
→ Creates reading session, returns session_id

POST /book/{id}/reading/sync
→ Adds delta_seconds to user_book_stats.total_seconds
→ Updates last_ping_at

POST /book/{id}/reading/end
→ Final sync before closing session

Frontend Flow
 * On load: startReadingSession() → get session_id.
 * Start local stopwatch for UI display only.
 * Every 15s: Send {session_id, delta_seconds} to backend.
 * Backend: user_book_stats.total_seconds += delta_seconds.
 * On exit/tab hidden: endReadingSession() → final delta sync.
Installation
Prerequisites
 * PHP 8.1+
 * Composer
 * MySQL
 * Node.js & NPM
📌 Required php.ini Settings
To ensure smooth PDF uploads (especially for large files), open your active php.ini file and verify the following values:
file_uploads = On

upload_max_filesize = 50M
post_max_size = 50M

max_execution_time = 300
max_input_time = 300
memory_limit = 256M

Setup
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
# Edit the .env file with your database credentials

# Run migrations
php artisan migrate

# Build assets
npm run build

# Start development server
php artisan serve

Create Admin Account
After running the migrations, create an admin account using Tinker:
php artisan tinker

Then run the following command inside Tinker:
use App\Models\User;

User::create([
    'name' => 'Admin Name',
    'email' => 'admin@library.com',
    'password' => bcrypt('secure_password'),
    'role' => 'admin',
    'is_active' => true,
]);

Replace the email and password as needed. Type exit to leave Tinker.
Tech Stack
Backend
 * Laravel 11 - Framework
 * MySQL - Database
 * Eloquent ORM - Database queries
Frontend
 * Vite - Build tool
 * Tailwind CSS - Styling
 * PDF.js - PDF rendering
 * Alpine.js - Interactivity
Project Structure
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

Development
Run migrations
php artisan migrate

Build assets for production
npm run build

Run tests
php artisan test

License
This project is open-sourced software licensed under the MIT license.
Would you like me to help you create a specific Contribution Guide or a Troubleshooting section for this project?
