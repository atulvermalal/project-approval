# Project Approval

Project Approval is a Laravel 12 + Bootstrap application for submitting projects, reviewing approvals, managing users and roles, sending queued email notifications, and exposing protected API endpoints.

## Overview

This project was built for a full-stack workflow task where:

- users submit projects
- admins approve or reject projects
- audit logs and approval history are stored
- queued email notifications are sent on submission and decision
- API responses are returned through Laravel API Resources
- access is protected with role and permission checks

## Main Features

- Login and registration with Laravel auth
- Role-based access control for admin and user workflows
- Dashboard with summary cards for admin and user
- Project submission with file uploads
- Project listing with filters, history, and bulk approval/rejection
- Project details page with attachment view/download
- Project delete action for allowed records
- Admin user management with separate index, create, and edit pages
- Admin role management with separate index, create, and edit pages
- MySQL stored procedure for approval
- Queued email notifications for project submitted, approved, and rejected events
- API endpoints using `ProjectResource`
- Optional real-time notification setup using Laravel Echo + Pusher

## Tech Stack

- Laravel 12
- PHP 8.2
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

If the worker is not running, emails will stay in the `jobs` table until they are processed.

If you want emails to send immediately during local development, you can switch:

```env
QUEUE_CONNECTION=sync
```

Then clear config:

```bash
php artisan config:clear
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
php artisan queue:work
```

## Environment Notes

Important `.env` items:

- `APP_NAME="Project Approval"`
- Gmail SMTP settings for outgoing mail
- `QUEUE_CONNECTION=database`
- MySQL database connection
- Optional Pusher keys for real-time notifications

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

- GitHub repository: `https://github.com/atulvermalal/project-approval`
- Demo video: `https://www.dropbox.com/scl/fi/mhjt3c5xjroqkdyo4kxzy/ui.mp4?rlkey=37j8d2q0vmy3aqfuacb50epw2&st=kmo8fcjc&dl=0`
- Code walkthrough video: `https://www.dropbox.com/scl/fi/p9uip4jxsedzms8otns2y/code.mp4?rlkey=j6s256el3zygb5owysef0s5zx&st=0ldvek7z&dl=0`

## Notes

- Frontend is implemented with Bootstrap and Blade
- Laravel Echo + Pusher integration is prepared and can be activated by filling the Pusher keys in `.env`
- If emails are queued but not delivered, make sure `php artisan queue:work` is running
- If MySQL is not running, login/session and queued jobs can also fail because this app uses database-backed sessions and queue storage
- If Gmail SMTP authentication fails, verify the app password and Gmail security settings
