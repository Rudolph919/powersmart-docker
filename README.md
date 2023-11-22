# Laravel EskomSePush API Integration

## Overview

This Laravel application integrates with the EskomSePush API to provide information about load shedding schedules and events. The application retrieves data from the EskomSePush API and presents it in a user-friendly manner.

## Features

- Display Eskom load shedding events.
- Show load shedding schedules for different days.

## Requirements

- PHP >= 8.1
- Laravel >= 10.0
- Composer (for package management)
- EskomSePush API Key (Sign up at [EskomSePush](https://eskomsepush.gumroad.com/l/api.) to obtain your API key)

## Installation and Configuration

1. Clone the repository:

    ```bash
    git clone https://github.com/Rudolph919/powersmart-docker.git
    ```

2. Navigate to the project directory:

    ```bash
    cd powersmart-docker
    ```

3. Install Composer dependencies:

    ```bash
    composer install
    ```

4. Copy the `.env.example` file to `.env` and configure your database settings and EskomSePush API key:

    ```bash
    cp .env.example .env
    ```

    Open the `.env` file and set the following variables:

    ```dotenv
    DB_CONNECTION=mysql
    DB_HOST=your_database_host
    DB_PORT=your_database_port
    DB_DATABASE=your_database_name
    DB_USERNAME=your_database_username
    DB_PASSWORD=your_database_password

    ESKOM_API_KEY=your_eskomsepush_api_key
    ```

    Replace `your_database_host`, `your_database_port`, `your_database_name`, `your_database_username`, `your_database_password`, and `your_eskomsepush_api_key` with your actual credentials.

5. Generate application key:

    ```bash
    php artisan key:generate
    ```

6. Run database migrations:

    ```bash
    php artisan migrate
    ```

7. Serve the application:

    ```bash
    php artisan serve
    ```

8. Access the application in your browser at `http://localhost:8000`

## Usage

1. Open the application in your web browser.
2. Enter your EskomSePush API key in the application settings.
3. Explore load shedding events and schedules for different days.
