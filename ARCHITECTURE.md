# 🏗️ SIYU - Architecture & System Design

## 📐 System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER (Browser)                   │
├──────────────────────────────────────┬──────────────────────────┤
│    Student Dashboard                 │    Admin Dashboard        │
│  (/student/dashboard)                │  (/admin/dashboard)       │
│                                      │                          │
│ - Upload Dokumen                     │ - Lihat Pengajuan        │
│ - Lihat Status                       │ - Verifikasi Dokumen     │
│ - Submit Pengajuan                   │ - Update Status          │
│ - Monitor Progress                   │ - Beri Feedback          │
└──────────────────────────────────────┴──────────────────────────┘
                            │
                            │ HTTP/AJAX
                            │
┌─────────────────────────────────────────────────────────────────┐
│                     ROUTING LAYER (routes/web.php)              │
├──────────────────────────────────────┬──────────────────────────┤
│  Student Routes                      │  Admin Routes            │
│  ├─ /student/dashboard               │  ├─ /admin/dashboard    │
│  ├─ /student/upload-document         │  ├─ /admin/submission   │
│  └─ /student/submit-application      │  ├─ /admin/doc/status   │
│                                      │  └─ /admin/doc/download │
│  [Auth Middleware]                   │  [Auth + Admin Middleware]
└──────────────────────────────────────┴──────────────────────────┘
                            │
                            │
┌─────────────────────────────────────────────────────────────────┐
│               CONTROLLER LAYER (App/Http/Controllers)           │
├──────────────────────────────────────┬──────────────────────────┤
│  StudentController                   │  AdminController         │
│  ├─ dashboard()                      │  ├─ dashboard()          │
│  ├─ uploadDocument()                 │  ├─ viewSubmission()     │
│  └─ submitApplication()              │  ├─ updateDocStatus()    │
│                                      │  └─ downloadDocument()   │
└──────────────────────────────────────┴──────────────────────────┘
                            │
                            │
┌─────────────────────────────────────────────────────────────────┐
│              MODEL LAYER (App/Models) - Business Logic          │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  User                    Student              Submission        │
│  ├─ id                   ├─ id                ├─ id             │
│  ├─ name                 ├─ user_id (FK)      ├─ student_id (FK)│
│  ├─ email                ├─ nim               ├─ status         │
│  ├─ role (enum)          ├─ nama              ├─ submitted_at   │
│  │  ├─ student           ├─ ipk               └─ getProgress()  │
│  │  └─ admin             ├─ total_sks         │                │
│  └─ password             └─ mata_kuliah       │                │
│                                               │                │
│  Document               DocumentVersion      Activity          │
│  ├─ id                  ├─ id                ├─ id             │
│  ├─ submission_id (FK)  ├─ document_id (FK) ├─ user_id (FK)   │
│  ├─ type                ├─ file_path         ├─ action         │
│  ├─ name                ├─ version_number    ├─ model_type     │
│  ├─ file_path           └─ notes             ├─ model_id       │
│  ├─ status (enum)                           ├─ description    │
│  │  ├─ pending                              └─ data (JSON)    │
│  │  ├─ approved                                               │
│  │  ├─ revision                          YudisiumResult       │
│  │  └─ rejected                          ├─ id               │
│  ├─ feedback                            ├─ student_id (FK)  │
│  └─ metadata (JSON)                     ├─ ipk              │
│                                         ├─ predikat         │
│  Relationships:                         ├─ status_pembimbing│
│  ├─ User → Student (1:1)               ├─ status_penguji   │
│  ├─ Student → Submission (1:Many)      ├─ cumlaude         │
│  ├─ Submission → Document (1:Many)     └─ title_cumlaude   │
│  ├─ Document → DocumentVersion (1:Many)                     │
│  └─ User → Activity (1:Many)                                │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
                            │
                            │ Query/ORM
                            │
┌─────────────────────────────────────────────────────────────────┐
│           DATABASE LAYER (PostgreSQL/MySQL/SQLite)              │
├─────────────────────────────────────────────────────────────────┤
│                                                                 │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────────┐     │
│  │ users        │  │ students     │  │ submissions      │     │
│  ├──────────────┤  ├──────────────┤  ├──────────────────┤     │
│  │ id (PK)      │  │ id (PK)      │  │ id (PK)          │     │
│  │ name         │  │ user_id (FK) │  │ student_id (FK)  │     │
│  │ email        │  │ nim          │  │ status           │     │
│  │ role         │  │ nama         │  │ submitted_at     │     │
│  │ password     │  │ ipk          │  │ progress         │     │
│  │ timestamps   │  │ total_sks    │  │ timestamps       │     │
│  └──────────────┘  └──────────────┘  └──────────────────┘     │
│                                                                 │
│  ┌──────────────────┐  ┌──────────────────┐                   │
│  │ documents        │  │ document_versions│                   │
│  ├──────────────────┤  ├──────────────────┤                   │
│  │ id (PK)          │  │ id (PK)          │                   │
│  │ submission_id    │  │ document_id (FK) │                   │
│  │ type             │  │ file_path        │                   │
│  │ name             │  │ version_number   │                   │
│  │ file_path        │  │ notes            │                   │
│  │ status           │  │ timestamps       │                   │
│  │ feedback         │  └──────────────────┘                   │
│  │ metadata         │                                          │
│  │ timestamps       │  ┌──────────────────┐                   │
│  └──────────────────┘  │ activities       │                   │
│                        ├──────────────────┤                   │
│  ┌──────────────────┐  │ id (PK)          │                   │
│  │ yudisium_results │  │ user_id (FK)     │                   │
│  ├──────────────────┤  │ action           │                   │
│  │ id (PK)          │  │ model_type       │                   │
│  │ student_id (FK)  │  │ model_id         │                   │
│  │ ipk              │  │ description      │                   │
│  │ predikat         │  │ data             │                   │
│  │ status_pembimbing│  │ timestamps       │                   │
│  │ status_penguji   │  └──────────────────┘                   │
│  │ cumlaude         │                                          │
│  │ timestamps       │                                          │
│  └──────────────────┘                                          │
│                                                                 │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🔄 Data Flow Diagram

### Student Upload & Submit Flow
```
┌─────────────────────────────────────────────────────────────────┐
│ STUDENT FLOW                                                    │
└─────────────────────────────────────────────────────────────────┘

1. Login
   └─→ [routes/auth] → Auth::check() → StudentController

2. Access Dashboard
   └─→ GET /student/dashboard
       └─→ StudentController::dashboard()
           ├─→ Get Student from User
           ├─→ Get latest Submission
           ├─→ Get Documents from Submission
           ├─→ Calculate Progress
           └─→ return view('student.dashboard', [...])

3. Upload Document
   └─→ POST /student/upload-document
       └─→ StudentController::uploadDocument()
           ├─→ Validate file & document type
           ├─→ Store file to storage/private
           ├─→ Check if document type exists
           ├─→ Create or Update Document
           ├─→ Activity::log('upload', ...)
           └─→ redirect()->with('success')

4. Submit Application
   └─→ POST /student/submit-application
       └─→ StudentController::submitApplication()
           ├─→ Get latest Submission
           ├─→ Update status to 'submitted'
           ├─→ Set submitted_at timestamp
           ├─→ Activity::log('submit', ...)
           └─→ redirect()->with('success')

5. Monitor Status
   └─→ Refresh /student/dashboard
       └─→ See updated progress & document statuses
```

### Admin Verify & Approve Flow
```
┌─────────────────────────────────────────────────────────────────┐
│ ADMIN FLOW                                                      │
└─────────────────────────────────────────────────────────────────┘

1. Login (role='admin')
   └─→ [routes/auth] → Auth::check() + IsAdmin middleware

2. Access Admin Dashboard
   └─→ GET /admin/dashboard
       └─→ AdminController::dashboard()
           ├─→ Get all Submissions with pagination
           ├─→ Calculate stats (total, approved, etc)
           ├─→ return view('admin.dashboard', [...])

3. View Submission Detail
   └─→ GET /admin/submission/{id}
       └─→ AdminController::viewSubmission()
           ├─→ Load Submission with Student & Documents
           ├─→ Calculate progress percentage
           └─→ return view('admin.submission-detail', [...])

4. Update Document Status
   └─→ PATCH /admin/document/{id}/status
       └─→ AdminController::updateDocumentStatus()
           ├─→ Validate status & feedback
           ├─→ Update Document (status, feedback)
           ├─→ Activity::log('verify', ...)
           ├─→ Check if all docs approved
           │   ├─→ If YES: Update Submission to 'approved'
           │   │   └─→ Activity::log('approve', ...)
           │   └─→ If NO: Update to 'under_review'
           └─→ redirect()->back()->with('success')

5. Download Document
   └─→ GET /admin/document/{id}/download
       └─→ AdminController::downloadDocument()
           ├─→ Check auth & permissions
           ├─→ Return file from private storage
           └─→ Activity::log('download', ...)
```

---

## 🔐 Security Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                    SECURITY LAYERS                              │
└─────────────────────────────────────────────────────────────────┘

Layer 1: AUTHENTICATION (Laravel Breeze)
├─→ Login form validation
├─→ Email + Password verification
├─→ Session/Token management
└─→ Logout functionality

Layer 2: AUTHORIZATION (Middleware)
├─→ IsAuthenticated [auth middleware]
│   └─→ Checks: Auth::check() === true
│
├─→ IsAdmin [custom admin middleware]
│   └─→ Checks: Auth::user()->role === 'admin'
│
└─→ IsStudent [implicit - no explicit middleware]
    └─→ Checks: role === 'student' in controller logic

Layer 3: RESOURCE-LEVEL ACCESS
├─→ Student can only access own data
│   └─→ StudentController checks User ID matches
│
├─→ Admin can access all data
│   └─→ No resource-level restriction
│
└─→ File access controlled
    └─→ Files stored in storage/private
    └─→ Download requires auth + permission check

Layer 4: DATA PROTECTION
├─→ Password hashing (bcrypt)
├─→ SQL Injection prevention (Eloquent ORM)
├─→ CSRF protection (CSRF tokens in forms)
├─→ XSS prevention (Blade escaping)
└─→ Activity logging for audit trail
```

---

## 🎯 Key Design Patterns

### 1. **Model-View-Controller (MVC)**
```
User Request
    ↓
Route Handler
    ↓
Controller (Business Logic)
    ↓
Model (Data Access)
    ↓
Database
    ↓
Response (View)
```

### 2. **Repository Pattern (via Eloquent Models)**
```
Controller
    ↓
Model.find(), Model.where(), Model.create()
    ↓
Database Query Builder
    ↓
Database
```

### 3. **Activity Logging Pattern**
```
Every Action
    ↓
Activity::log($action, $description, $model_type, $model_id)
    ↓
Insert to activities table
    ↓
Audit Trail Available
```

### 4. **Soft Relationships**
```
Student ──1──→ User
  ↓
  └──Many──→ Submissions
              ↓
              └──Many──→ Documents
                         ↓
                         └──Many──→ DocumentVersions
```

---

## 📊 Database Relationships Diagram

```
    ┌──────────────┐
    │    users     │
    ├──────────────┤
    │ id (PK)      │
    │ role         │
    │ email        │
    └──────────────┘
           ▲
           │ 1:1
           │
    ┌──────────────┐          ┌──────────────┐
    │  students    │──1:Many──→│ submissions  │
    ├──────────────┤          ├──────────────┤
    │ id (PK)      │          │ id (PK)      │
    │ user_id (FK) │          │ status (enum)│
    │ nim          │          │ submitted_at │
    │ ipk          │          └──────────────┘
    └──────────────┘                 │
           │                        │ 1:Many
           │ 1:Many                 │
           │                        ▼
           │                  ┌──────────────┐
           │                  │  documents   │
           │                  ├──────────────┤
           │                  │ id (PK)      │
           │                  │ status (enum)│
           │                  │ feedback     │
           │                  └──────────────┘
           │                        │
           │                        │ 1:Many
           │                        │
           │                        ▼
           │                  ┌──────────────────┐
           │                  │ doc_versions     │
           │                  ├──────────────────┤
           │                  │ id (PK)          │
           │                  │ version_number   │
           │                  └──────────────────┘
           │
           │ 1:Many
           │
           ▼
    ┌──────────────────┐
    │  activities      │
    ├──────────────────┤
    │ id (PK)          │
    │ action           │
    │ description      │
    │ timestamps       │
    └──────────────────┘

    Also:
    Student ──1:Many──→ YudisiumResults
```

---

## 🎨 UI/UX Component Hierarchy

```
┌─────────────────────────────────────────────────────┐
│         app.blade.php (Main Layout)                 │
├─────────────────────────────────────────────────────┤
│                                                     │
│ ┌─────────────────────────────────────────────────┐│
│ │ navigation.blade.php (Header/Nav)               ││
│ │ ├─→ Logo                                        ││
│ │ ├─→ Nav Links (conditional by role)             ││
│ │ └─→ User Dropdown                               ││
│ └─────────────────────────────────────────────────┘│
│                                                     │
│ ┌─────────────────────────────────────────────────┐│
│ │ @yield('content') - Page Content               ││
│ │                                                 ││
│ │ Student Dashboard:                              ││
│ │ ├─→ Status Card                                ││
│ │ ├─→ Progress Card                              ││
│ │ ├─→ Info Card                                  ││
│ │ ├─→ Upload Form                                ││
│ │ └─→ Documents Table                            ││
│ │                                                 ││
│ │ Admin Dashboard:                                ││
│ │ ├─→ Stats Cards (5)                            ││
│ │ └─→ Submissions Table                          ││
│ │                                                 ││
│ │ Submission Detail:                              ││
│ │ ├─→ Header                                     ││
│ │ ├─→ Status Cards                               ││
│ │ ├─→ Student Info                               ││
│ │ └─→ Documents List (with forms)                ││
│ └─────────────────────────────────────────────────┘│
│                                                     │
└─────────────────────────────────────────────────────┘
```

---

## 🚀 Performance Considerations

```
Optimization Strategies:

1. Database Queries
   ├─→ Eager loading (with, load)
   ├─→ Pagination (limit queries)
   ├─→ Proper indexing (FK columns)
   └─→ Query optimization

2. Caching
   ├─→ Config cache
   ├─→ Route cache
   └─→ View cache

3. File Handling
   ├─→ Private storage for uploads
   ├─→ Proper file cleanup
   └─→ Stream downloads for large files

4. Frontend
   ├─→ Tailwind CSS (minimal bundle)
   ├─→ No unnecessary JavaScript
   └─→ Responsive images
```

---

## 📈 Scalability Path

```
Current State (Monolithic)
    ↓
Future State (Microservices-ready)
    
├─ SOFI API Service
│  └─→ Fetch academic data
│
├─ OCR Service
│  └─→ Document processing
│
├─ Email Service
│  └─→ Notifications
│
├─ Report Service
│  └─→ PDF generation
│
└─ Analytics Service
   └─→ Dashboard metrics

All connected via API/Message Queue
```

---

**Architecture Version**: 1.0  
**Last Updated**: 27 Desember 2025
