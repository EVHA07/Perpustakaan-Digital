# 🧪 TEST PLAN - TIMER RESET BUG FIX

## Quick Test (5 menit)

### Test 1: Basic Timer Increment
**Durasi:** 2 menit

1. Buka aplikasi, login sebagai student
2. Masuk ke menu Buku, pilih satu buku
3. Klik "Baca" untuk membuka reader
4. Tunggu 30 detik tanpa melakukan apapun
5. Verifikasi timer di atas terus increment: 00:00:30 → 00:00:31 → dst ✓

**Expected Result:** Timer increment normal, tidak ada jeda

---

### Test 2: Reload Saat Timer Berjalan
**Durasi:** 2 menit

1. Dari test 1, biarkan timer terus berjalan
2. Di detik ke-10, tekan F5 (refresh page)
3. Tunggu page load selesai
4. Verifikasi timer menunjukkan ~10 detik, BUKAN reset ke 00:00:00 ✓
5. Tunggu 5 detik lagi, timer harus show ~15 detik ✓

**Expected Result:** 
- Refresh tidak reset timer
- Timer continue dari nilai sebelum refresh
- No time loss

**Failure Indicator:**
- Timer show 00:00:00 setelah refresh ❌
- Timer show 00:00:01 setelah refresh ❌

---

### Test 3: Home Page Total Waktu
**Durasi:** 1 menit

1. Dari test 2, catat timer saat ini, misal 15 detik
2. Tutup reader (klik X atau tekan ESC)
3. Redirect ke Home page
4. Lihat bagian "Total Waktu Membaca" 
5. Verifikasi menunjukkan setidaknya waktu yang sudah diakumulasi ✓

**Expected Result:**
- Home page menampilkan total waktu membaca > 0
- Format: "X menit Y detik" atau "X detik"

---

## Deep Test (15 menit)

### Test 4: Long Reading Session
**Durasi:** 5 menit

1. Buka reader buku
2. Biarkan berjalan selama 3 menit tanpa refresh/close
3. Console log setiap ping (setiap 15 detik):
   ```
   ✓ Ping sent! Delta: 15s, Total: 45s
   ✓ Ping sent! Delta: 15s, Total: 60s
   ✓ Ping sent! Delta: 15s, Total: 75s
   ...
   ```
4. Setelah 3 menit, verifikasi console log menunjukkan increment 15 detik setiap kali ✓
5. Total harus = 180 detik (3 menit) ✓

**Testing Console Log:**
- Buka DevTools (F12)
- Masuk ke Console tab
- Lihat output setiap ping

**Expected Pattern:**
```
✓ Ping sent! Delta: 15s, Total: 15s
✓ Ping sent! Delta: 15s, Total: 30s
✓ Ping sent! Delta: 15s, Total: 45s
✓ Ping sent! Delta: 15s, Total: 60s
...
```

**Failure Indicator:**
- Delta = 0 multiple times ❌
- Delta > 60s (kecuali ada jeda parah) ❌
- Total tidak bertambah atau lonjak ke belakang ❌

---

### Test 5: Multiple Refresh
**Durasi:** 5 menit

1. Buka reader, tunggu 10 detik
2. F5 refresh → tunggu 10 detik lagi → timer harus ~20 detik ✓
3. F5 refresh → tunggu 10 detik lagi → timer harus ~30 detik ✓
4. F5 refresh → tunggu 10 detik lagi → timer harus ~40 detik ✓
5. Verifikasi setiap refresh tidak menghilangkan waktu sebelumnya ✓

**Expected Result:**
- Setiap refresh, timer continue dari value sebelumnya
- Total time terus akumulasi: 10s → 20s → 30s → 40s ✓
- Zero time loss

**Failure Indicator:**
- Setelah refresh, timer menunjukkan angka yang sama atau lebih kecil ❌
- Timer menunjukkan reset ke angka rendah ❌

---

### Test 6: Network Throttling
**Durasi:** 5 menit

**Setup:**
1. Buka DevTools (F12)
2. Network tab → Throttling dropdown → pilih "Slow 3G"

**Test:**
1. Buka reader dengan throttling aktif
2. Tunggu 20 detik
3. Lihat di Network tab, delay ping akan lebih lama
4. Verifikasi timer masih increment normal ✓
5. Verifikasi console log still shows correct delta ✓

**Expected Result:**
- Timer increment normal meskipun network slow
- Ping mungkin tertunda, tapi data tidak hilang
- Saat ping finally dikirim, delta hitung benar

**Failure Indicator:**
- Timer freeze/stuck saat network lag ❌
- Timer loss saat network recovery ❌

---

### Test 7: Close Tab & Reopen
**Durasi:** 3 menit

1. Buka reader, tunggu 10 detik
2. CLOSE tab sepenuhnya (jangan minimize)
3. Tunggu 2 detik
4. Buka lagi /buku/{id}/read di tab baru
5. Verifikasi timer show ~10 detik (atau lebih), BUKAN reset ✓

**Expected Result:**
- Timer continue dari value sebelum tab ditutup
- Data tersimpan di database, diambil saat tab baru dibuka

**Failure Indicator:**
- Timer reset ke 00:00:00 ❌
- Timer show value acak ❌

---

## Production Test (30 menit)

### Test 8: Multiple Users Concurrent
**Durasi:** 10 menit

**Setup:**
- Persiapkan 2-3 device/browser/user

**Test:**
1. User A buka buku 1, baca 5 menit
2. User B buka buku 2, baca 3 menit  
3. User C buka buku 1 (sama seperti A), baca 2 menit
4. Verifikasi setiap user punya history terpisah ✓
5. Verifikasi total waktu each user accumulate correctly ✓

**Expected Result:**
- User A: 5 menit
- User B: 3 menit
- User C: 2 menit
- Tidak ada crosstalk atau mixing data

---

### Test 9: Data Persistence
**Durasi:** 10 menit

1. User A baca selama 5 menit
2. Go to Home → lihat "Total Waktu Membaca" = 5 menit ✓
3. Go to History → lihat buku dengan last_page & waktu ✓
4. Baca lagi selama 3 menit
5. Go to Home → "Total Waktu Membaca" = 8 menit ✓
6. Buka database → query SELECT total_time_spent FROM histories WHERE user_id = A
   - Seharusnya = 480 (8 menit dalam detik) ✓

**SQL Check:**
```sql
SELECT user_id, book_id, total_time_spent, last_page, last_read_at, last_ping_at
FROM histories 
WHERE user_id = 1
ORDER BY last_read_at DESC;
```

**Expected Result:**
- Total time accumulate correctly di database
- No duplicate records
- No reset to 0

---

## Edge Cases

### Test 10: Ping Interval Variance
**Scenario:** Simulasi ping yang tidak exact setiap 15 detik

**Test:**
- Gunakan throttling + slow network untuk simulate jeda > 15 detik
- Verifikasi backend still calculate delta correctly
- Verifikasi tidak ada double-counting

---

### Test 11: Idle Tab > 10 menit
**Scenario:** User open reader, leave for 15 menit, then comeback

**Expected Behavior:**
- First ping after 15 min idle → delta = 600 (capped at 10 min)
- Total += 600 (not 900) ← anti-cheating protection

---

## Regression Test

### Test 12: Existing Features Still Work
- [ ] Halaman navigation (prev/next page) masih berfungsi
- [ ] Save last_page masih akurat
- [ ] Theme toggle masih berfungsi
- [ ] Keyboard shortcut (ESC, arrow keys) masih berfungsi
- [ ] PDF rendering masih correct

---

## Success Criteria

✅ All tests pass = Timer reset bug FIXED
✅ No time loss in any scenario
✅ Data consistent in database & UI
✅ No regression in other features

---

## Failure Escalation

If any test FAILS:
1. Check browser console for errors
2. Check server logs: `storage/logs/laravel.log`
3. Check database: verify last_ping_at dan total_time_spent values
4. Compare with VISUAL_EXPLANATION.txt untuk debug timeline

---
