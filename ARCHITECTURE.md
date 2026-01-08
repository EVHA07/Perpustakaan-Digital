# Arsitektur Aplikasi Perpustakaan Digital - Laravel

## Struktur Folder Laravel

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Admin/
│   │   │   ├── UserController.php
│   │   │   └── BookController.php
│   │   ├── Frontend/
│   │   │   ├── HomeController.php
│   │   │   ├── SearchController.php
│   │   │   ├── HistoryController.php
│   │   │   └── BookController.php
│   │   └── Auth/
│   │       └── LoginController.php
│   ├── Middleware/
│   │   ├── Authenticate.php
│   │   ├── IsStudent.php
│   │   └── IsAdmin.php
│   └── Requests/
├── Models/
│   ├── User.php
│   ├── Book.php
│   └── History.php
database/
├── migrations/
│   ├── 2024_01_01_000001_create_users_table.php
│   ├── 2024_01_01_000002_create_books_table.php
│   └── 2024_01_01_000003_create_histories_table.php
resources/
├── views/
│   ├── layouts/
│   │   └── app.blade.php
│   ├── auth/
│   │   └── login.blade.php
│   ├── frontend/
│   │   ├── home.blade.php
│   │   ├── search.blade.php
│   │   ├── history.blade.php
│   │   └── book.blade.php
│   └── components/
routes/
├── web.php
└── admin.php
```

## Database Migrations
