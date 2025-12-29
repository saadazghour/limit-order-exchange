# Limit-Order Exchange Mini Engine

This project is a full-stack technical assessment to build a miniature limit-order exchange engine using Laravel and Vue.js.

## Technology Stack

- **Backend:** Laravel (Latest Stable)
- **Frontend:** Vue.js (Latest Stable, Composition API)
- **Database:** MySQL or PostgreSQL
- **Real-time:** Pusher via Laravel Broadcasting
- **Styling:** Tailwind CSS
### Phase 1: Backend Core Logic (API Endpoints)

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

3.  **Start Laravel Development Server:**
    ```bash
    php artisan serve
    ```

4.  **Register a New User:**
    Use an API client (like Postman, Insomnia, or `curl`) to register a new user.
    *   **Endpoint:** `http://127.0.0.1:8000/register`
    *   **Method:** `POST`
    *   **Body (JSON):**
        ```json
        {
            "name": "Test User",
            "email": "test@example.com",
            "password": "password",
            "password_confirmation": "password"
        }
        ```

5.  **Log In and Get Token:**
    Log in with the registered user to obtain an API token.
    *   **Endpoint:** `http://127.0.0.1:8000/login`
    *   **Method:** `POST`
    *   **Body (JSON):**
        ```json
        {
            "email": "test@example.com",
            "password": "password"
        }
        ```
    *   **Expected:** You should receive a `token` in the response. Copy this token.

6.  **Test `GET /api/profile`:**
    *   **Endpoint:** `http://127.0.0.1:8000/api/profile`
    *   **Method:** `GET`
    *   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>` (replace `<YOUR_AUTH_TOKEN>` with the token from step 5)
    *   **Expected:** You should receive JSON containing the user's details, including an initial `balance` of `0.00`, and empty `assets` and `orders` arrays.

7.  **Test `POST /api/orders` (Buy Order):**
    *   **Endpoint:** `http://127.0.0.1:8000/api/orders`
    *   **Method:** `POST`
    *   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>`
    *   **Body (JSON):**
        ```json
        {
            "symbol": "BTC",
            "side": "buy",
            "price": "50000.00",
            "amount": "0.001"
        }
        ```
    *   **Expected:** A successful response indicating the order was created. Retest `GET /api/profile` and observe the balance change (it should be negative if you didn't have enough balance, or USD locked). *Note: Initial balance is 0, so this will likely fail unless we seed initial balance or modify logic first.*

8.  **Test `POST /api/orders` (Sell Order):**
    *   **Endpoint:** `http://127.0.0.1:8000/api/orders`
    *   **Method:** `POST`
    *   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>`
    *   **Body (JSON):**
        ```json
        {
            "symbol": "ETH",
            "side": "sell",
            "price": "2500.00",
            "amount": "0.5"
        }
        ```
    *   **Expected:** A successful response. *Note: Initial assets are 0, so this will likely fail unless we seed initial assets.*

9.  **Test `GET /api/orders?symbol=BTC`:**
    *   **Endpoint:** `http://127.0.0.1:8000/api/orders?symbol=BTC`
    *   **Method:** `GET`
    *   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>`
    *   **Expected:** A list of open orders for BTC.

10. **Test `POST /api/orders/{id}/cancel`:**
    *   **Endpoint:** `http://127.0.0.1:8000/api/orders/<ORDER_ID>/cancel` (replace `<ORDER_ID>` with an ID from your created orders)
    *   **Method:** `POST`
    *   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>`
    *   **Expected:** A successful response indicating the order was cancelled, and `GET /api/profile` should show released funds/assets.

## Phase 2: Backend Core Logic (The Exchange Engine)

### How to Test This Phase

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
    *   The `users` table now has a `balance` column.
    *   The `assets`, `orders`, and `trades` tables exist with the defined columns.

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

    VITE_PUSHER_APP_KEY="${VITE_PUSHER_APP_KEY}"
    VITE_PUSHER_HOST="${VITE_PUSHER_HOST}"
    VITE_PUSHER_PORT="${VITE_PUSHER_PORT}"
    VITE_PUSHER_SCHEME="${VITE_PUSHER_SCHEME}"
    VITE_PUSHER_APP_CLUSTER="${VITE_PUSHER_APP_CLUSTER}"
    ```

### Frontend Setup

1.  **Install YARN Dependencies:**
    ```bash
    yarn install
    ```

2.  **Run Development Server:**
    To run the backend and frontend servers concurrently:
    *   **Terminal 1 (Backend):** `php artisan serve`
    *   **Terminal 2 (Frontend):** `yarn run dev`