___*This repository serves as an example of a real-world project I worked on. It is intended solely to demonstrate the scope and structure of the work I contributed to. Sensitive data has been redacted and cannot be misused.*___

# REST API Monolith – Dental Appointment Booking Platform

This repository contains a production-grade **monolithic REST API** built using Laravel, designed as the backend for a **dental appointment booking platform**. It showcases a scalable and maintainable architecture that encapsulates core business logic, resource management, role-based access, and notification delivery within a single service.

---

## 🚀 Project Overview

The API enables users to interact with a platform that facilitates scheduling and managing dental appointments. It supports multiple user roles — such as clients (dentists) and candidates (patients) — and includes a complete lifecycle for job advertisements (appointments), from creation and approval to cancellation and feedback.

---

## 🧱 Architecture

This project follows a **layered monolithic design** using Laravel best practices:

- **Controllers** handle HTTP request/response.
- **Services** encapsulate business logic.
- **Repositories** abstract data access.
- **Requests** manage validation.
- **Resources** format JSON responses.
- **Policies** enforce role-based authorization.

The codebase is modular, readable, and optimized for scalability and separation of concerns within a monolith.

---

## 🛠️ Core Technologies

- **Laravel 10.x**
- **PHP 8.2+**
- **MySQL** (or compatible RDBMS)
- **OpenAPI (Swagger)** via [L5-Swagger](https://github.com/DarkaOnLine/L5-Swagger)
- **Laravel Notifications** for real-time alerts
- **Role-based authorization** using Laravel Policies

---

## 📦 Main Components

### User Roles

- **Client (Dentist)** — Manages job advertisements and evaluates candidates.
- **Candidate (Patient)** — Applies for dental appointment slots.

### Controllers

| Controller         | Description                                                                 |
|--------------------|-----------------------------------------------------------------------------|
| `UserController`   | Manages user profile, status, notifications, and account lifecycle          |
| `JobAdController`  | Handles creation, update, feedback, status transitions, and candidate ops   |
| `ClientController` | Client-specific interactions and resources                                  |
| `CandidateController` | Candidate registration, applications, and data handling                  |

---

## 📚 API Features

- CRUD operations for Job Ads (appointments)
- Custom endpoints:
    - `cancelJobAdByClient`
    - `cancelJobAdByCandidate`
    - `approveCandidate`
    - `clientFeedback` / `candidateFeedback`
    - `getCandidatesApplied`
    - `getDates`
- Role-specific data filtering and policies
- Push notifications to users (via Expo tokens)
- Soft deletion for users
- History tracking and filtered listing of job ads

---

## 📖 API Documentation

API documentation is auto-generated via **L5-Swagger**.

To view the documentation locally:

```bash
php artisan l5-swagger:generate
