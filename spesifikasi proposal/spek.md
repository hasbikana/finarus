# Finarus Web - Backend Specification

## Project Overview

adalah aplikasi pencatatan keuangan pribadi berbasis Laravel.

Frontend telah dibuat menggunakan v0.dev dan akan dikonversi ke Laravel Blade.

Tugas backend adalah membuat seluruh API, database, business logic, validation, authorization, dan integrasi data agar sesuai dengan proposal FinanceApp.

Jika terdapat perbedaan antara desain frontend dan proposal, prioritaskan:

1. UX frontend tetap dipertahankan.
2. Database tetap mengikuti spesifikasi proposal.
3. API dapat mendukung kebutuhan frontend dan mobile app.

---

# Technology Stack

* Laravel 13
* MySQL
* Laravel Sanctum
* Blade Template
* tailwind
* REST API
* Eloquent ORM

---

# Core Features

## Authentication

### Register

Fields:

* name
* email
* password
* password_confirmation

Rules:

* email unique
* password minimum 8 chars

### Login

Fields:

* email
* password

Requirements:

* Sanctum authentication
* session login for web

### Logout

Requirements:

* invalidate session
* revoke sanctum token

---

# Database Design

## users

* id
* name
* email
* password
* created_at
* updated_at

## categories

* id
* user_id
* name
* created_at
* updated_at

Relationship:

* User hasMany Categories
* Category belongsTo User

## transactions

* id
* user_id
* category_id
* type (income, expense)
* amount
* description
* transaction_date
* created_at
* updated_at

Relationship:

* User hasMany Transactions
* Category hasMany Transactions

---

# Business Rules

## Categories

Each category belongs to a user.

Users can:

* create category
* update category
* delete category
* view category list

Users cannot access categories owned by another user.

---

## Transactions

Each transaction belongs to:

* one user
* one category

Types:

* income
* expense

Validation:

* amount > 0
* category must belong to current user

---

# Dashboard Logic

Dashboard must display:

## Current Balance

Balance = Total Income - Total Expense

## Total Income This Month

Sum all income transactions in current month.

## Total Expense This Month

Sum all expense transactions in current month.

## Recent Transactions

Latest 5 transactions.

---

# Reports

Provide report endpoints:

## Monthly Summary

Return:

* total income
* total expense
* balance

## Expense By Category

Group expense transactions by category.

## Income By Category

Group income transactions by category.

Support:

* month filter
* year filter

---

# API Endpoints

## Auth

POST /api/register

POST /api/login

POST /api/logout

---

## Categories

GET /api/categories

POST /api/categories

PUT /api/categories/{id}

DELETE /api/categories/{id}

---

## Transactions

GET /api/transactions

POST /api/transactions

PUT /api/transactions/{id}

DELETE /api/transactions/{id}

---

## Dashboard

GET /api/dashboard

---

## Reports

GET /api/reports/monthly

GET /api/reports/categories

---

# Development Rules

1. Use Form Request Validation.
2. Use Service Layer when business logic becomes complex.
3. Use Resource API responses.
4. Use Policy or Gate for authorization.
5. Use Repository Pattern only if necessary.
6. Create migrations, models, factories, seeders.
7. Create feature tests for critical flows.
8. Use Eloquent relationships properly.
9. Follow Laravel best practices.
10. Generate clean, maintainable code.

---

# Expected Deliverables

* Migrations
* Models
* Controllers
* Requests
* Resources
* Services
* Policies
* Routes
* Blade Integration
* API Documentation
* Seeders
* Tests

Frontend already exists and should be connected to these APIs.
