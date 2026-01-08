# Troubleshooting - Masalah Upload Buku

## ✅ Perbaikan yang Dilakukan

### 1. Error Handling yang Lebih Baik
- Menambahkan try-catch di BookController
- Menampilkan pesan error yang jelas
- Menyimpan old input saat error

### 2. Validation Error Messages
- Pesan error dalam Bahasa Indonesia
- Menampilkan error di atas form
- Border merah pada field yang error
- Pesan error di bawah setiap field

### 3. Storage Directories
- Direktori sudah dibuat: `storage/app/public/books/covers` dan `files`
- Permission sudah diset

## 🔍 Kemungkinan Masalah

### 1. File Terlalu Besar
**Cover Image**: Maksimal 2MB
**File Buku**: Maksimal 10MB

**Solusi**:
- Kompres gambar cover sebelum upload
- Kompres file PDF/EPUB jika terlalu besar
- Check `php.ini`:
  ```ini
  upload_max_filesize = 20M
  post_max_size = 20M
  ```

### 2. Format File Salah
**Cover**: Harus JPEG, PNG, atau JPG
**Buku**: Harus PDF atau EPUB

**Solusi**:
- Pastikan ekstensi file sesuai (huruf besar/kecil)
- Gunakan format yang didukung

### 3. PHP Configuration
Check `php.ini`:

```ini
; File Upload
file_uploads = On
upload_max_filesize = 20M
post_max_size = 20M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M
```

### 4. Server Error (500)
Jika halaman blank atau error 500:

**Cek Laravel Log**:
```bash
tail -f storage/logs/laravel.log
```

**Debug Mode**:
Di `.env`:
```env
APP_DEBUG=true
APP_ENV=local
```

## 📋 Checklist Debugging

1. ✅ Apakah semua field diisi?
2. ✅ Apakah format file sesuai?
3. ✅ Apakah ukuran file sesuai limit?
4. ✅ Apakah storage directories ada permission?
5. ✅ Apakah storage link sudah dibuat?

## 🛠️ Commands untuk Debug

```bash
# 1. Check storage permissions
ls -la storage/app/public/books/

# 2. Create storage link jika belum
php artisan storage:link

# 3. Check Laravel log
tail -n 50 storage/logs/laravel.log

# 4. Check PHP configuration
php -i | grep upload
php -i | grep post

# 5. Clear config cache
php artisan config:clear
php artisan cache:clear

# 6. Clear storage link
rm public/storage
php artisan storage:link
```

## 📝 Form Requirements

### Field yang Wajib:
1. ✅ **Judul** - Teks, max 255 karakter
2. ✅ **Kategori** - Teks, max 100 karakter
3. ✅ **Sinopsis** - Teks
4. ✅ **Cover Image** - Gambar, max 2MB
5. ✅ **File Buku** - PDF/EPUB, max 10MB

### Field Opsional:
1. ⭕ **Total Halaman** - Angka, min 0
2. ⭕ **Aktif** - Checkbox (default checked)

## 🎨 Validasi Error Display

Sekarang form akan menampilkan error jika:

### Validasi Gagal:
- Border field berwarna merah
- Pesan error di bawah field
- Pesan error detail di atas form

### Format Error:
```
┌────────────────────────────────────────────┐
│ ✕ Terjadi kesalahan:                   │
│ • Judul harus diisi                      │
│ • Cover harus berformat JPEG, PNG, atau JPG│
│ • Ukuran file buku maksimal 10MB         │
└────────────────────────────────────────────┘
```

## 🧪 Testing

### Test Case 1: Valid Input
- Judul: "Buku Test"
- Kategori: "Fiksi"
- Sinopsis: "Sinopsis test..."
- Cover: Image kecil (< 2MB)
- File: PDF kecil (< 10MB)
- **Result**: ✓ Buku berhasil ditambahkan

### Test Case 2: File Terlalu Besar
- Cover: Image > 2MB
- **Result**: ✕ "Ukuran cover maksimal 2MB"

### Test Case 3: Format Salah
- File: .docx bukan PDF/EPUB
- **Result**: ✕ "File buku harus berformat PDF atau EPUB"

### Test Case 4: Field Kosong
- Tanpa judul
- **Result**: ✕ "Judul harus diisi"

## 💡 Tips untuk Upload File

### Kompresi Cover Image:
1. Gunakan format JPEG (lebih kecil dari PNG)
2. Resize ke max 800x1200px
3. Gunakan kualitas 80-90%

### Kompresi File Buku:
1. Gunakan PDF optimizer (pdfsize)
2. Hapus font tidak terpakai
3. Kompres gambar dalam PDF

## 🚨 Jika Masih Error

### Step 1: Cek Laravel Log
```bash
tail -f storage/logs/laravel.log
```

### Step 2: Enable Debug Mode
```env
# .env
APP_DEBUG=true
```

### Step 3: Cek Error Message
Refresh form dan lihat error yang muncul

### Step 4: Cek Browser Console
F12 → Console tab untuk JavaScript errors

## 📞 Support

Jika masih mengalami masalah:
1. Screenshoot error message
2. Cek Laravel log untuk detail
3. Pastikan semua requirements terpenuhi
4. Cek php.ini configuration

---

## ✅ Next Steps

1. Coba upload file kecil untuk testing
2. Periksa error message yang muncul
3. Sesuaikan dengan format yang benar
4. Cek Laravel log untuk detail error

Form sekarang akan menampilkan pesan error yang jelas jika ada masalah!
