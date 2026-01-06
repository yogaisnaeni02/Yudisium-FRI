# QUICK START GUIDE - SIYU

## 🚀 Mulai Cepat dalam 5 Langkah

### Langkah 1: Setup Database
```bash
cd SIYU
php artisan migrate:fresh --seed
```

### Langkah 2: Jalankan Server
```bash
php artisan serve
```
Buka browser: `http://localhost:8000`

### Langkah 3: Login sebagai Admin
- **Email**: admin@yudisium.com
- **Password**: password
- Akses: http://localhost:8000/admin/dashboard

### Langkah 4: Login sebagai Mahasiswa
- **Email**: student@yudisium.com
- **Password**: password
- Akses: http://localhost:8000/student/dashboard

### Langkah 5: Test Workflow
**Sebagai Mahasiswa**:
1. Upload dokumen di form yang tersedia
2. Klik "Kirim Pengajuan Yudisium"

**Sebagai Admin**:
1. Lihat daftar pengajuan di dashboard
2. Klik "Lihat Detail" pengajuan mahasiswa
3. Update status dokumen (Approved/Revision/Rejected)
4. Tambah feedback jika diperlukan

---

## 📱 Main Screens

### Dashboard Mahasiswa (`/student/dashboard`)
- Status Pengajuan
- Progress Dokumen (%)
- Informasi Mahasiswa
- Form Upload Dokumen
- Tabel Status Dokumen dengan Feedback

### Dashboard Admin (`/admin/dashboard`)
- Statistik Pengajuan (5 metrics)
- Daftar Pengajuan dengan pagination
- Progress bar per pengajuan
- Action "Lihat Detail"

### Detail Pengajuan Admin (`/admin/submission/{id}`)
- Status & Progress
- Info Mahasiswa
- Daftar Dokumen
- Form Update Status Dokumen
- Feedback management

---

## 🎯 Features Overview

| Fitur | Mahasiswa | Admin |
|-------|-----------|-------|
| Lihat Dashboard | ✅ | ✅ |
| Upload Dokumen | ✅ | ❌ |
| Submit Pengajuan | ✅ | ❌ |
| Lihat Status Dokumen | ✅ | ✅ |
| Terima Feedback | ✅ | ❌ |
| Verifikasi Dokumen | ❌ | ✅ |
| Update Status | ❌ | ✅ |
| Beri Feedback | ❌ | ✅ |
| Download Dokumen | ❌ | ✅ |

---

## 📋 Test Data

**Mahasiswa 1 - Dengan Pengajuan**
```
Email: student@yudisium.com
Password: password
NIM: 123456789
Status: Under Review
Dokumen: 8 dokumen dengan berbagai status
```

**Mahasiswa 2 - Draft**
```
Email: student2@yudisium.com
Password: password
NIM: 987654321
Status: Draft
Dokumen: Belum ada
```

**Admin**
```
Email: admin@yudisium.com
Password: password
Role: Admin
```

---

## 🎨 Design Notes

- **Color Scheme**: Blue primary, Green success, Red error, Yellow warning
- **Framework**: Tailwind CSS
- **Layout**: Responsive grid system
- **Icons**: SVG embedded

---

## 🔍 Troubleshooting

### Error: Table not found
```bash
php artisan migrate:fresh --seed
```

### Error: Unauthorized
Pastikan Anda login dengan role yang tepat (admin/student)

### File tidak ter-upload
- Check storage permissions
- Pastikan file size < 5MB
- Format: PDF, DOC, DOCX

---

## 📂 Key Files

```
routes/web.php                          - Route definitions
app/Http/Controllers/
  ├─ StudentController.php              - Student logic
  └─ AdminController.php                - Admin logic
app/Models/
  ├─ Student.php
  ├─ Submission.php
  ├─ Document.php
  └─ Activity.php
resources/views/
  ├─ student/dashboard.blade.php
  ├─ admin/dashboard.blade.php
  └─ admin/submission-detail.blade.php
database/
  ├─ migrations/                        - Schema
  └─ seeders/DatabaseSeeder.php         - Test data
```

---

## 💡 Pro Tips

1. **Clear Cache** jika ada perubahan:
   ```bash
   php artisan cache:clear
   php artisan config:cache
   ```

2. **Check Activity Log** untuk debugging:
   ```bash
   SELECT * FROM activities ORDER BY created_at DESC LIMIT 10;
   ```

3. **Test Upload** dengan file sample PDF

4. **Monitor Progress** real-time dengan refresh halaman

---

**Selamat! Sistem SIYU siap digunakan! 🎉**
