CREATE DATABASE cafe_db;

USE cafe_db;

-- Create the admin table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phoneno VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
);

-- Create the customer table
CREATE TABLE customer (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    phoneno VARCHAR(15) NOT NULL,
    password VARCHAR(255) NOT NULL,
    registration_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP -- Registration date
);

-- Create the tables table
CREATE TABLE tables (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tableno INT NOT NULL UNIQUE,
    tablesize INT NOT NULL,
    status ENUM('available', 'reserved', 'occupied') DEFAULT 'available',
    customer_id INT, -- Reference to a reserved customer, if applicable
    FOREIGN KEY (customer_id) REFERENCES customer(id) ON DELETE SET NULL
);

-- Create the menu table
CREATE TABLE menu (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT, -- Description of the menu item
    amount DECIMAL(10, 2) NOT NULL,
    status ENUM('in_stock', 'out_of_stock') DEFAULT 'in_stock',
    category ENUM('veg', 'non_veg') NOT NULL,
    picture_url VARCHAR(255) -- Optional URL for images
);

-- Create the order table
CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    menu_name INT,
    amount DECIMAL(10, 2) NOT NULL, -- Total amount for the order
    quantity INT NOT NULL,
    status ENUM('pending', 'in_progress', 'completed', 'canceled') DEFAULT 'pending',
    order_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP, -- Time when the order was placed
    FOREIGN KEY (customer_id) REFERENCES customer(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_name) REFERENCES menu(id) ON DELETE CASCADE
);

-- Create the bill table
CREATE TABLE bill (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,    
    order_id INT,
    amount DECIMAL(10, 2) NOT NULL,
    quantity INT NOT NULL,
    tableno INT,
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status ENUM('pending', 'completed', 'discarded') DEFAULT 'pending',
    FOREIGN KEY (customer_id) REFERENCES customer(id) ON DELETE CASCADE,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (tableno) REFERENCES tables(tableno) ON DELETE SET NULL
);

-- Create the feedback table
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    rating INT CHECK (rating BETWEEN 1 AND 5), -- Star rating from 1 to 5
    suggestions TEXT, -- Feedback/suggestions
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (customer_id) REFERENCES customer(id) ON DELETE CASCADE
);


CREATE TABLE logbook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    bill_id INT,
    amount DECIMAL(10, 2),
    customer_id INT,
    status VARCHAR(20),  -- Added status column to store bill status
    date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
);


CREATE TABLE orderLogbook (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    status VARCHAR(20),  -- Added status column to store order status
    quantity INT,
);




CREATE TABLE table_allocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    customer_id INT,
    table_no INT,
    FOREIGN KEY (customer_id) REFERENCES customer(id) ON DELETE CASCADE,
    FOREIGN KEY (table_no) REFERENCES tables(tableno) ON DELETE CASCADE
);


DELIMITER $$

CREATE PROCEDURE AggregateOrders()
BEGIN
    DECLARE done INT DEFAULT 0;
    DECLARE curr_customer_id INT;
    DECLARE total_amount DECIMAL(10, 2);
    DECLARE total_quantity INT;

    -- Cursor to loop through each unique customer_id in the orders table
    DECLARE customer_cursor CURSOR FOR 
        SELECT DISTINCT customer_id 
        FROM orders;

    -- Handler for cursor completion
    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = 1;

    -- Open the cursor
    OPEN customer_cursor;

    -- Loop through each customer_id
    read_loop: LOOP
        FETCH customer_cursor INTO curr_customer_id;
        
        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Aggregate total amount and quantity for the current customer_id
        SELECT SUM(amount), SUM(quantity) INTO total_amount, total_quantity
        FROM orders
        WHERE customer_id = curr_customer_id;

        -- Insert the aggregated values into the bill table
        INSERT INTO bill (customer_id, amount, quantity, status)
        VALUES (curr_customer_id, total_amount, total_quantity, 'pending');
    END LOOP;

    -- Close the cursor
    CLOSE customer_cursor;
END $$

DELIMITER ;



DELIMITER //

CREATE TRIGGER after_order_cancelled
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'cancelled' THEN
        -- Insert the order details into orderLogbook
        INSERT INTO orderLogbook (order_id, status, quantity)
        VALUES (OLD.id, OLD.status, OLD.quantity);

    END IF;
END; //

DELIMITER ;

DELIMITER //

CREATE TRIGGER after_bill_discarded
AFTER UPDATE ON bill
FOR EACH ROW
BEGIN
    IF NEW.status = 'discarded' THEN
        -- Insert the bill details into logbook
        INSERT INTO logbook (bill_id, amount, customer_id, status)
        VALUES (OLD.id, OLD.amount, OLD.customer_id, OLD.status);

    END IF;
END; //

DELIMITER ;


DELIMITER //

CREATE TRIGGER after_order_completed
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' THEN
        -- Insert the order details into orderLogbook
        INSERT INTO orderLogbook (order_id, status, quantity)
        VALUES (OLD.id, OLD.status, OLD.quantity);
    END IF;
END; //

DELIMITER ;



DELIMITER //

CREATE TRIGGER after_bill_completed
AFTER UPDATE ON bill
FOR EACH ROW
BEGIN
    IF NEW.status = 'completed' THEN
        -- Insert the bill details into logbook
        INSERT INTO logbook (bill_id, amount, customer_id, status)
        VALUES (OLD.id, OLD.amount, OLD.customer_id, OLD.status);
    END IF;
END; //

DELIMITER ;

DROP TRIGGER IF EXISTS after_order_cancelled;
DROP TRIGGER IF EXISTS after_order_completed;
DROP TRIGGER IF EXISTS after_bill_discarded;
DROP TRIGGER IF EXISTS after_bill_completed;



DELIMITER //

CREATE TRIGGER after_order_status_update
AFTER UPDATE ON orders
FOR EACH ROW
BEGIN
    IF NEW.status IN ('completed', 'cancelled') THEN
        -- Delete the order from the orders table
        DELETE FROM orders WHERE id = OLD.id;
    END IF;
END; //

DELIMITER ;


DELIMITER //

CREATE TRIGGER after_bill_status_update
AFTER UPDATE ON bill
FOR EACH ROW
BEGIN
    IF NEW.status IN ('completed', 'discarded') THEN
        -- Delete the bill from the bill table
        DELETE FROM bill WHERE id = OLD.id;
    END IF;
END; //

DELIMITER ;


DROP TRIGGER IF EXISTS after_order_status_update;
DROP TRIGGER IF EXISTS after_bill_status_update;
