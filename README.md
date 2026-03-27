# Project Approval

Project Approval is a Laravel 13 + Bootstrap application for submitting projects, reviewing approvals, managing users and roles, sending email notifications, and exposing protected API endpoints.

## Overview

This project was built for a full-stack workflow task where:

- users submit projects
- admins approve or reject projects
- audit logs and approval history are stored
- email notifications are sent on submission and decision
- API responses are returned through Laravel API Resources
- access is protected with role and permission checks

## Main Features

- Login and registration with Laravel auth
- Role-based access control for admin and user workflows
- Dashboard with summary cards
- Project submission with file uploads
- Project listing with filters, history, and bulk approval/rejection
- Project details page
- Project delete action for allowed records
- Admin user management:
  - user index
  - create user
  - edit user
- Admin role management:
  - role index
  - create role
  - edit role
- MySQL stored procedure for approval
- Email notifications for:
  - project submitted
  - project approved
  - project rejected
- API endpoints using `ProjectResource`

## Tech Stack

- Laravel 13
- PHP 8.3
- Bootstrap 5
- Blade templates
- MySQL

## Database Tables

- `users`
- `projects`
- `approvals`
- `audit_logs`
- `roles`
- `permissions`
- `permission_role`

## Stored Procedure

The project uses a MySQL stored procedure named `sp_approve_project`.

Laravel calls it with:

```php
DB::select('CALL sp_approve_project(?)', [$project->id]);
```

It:

- updates the project status to `approved`
- clears rejection reason
- inserts an audit log entry

## API Endpoints

- `POST /api/projects`
- `PATCH /api/projects/{id}/approve`

These routes are authenticated and approval is authorization-protected.

## Email Notifications

Project workflow emails are configured through Gmail SMTP and dispatched through Laravel queues.

Events covered:

- project submitted
- project approved
- project rejected

Run a queue worker for queued notifications:

```bash
php artisan queue:work
```

## File Access

Uploaded attachments such as PDF and Word files can be viewed or downloaded by authorized users from the project pages.

## Demo Accounts

- Admin: `atulvermalal@gmail.com` / `password`
- User: `vatul7700@gmail.com` / `password`

## Local Setup

```bash
composer install
php artisan migrate --seed
php artisan storage:link
php artisan serve
```

## Environment Notes

Important `.env` items:

- `APP_NAME="Project Approval"`
- Gmail SMTP settings for outgoing mail
- MySQL database connection

## Testing

Run tests with:

```bash
php artisan test
```

Current feature coverage includes:

- auth flow
- dashboard access
- project pages
- project submission
- project approval API
- role and permission management

## Submission Links

- GitHub repository: `ADD_GITHUB_REPOSITORY_LINK`
- Demo video: `ADD_DEMO_VIDEO_LINK`
- Code walkthrough video: `ADD_CODE_WALKTHROUGH_LINK`

## Notes

- Frontend is implemented with Bootstrap and Blade
- Bonus real-time Echo/Pusher notifications are not included
- If Gmail SMTP authentication fails, verify the app password and Gmail security settings
