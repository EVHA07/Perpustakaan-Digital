# Perpustakaan Digital - Quick Reference

## Project Type
**Laravel 12 Digital Library** with automatic reading time tracking via PDF reader

## Key Statistics
- **Backend**: Laravel 12 (PHP)
- **Frontend**: Blade + Vanilla JavaScript
- **Database**: SQLite (with Users, Books, Histories tables)
- **PDF Rendering**: PDF.js library
- **Styling**: Tailwind CSS

---

## Where Is What?

### Timer/Reading Logic
- **Main Timer File**: `resources/views/frontend/reader.blade.php` (lines 116-168)
- **Backend Timer Logic**: `app/Http/Controllers/Frontend/BookController.php::ping()` (lines 97-151)
- **Data Storage**: `app/Models/History.php` (total_time_spent, last_ping_at fields)

### Home Page Display
- **Controller**: `app/Http/Controllers/Frontend/HomeController.php::index()`
- **View**: `resources/views/frontend/home.blade.php` (lines 14-43)

### Reading Stats Display
- **Controller**: `app/Http/Controllers/Frontend/HistoryController.php::index()`
- **View**: `resources/views/frontend/history.blade.php`

### Routes
- **All Routes**: `routes/web.php`
- **Main Timer Endpoint**: `POST /buku/{id}/ping`

---

## How Timer Works (Simplified)

1. **User opens reader** → `GET /buku/{id}/read`
2. **Frontend gets data** from server:
   - `total_time_spent` (accumulated seconds)
   - `last_ping_at` (last anchor timestamp)
3. **Frontend displays**: `total_time_spent + (current_time - last_ping_at)`
4. **Every 15 seconds**, frontend sends `POST /buku/{id}/ping`
5. **Backend calculates**: `delta = now - last_ping_at`
6. **Backend updates**: `total_time_spent += delta` (capped at 600s)
7. **Backend updates**: `last_ping_at = now` (new anchor)
8. **Result**: Time persists even after page refresh

---

## Database Tables

```
HISTORIES (Core Timer Table):
- id (PK)
- user_id (FK)
- book_id (FK)
- last_page (current page)
- total_time_spent (cumulative seconds) ⭐
- last_ping_at (timestamp anchor) ⭐
- created_at, updated_at
```

---

## Main Routes

| Route | Method | Purpose |
|-------|--------|---------|
| `/home` | GET | Home dashboard with stats |
| `/buku/{id}` | GET | Book detail page |
| `/buku/{id}/read` | GET | PDF reader with timer |
| `/buku/{id}/ping` | POST | ⭐ Accumulate reading time |
| `/buku/{id}/progress` | POST | Save current page |
| `/history` | GET | Reading history view |

---

## Key Controllers

1. **HomeController** - Calculates and displays reading stats
2. **BookController** - Main timer logic in `ping()` method
3. **HistoryController** - Shows reading history

---

## Testing Timer

Quick tests:
1. Open reader → wait 30s → verify timer ~30s ✓
2. Open reader → wait 10s → refresh (F5) → should still show ~10s ✓
3. Read 10s → refresh → read 5s → total should be ~15s ✓

---

## Recent Fixes

All timer bugs have been fixed:
- ✅ Timer doesn't reset on page refresh
- ✅ Time doesn't get lost on page load
- ✅ Small deltas are properly counted
- See: `CODEBASE_ANALYSIS.md` for details

---

## Files to Review

**Must Read** (for timer understanding):
1. `resources/views/frontend/reader.blade.php` - Main timer implementation
2. `app/Http/Controllers/Frontend/BookController.php` - Backend logic
3. `app/Models/History.php` - Data model

**Reference**:
- `CODEBASE_ANALYSIS.md` - Comprehensive technical breakdown
- `CODEBASE_SUMMARY.txt` - Visual overview

---

## Common Questions

**Q: Where does the timer display?**
A: In the header of `reader.blade.php`, element ID `#readingTimer`

**Q: How often is time saved?**
A: Every 15 seconds via POST `/buku/{id}/ping`

**Q: What if user closes browser without exiting?**
A: Time is still saved (last ping before close is persisted)

**Q: Can users cheat the timer?**
A: No - server-authoritative, capped at 600s per session

**Q: Does timer survive page refresh?**
A: Yes - stored in database with `last_ping_at` anchor point

---

Generated: 2026-01-09
Status: ✅ Exploration Complete
