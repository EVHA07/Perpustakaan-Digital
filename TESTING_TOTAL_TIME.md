# 🧪 TESTING: Total Waktu Membaca di Home Page

## Checklist: Total Waktu Tidak Muncul (0 detik)

Jika Home page menampilkan "0 detik" padahal sudah membaca, debug dengan:

### Step 1: Cek User yang Login
```
Jika role = "admin" → TIDAK akan ada total waktu (admin tidak punya reading history)
Jika role = "student" → Seharusnya menampilkan total waktu
```

**Solution:** Login sebagai **student**, bukan **admin**

### Step 2: Cek Database
User yang login tidak punya history:

```bash
php artisan tinker

# Check current user
$user = \App\Models\User::where('email', 'your-email@example.com')->first();
$histories = \App\Models\History::where('user_id', $user->id)->get();
$total = \App\Models\History::where('user_id', $user->id)->sum('total_time_spent');

echo "User: " . $user->name . "\n";
echo "Histories: " . count($histories) . "\n";
echo "Total Time: " . $total . "s\n";
```

Jika output: `Histories: 0, Total Time: 0s` → User belum membaca apapun

**Solution:** Baca buku dulu sampai ping terkirim (15 detik)

### Step 3: Verifikasi Ping Terkirim
1. Buka reader buku
2. Buka DevTools (F12) → Console
3. Tunggu sampai muncul: `✓ Ping sent! Delta: XXs, Total: XXs`
4. Refresh Home page, total waktu seharusnya > 0

---

## Full Test Scenario

### Scenario A: User Baru Membaca Buku Pertama Kali

**Setup:**
```bash
# Pastikan user sudah login sebagai student
# User belum pernah membaca apapun (history kosong)
```

**Steps:**
1. Login sebagai student
2. Go to Home → "Total Waktu Membaca" = "0 detik" (wajar, belum baca)
3. Buka satu buku
4. Klik "Baca" (reader terbuka)
5. Tunggu 15 detik
6. Cek DevTools Console → harus muncul `✓ Ping sent!`
7. Go to Home page
8. "Total Waktu Membaca" seharusnya menampilkan waktu (minimal 15 detik)

**Expected Result:**
- Jika delta dihitung benar: "15 detik" (atau lebih jika lebih dari 15s)
- Jika ada delay: "X menit Y detik"
- BUKAN "0 detik" ❌

### Scenario B: User Sudah Membaca Sebelumnya

**Setup:**
```bash
php artisan tinker

# Add history manually for testing
$user = \App\Models\User::find(3); // Evan
$book = \App\Models\Book::first();

\App\Models\History::create([
    'user_id' => $user->id,
    'book_id' => $book->id,
    'last_page' => 50,
    'total_time_spent' => 600, // 10 minutes
    'last_read_at' => now(),
    'last_ping_at' => now(),
]);
```

**Steps:**
1. Login sebagai Evan
2. Go to Home → "Total Waktu Membaca" seharusnya "10 menit 0 detik" ✓
3. Buka buku yang sama
4. Tunggu 15 detik
5. Go to Home → "Total Waktu Membaca" seharusnya "10 menit 15 detik" ✓

**Expected Result:**
- Home page menampilkan total dari semua histories
- Format: "X menit Y detik" atau "X hari Y jam Z menit"

---

## Debugging Checklist

| Check | Command | Expected |
|-------|---------|----------|
| User role | `SELECT role FROM users WHERE id = 1;` | "student" (tidak "admin") |
| Histories exist | `SELECT COUNT(*) FROM histories WHERE user_id = 1;` | > 0 |
| Total time | `SELECT SUM(total_time_spent) FROM histories WHERE user_id = 1;` | > 0 |
| Ping sent | DevTools Console | `✓ Ping sent!` |
| Home response | Network tab | status 200 |
| Blade variable | View source | `$timeFormatted` tidak kosong |

---

## If Still "0 detik"

### Check #1: User Role
```bash
php artisan tinker
$user = Auth::user();
echo $user->role; // Should be "student"
```

### Check #2: History Records
```bash
php artisan tinker
$histories = History::where('user_id', Auth::id())->get();
dd($histories); // Should not be empty
```

### Check #3: Ping Error
```
DevTools → Console
Lihat apakah ada error: "Error sending ping"
Jika ada → check network request
```

### Check #4: HomeController
```bash
# Add debugging to HomeController
public function index() {
    $totalTimeSpent = History::where('user_id', Auth::id())->sum('total_time_spent');
    \Log::info('Home page', ['user_id' => Auth::id(), 'total' => $totalTimeSpent]);
    ...
}
```

Check `storage/logs/laravel.log` untuk melihat `$totalTimeSpent` value.

---

## Solution If Still 0

### Solution 1: Reload Browser
```
Ctrl+Shift+R (hard refresh)
```
Cache mungkin belum clear.

### Solution 2: Manually Add Test Data
```bash
php artisan tinker

$user = Auth::user();
\App\Models\History::create([
    'user_id' => $user->id,
    'book_id' => 1,
    'last_page' => 50,
    'total_time_spent' => 600, // 10 minutes
    'last_read_at' => now(),
    'last_ping_at' => now(),
]);
```

Refresh Home page → harus muncul "10 menit 0 detik"

### Solution 3: Check Database Connection
```bash
php artisan tinker

$total = \App\Models\History::sum('total_time_spent');
echo $total; // Should work without error
```

---

## Expected After Full Fix

✅ User login sebagai student
✅ Buka buku, tunggu 15 detik
✅ Home page menampilkan total waktu (bukan "0 detik")
✅ Baca lagi, total waktu terakumulasi
✅ Close dan reopen reader, timer tidak reset
✅ Refresh Home page, total waktu tetap (dari database)

