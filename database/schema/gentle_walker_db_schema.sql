-- 1. Products
CREATE TABLE products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    category VARCHAR(50),
    description TEXT,
    base_unit VARCHAR(20) NOT NULL,
    is_perishable BOOLEAN DEFAULT FALSE,
    sku VARCHAR(50) UNIQUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 2. Product Prices (Price Tiers)
CREATE TABLE product_prices (
    price_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    tier ENUM('Retail', 'Wholesale', 'Distributor') NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 3. Unit Conversion
CREATE TABLE unit_conversion (
    conversion_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    from_unit VARCHAR(20) NOT NULL,
    to_unit VARCHAR(20) NOT NULL,
    multiplier DECIMAL(10,4) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 4. Inventory with Batches
CREATE TABLE inventory (
    inventory_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    batch_number VARCHAR(50) NOT NULL,
    expiry_date DATE,
    location VARCHAR(100),
    quantity INT NOT NULL,
    received_date DATE,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE(product_id, batch_number),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 5. Suppliers
CREATE TABLE suppliers (
    supplier_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    contact_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 6. Purchase Orders
CREATE TABLE purchase_orders (
    po_id INT AUTO_INCREMENT PRIMARY KEY,
    supplier_id INT NOT NULL,
    order_date DATE NOT NULL,
    status ENUM('Pending', 'Partially Received', 'Received', 'Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (supplier_id) REFERENCES suppliers(supplier_id)
);

-- 7. PO Items + Receiving
CREATE TABLE purchase_order_lines (
    po_line_id INT AUTO_INCREMENT PRIMARY KEY,
    po_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity_ordered INT NOT NULL,
    quantity_received INT DEFAULT 0,
    batch_number VARCHAR(50),
    expiry_date DATE,
    cost_price DECIMAL(10, 2) NOT NULL,
    uom VARCHAR(20) NOT NULL,
    FOREIGN KEY (po_id) REFERENCES purchase_orders(po_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 8. Customers
CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    type ENUM('Retail', 'Wholesale', 'Distributor') DEFAULT 'Retail',
    contact_info TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 9. Sales Orders
CREATE TABLE sales_orders (
    so_id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT NOT NULL,
    order_date DATE NOT NULL,
    status ENUM('Pending', 'Prepared', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Pending',
    FOREIGN KEY (customer_id) REFERENCES customers(customer_id)
);

-- 10. Sales Order Items
CREATE TABLE sales_order_items (
    so_item_id INT AUTO_INCREMENT PRIMARY KEY,
    so_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL,
    uom VARCHAR(20) NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    batch_number VARCHAR(50),
    FOREIGN KEY (so_id) REFERENCES sales_orders(so_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 11. Shipping
CREATE TABLE shipping (
    shipping_id INT AUTO_INCREMENT PRIMARY KEY,
    so_id INT NOT NULL,
    shipping_date DATE NOT NULL,
    driver VARCHAR(100),
    vehicle_plate VARCHAR(20),
    status ENUM('Planned', 'Shipped', 'Delivered', 'Cancelled') DEFAULT 'Planned',
    FOREIGN KEY (so_id) REFERENCES sales_orders(so_id)
);

CREATE TABLE shipping_items (
    shipping_item_id INT AUTO_INCREMENT PRIMARY KEY,
    shipping_id INT NOT NULL,
    product_id INT NOT NULL,
    batch_number VARCHAR(50),
    quantity INT NOT NULL,
    uom VARCHAR(20) NOT NULL,
    FOREIGN KEY (shipping_id) REFERENCES shipping(shipping_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 12. Stock Movements
CREATE TABLE stock_movements (
    movement_id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    batch_number VARCHAR(50),
    movement_type ENUM('IN', 'OUT', 'ADJUSTMENT', 'TRANSFER') NOT NULL,
    reference VARCHAR(50),
    quantity INT NOT NULL,
    uom VARCHAR(20) NOT NULL,
    date DATE NOT NULL,
    location VARCHAR(100),
    remarks TEXT,
    FOREIGN KEY (product_id) REFERENCES products(product_id)
);

-- 13. Finance
CREATE TABLE finance (
    finance_id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('Invoice', 'Bill', 'Payment', 'Expense') NOT NULL,
    reference_id VARCHAR(50),
    party VARCHAR(100),
    date DATE NOT NULL,
    due_date DATE,
    amount DECIMAL(10, 2) NOT NULL,
    payment_method ENUM('Cash', 'Bank', 'GCash', 'Check') DEFAULT 'Cash',
    status ENUM('Unpaid', 'Paid', 'Partial', 'Cancelled') DEFAULT 'Unpaid',
    remarks TEXT
);

-- 14. Users
CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    role ENUM('Admin', 'Warehouse', 'Sales', 'Finance') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 15. Logs and Notifications
CREATE TABLE logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    action VARCHAR(50),
    entity VARCHAR(50),
    reference_id VARCHAR(50),
    message VARCHAR(255),
    status ENUM('Unread', 'Read') DEFAULT 'Unread',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
