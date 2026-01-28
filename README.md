# Hair Salon Booking System
A web-based booking and staff scheduling system for hair salons, built with Laravel.

## Introduction
This project is a hair salon booking system that allows customers to book services with available staff members based on working schedules.
The system supports multiple branches, staff availability management, holidays, and service-based bookings.

## Features
- Customer booking with email verification
- Staff availability management (working schedules)
- Holiday management (global & branch-based)
- Automatic staff assignment
- Booking conflict prevention
- Multi-branch support
- Role-based access control (Owner / Manager / Staff)
- Display total service cost before booking

## Tech Stack
Backend:
- Laravel 12.x
- PHP 8.2 (Docker – PHP-FPM)

Frontend:
- Blade Template
- JavaScript
- Vite

Database:
- MySQL 8.0 (Docker)

Web Server:
- Nginx (Docker)

Development Tools:
- Docker & Docker Compose

## Installation
1. Clone the repository:
	```bash
	git clone https://github.com/your-username/your-repo.git
	cd your-repo

2. Create environment file:
	```bash
	cp .env.example .env

3. Configure database in .env:
	```bash
	DB_DATABASE=your_database
	DB_USERNAME=your_username
	DB_PASSWORD=your_password
    DB_ROOT_PASSWORD=your_root_password

4. Build & start Docker containers:
	```bash
	docker compose up -d --build

5. Generate application key:
	```bash
	docker compose exec app php artisan key:generate

6. Run migrations and seeders:
	```bash
	docker compose exec app php artisan migrate:fresh --seed

7. Access the application:

- Web app: http://localhost:8000

## Database & Seeder
The project includes seeders for:
- Users (Owner, Manager, Staff)
- Addresses (branches)
- Services
- Holidays
- Staff availabilities (with business logic)
- Bookings (based on availability)

Seeders respect real business rules such as:
- Avoiding holidays
- Preventing overlapping working shifts
- Preventing booking conflicts

## Booking Flow
1. Customer enters booking information and selects services
2. Customer selects branch and date
3. Available staff and time slots are loaded dynamically
4. Email verification is required before confirmation
5. Booking is confirmed if no scheduling conflict exists

## Roles & Permissions
- Owner: Manage all branches and staff
- Manager: Manage staff within assigned branch
- Staff: View own needed information

## List of Accounts
| Name          | Email                    | Password    |
|----------------|--------------------------|-------------|
| System Owner   | owner123@gmail.com       | Owner123@   |
| Manager 1      | manager1@gmail.com       | Manager1@   |
| Manager 2      | manager2@gmail.com       | Manager2@   |
| Staff 1        | staff1@gmail.com         | Staff1@     |
| Staff 2        | staff2@gmail.com         | Staff2@     |
| Staff 3        | staff3@gmail.com         | Staff3@     |
| Staff 4        | staff4@gmail.com         | Staff4@     |

## Notes for Recruiters
- All core business logic is handled on the backend
- Seeders allow the system to be tested immediately after setup
- Availability and booking logic follows real salon workflows
- Mail driver is configured to use log for testing purposes
