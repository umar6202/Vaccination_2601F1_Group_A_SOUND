CREATE TABLE IF NOT EXISTS store_orders (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_number VARCHAR(24) NOT NULL,
    customer_name VARCHAR(150) NOT NULL,
    customer_email VARCHAR(254) NOT NULL,
    customer_phone VARCHAR(40) NOT NULL,
    address_line1 VARCHAR(255) NOT NULL,
    address_line2 VARCHAR(255) DEFAULT NULL,
    city VARCHAR(120) NOT NULL,
    region VARCHAR(120) DEFAULT NULL,
    postal_code VARCHAR(32) DEFAULT NULL,
    country VARCHAR(120) NOT NULL,
    customer_note TEXT DEFAULT NULL,
    payment_method VARCHAR(32) NOT NULL DEFAULT 'cash_on_delivery',
    status VARCHAR(32) NOT NULL DEFAULT 'pending',
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_amount DECIMAL(10,2) DEFAULT NULL,
    total_amount DECIMAL(10,2) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_store_orders_order_number (order_number),
    KEY idx_store_orders_status_created (status, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS store_order_items (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    order_id BIGINT UNSIGNED NOT NULL,
    product_key VARCHAR(80) NOT NULL,
    sku VARCHAR(80) NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_size VARCHAR(16) DEFAULT NULL,
    unit_price DECIMAL(10,2) NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    line_total DECIMAL(10,2) NOT NULL,
    PRIMARY KEY (id),
    KEY idx_store_order_items_order (order_id),
    CONSTRAINT fk_store_order_items_order
        FOREIGN KEY (order_id) REFERENCES store_orders (id)
        ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
