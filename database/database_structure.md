# Database Structure

## Tables

### 1. users
User accounts for the POS system (Super Admin, Admin, Cashier)

| Column                    | Type                                    | Attributes                                                |
| ------------------------- | --------------------------------------- | --------------------------------------------------------- |
| id                        | BIGINT UNSIGNED                         | PRIMARY KEY, AUTO_INCREMENT                               |
| name                      | VARCHAR(255)                            | NOT NULL                                                  |
| email                     | VARCHAR(255)                            | NOT NULL, UNIQUE (partial index where deleted_at IS NULL) |
| email_verified_at         | TIMESTAMP                               | NULLABLE                                                  |
| password                  | VARCHAR(255)                            | NOT NULL                                                  |
| role                      | ENUM('super-admin', 'admin', 'cashier') | NOT NULL, DEFAULT 'cashier'                               |
| two_factor_secret         | TEXT                                    | NULLABLE                                                  |
| two_factor_recovery_codes | TEXT                                    | NULLABLE                                                  |
| two_factor_confirmed_at   | TIMESTAMP                               | NULLABLE                                                  |
| remember_token            | VARCHAR(100)                            | NULLABLE                                                  |
| created_at                | TIMESTAMP                               | NULLABLE                                                  |
| updated_at                | TIMESTAMP                               | NULLABLE                                                  |
| deleted_at                | TIMESTAMP                               | NULLABLE                                                  |

**Indexes:**
- PRIMARY KEY (id)
- UNIQUE KEY unique_email_not_deleted (email) WHERE deleted_at IS NULL
- INDEX idx_role (role)
- INDEX idx_deleted_at (deleted_at)

**Constraints:**
- email must be unique among non-deleted records
- role must be one of: 'super-admin', 'admin', 'cashier'

---

### 2. products
Product catalog for the POS system

| Column        | Type            | Attributes                                                |
| ------------- | --------------- | --------------------------------------------------------- |
| id            | BIGINT UNSIGNED | PRIMARY KEY, AUTO_INCREMENT                               |
| name          | VARCHAR(255)    | NOT NULL                                                  |
| sku           | VARCHAR(255)    | NOT NULL, UNIQUE (partial index where deleted_at IS NULL) |
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

## Relationships

### users
- **Has Many:** stock_histories (one user can have many stock history records)

### products
- **Has Many:** stock_histories (one product can have many stock history records)

### stock_histories
- **Belongs To:** products (many stock histories belong to one product)
- **Belongs To:** users (many stock histories belong to one user who made the change)

---

## Notes

1. **Soft Deletes**: The `users` and `products` tables implement soft deletes using the `deleted_at` column.

2. **Partial Unique Indexes**: To prevent unique constraint violations with soft deletes:
   - `users.email` is unique only when `deleted_at IS NULL`
   - `products.sku` is unique only when `deleted_at IS NULL`

3. **Foreign Key Constraints**:
   - `stock_histories.product_id` uses CASCADE on delete (if product is hard deleted, history is removed)
   - `stock_histories.user_id` uses RESTRICT on delete (cannot delete user who has stock history)

4. **Enums**:
   - User roles: 'super-admin', 'admin', 'cashier'
   - Stock history types: 'increase', 'decrease'

5. **Default Values**:
   - `products.current_stock`: 0
   - `products.total_sold`: 0
   - `users.role`: 'cashier'

6. **Auto-generated Fields**:
   - `products.sku` can be auto-generated if not provided during creation
