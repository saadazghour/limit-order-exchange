# Limit-Order Exchange Mini Engine

This project is a full-stack technical assessment to build a miniature limit-order exchange engine using Laravel and Vue.js.

## Technology Stack

- **Backend:** Laravel (Latest Stable)
- **Frontend:** Vue.js (Latest Stable, Composition API)
- **Database:** MySQL or PostgreSQL
- **Real-time:** Pusher via Laravel Broadcasting
- **Styling:** Tailwind CSS

---

### Phase 2: Backend Core Logic (The Exchange Engine)

#### Pull Request Description

**Title:** `feat(backend): Phase 2.3 - Matching Engine`

**Description:**

This PR implements the core matching engine for the limit-order exchange, along with real-time event broadcasting using Pusher.

Key Changes:
-   **Matching Engine Job:** Created a `MatchOrderJob` that is dispatched whenever a new order is created. This job handles the core matching logic:
    -   Finds the first valid counter-order based on price and time.
    -   Executes the trade within a database transaction, updating balances for both buyer and seller.
    -   Deducts a 1.5% commission from the trade.
    -   Updates the status of both matched orders to "filled".
-   **Real-Time Broadcasting:**
    -   Created an `OrderMatched` event that is broadcast on a private Pusher channel (`private-user.{id}`) for both the buyer and seller upon a successful match.
-   **Service Layer:** Added a dedicated `MatchingService` to encapsulate the complex matching logic, keeping the controller clean and focused.

This PR completes the backend business logic for order matching and real-time notifications.

#### How to Test This Phase

1.  **Ensure MySQL Container is Running:**
    ```bash
    docker start mysql-for-laravel
    docker ps
    ```
2.  **Ensure a Queue Worker is Running:**
    In a separate terminal, start the queue worker to process jobs:
    ```bash
    cd backend && php artisan queue:work
    ```
3.  **Register and Log In:**
    Use an API client (like Postman) to register two different users and log them both in to get their API tokens.
4.  **Seed Initial Balance and Assets:**
    To test matching, users need funds. For now, you can manually update the database:
    -   In your MySQL client, give User 1 a balance: `UPDATE users SET balance = 100000.00 WHERE id = 1;`
    -   Give User 2 some assets: `INSERT INTO assets (user_id, symbol, amount) VALUES (2, 'BTC', 1.0);`
5.  **Create Orders:**
    -   **User 2 (Seller):** Create a sell order for BTC.
        -   **Endpoint:** `http://127.0.0.1:8000/api/orders` (POST)
        -   **Headers:** `Authorization: Bearer <USER_2_TOKEN>`
        -   **Body:** `{"symbol": "BTC", "side": "sell", "price": "50000.00", "amount": "0.1"}`
    -   **User 1 (Buyer):** Create a matching buy order for BTC.
        -   **Endpoint:** `http://127.0.0.1:8000/api/orders` (POST)
        -   **Headers:** `Authorization: Bearer <USER_1_TOKEN>`
        -   **Body:** `{"symbol": "BTC", "side": "buy", "price": "50000.00", "amount": "0.1"}`
6.  **Verify Match:**
    -   **Queue Worker Terminal:** You should see the `MatchOrderJob` being processed successfully.
    -   **Pusher Debug Console:** Check your Pusher dashboard's Debug Console. You should see the `OrderMatched` event being broadcast on the private channels for both User 1 and User 2.
    -   **Database:** Check the `orders` table to see if the status of both orders is now "filled". Check the `trades` table for a new entry. Check the `users` and `assets` tables to see if balances and asset amounts have been updated correctly (including the 1.5% commission).

                                         │
### Phase 2: Backend Core Logic (API Endpoints)

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
    -   **Endpoint:** `http://127.0.0.1:8000/register`
    -   **Method:** `POST`
    -   **Body (JSON):**
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
    -   **Endpoint:** `http://127.0.0.1:8000/login`
    -   **Method:** `POST`
    -   **Body (JSON):**
        ```json
        {
            "email": "test@example.com",
            "password": "password"
        }
        ```
    -   **Expected:** You should receive a `token` in the response. Copy this token.
6.  **Test `GET /api/profile`:**
    -   **Endpoint:** `http://127.0.0.1:8000/api/profile`
    -   **Method:** `GET`
    -   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>` (replace `<YOUR_AUTH_TOKEN>` with the token from step 5)
    -   **Expected:** You should receive JSON containing the user's details, including an initial `balance` of `0.00`, and empty `assets` and `orders` arrays.
7.  **Test `POST /api/orders` (Buy Order):**
    -   **Endpoint:** `http://127.0.0.1:8000/api/orders`
    -   **Method:** `POST`
    -   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>`
    -   **Body (JSON):**
        ```json
        {
            "symbol": "BTC",
            "side": "buy",
            "price": "50000.00",
            "amount": "0.001"
        }
        ```
    -   **Expected:** A successful response indicating the order was created. Retest `GET /api/profile` and observe the balance change (it should be negative if you didn't have enough balance, or USD locked). *Note: Initial balance is 0, so this will likely fail unless we seed initial balance or modify logic first.*
8.  **Test `POST /api/orders` (Sell Order):**
    -   **Endpoint:** `http://127.0.0.1:8000/api/orders`
    -   **Method:** `POST`
    -   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>`
    -   **Body (JSON):**
        ```json
        {
            "symbol": "ETH",
            "side": "sell",
            "price": "2500.00",
            "amount": "0.5"
        }
        ```
    -   **Expected:** A successful response. *Note: Initial assets are 0, so this will likely fail unless we seed initial assets.*
9.  **Test `GET /api/orders?symbol=BTC`:**
    -   **Endpoint:** `http://127.0.0.1:8000/api/orders?symbol=BTC`
    -   **Method:** `GET`
    -   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>`
    -   **Expected:** A list of open orders for BTC.
10. **Test `POST /api/orders/{id}/cancel`:**
    -   **Endpoint:** `http://127.0.0.1:8000/api/orders/<ORDER_ID>/cancel` (replace `<ORDER_ID>` with an ID from your created orders)
    -   **Method:** `POST`
    -   **Headers:** `Authorization: Bearer <YOUR_AUTH_TOKEN>`
    -   **Expected:** A successful response indicating the order was cancelled, and `GET /api/profile` should show released funds/assets.


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
