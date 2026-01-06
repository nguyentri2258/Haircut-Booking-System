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
- PHP >= 8.2

Database:
- MySQL

Frontend:
- Blade Template
- JavaScript

## Installation
1. Clone the repository:
	```bash
	git clone https://github.com/your-username/your-repo.git
	cd your-repo

2. Install PHP dependencies:
	```bash
	composer install

3. Create environment file and generate app key:
	```bash
	cp .env.example .env
	php artisan key:generate

4. Configure database in .env:
	```bash
	DB_DATABASE=your_database
	DB_USERNAME=your_username
	DB_PASSWORD=your_password

5. Run migrations and seeders:
	```bash
	php artisan migrate:fresh --seed

6. Install and build frontend assets:
	```bash
	npm install
	npm run dev

7. Start the development server:
	```bash
	php artisan serve

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

## Notes for Recruiters
- All core business logic is handled on the backend
- Seeders allow the system to be tested immediately after setup
- Availability and booking logic follows real salon workflows
- Mail driver is configured to use log for testing purposes