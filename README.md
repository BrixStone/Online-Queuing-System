<p align="center">
  <img src="public/System_Logo.png" alt="SmartQueue Logo" width="600">
</p>

# Online Queuing System

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white" alt="Laravel">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP">
  <img src="https://img.shields.io/badge/Livewire-4e56a6?style=for-the-badge&logo=livewire&logoColor=white" alt="Livewire">
  <img src="https://img.shields.io/badge/License-MIT-blue?style=for-the-badge" alt="License">
</p>

A modern, efficient Online Queuing System built with Laravel and Livewire to streamline line management and optimize customer flow. This system is designed to provide real-time queue status updates, assign users to tellers, and manage queue progression dynamically.

##  Features

- **Phone Number Verification:** Users must verify their mobile numbers via SMS OTP using the TextBee API before entering the queue.
- **Live Queue Position:** Users can track their live position number in the queue.
- **Automated Ticket Generation:** Automatically generates sequential tracking numbers (e.g., `TKT-001`) for new queue entries.
- **Dynamic Status Tracking:** Monitors tickets through various lifecycle states:
  - `holding` (Waiting in the general queue)
  - `active` (Moved to the active queue, max capacity of 5)
  - `serving` (Currently being served by a teller)
  - `held` (Temporarily paused/held by teller)
  - `completed` (Successfully served)
- **Teller Queue Management:** Comprehensive operations for tellers including calling the next ticket, holding, and completing current tickets.
- **Smart Active Queue Balancing:** Automatically fills the active queue up to maximum capacity (default: 5) as tickets are served and completed.

##  Tech Stack

- **Framework:** Laravel 11.x
- **Language:** PHP 8.3
- **Frontend / Interactivity:** Livewire 3.x / Livewire Volt
- **Testing:** Pest PHP
- **Database:** Supabase (PostgreSQL)
- **SMS Gateway:** TextBee API (for OTP verification)

## Prerequisites

Before you begin, ensure you have the following installed on your local machine:
- **PHP** 8.3 or higher
- **Composer** (Dependency manager for PHP)
- **Node.js & npm** (For compiling frontend assets)
- **Database** (Supabase project & credentials)

##  Installation

Follow these steps to set up the project locally:

1. **Clone the repository:**
   ```bash
   git clone https://github.com/MysteriousTheory/Online-Queuing-System.git
   cd Online-Queuing-System
   ```

2. **Install Composer dependencies:**
   ```bash
   composer install
   ```

3. **Install NPM packages and build assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Set up the environment:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   *Open the newly created `.env` file and configure your Database connection and TextBee SMS API credentials:*
   ```env
   DB_CONNECTION=pgsql
   # Add your Supabase Database credentials here (DB_HOST, DB_PORT, DB_DATABASE, etc.)
   
   TEXTBEE_API_KEY=your_textbee_api_key_here
   ```

5. **Run Database Migrations:**
   ```bash
   php artisan migrate
   ```

6. **Start the development server:**
   ```bash
   php artisan serve
   ```
   Navigate to `http://localhost:8000` in your browser.

##  Usage / Workflow

Here is how the system handles the queuing flow from the user's perspective:

1. **User Registration:** The user visits the home page and enters their mobile number.
2. **OTP Verification:** An SMS containing an OTP is sent to the user via TextBee. The user enters this OTP to verify their identity.
3. **Ticket Generation:** Upon successful verification, the system assigns them a unique tracking number (e.g., `TKT-005`) and places them in the `holding` queue.
4. **Queue Progression:** The user can watch their live position dynamically update on their screen as tellers clear the queue.
5. **Teller Calling:** A cashier/teller clicks "Call Next" on their dashboard, moving the user from `holding` to `serving`.
6. **Check-In:** The user is notified it's their turn and must click the "Check In" button to confirm they are present at the window.
7. **Completion:** Once the transaction is done, the teller marks the ticket as `completed`.

##  System Architecture

### Core Models
- `QueueTicket`: The central entity managing the ticketing information, capturing the user's name, tracking number, and current status.
- `User`: Handles teller and admin authentication and access control.

### Core Controllers
- `QueueController`: Manages the business logic for the queue lifecycle:
  - `requestQueue(string $studentName)`: Adds a user to the holding queue.
  - `startQueue(string $tellerName)`: Initializes the teller session.
  - `callNext(string $tellerName)`: Pulls the next active ticket for serving.
  - `holdCurrent(string $tellerName)` / `completeCurrent(string $tellerName)`: Updates ticket states accordingly.

##  Contributing

Contributions, issues, and feature requests are welcome!

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

##  License

This project is licensed under the MIT License.
