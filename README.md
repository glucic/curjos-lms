# Curjos LMS Installation Guide

## Prerequisites

-   PHP >= 8.2 (recommended: 8.4)
-   Composer
-   Node.js (recommended: v20.x LTS)
-   npm
-   A supported database (e.g., MySQL, PostgreSQL)
-   Homebrew (for macOS users)

## 1. Clone the Repository

```sh
git clone https://github.com/glucic/curjos-lms.git
cd curjos-lms
```

## 2. Install PHP Dependencies

```sh
composer install
```

## 3. Install Node.js Dependencies

```sh
npm install
```

## 4. Configure Environment

-   Copy `.env.example` to `.env` and update database and JWT settings as needed.

## 5. Generate JWT Key Pair

```sh
php bin/console lexik:jwt:generate-keypair
```

## 6. Run Database Migrations

```sh
php bin/console doctrine:migrations:migrate
```

## 7. Build Frontend Assets

```sh
npm run build
```

Or for development:

```sh
npm run watch
```

## 8. Start the Symfony Server

```sh
symfony serve
```

Or with PHP's built-in server:

```sh
php -S localhost:8000 -t public
```

## 9. Access the Application

-   Visit `http://localhost:8000` in your browser.

## Troubleshooting

-   Ensure your PHP and Node.js versions match the recommendations above.
-   If you encounter architecture errors on Apple Silicon, reinstall Node.js and dependencies for arm64.
-   For JWT issues, verify your key pair and `.env` configuration.

## Useful Commands

-   Run tests: `php bin/phpunit`
-   Clear cache: `php bin/console cache:clear`
-   Build assets: `npm run build`

---

## Demo Data & Entity Relationships

Upon installation, the system loads demo data for quick evaluation and development:

-   **Organizations:**
    -   `System` (for system admin)
    -   `Demo University` (sample organization)
-   **Users:**
    -   System Admin: `super@system.local` (role: SUPER_ADMIN, password: SuperSecret123!)
    -   Demo Admin: `admin@demo.com` (role: ADMIN)
    -   Demo Instructor: `instructor@demo.com` (role: INSTRUCTOR)
    -   Demo Student: `student@demo.com` (role: STUDENT)
-   **Roles:**
    -   Roles are global and each user can have only one role (e.g., SUPER_ADMIN, ADMIN, INSTRUCTOR, STUDENT)
-   **Courses & Lessons:**
    -   Demo course: "Introduction to Leadership" with sample lessons

### Entity Relationships

-   Each user belongs to one organization and has one global role
-   Organizations have many users and courses
-   Courses belong to organizations and have many lessons
-   Roles have many permissions

### System Admin

-   The system admin user (`super@system.local`) has full access to all organizations, users, and data
-   Use this account for initial setup and system-wide administration

---

For more details feel free to contact me at gabriel.lucic@pm.me!
