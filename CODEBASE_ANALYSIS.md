# Perpustakaan Digital - Codebase Structure & Analysis

## Overview
This is a **Laravel 12** web application for managing a digital library with integrated PDF reading functionality and reading time tracking. The application uses Tailwind CSS for styling and PDF.js for rendering PDF files.

---

## 1. PROJECT STRUCTURE

### Tech Stack
- **Backend**: Laravel 12 (PHP)
- **Frontend**: Blade templates + vanilla JavaScript
- **Database**: SQLite (default) or any Laravel-compatible DB
- **Styling**: Tailwind CSS 4.0
- **Build Tool**: Vite 7.0
- **PDF Rendering**: PDF.js 3.11.174
- **PDF Parsing**: smalot/pdfparser 2.12 (composer)

### Root Directory Layout
```
project-root/
├── app/                           ← Laravel application code
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Frontend/          ← Student features (HomeController, BookController, etc.)
│   │   │   ├── Admin/             ← Admin features (DashboardController, BookController, UserController)
│   │   │   └── Auth/              ← LoginController
│   │   └── Middleware/            ← IsStudent, IsAdmin, Authenticate
│   └── Models/
│       ├── User.php               ← User model with reading stats
│       ├── Book.php               ← Book model
│       └── History.php            ← Reading session tracker (CORE)
├── resources/
│   ├── views/                     ← Blade templates
│   │   ├── layouts/               ← app.blade.php, admin.blade.php
│   │   ├── frontend/              ← home.blade.php, reader.blade.php, book.blade.php, history.blade.php
│   │   ├── auth/                  ← login.blade.php
│   │   └── admin/                 ← dashboard.blade.php
│   ├── js/                        ← app.js, bootstrap.js
│   └── css/                       ← app.css (Tailwind)
├── routes/
│   └── web.php                    ← All routes defined here
├── database/
│   └── migrations/                ← Database schema files
├── public/
│   ├── storage/                   ← PDF files & cover images
│   └── js/
│       └── toast.js               ← Toast notification system
├── package.json                   ← Frontend dependencies (Vite, Tailwind)
└── composer.json                  ← Backend dependencies (Laravel, PDF parser)
```

---

## 2. READING/TIMER FUNCTIONALITY - DETAILED

### A. Database Models

#### **1. User Model** (app/Models/User.php)
- **Fields**: name, email, password, role (admin/student), is_active
- **Stats**: total_reading_time, total_books_read (deprecated - calculated from histories)
- **Relationships**: hasMany(History)
- **Purpose**: Authentication + Reading statistics

#### **2. Book Model** (app/Models/Book.php)
- **Fields**: judul, kategori, sinopsis, cover_image, file_path, total_pages, is_active
- **Relationships**: hasMany(History)
- **Purpose**: Book metadata + PDF file reference

#### **3. History Model** (app/Models/History.php) - **CRITICAL FOR TIMER**
- **Fields**:
  - `user_id` (FK) - Which user
  - `book_id` (FK) - Which book
  - `last_page` - Current page being read
  - **`total_time_spent`** (int seconds) - CUMULATIVE TIME ACCUMULATED
  - **`last_ping_at`** (datetime) - ANCHOR FOR DELTA CALCULATION
  - `last_read_at` - When user last viewed book
  - `created_at`, `updated_at`
- **Unique Constraint**: (user_id, book_id) - One record per user per book
- **Purpose**: Tracks reading session + accumulated time

---

### B. Backend Controllers - Timer Logic

#### **Frontend BookController** (app/Http/Controllers/Frontend/BookController.php)

| Method | Purpose | Timer Related |
|--------|---------|---------------|
| `show($id)` | Book detail page | No |
| `read($id)` | Load reader with PDF | Creates History with firstOrCreate |
| `startReading($id)` | Begin reading | Calls firstOrCreate, redirects to read |
| `updateReadingProgress($id)` | Save page number | POST from reader.js |
| **`ping($id)`** | **ACCUMULATE TIME** | **YES - Main timer logic** |

#### **The Ping Endpoint** (POST /buku/{id}/ping)
```php
1. Get or create History record for user + book
2. Calculate: delta = now - last_ping_at (seconds)
3. If delta > 0:
   - Cap to 600 seconds (10 min anti-cheat)
   - Add to total_time_spent: total_time_spent += timeToAdd
4. Update last_ping_at = now (new anchor point)
5. Update last_read_at = now
6. Save to database
7. Return JSON:
   {
     "success": true,
     "total_time_spent": X,
     "delta": Y,
     "is_new": false
   }
```

**Critical Implementation Details**:
- `total_time_spent` is **CUMULATIVE** (always +=, never =)
- `last_ping_at` is the **anchor point** for next ping's delta
- Every delta > 0 is counted (no minimum threshold)
- Max 600s per ping prevents cheating

---

### C. Frontend Timer Implementation

#### **Reader Page** (resources/views/frontend/reader.blade.php)

**Initialization** (Lines 42-46):
```blade
<body
  data-book-id="{{ $book->id }}"
  data-total-seconds="{{ $history->total_time_spent ?? 0 }}"
  data-last-ping-at="{{ $history->last_ping_at ? $history->last_ping_at->getTimestamp() : null }}"
  data-total-pages="{{ $book->total_pages ?? 0 }}"
  data-last-page="{{ $history->last_page ?? 1 }}"
>
```

**Timer Display** (Line 62):
```html
<span id="readingTimer" class="...">00:00:00</span>
```

**JavaScript Variables** (Lines 116-119):
```javascript
serverTotalSeconds = parseInt(document.body.dataset.totalSeconds) || 0
lastPingTimestamp = parseInt(document.body.dataset.lastPingAt) || Math.floor(Date.now() / 1000)
```

**Update Timer** (Every 1 second):
```javascript
updateTimer() {
  nowTimestamp = Math.floor(Date.now() / 1000)
  elapsedSinceLastPing = nowTimestamp - lastPingTimestamp
  currentTotal = serverTotalSeconds + elapsedSinceLastPing  // Display calculation
  document.getElementById('readingTimer').textContent = formatTime(currentTotal)
}
```

**Send Ping** (Every 15 seconds):
```javascript
sendPing() {
  POST /buku/{id}/ping
  
  On success:
  - serverTotalSeconds = response.total_time_spent  (update from server)
  - lastPingTimestamp = Math.floor(Date.now() / 1000)  (reset anchor)
  - Call updateTimer()  (refresh display)
}
```

---

### D. Home Page Reading Stats Display

**HomeController** (app/Http/Controllers/Frontend/HomeController.php):
```php
1. Get continue reading (last 4 reading sessions ordered by last_read_at desc)
2. Get latest books (8 active books ordered by created_at desc)
3. Calculate from database:
   - totalTimeSpent = SUM of all history.total_time_spent for user
   - totalBooksRead = COUNT of distinct book_id for user
4. Format time: "X hari Y jam Z menit"
5. Pass to view: timeFormatted, totalBooksRead, continueReading, latestBooks
```

**Home View** (resources/views/frontend/home.blade.php):
- **Line 14-43**: Stats cards showing total reading time + books read
- **Line 45-62**: Continue Reading section (last 4 books)
- **Line 65-81**: Latest Collections (newest 8 books)

---

### E. History Page

**HistoryController** (app/Http/Controllers/Frontend/HistoryController.php):
```php
Gets:
- All histories for user (with book details, ordered by last_read_at desc)
- Total reading time (sum of total_time_spent)
- Total books read (count distinct book_id)
- Formats time: "X jam Y menit" or "Y menit" if < 1 hour
```

---

## 3. COMPLETE READING WORKFLOW

```
1. LOGIN
   └─ POST /login → LoginController@login
      └─ Authenticates user, redirects based on role (admin → dashboard, student → home)

2. HOME PAGE
   └─ GET /home → HomeController@index
      ├─ Shows reading stats (total time, books read)
      ├─ Shows continue reading (last 4 books)
      └─ Shows new books

3. VIEW BOOK DETAILS
   └─ GET /buku/{id} → FrontendBookController@show
      ├─ Displays book cover, title, synopsis
      ├─ Shows "Mulai Membaca" or "Lanjutkan Membaca"
      └─ Checks if user has existing history

4. START READING
   └─ POST /buku/{id}/start → FrontendBookController@startReading
      ├─ Creates History if doesn't exist (firstOrCreate)
      │  └─ Initializes: last_page=1, total_time_spent=0, last_ping_at=now
      └─ Redirects to /buku/{id}/read

5. READER PAGE (TIMER ACTIVE)
   └─ GET /buku/{id}/read → FrontendBookController@read
      ├─ Returns reader.blade.php wi
