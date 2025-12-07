# Database Structure

## Tables

### 1. users
User accounts for the POS system (Super Admin, Admin, Cashier)

| Column         | Type                                    | Attributes                                                |
| -------------- | --------------------------------------- | --------------------------------------------------------- |
| id             | BIGINT UNSIGNED                         | PRIMARY KEY, AUTO_INCREMENT                               |
| name           | VARCHAR(255)                            | NOT NULL                                                  |
| username       | VARCHAR(255)                            | NOT NULL, UNIQUE (partial index where deleted_at IS NULL) |
| password       | VARCHAR(255)                            | NOT NULL                                                  |
| role           | ENUM('super-admin', 'admin', 'cashier') | NOT NULL, DEFAULT 'cashier'                               |
| remember_token | VARCHAR(100)                            | NULLABLE                                                  |
| created_at     | TIMESTAMP                               | NULLABLE                                                  |
| updated_at     | TIMESTAMP                               | NULLABLE                                                  |
| deleted_at     | TIMESTAMP                               | NULLABLE                                                  |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY unique_username_not_deleted (username) WHERE deleted_at IS NULL
- INDEX idx_role (role)
- INDEX idx_deleted_at (deleted_at)

**Constraints:**
- username must be unique among non-deleted records
- role must be one of: 'super-admin', 'admin', 'cashier'

---

### 2. products
Product catalog for the POS system

| Column        | Type            | Attributes                                                |
| ------------- | --------------- | --------------------------------------------------------- |
| id            | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT                               |
| name          | VARCHAR(255)    | NOT NULL                                                  |
| sku           | VARCHAR(255)    | NOT NULL, UNIQUE (partial index where deleted_at IS NULL) |
| price         | DECIMAL(15,2)   | NOT NULL, DEFAULT 0.00                                    |
| description   | TEXT            | NULLABLE (rich text / HTML)                               |
| image         | VARCHAR(255)    | NULLABLE (path to stored product image on public disk)    |
| current_stock | INT             | NOT NULL, DEFAULT 0                                       |
| total_sold    | INT             | NOT NULL, DEFAULT 0                                       |
| created_at    | TIMESTAMP       | NULLABLE                                                  |
| updated_at    | TIMESTAMP       | NULLABLE                                                  |
| deleted_at    | TIMESTAMP       | NULLABLE                                                  |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY unique_sku_not_deleted (sku) WHERE deleted_at IS NULL
- INDEX idx_name (name)
- INDEX idx_deleted_at (deleted_at)

**Constraints:**
- sku must be unique among non-deleted records
- current_stock must be >= 0
- total_sold must be >= 0

---

### 3. stock_histories
Stock movement history tracking (increases and decreases)

| Column     | Type                         | Attributes                  |
| ---------- | ---------------------------- | --------------------------- |
| id         | BIGINT UNSIGNED              | PRIMARY KEY, AUTO_INCREMENT |
| product_id | BIGINT UNSIGNED              | NOT NULL, FOREIGN KEY       |
| user_id    | BIGINT UNSIGNED              | NOT NULL, FOREIGN KEY       |
| type       | ENUM('increase', 'decrease') | NOT NULL                    |
| quantity   | INT                          | NOT NULL                    |
| notes      | TEXT                         | NULLABLE                    |
| created_at | TIMESTAMP                    | NULLABLE                    |
| updated_at | TIMESTAMP                    | NULLABLE                    |

**Indexes:**
- PRIMARY KEY (id)
- INDEX idx_product_id (product_id)
- INDEX idx_user_id (user_id)
- INDEX idx_type (type)
- INDEX idx_created_at (created_at)

**Foreign Keys:**
- product_id REFERENCES products(id) ON DELETE CASCADE
- user_id REFERENCES users(id) ON DELETE RESTRICT

**Constraints:**
- type must be one of: 'increase', 'decrease'
- quantity must be > 0

---

### 4. transactions
Transaction records for completed sales (POS checkouts)

| Column     | Type            | Attributes                  |
| ---------- | --------------- | --------------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| user_id    | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY       |
| total      | DECIMAL(15,2)   | NOT NULL, DEFAULT 0.00      |
| created_at | TIMESTAMP       | NULLABLE                    |
| updated_at | TIMESTAMP       | NULLABLE                    |

**Indexes:**
- PRIMARY KEY (id)
- INDEX idx_user_id (user_id)
- INDEX idx_created_at (created_at)

**Foreign Keys:**
- user_id REFERENCES users(id) ON DELETE CASCADE

**Constraints:**
- total must be >= 0

---

### 5. transaction_details
Line items for each transaction (products sold)

| Column         | Type            | Attributes                  |
| -------------- | --------------- | --------------------------- |
| id             | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| transaction_id | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY       |
| product_id     | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY       |
| quantity       | INT             | NOT NULL, DEFAULT 1         |
| price          | DECIMAL(15,2)   | NOT NULL, DEFAULT 0.00      |
| total          | DECIMAL(15,2)   | NOT NULL, DEFAULT 0.00      |
| created_at     | TIMESTAMP       | NULLABLE                    |
| updated_at     | TIMESTAMP       | NULLABLE                    |

**Indexes:**
- PRIMARY KEY (id)
- INDEX idx_transaction_id (transaction_id)
- INDEX idx_product_id (product_id)

**Foreign Keys:**
- transaction_id REFERENCES transactions(id) ON DELETE CASCADE
- product_id REFERENCES products(id) ON DELETE CASCADE

**Constraints:**
- quantity must be > 0
- price must be >= 0
- total must be >= 0

---

### 6. cart_items
Shopping cart items (per-user cart for POS terminal)

| Column     | Type            | Attributes                        |
| ---------- | --------------- | --------------------------------- |
| id         | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT       |
| user_id    | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY             |
| product_id | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY             |
| quantity   | INT             | NOT NULL, DEFAULT 1               |
| price      | DECIMAL(15,2)   | NOT NULL, DEFAULT 0.00 (snapshot) |
| created_at | TIMESTAMP       | NULLABLE                          |
| updated_at | TIMESTAMP       | NULLABLE                          |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY unique_user_product (user_id, product_id)

**Foreign Keys:**
- user_id REFERENCES users(id) ON DELETE CASCADE
- product_id REFERENCES products(id) ON DELETE CASCADE

**Constraints:**
- quantity must be > 0
- Each user can only have one cart item per product (enforced by unique index)

---

### 7. chat_topics
Chat conversation topics for the AI chatbot

| Column               | Type            | Attributes                  |
| -------------------- | --------------- | --------------------------- |
| id                   | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT |
| user_id              | BIGINT UNSIGNED | NOT NULL, FOREIGN KEY       |
| title                | VARCHAR(255)    | NOT NULL                    |
| last_message_at      | TIMESTAMP       | NULLABLE                    |
| confirmation_action  | VARCHAR(255)    | NULLABLE                    |
| confirmation_payload | JSON            | NULLABLE                    |
| created_at           | TIMESTAMP       | NULLABLE                    |
| updated_at           | TIMESTAMP       | NULLABLE                    |
| deleted_at           | TIMESTAMP       | NULLABLE                    |

**Indexes:**
- PRIMARY KEY (id)
- INDEX idx_user_last_message (user_id, last_message_at)

**Foreign Keys:**
- user_id REFERENCES users(id) ON DELETE CASCADE

**Constraints:**
- Soft deletes enabled
- confirmation_action and confirmation_payload store pending user confirmations

---

### 8. chat_messages
Messages within chat topics (user and AI assistant messages)

| Column        | Type                      | Attributes                  |
| ------------- | ------------------------- | --------------------------- |
| id            | BIGINT UNSIGNED           | PRIMARY KEY, AUTO_INCREMENT |
| chat_topic_id | BIGINT UNSIGNED           | NOT NULL, FOREIGN KEY       |
| user_id       | BIGINT UNSIGNED           | NULLABLE, FOREIGN KEY       |
| role          | ENUM('user', 'assistant') | NOT NULL                    |
| content       | LONGTEXT                  | NOT NULL                    |
| created_at    | TIMESTAMP                 | NULLABLE                    |
| updated_at    | TIMESTAMP                 | NULLABLE                    |

**Indexes:**
- PRIMARY KEY (id)
- INDEX idx_topic_created (chat_topic_id, created_at)

**Foreign Keys:**
- chat_topic_id REFERENCES chat_topics(id) ON DELETE CASCADE
- user_id REFERENCES users(id) ON DELETE CASCADE

**Constraints:**
- role must be one of: 'user', 'assistant'
- user_id is nullable (assistant messages don't have a user_id)

---

## Relationships

### users
- **Has Many:** stock_histories (one user can have many stock history records)
- **Has Many:** transactions (one user can create many transactions)
- **Has Many:** cart_items (one user can have many cart items)
- **Has Many:** chat_topics (one user can have many chat topics)
- **Has Many:** chat_messages (one user can send many messages)

### products
- **Has Many:** stock_histories (one product can have many stock history records)
- **Has Many:** transaction_details (one product can appear in many transaction details)
- **Has Many:** cart_items (one product can be in many carts)

### stock_histories
- **Belongs To:** products (many stock histories belong to one product)
- **Belongs To:** users (many stock histories belong to one user who made the change)

### transactions
- **Belongs To:** users (many transactions belong to one user/cashier)
- **Has Many:** transaction_details (one transaction has many line items)

### transaction_details
- **Belongs To:** transactions (many details belong to one transaction)
- **Belongs To:** products (many details reference one product)

### cart_items
- **Belongs To:** users (many cart items belong to one user)
- **Belongs To:** products (many cart items reference one product)

### chat_topics
- **Belongs To:** users (many topics belong to one user)
- **Has Many:** chat_messages (one topic has many messages)

### chat_messages
- **Belongs To:** chat_topics (many messages belong to one topic)
- **Belongs To:** users (many messages belong to one user, nullable for assistant)

---

## Notes

1. **Soft Deletes**: The `users`, `products`, and `chat_topics` tables implement soft deletes using the `deleted_at` column.

2. **Partial Unique Indexes**: To prevent unique constraint violations with soft deletes:
   - `users.username` is unique only when `deleted_at IS NULL`
   - `products.sku` is unique only when `deleted_at IS NULL`

3. **Foreign Key Constraints**:
   - `stock_histories.product_id` uses CASCADE on delete (if product is hard deleted, history is removed)
   - `stock_histories.user_id` uses RESTRICT on delete (cannot delete user who has stock history)
   - `transactions.user_id` uses CASCADE on delete
   - `transaction_details.transaction_id` and `transaction_details.product_id` use CASCADE on delete
   - `cart_items.user_id` and `cart_items.product_id` use CASCADE on delete
   - `chat_topics.user_id` uses CASCADE on delete
   - `chat_messages.chat_topic_id` and `chat_messages.user_id` use CASCADE on delete

4. **Enums**:
   - User roles: 'super-admin', 'admin', 'cashier'
   - Stock history types: 'increase', 'decrease'
   - Chat message roles: 'user', 'assistant'

5. **Default Values**:
   - `products.current_stock`: 0
   - `products.total_sold`: 0
   - `users.role`: 'cashier'
   - `transactions.total`: 0.00
   - `transaction_details.quantity`: 1
   - `transaction_details.price`: 0.00
   - `transaction_details.total`: 0.00
   - `cart_items.quantity`: 1
   - `cart_items.price`: 0.00

6. **Auto-generated Fields**:
   - `products.sku` can be auto-generated if not provided during creation

7. **Username-based Authentication**:
   - The system uses `username` for authentication instead of email
   - Users log in with username and password

8. **Cart Uniqueness**:
   - Each user can only have one cart item per product (enforced by unique composite index on user_id and product_id)
   - Multiple users can have the same product in their carts

9. **Price Snapshots**:
   - `cart_items.price` stores the product price at the time of adding to cart
   - `transaction_details.price` stores the product price at the time of purchase
   - This preserves historical pricing even if product prices change

10. **Chatbot Confirmations**:
    - `chat_topics.confirmation_action` and `confirmation_payload` store pending user confirmations
    - Used for multi-step chatbot interactions requiring user approval
