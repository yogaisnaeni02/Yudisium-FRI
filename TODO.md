# TODO List for Yudisium Information System

## Database Setup
- [x] Create migrations for users, students, documents, submissions, yudisium_results, activities, document_versions
- [x] Edit migrations with appropriate fields
- [x] Create models with relationships and fillable fields
- [x] Run migrations to create database tables

## Authentication & Authorization
- [ ] Set up Laravel Breeze or Jetstream for authentication
- [ ] Implement role-based access control (RBAC) for student and admin
- [ ] Create middleware for role checking

## Controllers & Logic
- [x] Create StudentController
- [x] Create AdminController
- [ ] Implement methods in controllers for CRUD operations
- [ ] Add logic for document upload, verification, feedback
- [ ] Implement progress tracking and timeline

## Views & Frontend
- [ ] Create dashboard views for students and admins
- [ ] Design forms for document submission
- [ ] Create views for document verification and feedback
- [ ] Implement progress bar and timeline UI

## API Integration
- [ ] Integrate with SOFI endpoint for student data
- [ ] Implement fallback mechanism for data retrieval
- [ ] Add OCR functionality for document processing

## Additional Features
- [ ] Implement document versioning
- [ ] Add activity logging
- [ ] Create reports and SKL download
- [ ] Set up notifications for status updates

## Testing & Deployment
- [ ] Write unit and feature tests
- [ ] Test the system with sample data
- [ ] Deploy to production environment
