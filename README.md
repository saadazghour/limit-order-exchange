# Limit-Order Exchange Mini Engine

This project is a full-stack technical assessment to build a miniature limit-order exchange engine using Laravel and Vue.js.

## Technology Stack

- **Backend:** Laravel (Latest Stable)
- **Frontend:** Vue.js (Latest Stable, Composition API)
- **Database:** MySQL or PostgreSQL
- **Real-time:** Pusher via Laravel Broadcasting
- **Styling:** Tailwind CSS

---

## Phase 1: Foundation & Project Setup

### Phase 2: Backend Core Logic (The Exchange Engine)

#### How to Test This Phase

1.  **Ensure MySQL Container is Running:**
    ```bash
    docker start mysql-for-laravel
    docker ps # Verify it's running
    ```
2.  **Navigate to the Backend Directory:**
    ```bash
    cd backend
    ```
3.  **Run Migrations:**
    ```bash
    php artisan migrate
    ```
    You should see output indicating that all new migrations (add_balance_to_users_table, create_assets_table, create_orders_table, create_trades_table) have run successfully.
4.  **Verify Database Structure (Optional):**
    You can connect to your MySQL database using a client (like MySQL Workbench, DataGrip, or the command line) and verify that:
    - The `users` table now has a `balance` column.
    - The `assets`, `orders`, and `trades` tables exist with the defined columns.

## General Setup Instructions

### Backend Setup

1.  **Clone the repository:**
    ```bash
    git clone https://github.com/saadazghour/limit-order-exchange.git
    cd limit-order-exchange
    ```

2.  **Install PHP Dependencies:**
    ```bash
    composer install
    ```

3.  **Create Environment File:**
    ```bash
    cp .env.example .env
    ```

4.  **Generate App Key:**
    ```bash
    php artisan key:generate
    ```

5.  **Configure `.env` file:**
    Update the following variables in your `backend/.env` file with your local environment's details:
    ```ini
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=limit_order_exchange
    DB_USERNAME=root
    DB_PASSWORD=

    # Add your Pusher credentials
    PUSHER_APP_ID=
    PUSHER_APP_KEY=
    PUSHER_APP_SECRET=
    PUSHER_HOST=
    PUSHER_PORT=443
    PUSHER_SCHEME=https
    PUSHER_APP_CLUSTER=mt1

    VITE_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
    VITE_PUSHER_HOST="${PUSHER_HOST}"
    VITE_PUSHER_PORT="${PUSHER_PORT}"
    VITE_PUSHER_SCHEME="${PUSHER_SCHEME}"
    VITE_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"
    ```

### Frontend Setup

1.  **Install YARN Dependencies:**
    ```bash
    yarn install
    ```

2.  **Run Development Server:**
    To run the backend and frontend servers concurrently:
    - **Terminal 1 (Backend):** `php artisan serve`
    - **Terminal 2 (Frontend):** `yarn run dev`